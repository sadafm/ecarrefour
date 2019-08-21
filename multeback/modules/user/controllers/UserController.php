<?php

namespace multeback\modules\user\controllers;

use Yii;
use multebox\models\User;
use multebox\models\SessionDetails;
use multebox\models\ImageUpload;
use multebox\models\search\User as UserSearch;
use multebox\models\search\History as HistorySearch;
use multebox\models\search\SessionDetails as UserSessionSearch;
use multebox\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use multebox\models\SendEmail;
use multebox\models\AuthAssignment;
use multebox\models\search\UserType as UserTypeSearch;
use multebox\models\search\MulteModel;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
	public function init(){
		if(!Yii::$app->user->can('Users.Index')){
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(Yii::$app->params['user_role'] != 'admin')
		{
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

        $searchModel = new UserSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
		if(!empty($_REQUEST['multiple_del'])){

			$rows=$_REQUEST['selection'];
			
			if($rows)
			{
				for($i=0;$i<count($rows);$i++){

					$this->findModel($rows[$i])->delete();

				}
			}

		}
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $img = new ImageUpload();
        $model = new User;
		$emailObj = new SendEmail;
       // $model->generateAuthKey();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$model->added_at=time();
			//if(Yii::$app->params['AUTO_PASSWORD'] =='Yes'){
				$length = 8;
				$new_password = Yii::$app->security->generateRandomString ($length);
				$model->password_hash=Yii::$app->security->generatePasswordHash ($new_password);//$new_password;
			/*}else{
				$new_password = $_POST['User']['password_hash'];
				$model->password_hash=Yii::$app->security->generatePasswordHash ($new_password);
			}*/
			// Role Assign
            if($model->user_type_id ==UserTypeSearch::getCompanyUserType('Customer')->id){
				$model1 = new AuthAssignment;
				$model1->item_name = 'Customer';
				$model1->user_id = $model->id;
				$model1->save();

				$model->entity_type='customer';
			}

			// Role Assign
            if($model->user_type_id ==UserTypeSearch::getCompanyUserType('Employee')->id){
				$model1 = new AuthAssignment;
				$model1->item_name = 'Employee';
				$model1->user_id = $model->id;
				$model1->save();

				$model->entity_type='employee';
			}

			$model->active = 1;
			// lets insert the user now
			$model->update();
			/*$img->loadImage('users/nophoto.jpg')->saveImage("users/".$model->id.".png");
			$img->loadImage('users/nophoto.jpg')->resize(30, 30)->saveImage("users/user_".$model->id.".png");*/
			MulteModel::saveFileToServer('nophoto.jpg', $model->id.'.png', Yii::$app->params['web_folder']."/users");
			SendEmail::sendNewUserEmail($model->email,$model->first_name." ".$model->last_name, $model->username,$new_password, true);
			
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $img = new ImageUpload();
		$emailObj = new SendEmail;
        $model = $this->findModel($id);
		/*if(!empty($_GET['img_del'])){
			unlink('users/'.$model->id.'.png');
			unlink('users/user_'.$model->id.'.png');
			return $this->redirect(['update', 'id' => $model->id]);
		}*/
		if(!empty($_GET['active'])){
			$status = $_GET['active']=='yes'?'1':'0';
			$userUpdate = User::findOne($model->id);
			$userUpdate->updated_at=time();
			$userUpdate->active = $status;
			$userUpdate->save();
			if($_GET['active']=='yes'){
			//$emailObj->sendActivateUserEmailTemplate($userUpdate->email,$userUpdate->first_name." ".$userUpdate->last_name, $userUpdate->username,'*******','<a href="'.Url::to(['/site/login']).'">here</a>');
			}
			return $this->redirect(['view', 'id' => $model->id]);
		}
		if(!empty($_GET['reset_password'])){
			$new_password = Yii::$app->security->generateRandomString (8);
			$userUpdate = User::findOne($model->id);
			$userUpdate->password_hash= Yii::$app->security->generatePasswordHash ($new_password);///$new_password;
			$userUpdate->updated_at=time();
			$userUpdate->save();
			//Send an Email
			SendEmail::sendResetPasswordEmail($model->email,$model->first_name." ".$model->last_name,$new_password);
			
			return $this->render('update', [
                'model' => $model,
				'new_password'=>$new_password,
            ]);	
			
		}
		/*if(!empty($_FILES['user_image']['tmp_name'])){
				$img->loadImage($_FILES['user_image']['tmp_name'])->saveImage("users/".$model->id.".png");
				$img->loadImage($_FILES['user_image']['tmp_name'])->resize(30, 30)->saveImage("users/user_".$model->id.".png");
			}*/
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			// Role Assign
            if($model->user_type_id == UserTypeSearch::getCompanyUserType('Customer')->id){
				$model1 = new AuthAssignment;
				$model1->item_name = 'Customer';
				$model1->user_id = $model->id;
				$model1->save();
			}
			if(!empty($_FILES['user_image']['tmp_name'])){
				//move_uploaded_file($_FILES['user_image']['tmp_name'],'users/'.$model->id.'.png');
				/*$img->loadImage($_FILES['user_image']['tmp_name'])->saveImage("users/".$model->id.".png");
				$img->loadImage($_FILES['user_image']['tmp_name'])->resize(30, 30)->saveImage("users/user_".$model->id.".png");*/
				MulteModel::saveFileToServer($_FILES['user_image']['tmp_name'], $model->id.'.png', Yii::$app->params['web_folder']."/users");
			}
			$model->updated_at = time();
			$model->update();
            return $this->redirect(['view', 'id' => $model->id,'reload'=>'true']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
	public function actionChangePassword(){
		$msg='';
		$emailObj = new SendEmail;
		if(!empty($_REQUEST['password']) && !empty($_REQUEST['confirm_pass'])){
			$userUpdate = User::findOne(Yii::$app->user->identity->id);
			$userUpdate->password_hash = Yii::$app->security->generatePasswordHash ($_REQUEST['password']);//$_REQUEST['password'];
			$userUpdate->save();
			$msg=Yii::t('app', 'Your password has been changed - Please check your email!');
			//Send an Email
			SendEmail::sendResetPasswordEmail($userUpdate->email,$userUpdate->first_name." ".$userUpdate->last_name,$_REQUEST['password']);
			
			/* Below code was added to log out user after password change */
			date_default_timezone_set(Yii::$app->params['TIME_ZONE']);
			$last_logged = time();
			$logged_out = time();
			$user_id=Yii::$app->user->identity->id;
			$sql="select * from tbl_session_details where user_id='$user_id'";
			$connection = \Yii::$app->db;
			$command=$connection->createCommand($sql);
			$dataReader=$command->queryAll();
			if($dataReader){
				$sql="update tbl_session_details set logged_out='$logged_out' where user_id='$user_id' and session_id='".session_id()."'";
				$connection = \Yii::$app->db;
				$command=$connection->createCommand($sql);
				$dataReader=$command->execute();
			}
			Yii::$app->user->logout ();
			return $this->goHome ();
		}
		return $this->render('change-password', [
                'msg' => $msg,
            ]);
	}
	public function actionUserSessions(){
		if(Yii::$app->user->identity->username !='admin'){
          $this->redirect(array('/site/index'));
		}
		if(isset($_GET['del_id'])){
			$sessionObj = SessionDetails::findOne($_GET['del_id']);
			$sessionObj->logged_out = time();
			$sessionObj->update();
			return $this->redirect(['index']);
		}
		$searchModel = new UserSessionSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('user-sessions', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
	}
	public function actionUserSessionDetail(){
		if(Yii::$app->user->identity->username !='admin'){
          $this->redirect(array('/site/index'));
		}
		$searchModel = new HistorySearch;
        $dataProvider = $searchModel->searchSessionActivities(Yii::$app->request->getQueryParams());

        return $this->render('user-session-detail', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
	}
	public function actionMailCompose(){
		$emailObj = new SendEmail;
		if(!empty($_REQUEST['to'])){
			$emailObj->sendMultEmail($_REQUEST['to'],$_REQUEST['email_body'], $_REQUEST['cc'],$_REQUEST['subject'], true, false, false);
			$msg = Yii::t('app', 'Email is sent');	
		}
		else
		{
			$msg = '';
		}

		$user = $this->findModel($_GET['id']);
        return $this->render('mail-compose', [
            'user' => $user,
			'msg'=>$msg
        ]);
	}
	
    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
	public function actionAjexGetRoles($id){
		$connection = \Yii::$app->db;
		$sql="select auth_item.* from auth_item,auth_assignment where auth_item.type=2 and auth_assignment.user_id=$id and auth_assignment.item_name=auth_item.name";
		$command=$connection->createCommand($sql);
		$dataReader=$command->queryAll();
		$roles ='<ul class="list-group">';
		if($dataReader){
			foreach($dataReader as $role){
				$roles.='<li class="list-group-item active">'.$role['name']."</li>";
			}
		}else{
			return '<div class="alert alert-danger">No Roles</div>';
		}
		
		return $roles."</ul>";
	}
    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
		if(Yii::$app->user->identity->userType->type=="Customer")
		{
			if ($id == Yii::$app->user->identity->id) {
					if (($model = User::findOne($id)) !== null) {
					return $model;
				} else {
					throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
				}
			} else {
				throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
			}
		}
		else
		{
			if (($model = User::findOne($id)) !== null) {
				return $model;
			} else {
				throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
			}
		}
    }
}
