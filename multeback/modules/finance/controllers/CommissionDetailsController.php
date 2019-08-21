<?php

namespace multeback\modules\finance\controllers;

use Yii;
use multebox\models\Commission;
use multebox\models\CommissionDetails;
use multebox\models\VendorInvoices;
use multebox\models\OrderStatus;
use multebox\models\SubOrder;
use multebox\models\SendEmail;
use multebox\models\search\MulteModel;
use multebox\models\search\CommissionDetails as CommissionDetailsSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use \Exception;
use kartik\mpdf\Pdf;

/**
 * CommissionDetailsController implements the CRUD actions for CommissionDetails model.
 */
class CommissionDetailsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'delete' => ['post'],
					'process' => ['post'],
					'generate-invoices' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all CommissionDetails models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('Commission.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $searchModel = new CommissionDetailsSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

	public function actionGenerateInvoices()
	{
		if(!Yii::$app->user->can('Commission.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		$vendors = CommissionDetails::find()->where("invoiced_ind=0")->select('vendor_id')->distinct()->all();

		foreach ($vendors as $vendor)
		{
			$vendor_data = CommissionDetails::find()->where("vendor_id=".$vendor->vendor_id." and invoiced_ind=0")->all();
			
			$total_commission = 0;
			$total_order_amount = 0;
			foreach($vendor_data as $data)
			{
				$total_commission += $data->commission;
				$total_order_amount += $data->sub_order_total;
			}

			try
			{
				$connection = Yii::$app->db;
				$transaction = $connection->beginTransaction();

				$vendor_invoice = new VendorInvoices;

				$vendor_invoice->vendor_id = $vendor->vendor_id;
				$vendor_invoice->total_commission = $total_commission;
				$vendor_invoice->total_order_amount = $total_order_amount;
				$vendor_invoice->paid_ind = 0;
				$vendor_invoice->added_at = time();
				$vendor_invoice->save();

				CommissionDetails::updateAll(['invoiced_ind' => 1, 'vendor_invoice_id' => $vendor_invoice->id], ['and', ['=', 'vendor_id', $vendor->vendor_id], ['=', 'invoiced_ind', 0]]);

				// get your HTML raw content without any layouts or scripts
				$content = $this->renderPartial('/vendor-invoices/invoice-pdf', [
					'invoice_id' => $vendor_invoice->id
				]);
				$filename = 'vendor_invoices/'.$vendor_invoice->id.'.pdf';
				
				$pdf = new Pdf([
					'mode' => Pdf::MODE_UTF8, 
					'format' => Pdf::FORMAT_A4, 
					'orientation' => Pdf::ORIENT_LANDSCAPE, 
					'filename' => $filename,
					'destination' => Pdf::DEST_FILE , 
					'content' => $content,  
					//'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.css',
					'cssFile' => 'css/techraft.css',
					'cssInline' => '.kv-heading-1{font-size:18px}', 
					'options' => ['title' => Yii::t('app', 'Invoice#').$_REQUEST['id']],
					'methods' => [ 
						'SetHeader'=>[Yii::t('app', 'Vendor Invoice#').$_REQUEST['id']], 
						'SetFooter'=>[Yii::t('app', 'Thanks for partenering with').' '.Yii::$app->params['COMPANY_NAME'].' | |'.Yii::t('app', 'Page').'#'.'{PAGENO}'],
					]
				]);
				
				// render the pdf output as per the destination setting
				$pdf->render();

				// Mail the generated invoice to vendor
				SendEmail::sendVendorInvoiceEmail($vendor->vendor_id, $filename);
				
				// Now remove the file from disk
				unlink($filename);

				$transaction->commit();
			}
			catch (Exception $e)
			{
				$transaction->rollback();
				Yii::$app->session->setFlash('error', Yii::t('app', 'Error trying to generate vendor invoices - Error message: ').$e->getMessage());
				break;
			}
		}

		$searchModel = new CommissionDetailsSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
	}

	public function actionProcess()
    {
		if(!Yii::$app->user->can('Commission.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		$query = "select a.*, c.category_id, c.sub_category_id, c.sub_subcategory_id from tbl_sub_order a, tbl_inventory b, tbl_product c, tbl_product_sub_subcategory d
					where a.is_processed = 0
					and a.sub_order_status in ('".OrderStatus::_DELIVERED."', '".OrderStatus::_RETURN_CANCELED."', '".OrderStatus::_RETURN_REJECTED."')
					and a.inventory_id = b.id
					and b.product_id = c.id
					and c.sub_subcategory_id = d.id
					and ifnull(d.return_window,0)*24*60*60 + a.updated_at < unix_timestamp()";
//var_dump($query);exit;
		$connection = \Yii::$app->db;

		$model = $connection->createCommand($query);

		$list_of_suborders = $model->queryAll();

		$commission_rules = Commission::find()->orderBy('sub_subcategory_id desc, sub_category_id desc, category_id desc')->all();
		$commission = 0;

		foreach ($list_of_suborders as $suborder)
		{
			foreach ($commission_rules as $rule)
			{
				if ($rule->category_id == '' || $rule->category_id == 0)
				{
					$commission = MulteModel::getCommission($suborder, $rule);
					break;
				}
				else if (($rule->sub_category_id == '' || $rule->sub_category_id == 0) && ($rule->category_id == $suborder['category_id']))
				{
					$commission = MulteModel::getCommission($suborder, $rule);
					break;
				}
				else if (($rule->sub_subcategory_id == '' || $rule->sub_subcategory_id == 0) && (($rule->category_id == $suborder['category_id'] && $rule->sub_category_id == $suborder['sub_category_id'])))
				{
					$commission = MulteModel::getCommission($suborder, $rule);
					break;
				}
				else if ($rule->category_id == $suborder['category_id'] && $rule->sub_category_id == $suborder['sub_category_id'] && $rule->sub_subcategory_id == $suborder['sub_subcategory_id'])
				{
					$commission = MulteModel::getCommission($suborder, $rule);
					break;
				}
			}
			
			try
			{
				$connection = Yii::$app->db;
				$transaction = $connection->beginTransaction();

				$commissiondetail = new CommissionDetails;
				$commissiondetail->sub_order_id = $suborder['id'];
				$commissiondetail->sub_order_total = MulteModel::getSubOrderVendorCost($suborder);//$suborder['total_cost'];
				$commissiondetail->vendor_id = $suborder['vendor_id'];
				$commissiondetail->inventory_id = $suborder['inventory_id'];
				$commissiondetail->commission_snapshot = Json::encode($rule);
				$commissiondetail->commission = $commission;
				$commissiondetail->invoiced_ind = 0;
				$commissiondetail->vendor_invoice_id = 0;
				$commissiondetail->added_at = time();

				$commissiondetail->save();

				$sorder = SubOrder::findOne($suborder['id']);

				$sorder->is_processed = 1;
				$sorder->save();

				$transaction->commit();
			}
			catch (Exception $e)
			{
				$transaction->rollback();
				Yii::$app->session->setFlash('error', Yii::t('app', 'Error processing commission.').$e->getMessage());
				return $this-redirect(['index']);
			}
		}

		//var_dump($list_of_suborders);exit;

        $searchModel = new CommissionDetailsSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single CommissionDetails model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!Yii::$app->user->can('Commission.View')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    /**
     * Creates a new CommissionDetails model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new CommissionDetails;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Updates an existing CommissionDetails model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Deletes an existing CommissionDetails model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the CommissionDetails model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CommissionDetails the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CommissionDetails::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
