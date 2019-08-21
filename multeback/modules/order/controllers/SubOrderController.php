<?php

namespace multeback\modules\order\controllers;

use Yii;
use multebox\models\Order;
use multebox\models\SubOrder;
use multebox\models\OrderStatus;
use multebox\models\PaymentMethods;
use multebox\models\Invoice;
use multebox\models\search\MulteModel;
use multebox\models\SendEmail;
use multebox\models\ShippingDetail;
use multebox\models\search\SubOrder as SubOrderSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \Exception;
use kartik\mpdf\Pdf;
use yii\helpers\Url;

/**
 * SubOrderController implements the CRUD actions for SubOrder model.
 */
class SubOrderController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
					'update-status' => ['post'],
					'get-invoice' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all SubOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('SubOrder.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        /*$searchModel = new SubOrderSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);*/
    }

	public function actionVendorIndex()
    {
		if(!Yii::$app->user->can('SubOrder.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

		if (Yii::$app->user->identity->entity_type != 'vendor')
		{
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

        $searchModel = new SubOrderSearch;
        $dataProvider = $searchModel->vendorSearch(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

	public function actionViewOrder($id)
    {
		if(!Yii::$app->user->can('SubOrder.View')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $searchModel = new SubOrderSearch;
        $dataProvider = $searchModel->orderSearch(Yii::$app->request->getQueryParams(), $id);

		 return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

	public function actionGetInvoice()
    {
		if(!Yii::$app->user->can('SubOrder.View')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $sub_order_id = $_REQUEST['id'];
		$sub_order = SubOrder::findOne($sub_order_id);
		$order = Order::findOne($sub_order->order_id);

		$invoice = Invoice::find()->where("sub_order_id=".$sub_order_id)->one();

		if(!$invoice)
		{
			$invoice = new Invoice;
			$invoice->sub_order_id = $sub_order_id;
			$invoice->save();
		}

		return $this->render('invoice', [
            'sub_order' => $sub_order,
            'order' => $order,
			'invoice' => $invoice
        ]);
    }

	public function actionGetInvoicePdf()
	{
		if(!Yii::$app->user->can('SubOrder.View')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		$sub_order_id = $_REQUEST['id'];
		$sub_order = SubOrder::findOne($sub_order_id);
		$order = Order::findOne($sub_order->order_id);

		$invoice = Invoice::find()->where("sub_order_id=".$sub_order_id)->one();

		// get your HTML raw content without any layouts or scripts
		$content = $this->renderPartial('invoice-pdf', [
            'sub_order' => $sub_order,
            'order' => $order,
			'invoice' => $invoice
        ]);
		
		// setup kartik\mpdf\Pdf component
		$pdf = new Pdf([
			// set to use core fonts only
			'mode' => Pdf::MODE_UTF8, 
			// A4 paper format
			'format' => Pdf::FORMAT_A4, 
			// portrait orientation
			'orientation' => Pdf::ORIENT_LANDSCAPE, 
			'filename' => $invoice->id.'.pdf',
			// stream to browser inline
			'destination' => Pdf::DEST_DOWNLOAD , 
			// your html content input
			'content' => $content,  
			// format content from your own css file if needed or use the
			// enhanced bootstrap css built by Krajee for mPDF formatting 
			//'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.css',
			'cssFile' => 'css/techraft.css',
			// any css to be embedded if required
			'cssInline' => '.kv-heading-1{font-size:18px}', 
			 // set mPDF properties on the fly
			'options' => ['title' => Yii::t('app', 'Invoice for Order ID#').$order->id],
			 // call mPDF methods on the fly
			'methods' => [ 
				'SetHeader'=>[Yii::t('app', 'Invoice for Order ID#').$sub_order->id], 
				//'SetFooter'=>['{PAGENO}'],
				'SetFooter'=>[Yii::t('app', 'Thanks for shopping with').' '.Yii::$app->params['COMPANY_NAME'].' | |'.Yii::t('app', 'Page').'#'.'{PAGENO}'],
			]
		]);
		
		// return the pdf output as per the destination setting
		return $pdf->render(); 
	}

	public function actionUpdateStatus()
    {
		if(!Yii::$app->user->can('SubOrder.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $sub_order_id = $_REQUEST['id'];
		$rqst = $_REQUEST['rqst'];
		
		try
		{
			$connection = Yii::$app->db;
			$transaction = $connection->beginTransaction();

			$suborder = SubOrder::findOne($sub_order_id);

			switch ($rqst)
			{
				case "accept":
				case "inprocess":
					$suborder->sub_order_status = OrderStatus::_IN_PROCESS;
					$suborder->save();
					MulteModel::updateMainOrderStatus($suborder->id);
					SendEmail::sendOrderInProcessEmail($suborder->order_id);

					break;
				
				case "reject":
				case "cancel":
					$suborder->sub_order_status = OrderStatus::_CANCELED;
					$suborder->save();
					MulteModel::updateMainOrderStatus($suborder->id);
					Yii::$app->session->setFlash('success', Yii::t('app', 'Order Successfully Canceled!'));
					
					MulteModel::releaseAndRefund($suborder);
					
					if ($suborder->payment_method != PaymentMethods::_COD)
					{
						$suborder->sub_order_status = OrderStatus::_REFUNDED;
						$suborder->save();
						MulteModel::updateMainOrderStatus($suborder->id);
						Yii::$app->session->setFlash('success', Yii::t('app', 'Order Successfully Refunded!'));
					}

					break;

				case "readytoship":
					$suborder->sub_order_status = OrderStatus::_READY_TO_SHIP;
					$suborder->save();
					MulteModel::updateMainOrderStatus($suborder->id);
					break;

				case "shipped":
					$suborder->sub_order_status = OrderStatus::_SHIPPED;
					$suborder->save();
					MulteModel::updateMainOrderStatus($suborder->id);
					break;

				case "delivered":
					$suborder->sub_order_status = OrderStatus::_DELIVERED;
					$suborder->updated_at = time();
					$suborder->save();
					MulteModel::updateMainOrderStatus($suborder->id);
					break;

				case "approvereturn":
					$suborder->sub_order_status = OrderStatus::_RETURN_APPROVED;
					$suborder->save();
					MulteModel::updateMainOrderStatus($suborder->id);
					break;

				case "rejectreturn":
					$suborder->sub_order_status = OrderStatus::_RETURN_REJECTED;
					$suborder->save();
					MulteModel::updateMainOrderStatus($suborder->id);
					break;

				case "returned":
					$suborder->sub_order_status = OrderStatus::_RETURNED;
					$suborder->save();
					MulteModel::updateMainOrderStatus($suborder->id);
					Yii::$app->session->setFlash('success', Yii::t('app', 'Order Successfully Returned!'));

					MulteModel::releaseAndRefund($suborder);					

					if ($suborder->payment_method != PaymentMethods::_COD)
					{
						$suborder->sub_order_status = OrderStatus::_REFUNDED;
						$suborder->save();
						MulteModel::updateMainOrderStatus($suborder->id);
						Yii::$app->session->setFlash('success', Yii::t('app', 'Order Successfully Refunded!'));
					}

					break;

				case "refund":
					MulteModel::releaseAndRefund($suborder);

					$suborder->sub_order_status = OrderStatus::_REFUNDED;
					$suborder->save();
					MulteModel::updateMainOrderStatus($suborder->id);

					Yii::$app->session->setFlash('success', Yii::t('app', 'Order Successfully Refunded!'));
					break;
			}

			$shipmodel = ShippingDetail::findOne(['sub_order_id' => $_REQUEST['id']]);
			if(!$shipmodel)
				$shipmodel = new ShippingDetail();
			if ($shipmodel->load(Yii::$app->request->post()) && $shipmodel->save()) 
			{
				Yii::$app->session->setFlash('success', Yii::t('app', 'Shipping Details Successfully Updated!'));
			}
			else
			{
				//print_r($shipmodel->getErrors());exit;
			}


			\multebox\models\SendEmail::sendSubOrderStatusChangeEmail($sub_order_id);

			$transaction->commit();
		}
		catch (Exception $e)
		{
			$transaction->rollback();
			Yii::$app->session->setFlash('error', Yii::t('app', 'Unable to update order status - Error message: ').$e->getMessage());
		}

		return $this->redirect(['sub-order-view', 'id' => $sub_order_id]);
    }


	/**
     * Displays a single SubOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionSubOrderView($id)
    {
		if(!Yii::$app->user->can('SubOrder.View')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		$model = $this->findModel($id);
		$shipmodel = ShippingDetail::findOne(['sub_order_id' => $id]);
		if(!$shipmodel)
			$shipmodel = new ShippingDetail();

		if(Yii::$app->user->identity->entity_type == 'vendor' && Yii::$app->user->identity->entity_id != $model->vendor_id)
		{
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['sub-order-view', 'id' => $model->id]);
        } else {
            return $this->render('sub-order-view', [
                'model' => $model,
				'shipmodel' => $shipmodel,
            ]);
        }
    }

    /**
     * Displays a single SubOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        /*$model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }*/
    }

    /**
     * Creates a new SubOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));

		/*
        $model = new SubOrder;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }*/
    }

    /**
     * Updates an existing SubOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		if(!Yii::$app->user->can('SubOrder.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SubOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		/*
        $this->findModel($id)->delete();

        return $this->redirect(['index']);*/
    }

    /**
     * Finds the SubOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SubOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SubOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
