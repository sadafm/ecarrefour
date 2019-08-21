<?php
namespace multeback\modules\vendor\controllers;
use Yii;
use multebox\models\VendorType;
use multebox\models\search\VendorType as VendorTypeSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
/**
 * VendorTypeController implements the CRUD actions for VendorType model.
 */
class VendorTypeController extends Controller
{
	public function init(){
		
	}
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
     * Lists all VendorType models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('VendorType.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		// Make Default 
		\multebox\models\DefaultValueModule::upsertDefault('vendor_type');
		extract(VendorType::find()->select("Max(sort_order) max_sort_order")->asArray()->one());
		//var_dump($_REQUEST['actionType']);
		if(!empty($_REQUEST['sort_order_update']) && !empty($_REQUEST['actionType'])){
			//$model = $this->findModel($_REQUEST['sort_order_update']);
			$statusId = $_REQUEST['sort_order_update'];
			$sortValue = $_REQUEST['sort_order_update'.$statusId];	
			//var_dump($statusId." ".$sortValue);
			if($_REQUEST['actionType'] !='Down'){
				if($sortValue !='1'){
					$minusValue = intval($sortValue)-1;
					$cusotomerUpdate= VendorType::find()->where(['sort_order' => $sortValue])->one();
					$cusotomerUpdate1= VendorType::find()->where(['sort_order' => $minusValue])->one();
					$cusotomerUpdate->sort_order=$minusValue;
					$cusotomerUpdate->update();
					
					$cusotomerUpdate1->sort_order=$sortValue;
					$cusotomerUpdate1->update();
				}
			}else if($_REQUEST['actionType'] == 'Down'){
				if($max_sort_order !=$sortValue){
					$plusValue = intval($sortValue)+1;
					$cusotomerUpdate= VendorType::find()->where(['sort_order' => $sortValue])->one();
					$cusotomerUpdate1= VendorType::find()->where(['sort_order' => $plusValue])->one();
					$cusotomerUpdate->sort_order=$plusValue;
					$cusotomerUpdate->update();
					$cusotomerUpdate1->sort_order=$sortValue;
					$cusotomerUpdate1->update();
				}
			}
		}
        $searchModel = new VendorTypeSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * Displays a single VendorType model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!Yii::$app->user->can('VendorType.Index')){
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
     * Creates a new VendorType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		if(!Yii::$app->user->can('VendorType.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = new VendorType;
		extract(VendorType::find()->select("Max(sort_order) max_sort_order")->asArray()->one());
        
		$model->sort_order=$max_sort_order+1;
        // Make Default 
		\multebox\models\DefaultValueModule::upsertDefault('vendor_type');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'added' => 'yes']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Updates an existing VendorType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		if(!Yii::$app->user->can('VendorType.Index')){
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
     * Deletes an existing VendorType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		if(!Yii::$app->user->can('VendorType.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }
    /**
     * Finds the VendorType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VendorType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VendorType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
