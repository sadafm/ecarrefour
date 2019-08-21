<?php

namespace multeback\modules\support\controllers;

use Yii;
use yii\helpers\Url;
use multebox\models\NoteModel;
use multebox\models\FileModel;
use multebox\models\AssignmentHistoryModel;
use multebox\models\HistoryModel;
use multebox\models\File;
use multebox\models\User;
use multebox\models\Note;
use multebox\models\History;
use multebox\models\TicketSla;
use multebox\models\TimeEntry;
use multebox\models\AssignmentHistory;
use multebox\models\AuthAssignment;


use multebox\models\Ticket;
use multebox\models\TicketStatus;
use multebox\models\TicketPriority;
use multebox\models\search\Ticket as TicketSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use multebox\models\SendEmail;
use multebox\models\Customer as CustomerDetail;
use multebox\models\User as UserDetail;
use multebox\models\search\CommonModel as SessionVerification;
use multebox\models\TimeDiffModel;
use multebox\models\TimesheetModel;
use multebox\models\TicketResolution;
use multebox\models\ResolutionReference;

use \Datetime;
use \DateInterval;

/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends Controller
{
	public $entity_type='ticket';

	public static function getUserEmail($id){
		$userModel = UserDetail::findOne($id);	
		return $userModel->email;
	}

	public static function getCustomerEmail($id){
		$userModel = User::find()->where("entity_type='customer' and entity_id=".$id)->one();
		return $userModel->email;
	}

	public static function getCustomerFullName($id){
		$userModel = User::find()->where("entity_type='customer' and entity_id=".$id)->one();
		
		return $userModel->first_name." ".$userModel->last_name;	
	}

	public static function getUserFullName($id){
		$user = UserDetail::findOne($id);
		
		return $user->first_name." ".$user->last_name;	
	}

	public static function getLoggedUserFullName(){
		$user = UserDetail::findOne(Yii::$app->user->identity->id);
		return $user->first_name." ".$user->last_name;	
	}

	public static function getLoggedUserDetail(){
		$user = UserDetail::find()->where('id='.Yii::$app->user->identity->id)->asArray()->one();
		return $user;	
	}

	public function getTicketStatus($status){
		$status = TicketStatus::find()->where("status=".$status)->one();
		
		return $status->label;	
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

	public function init(){

	}

    /**
     * Lists all Ticket models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('Ticket.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		if(!empty($_REQUEST['multiple_del'])){
			if(!Yii::$app->user->can('Ticket.Delete')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
			$rows=$_REQUEST['selection'];
			if($rows)
			{
				for($i=0;$i<count($rows);$i++){
					$this->findModel($rows[$i])->delete();
				}
			}
		}
		if(!empty($_REQUEST['ticket_assigned_id']))
        {        	
        	$id = $_REQUEST['ticket_assigned_id'];        	
        	$ticketUpdate = Ticket::find()->where(['id' => $id])->one();        	
			$ticketUpdate->user_assigned_id=Yii::$app->user->identity->id;
			$ticketUpdate->update();
			if($_REQUEST['page']=="update")
			{
				return $this->redirect(['update', 'id'=>$id]);
			}
	
			if($_REQUEST['page']=="index")
			{
				return $this->redirect(['index']);
			}	
        }
        $searchModel = new TicketSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);

    }
	

    /**
     * Displays a single Ticket model.
     * @param integer $id
     * @return mixed
     */
	 
	 public function actionQueue($id){
		 
		 if(!Yii::$app->user->can('Queue.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		
		$operatorIds = Yii::$app->authManager->getUserIdsByRole('Admin'); 
		
        $searchModel = new TicketSearch;
		if(in_array( Yii::$app->user->identity->id,$operatorIds)) //Admin
		{
			$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
		}
		else
		{
			$dataProvider = $searchModel->searchTicketsWithQueueID($id);
		}

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
	 }
	 
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
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		if(!Yii::$app->user->can('Ticket.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

	    $model = new Ticket;
		
		if($model->load(Yii::$app->request->post()))
		{
			if ($model->save()) {
				$stringId='TICKET'.str_pad($model->id, 9, "0", STR_PAD_LEFT);
				$model->ticket_id=$stringId;
				$model->added_at = time();
		
				$slaObj = TicketSla::find()->where('ticket_priority_id ='.$model->ticket_priority_id .' and ticket_impact_id="'.$model->ticket_impact_id.'"')->one();
				$slaSecs=$slaObj->sla_duration * 60 * 60;
				$dueDate=$model->added_at+$slaSecs;
				$model->due_date = $dueDate;

				$model->save();

				// Add Notes
				$note = new Note();
				$note->entity_id=$model->id;
				$note->entity_type='ticket';
				$note->notes=$model->ticket_description;
				$note->user_id=$model->added_by_user_id;
				$note->added_at=time();
				$note->save();
				
				$ticketStatusModel = TicketStatus::find()->where("status='".$model->ticket_status."'")->one();

				if($model->user_assigned_id)
				{
					SendEmail::sendTicketAssignedEmail($this->getUserEmail($model->user_assigned_id), $this->getUserFullName($model->user_assigned_id),'<a href="'.Url::to(['/support/ticket/update', 'id' => $model->id], true).'">'.$stringId.'</a>', $model->ticket_title, $model->ticket_description);
				}

				SendEmail::sendCustomerTicketNotification($this->getCustomerEmail($model->ticket_customer_id),$this->getCustomerFullName($model->ticket_customer_id), $model->ticket_title, $ticketStatusModel->label);
				
				return $this->redirect(['update', 'id' => $model->id]);

			} else {
				return $this->render('create', [
					'model' => $model,
				]);
			}
		}
		else {
				return $this->render('create', [
					'model' => $model,
				]);
			}
    }

    /**
     * Updates an existing Ticket model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		if(!(Yii::$app->user->can('Ticket.Update') || Yii::$app->user->can('Ticket.View'))) {
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

        $model = $this->findModel($id);
		$attachModelR='';
		$noteModelR='';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$model->last_updated_by_user_id = Yii::$app->user->identity->id;
			$model->updated_at = time();

			$slaObj = TicketSla::find()->where('ticket_priority_id ='.$model->ticket_priority_id .' and ticket_impact_id="'.$model->ticket_impact_id.'"')->one();
			$slaSecs=$slaObj->sla_duration * 60 * 60;
			$newDueDate=$model->added_at+$slaSecs;
			$model->due_date = $newDueDate;

			$model->save();

			$old_owner=!empty($_REQUEST['old_owner'])?$_REQUEST['old_owner']:'';
			$old_ticket_priority_id=!empty($_REQUEST['old_ticket_priority_id'])?$_REQUEST['old_ticket_priority_id']:'';
			$old_ticket_status=!empty($_REQUEST['old_ticket_status'])?$_REQUEST['old_ticket_status']:'';

			// Assigned user Changed
			if($model->user_assigned_id != $old_owner){				
				//Send an Email
				SendEmail::sendTicketAssignedEmail($this->getUserEmail($model->user_assigned_id), $this->getUserFullName($model->user_assigned_id),'<a href="'.Url::to(['/support/ticket/update', 'id' => $model->id], true).'">'.$model->ticket_id.'</a>', $model->ticket_title, $model->ticket_description);
			}

			/// Ticket Priority Changed
			/*if($model->ticket_priority_id != $old_ticket_priority_id){
				$ticketPriorityModel = TicketPriority::findOne($model->ticket_priority_id);
				$ticketPriorityModelOld = TicketPriority::findOne($old_ticket_priority_id);

				//Send an Email
				SendEmail::sendTicketChangedPriorityEmailTemplate($this->getUserEmail($model->user_assigned_id),$this->getUserFullName($model->user_assigned_id),$this->getLoggedUserFullName(),'<a href="'.Url::to(['/support/ticket/update', 'id' => $model->id], true).'">'.$model->ticket_id.'</a>', $ticketPriorityModelOld->label,$ticketPriorityModel->label);
			}*/
			
			/// Ticket Status Changed
			/*if($model->ticket_status != $old_ticket_status){
				$ticketStatusModel = TicketStatus::find()->where("status=".$model->ticket_status)->one();
				$ticketStatusModelOld = TicketStatus::find()->where("status=".$old_ticket_status)->one();

				//Send an Email
				SendEmail::sendTicketChangedStatusEmailTemplate($this->getUserEmail($model->user_assigned_id),$this->getUserFullName($model->user_assigned_id),$this->getLoggedUserFullName(),'<a href="'.Url::to(['/support/ticket/update', 'id' => $model->id], true).'">'.$model->ticket_id.'</a>',$ticketStatusModelOld->label,$ticketStatusModel->label);
			}*/
			
			return $this->redirect(['update', 'id' => $model->id]);
            
        } else {

			if(!empty($_REQUEST['uemail']))
			{
				$uemail = $_REQUEST['uemail'];
				$body = $_REQUEST['email_body'];
				$cc = '';
				$subject = $_REQUEST['subject'];		

				SendEmail::sendMultEmail ($uemail, $body, $cc, $subject, false);

				return $this->redirect(['update', 'id' => $_REQUEST['id']]);
			}

			// Send Attachment File to Ticket Assigned User
			if(!empty($_REQUEST['send_attachment_file'])){
				//Send an Email
				SendEmail::sendMultEmail($_REQUEST['uemail'],$_REQUEST['email_body'], $_REQUEST['cc'], $_REQUEST['subject']);
				return $this->redirect(['update', 'id' => $_REQUEST['id']]);
			}
			// Delete Ticket Attachment
			if(!empty($_REQUEST['attachment_del_id'])){
					$Attachmodel = File::findOne($_REQUEST['attachment_del_id']);
					if (!is_null($Attachmodel)) {
						$Attachmodel->delete();
					}
			}
			// Delete Ticket Notes
			if(!empty($_REQUEST['note_del_id'])){
					$NoteDel = Note::findOne($_REQUEST['note_del_id']);
					if (!is_null($NoteDel)) {
						$NoteDel->delete();
					}
			}

			// Add Attachment for Ticket
			if(!empty($_REQUEST['add_attach'])){
				$aid=FileModel::fileInsert($_REQUEST['entity_id'],$this->entity_type);
				if($aid > 0)
				{
					/* Send email if needed */
				}
				else
				{
					if($aid == 0) // Invalid extension
					{
						$msg = Yii::t('app', 'File type not allowed to be uploaded!');
					}
					else // File size exceeded maximum limit
					{
						$msg = Yii::t('app', 'File size exceeded maximum allowed size')." (".Yii::$app->params['FILE_SIZE'].")";
					}

					return $this->redirect(['update', 'id' => $_REQUEST['id'], 'err_msg' => $msg]);
				}
			}
			
			// Ticket Attachment get
			if(!empty($_REQUEST['attach_update'])){
				$attachModelR=File::findOne($_REQUEST['attach_update']);
			}

			// Ticket Notes get
			if(!empty($_REQUEST['note_id'])){
				$noteModelR=Note::findOne($_REQUEST['note_id']);
			}

			// Ticket Attachment Update
			if(!empty($_REQUEST['edit_attach']))
			{
				$file=FileModel::fileEdit();
			}
			
			// Add Notes
			if(!empty($_REQUEST['add_note_model'])){
				$nid = NoteModel::noteInsert($_REQUEST['id'],$this->entity_type);
			}
			
			// Update Notes
			if(!empty($_REQUEST['edit_note_model'])){
				$nid = NoteModel::noteEdit();
			}
			
            return $this->render('update', [
                'model' => $model,
				'attachModel'=>$attachModelR,
				'noteModel'=>$noteModelR,
            ]);
        }
    }

	public function actionMyTickets()
    {
		if(!Yii::$app->user->can('Ticket.MyTicket')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

		if(!empty($_REQUEST['multiple_del'])){
			if(!Yii::$app->user->can('Ticket.Delete')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
			$rows=$_REQUEST['selection'];
			if($rows)
			{
				for($i=0;$i<count($rows);$i++){
					$this->findModel($rows[$i])->delete();
				}
			}
		}
        $searchModel = new TicketSearch;
        $dataProvider = $searchModel->searchMyTickets(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

	public function actionPendingTicket()
    {
		if(!empty($_REQUEST['multiple_del'])){
			if(!Yii::$app->user->can('Ticket.Delete')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
			$rows=$_REQUEST['selection'];
			if($rows)
			{
				for($i=0;$i<count($rows);$i++){
					$this->findModel($rows[$i])->delete();
				}
			}
		}
        $searchModel = new TicketSearch;
        $dataProvider = $searchModel->searchPendingTickets(Yii::$app->request->getQueryParams());

        return $this->render('pending-ticket', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

	public function actionAjaxQueueUsers(){
		$queue_id=$_REQUEST['queue_id'];
		$user_id=$_REQUEST['user_id'];
		$sql="SELECT * FROM tbl_user WHERE id in(select user_id from tbl_queue_users where queue_id=$queue_id)";
			$connection = \Yii::$app->db;
			$command=$connection->createCommand($sql);
			$dataReader=$command->queryAll();
		 return $this->renderPartial('ajax-queue-users', [
                'dataReader' => $dataReader,
				'user_id'=>$user_id,
            ]);
	}

	public function actionAjaxDepartmentQueue(){
		$department_id=$_REQUEST['department_id'];
		$queue_id=$_REQUEST['queue_id'];
		$sql="SELECT * FROM tbl_queue WHERE department_id=$department_id and active=1";
			$connection = \Yii::$app->db;
			$command=$connection->createCommand($sql);
			$dataReader=$command->queryAll();
		 return $this->renderPartial('ajax-department-queue', [
                'dataReader' => $dataReader,
				'queue_id'=>$queue_id,
            ]);
	}
	
	public function actionAjaxTicketSla(){
      $ticket_priority_id=$_REQUEST['ticket_priority_id'];
      $ticket_impact_id=$_REQUEST['ticket_impact_id'];
      if(TicketSla::find()->where(['ticket_priority_id'=>$ticket_priority_id,'ticket_impact_id'=>$ticket_impact_id])->exists())
      { 
        return false;
      } else{
          echo Yii::t('app', 'No SLA defined for the selected Ticket Priority & Impact.')."\n".Yii::t('app', 'Proceeding further will set current Datetime as Due Date');
      }
	}
	
	public function actionAjaxTicketCategory(){
		$department_id=$_REQUEST['department_id'];
		$ticket_category_id_1=$_REQUEST['ticket_category_id_1'];
		$sql="SELECT * FROM tbl_ticket_category WHERE parent_id=0 and department_id=$department_id and active=1";
			$connection = \Yii::$app->db;
			$command=$connection->createCommand($sql);
			$dataReader=$command->queryAll();
		 return $this->renderPartial('ajax-ticket-category', [
                'dataReader' => $dataReader,
				'ticket_category_id_1'=>$ticket_category_id_1,
            ]);
	}

	public function actionAjaxCategoryChange(){
		$ticket_category_id=$_REQUEST['ticket_category_id'];
		$ticket_category_id_2=$_REQUEST['ticket_category_id_2'];
		
		$sql="SELECT * FROM tbl_ticket_category WHERE parent_id=$ticket_category_id and active=1";
			$connection = \Yii::$app->db;
			$command=$connection->createCommand($sql);
			$dataReader=$command->queryAll();
		 return $this->renderPartial('ajax-category-change', [
                'dataReader' => $dataReader,
				'ticket_category_id_2'=>$ticket_category_id_2,
            ]);
	}
	
    /**
     * Deletes an existing Ticket model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		 if(!Yii::$app->user->can('Ticket.Delete')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
		if(Yii::$app->user->identity->userType->type=="Customer")
		{
			if (($model = Ticket::find()->where("id=$id and ticket_customer_id=".Yii::$app->user->identity->entity_id)->one()) !== null) {
				return $model;
			} else {
				throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
			}
		}
		else
		{
			if (($model = Ticket::findOne($id)) !== null) {
				return $model;
			} else {
				throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
			}
		}
    }
}
