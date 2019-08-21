<?php

namespace multeback\modules\support\controllers;

use Yii;
use multebox\models\TicketStatus;
use multebox\models\search\TicketStatus as TicketStatusSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TicketStatusController implements the CRUD actions for TicketStatus model.
 */
class TicketStatusController extends Controller
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

	public function init(){
    	if(!isset(Yii::$app->user->identity->id)){
          $this->redirect(['/site/login']);
		}

		if(!Yii::$app->user->can('Settings.Index')){
          $this->redirect(['/site/index']);
		}
	}

    /**
     * Lists all TicketStatus models.
     * @return mixed
     */
    public function actionIndex()
    {
		extract(TicketStatus::find()->select("Max(sort_order) max_sort_order")->asArray()->one());

		//var_dump();

		if(!empty($_REQUEST['actionType'])){

			//$model = $this->findModel($_REQUEST['sort_order_update']);

			$statusId = $_REQUEST['sort_order_update'];

			$sortValue = $_REQUEST['sort_order_update'.$statusId];	

			//var_dump($statusId." ".$sortValue);

			if(!empty($_REQUEST['sort_order_update']) && $_REQUEST['actionType'] !='Down'){

				if($sortValue !='1'){

					$minusValue = intval($sortValue)-1;

					$ticketUpdate= TicketStatus::find()->where(['sort_order' => $sortValue])->one();

					$ticketUpdate1= TicketStatus::find()->where(['sort_order' => $minusValue])->one();

					$ticketUpdate->sort_order=$minusValue;

					$ticketUpdate->update();

					

					$ticketUpdate1->sort_order=$sortValue;

					$ticketUpdate1->update();

				}

			}else{

				if($max_sort_order !=$sortValue){

					$plusValue = intval($sortValue)+1;

					$ticketUpdate= TicketStatus::find()->where(['sort_order' => $sortValue])->one();

					$ticketUpdate1= TicketStatus::find()->where(['sort_order' => $plusValue])->one();

					$ticketUpdate->sort_order=$plusValue;

					$ticketUpdate->update();

					$ticketUpdate1->sort_order=$sortValue;

					$ticketUpdate1->update();

				}

			}

		}
        $searchModel = new TicketStatusSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single TicketStatus model.
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
     * Creates a new TicketStatus model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));

        $model = new TicketStatus;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'added' => 'yes']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TicketStatus model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
           // return $this->redirect(['update', 'id' => $model->id]);
		   return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TicketStatus model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TicketStatus model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TicketStatus the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TicketStatus::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
