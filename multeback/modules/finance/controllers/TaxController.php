<?php
namespace multeback\modules\finance\controllers;
use Yii;
use multebox\models\Tax;
use multebox\models\StateTax;
use multebox\models\search\Tax as TaxSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
/**
 * TaxController implements the CRUD actions for Tax model.
 */
class TaxController extends Controller
{
	public function init(){
		if(!Yii::$app->user->can('Tax.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
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
     * Lists all Tax models.
     * @return mixed
     */
    public function actionIndex()
    {
		extract(Tax::find()->select("Max(sort_order) max_sort_order")->asArray()->one());
		
		if(!empty($_REQUEST['sort_order_update']) && !empty($_REQUEST['actionType'])){
			//$model = $this->findModel($_REQUEST['sort_order_update']);
			$statusId = $_REQUEST['sort_order_update'];
			$sortValue = $_REQUEST['sort_order_update'.$statusId];	
			//var_dump($statusId." ".$sortValue);
			if($_REQUEST['actionType'] !='Down'){
				if($sortValue !='1'){
					$minusValue = intval($sortValue)-1;
					$taxupdate= Tax::find()->where(['sort_order' => $sortValue])->one();
					$taxupdate1= Tax::find()->where(['sort_order' => $minusValue])->one();
					$taxupdate->sort_order=$minusValue;
					$taxupdate->update();
					
					$taxupdate1->sort_order=$sortValue;
					$taxupdate1->update();
				}
			}else{
				if($max_sort_order !=$sortValue){
					$plusValue = intval($sortValue)+1;
					$taxupdate= Tax::find()->where(['sort_order' => $sortValue])->one();
					$taxupdate1= Tax::find()->where(['sort_order' => $plusValue])->one();
					$taxupdate->sort_order=$plusValue;
					$taxupdate->update();
					$taxupdate1->sort_order=$sortValue;
					$taxupdate1->update();
				}
			}
		}
		
        $searchModel = new TaxSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * Displays a single Tax model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['view', 'id' => $model->id]);
        } else {
        return $this->render('view', ['model' => $model]);
}
    }
    /**
     * Creates a new Tax model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tax;
         if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'added' => 'yes']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Updates an existing Tax model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

		if(isset($_REQUEST['state_tax_add']))
		{
			$state_tax = new StateTax();
			
			$state_tax->tax_id = $model->id;
			$state_tax->country_id = $_REQUEST['country_id'];
			$state_tax->state_id = $_REQUEST['state_id'];
			$state_tax->tax_percentage = $_REQUEST['tax_percentage'];
			$state_tax->added_at = time();

			if($state_tax->save())
			{
				Yii::$app->session->setFlash('success', Yii::t('app', 'Tax value added successfully!'));
			}
			else
			{
				Yii::$app->session->setFlash('error', Yii::t('app', 'Error trying to add state tax'));
			}
			
			return $this->render('update', [
                'model' => $model,
            ]);
		}

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['update', 'id' => $model->id]);

			return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Deletes an existing Tax model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }
    /**
     * Finds the Tax model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tax the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tax::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
