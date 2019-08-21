<?php

namespace multeback\modules\product\controllers;

use Yii;
use multebox\models\BannerData;
use multebox\models\search\BannerData as BannerDataSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * BannerDataController implements the CRUD actions for BannerData model.
 */
class BannerDataController extends Controller
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
     * Lists all BannerData models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BannerDataSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single BannerData model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    /**
     * Creates a new BannerData model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BannerData;
		$model->scenario = 'create';

        if ($model->load(Yii::$app->request->post())) 
		{
			$model->banner_file = UploadedFile::getInstance($model, 'banner_file');
			$name = uniqid($model->id).'.'.$model->banner_file->extension;
			$model->banner_new_name = $name;

			if($model->save())
			{
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
     * Updates an existing BannerData model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$oldname = $model->banner_file;

        if ($model->load(Yii::$app->request->post())) 
		{
			$newbanner = UploadedFile::getInstance($model, 'banner_file');

			if(!empty($newbanner))
			{
				$model->banner_file = $newbanner;
				$name = $model->banner_new_name;
			}
			else
			{
				$model->banner_file = $oldname;
			}

			if($model->save())
			{
				if(!empty($newbanner))
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
     * Deletes an existing BannerData model.
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
     * Finds the BannerData model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BannerData the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BannerData::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
