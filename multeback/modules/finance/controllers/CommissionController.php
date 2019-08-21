<?php

namespace multeback\modules\finance\controllers;

use Yii;
use multebox\models\Commission;
use multebox\models\search\Commission as CommissionSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommissionController implements the CRUD actions for Commission model.
 */
class CommissionController extends Controller
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
     * Lists all Commission models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('Commission.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $searchModel = new CommissionSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Commission model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!Yii::$app->user->can('Commission.View')){
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
     * Creates a new Commission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		if(!Yii::$app->user->can('Commission.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = new Commission;

        if ($model->load(Yii::$app->request->post())) {
			if($model->category_id == '')
				$model->category_id = 0;
			
			if($model->sub_category_id == '')
				$model->sub_category_id = 0;

			if($model->sub_subcategory_id == '')
				$model->sub_subcategory_id = 0;

			if($model->save())
			{
				//return $this->redirect(['view', 'id' => $model->id]);
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
     * Updates an existing Commission model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		if(!Yii::$app->user->can('Commission.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = $this->findModel($id);

         if ($model->load(Yii::$app->request->post())) {
			if($model->category_id == '')
				$model->category_id = 0;
			
			if($model->sub_category_id == '')
				$model->sub_category_id = 0;

			if($model->sub_subcategory_id == '')
				$model->sub_subcategory_id = 0;

			if($model->save())
			{
				//return $this->redirect(['view', 'id' => $model->id]);
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
     * Deletes an existing Commission model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		if(!Yii::$app->user->can('Commission.Delete')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Commission model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Commission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Commission::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
