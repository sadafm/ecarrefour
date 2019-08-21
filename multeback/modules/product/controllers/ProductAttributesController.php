<?php

namespace multeback\modules\product\controllers;

use Yii;
use multebox\models\ProductAttributes;
use multebox\models\ProductAttributeValues;
use multebox\models\search\ProductAttributes as ProductAttributesSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductAttributesController implements the CRUD actions for ProductAttributes model.
 */
class ProductAttributesController extends Controller
{
	public function init(){
		if(!Yii::$app->user->can('ProductCategory.Index')){
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
     * Lists all ProductAttributes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductAttributesSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single ProductAttributes model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id]);
			 return $this->redirect(['product-sub-sub-category/view', 'id' => $_REQUEST['sub_sub_category_id']]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    /**
     * Creates a new ProductAttributes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductAttributes;

        if ($model->load(Yii::$app->request->post()) && $model->save()) 
		{
			if($model->fixed == '0')
			{
				$model->fixed_id='';
			}
			else
			{
				$model->name=ProductAttributeValues::findOne($model->fixed_id)->name;
			}
			$model->added_at = time();
			$model->save();

            return $this->redirect(['product-sub-sub-category/view', 'id' => $_REQUEST['parent_id']]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ProductAttributes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if($model->fixed == '0')
			{
				$model->fixed_id='';
			}
			else
			{
				$model->name='';
			}
			$model->added_at = time();
			$model->save();
            return $this->redirect(['update', 'id' => $model->id, 'parent_id' => $model->parent_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ProductAttributes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['product-sub-sub-category/view', 'id' => $_REQUEST['sub_sub_category_id']]);
    }

    /**
     * Finds the ProductAttributes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductAttributes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductAttributes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

	public function actionActivate($id)
    {
        $result = $this->findModel($id);
		$result->active = 1;
		$result->updated_at = time();
		$result->save();
        return $this->redirect(['product-sub-sub-category/view', 'id' => $_REQUEST['sub_sub_category_id']]);
    }

	public function actionDeactivate($id)
    {
        $result = $this->findModel($id);
		$result->active = 0;
		$result->updated_at = time();
		$result->save();
        return $this->redirect(['product-sub-sub-category/view', 'id' => $_REQUEST['sub_sub_category_id']]);
    }
}
