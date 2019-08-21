<?php

namespace multeback\modules\finance\controllers;

use Yii;
use multebox\models\PaymentMethods;
use multebox\models\search\PaymentMethods as PaymentMethodsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
/**
 * PaymentMethodsController implements the CRUD actions for PaymentMethods model.
 */
class PaymentMethodsController extends Controller
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
     * Lists all PaymentMethods models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('PaymentMethods.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		// Make Default 
		\multebox\models\DefaultValueModule::upsertDefault('payment_method');
		extract(PaymentMethods::find()->select("Max(sort_order) max_sort_order")->asArray()->one());
		//var_dump($_REQUEST['actionType']);
		if(!empty($_REQUEST['sort_order_update']) && !empty($_REQUEST['actionType'])){
			//$model = $this->findModel($_REQUEST['sort_order_update']);
			$statusId = $_REQUEST['sort_order_update'];
			$sortValue = $_REQUEST['sort_order_update'.$statusId];	
			//var_dump($statusId." ".$sortValue);
			if($_REQUEST['actionType'] !='Down'){
				if($sortValue !='1'){
					$minusValue = intval($sortValue)-1;
					$cusotomerUpdate= PaymentMethods::find()->where(['sort_order' => $sortValue])->one();
					$cusotomerUpdate1= PaymentMethods::find()->where(['sort_order' => $minusValue])->one();
					$cusotomerUpdate->sort_order=$minusValue;
					$cusotomerUpdate->update();
					
					$cusotomerUpdate1->sort_order=$sortValue;
					$cusotomerUpdate1->update();
				}
			}else if($_REQUEST['actionType'] == 'Down'){
				if($max_sort_order !=$sortValue){
					$plusValue = intval($sortValue)+1;
					$cusotomerUpdate= PaymentMethods::find()->where(['sort_order' => $sortValue])->one();
					$cusotomerUpdate1= PaymentMethods::find()->where(['sort_order' => $plusValue])->one();
					$cusotomerUpdate->sort_order=$plusValue;
					$cusotomerUpdate->update();
					$cusotomerUpdate1->sort_order=$sortValue;
					$cusotomerUpdate1->update();
				}
			}
		}
        $searchModel = new PaymentMethodsSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * Displays a single PaymentMethods model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!Yii::$app->user->can('PaymentMethods.Index')){
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
     * Creates a new PaymentMethods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		//if(!Yii::$app->user->can('PaymentMethods.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		//}
       /* $model = new PaymentMethods;
		extract(PaymentMethods::find()->select("Max(sort_order) max_sort_order")->asArray()->one());
        
		$model->sort_order=$max_sort_order+1;
        // Make Default 
		\multebox\models\DefaultValueModule::upsertDefault('payment_method');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }*/
    }
    /**
     * Updates an existing PaymentMethods model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		//if(!Yii::$app->user->can('PaymentMethods.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		//}
       /* $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }*/
    }
    /**
     * Deletes an existing PaymentMethods model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		//if(!Yii::$app->user->can('PaymentMethods.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
	//	}
      //  $this->findModel($id)->delete();
        //return $this->redirect(['index']);
    }
    /**
     * Finds the PaymentMethods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentMethods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PaymentMethods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

	public function actionActivate($id)
    {
		if(!Yii::$app->user->can('PaymentMethods.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $result = $this->findModel($id);
		$result->active = 1;
		$result->updated_at = time();
		$result->save();

        return $this->redirect(['index']);
    }

	public function actionDeactivate($id)
    {
		if(!Yii::$app->user->can('PaymentMethods.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $result = $this->findModel($id);
		$result->active = 0;
		$result->updated_at = time();
		$result->save();

        return $this->redirect(['index']);
    }
}
