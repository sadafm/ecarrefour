<?php

namespace multeback\modules\product\controllers;

use Yii;
use multebox\models\ProductBrand;
use multebox\models\search\ProductBrand as ProductBrandSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ProductBrandController implements the CRUD actions for ProductBrand model.
 */
class ProductBrandController extends Controller
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
     * Lists all ProductBrand models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('ProductBrand.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $searchModel = new ProductBrandSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single ProductBrand model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!Yii::$app->user->can('ProductBrand.View')){
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
     * Creates a new ProductBrand model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		if(!Yii::$app->user->can('ProductBrand.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

        $model = new ProductBrand;

        if ($model->load(Yii::$app->request->post())) 
		{
			$model->brand_image = UploadedFile::getInstance($model, 'brand_image');

			if($model->brand_image)
			{
				$name = uniqid($model->id).'.'.$model->brand_image->extension;
				$model->brand_new_image = $name;
			}

			if($model->save())
			{
				if($model->brand_image)
					$model->upload($name);

				return $this->redirect(['index']);
			}
			else 
			{
				return $this->render('create', [
					'model' => $model,
				]);
			}
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ProductBrand model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		if(!Yii::$app->user->can('ProductBrand.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

        $model = $this->findModel($id);
		$oldname = $model->brand_image;
		
		if(Yii::$app->user->identity->entity_type == 'vendor' && Yii::$app->user->identity->id != $model->added_by_id)
		{
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

        if ($model->load(Yii::$app->request->post())) 
		{
			$newimage = UploadedFile::getInstance($model, 'brand_image');

			if(!empty($newimage))
			{
				$model->brand_image = $newimage;

				if($model->brand_new_image)
				{
					$name = $model->brand_new_image;
				}
				else
				{
					$name = uniqid($model->id).'.'.$newimage->extension;
					$model->brand_new_image = $name;
				}
			}
			else
			{
				$model->brand_image = $oldname;
			}

			if($model->save())
			{
				if(!empty($newimage))
					$model->upload($name);

				return $this->redirect(['index']);
			}
			else 
			{
				return $this->render('update', [
					'model' => $model,
				]);
			}
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ProductBrand model.
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
     * Finds the ProductBrand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductBrand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductBrand::findOne($id)) !== null) {
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
        return $this->redirect(['index']);
    }

	public function actionDeactivate($id)
    {
        $result = $this->findModel($id);
		$result->active = 0;
		$result->updated_at = time();
		$result->save();
        return $this->redirect(['index']);
    }
}
