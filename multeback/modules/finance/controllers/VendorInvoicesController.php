<?php

namespace multeback\modules\finance\controllers;

use Yii;
use multebox\models\VendorInvoices;
use multebox\models\search\VendorInvoices as VendorInvoicesSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * VendorInvoicesController implements the CRUD actions for VendorInvoices model.
 */
class VendorInvoicesController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all VendorInvoices models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('VendorInvoices.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $searchModel = new VendorInvoicesSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

	public function actionGetInvoice()
    {
		if(!Yii::$app->user->can('VendorInvoices.View')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        return $this->render('invoice', [
            'invoice_id' => $_REQUEST['id']
        ]);
    }

	public function actionGetInvoicePdf()
	{
		if(!Yii::$app->user->can('VendorInvoices.View')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		// get your HTML raw content without any layouts or scripts
		$content = $this->renderPartial('invoice-pdf', [
            'invoice_id' => $_REQUEST['id']
        ]);
		
		$pdf = new Pdf([
			'mode' => Pdf::MODE_UTF8, 
			'format' => Pdf::FORMAT_A4, 
			'orientation' => Pdf::ORIENT_LANDSCAPE, 
			'filename' => $_REQUEST['id'].'.pdf',
			'destination' => Pdf::DEST_DOWNLOAD , 
			'content' => $content,  
			//'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.css',
			'cssFile' => 'css/techraft.css',
			'cssInline' => '.kv-heading-1{font-size:18px}', 
			'options' => ['title' => Yii::t('app', 'Invoice#').$_REQUEST['id']],
			'methods' => [ 
				'SetHeader'=>[Yii::t('app', 'Vendor Invoice#').$_REQUEST['id']], 
				'SetFooter'=>[Yii::t('app', 'Thanks for partnering with').' '.Yii::$app->params['COMPANY_NAME'].' | |'.Yii::t('app', 'Page').'#'.'{PAGENO}'],
			]
		]);
		
		// return the pdf output as per the destination setting
		return $pdf->render(); 
	}

    /**
     * Displays a single VendorInvoices model.
     * @param integer $id
     * @return mixed
     */
    /*public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }*/

    /**
     * Creates a new VendorInvoices model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new VendorInvoices;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Updates an existing VendorInvoices model.
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
     * Deletes an existing VendorInvoices model.
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
     * Finds the VendorInvoices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VendorInvoices the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VendorInvoices::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

	public function actionMarkPaid($id)
    {
		if(!Yii::$app->user->can('VendorInvoices.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $result = $this->findModel($id);
		$result->paid_ind = 1;
		$result->updated_at = time();
		$result->save();
        return $this->redirect(['index']);
    }

	public function actionMarkUnpaid($id)
    {
		if(!Yii::$app->user->can('VendorInvoices.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $result = $this->findModel($id);
		$result->paid_ind = 0;
		$result->updated_at = time();
		$result->save();
        return $this->redirect(['index']);
    }
}
