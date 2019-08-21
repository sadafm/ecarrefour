<?php

namespace multeback\modules\finance\controllers;

use Yii;
use multebox\models\DiscountCoupons;
use multebox\models\search\DiscountCoupons as DiscountCouponsSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \DateTime;
use \DateTimeZone;

/**
 * DiscountCouponsController implements the CRUD actions for DiscountCoupons model.
 */
class DiscountCouponsController extends Controller
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
     * Lists all DiscountCoupons models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('DiscountCoupons.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $searchModel = new DiscountCouponsSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single DiscountCoupons model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!Yii::$app->user->can('DiscountCoupons.View')){
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
     * Creates a new DiscountCoupons model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		if(!Yii::$app->user->can('DiscountCoupons.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = new DiscountCoupons;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			$model->added_at = time();

			$expiry_datetime = new DateTime($_REQUEST['DiscountCoupons']['expiry_datetime'], new DateTimeZone(Yii::$app->params['TIME_ZONE']));
			$model->expiry_datetime = $expiry_datetime->getTimestamp();
			$model->save();

            //return $this->redirect(['view', 'id' => $model->id]);
			return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing DiscountCoupons model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		if(!Yii::$app->user->can('DiscountCoupons.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = $this->findModel($id);

		if(Yii::$app->user->identity->entity_type == 'vendor' && Yii::$app->user->identity->id != $model->added_by_id)
		{
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

			$expiry_datetime = new DateTime($_REQUEST['DiscountCoupons']['expiry_datetime'], new DateTimeZone(Yii::$app->params['TIME_ZONE']));
			$model->expiry_datetime = $expiry_datetime->getTimestamp();
			$model->save();
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing DiscountCoupons model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		if(!Yii::$app->user->can('DiscountCoupons.Delete')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DiscountCoupons model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DiscountCoupons the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DiscountCoupons::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

	public function actionAjaxDiscountCoupons(){
      $coupon_code=$_REQUEST['coupon_code'];
      //var_dump($ticket_priority_id);die();
      if(DiscountCoupons::find()->andwhere(['=','coupon_code',$coupon_code])->exists())
      { 
        echo Yii::t('app', 'Discount Coupon Code Already Taken - Please use another!');
      } else{
		  return false;
      }
  }
}
