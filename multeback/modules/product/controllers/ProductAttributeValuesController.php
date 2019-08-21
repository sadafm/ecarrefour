<?php

namespace multeback\modules\product\controllers;

use Yii;
use multebox\models\ProductAttributeValues;
use multebox\models\search\ProductAttributeValues as ProductAttributeValuesSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

/**
 * ProductAttributeValuesController implements the CRUD actions for ProductAttributeValues model.
 */
class ProductAttributeValuesController extends Controller
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
     * Lists all ProductAttributeValues models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('AttributeValues.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $searchModel = new ProductAttributeValuesSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single ProductAttributeValues model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!Yii::$app->user->can('AttributeValues.View')){
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
     * Creates a new ProductAttributeValues model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		if(!Yii::$app->user->can('AttributeValues.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = new ProductAttributeValues;

        /*if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }*/
//var_dump($_REQUEST);exit;
		if(isset($_REQUEST['ProductAttributeValues']['name']) && isset($_REQUEST['attribute_value']))
		{
			$model->name = $_REQUEST['ProductAttributeValues']['name'];
			$model->values = Json::encode($_REQUEST['attribute_value']);
			$model->added_at = time();
			$model->added_by_id = $_REQUEST['ProductAttributeValues']['added_by_id'];
			$model->save();

			//return $this->redirect(['update', 'id' => $model->id]);
			return $this->redirect(['index']);
		}
		else
		{
			return $this->render('create', [
                'model' => $model,
            ]);
		}
    }

    /**
     * Updates an existing ProductAttributeValues model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		if(!Yii::$app->user->can('AttributeValues.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = $this->findModel($id);

		$ProductAttributeValues = Json::decode($model->values);

        /*if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } */
		if(isset($_REQUEST['ProductAttributeValues']['name']) && isset($_REQUEST['attribute_value']))
		{
			$model->name = $_REQUEST['ProductAttributeValues']['name'];
			$model->values = Json::encode($_REQUEST['attribute_value']);
			$model->added_at = time();
			$model->save();

			//return $this->redirect(['update', 'id' => $model->id]);
			return $this->redirect(['index']);
		}
		else {
            return $this->render('update', [
                'model' => $model,
				'ProductAttributeValues' => $ProductAttributeValues,
            ]);
        }
    }

    /**
     * Deletes an existing ProductAttributeValues model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		if(!Yii::$app->user->can('AttributeValues.Delete')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProductAttributeValues model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductAttributeValues the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductAttributeValues::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
