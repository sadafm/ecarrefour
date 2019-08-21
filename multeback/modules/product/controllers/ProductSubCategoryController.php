<?php

namespace multeback\modules\product\controllers;

use Yii;
use multebox\models\ProductSubCategory;
use multebox\models\search\ProductSubCategory as ProductSubCategorySearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductSubCategoryController implements the CRUD actions for ProductSubCategory model.
 */
class ProductSubCategoryController extends Controller
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
     * Lists all ProductSubCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSubCategorySearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single ProductSubCategory model.
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

	public function actionReview($id)
    {
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Creates a new ProductSubCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductSubCategory;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['product-category/view', 'id' => $_REQUEST['parent_id']]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ProductSubCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
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
     * Deletes an existing ProductSubCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['product-category/view', 'id' => $_REQUEST['category_id']]);
    }

    /**
     * Finds the ProductSubCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductSubCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductSubCategory::findOne($id)) !== null) {
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
        return $this->redirect(['product-category/view', 'id' => $_REQUEST['category_id']]);
    }

	public function actionDeactivate($id)
    {
        $result = $this->findModel($id);
		$result->active = 0;
		$result->updated_at = time();
		$result->save();
        return $this->redirect(['product-category/view', 'id' => $_REQUEST['category_id']]);
    }
}
