<?php

namespace multeback\modules\vendor\controllers;

use Yii;
use multebox\models\Vendor;
use multebox\models\search\Vendor as VendorSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use multebox\models\AddressModel;
use multebox\models\ContactModel;
use multebox\models\Address;
use multebox\models\Contact;
use multebox\models\Note;
use multebox\models\NoteModel;
use multebox\models\FileModel;
use multebox\models\File;
use multebox\models\ImageUpload;
use multebox\models\SendEmail;
use multebox\models\HistoryModel;
use multebox\models\Inventory;
use multebox\models\User;
use multebox\models\search\MulteModel;
use multebox\models\search\UserType as UserTypeSearch;
use multebox\models\AuthAssignment;
use yii\helpers\Url;

/**
 * VendorController implements the CRUD actions for Vendor model.
 */
class VendorController extends Controller
{
	public $entity_type='vendor';

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
     * Lists all Vendor models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('Vendor.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $searchModel = new VendorSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Vendor model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!Yii::$app->user->can('Vendor.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $img = new ImageUpload();
		$emailObj = new SendEmail;

        $model = $this->findModel($id);

		$old_vendor_state = $model->active;

		$vendor_email = Contact::find()->where("is_primary=1 and entity_type='vendor' and entity_id='".$id."'")->one()->email;

		$addressModel = Address::find()->where("entity_type='vendor' and entity_id='".$id."'")->one();
		
		/// Contact Primary
		if(!empty($_GET['primary']))
		{
			$contactModel = Contact::find()->where("entity_type='vendor' and entity_id=".$model->id." and is_primary=1")->one();
			if(!is_null($contactModel)){
				$contactModel->is_primary=0;
				$contactModel->save();
				if (($obj = Contact::findOne($_GET['primary'])) !== null) {
					$obj->is_primary=1;
					$obj->save();
				}
			}else{
				if (($obj = Contact::findOne($_GET['primary'])) !== null) {
					$obj->is_primary=1;
					$obj->save();
				}
			}
			return $this->redirect(['view','id'=>$model->id]);
		}
		/// Address Primary
		if(!empty($_GET['address_primary']))
		{
			$addressModel = Address::find()->where("entity_type='vendor' and entity_id=".$model->id." and is_primary=1")->one();
			if(!is_null($addressModel)){
				$addressModel->is_primary=0;
				$addressModel->save();
				if (($obj = Address::findOne($_GET['address_primary'])) !== null) {
					$obj->is_primary=1;
					$obj->save();
				}
			}else{
				if (($obj = Address::findOne($_GET['address_primary'])) !== null) {
					$obj->is_primary=1;
					$obj->save();
				}
			}
			return $this->redirect(['view','id'=>$model->id]);
		}

		if(!empty($_FILES['vendor_image']['tmp_name']))
		{
			//move_uploaded_file($_FILES['vendor_image']['tmp_name'],'vendors/'.$model->id.'.png');
			MulteModel::saveFileToServer($_FILES['vendor_image']['tmp_name'], $model->id.'.png', Yii::$app->params['web_folder']."/vendors");
		}

        if ($model->load(Yii::$app->request->post()) && $model->save()) 
		{
			$model->updated_at = time();
			$model->update();

			if($old_vendor_state != $model->active)
			{
				if ($model->active == 0)
				{
					$user = User::find()->where("entity_type='vendor' and entity_id=".$model->id)->one();
					$user->active = 0;
					$user->save();

					// Update all inventory items to inactive
					Inventory::updateAll(['active' => 0], ['=', 'vendor_id', $model->id]);
				}
				else
				{
					$user = User::find()->where("entity_type='vendor' and entity_id=".$model->id)->one();
					$user->active = 1;
					$user->save();

					// Update all inventory items to active
					Inventory::updateAll(['active' => 1], ['=', 'vendor_id', $model->id]);
				}
			}

			if(!empty($_FILES['vendor_image']['tmp_name'])){
				//move_uploaded_file($_FILES['vendor_image']['tmp_name'],'vendors/'.$model->id.'.png');
				MulteModel::saveFileToServer($_FILES['vendor_image']['tmp_name'], $model->id.'.png', Yii::$app->params['web_folder']."/vendors");
			}

            return $this->redirect(['index']);
        }
		else
		{
			if(!empty($_REQUEST['sendemaildesc']))
			{
				//Send an Email
				SendEmail::sendMultEmail($_REQUEST['toemail'],$_REQUEST['sendemaildesc'], $_REQUEST['cc'], $_REQUEST['subject']);
				return $this->redirect(['view', 'id' => $_REQUEST['id'], 'msg' => 'Email is sent']);
			}

			//Contact Model
			if(!empty($_REQUEST['contact_edit']))
			{
				$contact=Contact::findOne($_REQUEST['contact_edit']);
			}
			else
			{
				$contact= new Contact();
			}

			// Contact Add / Update
			if(!empty($_REQUEST['contactae']))
			{
				if(!empty($_REQUEST['first_name']))
				{
					if(!empty($_REQUEST['contact_id']))
					{
						ContactModel::contactUpdate($_REQUEST['contact_id']);

						return $this->redirect(['view', 'id' => $_REQUEST['id']]);
					}
					else
					{
						$con_id=ContactModel::contactInsert($_REQUEST['id'],'vendor', 0, false); //non primary
					}
				}
			}

			// Contact Delete
			if(!empty($_REQUEST['contact_del']))
			{
				$contactResult = Contact::find()->where("id = '".$_REQUEST['contact_del']."' and entity_type='vendor' and entity_id='".$model->id."'")->one();
				
				if(empty($contactResult))
				{
					return $this->redirect(['view', 'id' => $_REQUEST['id']]);
				}
				
				if($contactResult->is_primary == '1')
				{
					throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
				}

				$contactResult->delete();

				return $this->redirect(['view', 'id' => $_REQUEST['id']]);
			}

			//Address Model
			if(!empty($_REQUEST['address_edit']))
			{
				$sub_address_model=Address::findOne($_REQUEST['address_edit']);
			}
			else
			{
				$sub_address_model= new Address();
			}

			// Address Delete
			if(!empty($_REQUEST['address_del']))
			{
				$addressResult = Address::find()->where("id = '".$_REQUEST['address_del']."' and entity_type='vendor' and entity_id='".$model->id."'")->one();
				
				if(empty($addressResult))
				{
					return $this->redirect(['view', 'id' => $_REQUEST['id']]);
				}
				
				if($addressResult->is_primary == '1')
				{
					throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
				}

				$addressResult->delete();

				return $this->redirect(['view', 'id' => $_REQUEST['id']]);
			}

			// Address Add / Update
			if(!empty($_REQUEST['addressae']))
			{
				if(!empty($_REQUEST['sub_address_1']))
				{
					if(!empty($_REQUEST['address_id']))
					{
						AddressModel::subAddressUpdateWithCity($_REQUEST['address_id']);

						return $this->redirect(['view', 'id' => $_REQUEST['id']]);
					}
					else
					{
						$sub_aid=AddressModel::subAddressInsertWithCity($model->id,'vendor');
					}
				}
			}

			if(!empty($_REQUEST['send_attachment_file']))
			{
				//Send an Email
				SendEmail::sendMultEmail($_REQUEST['uemail'],$_REQUEST['email_body'], $_REQUEST['cc'], $_REQUEST['subject']);

				return $this->redirect(['view', 'id' => $_REQUEST['id']]);
			}

			// Delete  Attachment
			if(!empty($_REQUEST['attachment_del_id']))
			{
				$fileResult = File::find()->where("id = '".$_REQUEST['attachment_del_id']."' and entity_type='vendor' and entity_id='".$model->id."'")->one();

				//$Attachmodel = File::findOne($_REQUEST['attachment_del_id']);
				if (!is_null($fileResult)) 
				{
					$fileResult->delete();
				}

				return $this->redirect(['view', 'id' => $_REQUEST['id']]);
			}

			// Delete  Notes
			if(!empty($_REQUEST['note_del_id']))
			{
				$noteResult = Note::find()->where("id = '".$_REQUEST['note_del_id']."' and entity_type='vendor' and entity_id='".$model->id."'")->one();
				//$NoteDel = Note::findOne($_REQUEST['note_del_id'])->delete();
				
				if (!is_null($noteResult)) 
				{
					$noteResult->delete();
				}

				return $this->redirect(['view', 'id' => $_REQUEST['id']]);
			}

			// Add Attachment for Vendor
			if(!empty($_REQUEST['add_attach']))
			{
				$aid=FileModel::fileInsert($_REQUEST['entity_id'],$this->entity_type);
				
				if($aid > 0)
				{
					return $this->redirect(['view', 'id' => $_REQUEST['id']]);
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

					return $this->redirect(['view', 'id' => $_REQUEST['id'], 'err_msg' => $msg]);
				}
			}

			// Vendor Attachment get
			if(!empty($_REQUEST['attach_update']))
			{
				$attachModelR=File::findOne($_REQUEST['attach_update']);
			}

			// Vendor Notes get
			if(!empty($_REQUEST['note_id']))
			{
				$noteModelR=Note::findOne($_REQUEST['note_id']);
			}

			// Vendor Attachment Update
			if(!empty($_REQUEST['edit_attach']))
			{
				$file=FileModel::fileEdit();

				if(!empty($_FILES['attach']['name']))
				{
					$aid=$_REQUEST['att_id'];
				}

				return $this->redirect(['view', 'id' => $_REQUEST['id']]);
			}

			// Add Notes
			if(!empty($_REQUEST['add_note_model']))
			{
				$nid = NoteModel::noteInsert($_REQUEST['id'],$this->entity_type);

				return $this->redirect(['view', 'id' => $_REQUEST['id']]);
			}

			// Update Notes
			if(!empty($_REQUEST['edit_note_model']))
			{
				$nid = NoteModel::noteEdit();

				return $this->redirect(['view', 'id' => $_REQUEST['id']]);
			}

            return $this->render('view', [
                'model' => $model,
				'addressModel'=>$addressModel,
				'attachModel'=>$attachModelR,
				'noteModel'=>$noteModelR,
				'sub_address_model'=>$sub_address_model,
				'contact'=>$contact,
            ]);
        }
    }

    /**
     * Creates a new Vendor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		//print_r($_REQUEST['city_id']);exit;
       if(!Yii::$app->user->can('Vendor.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		$img = new ImageUpload();
		$emailObj = new SendEmail;
        $model = new Vendor;

		$connection = Yii::$app->db;

//print_r(Yii::$app->request->post());exit;
		try
		{
			$transaction = $connection->beginTransaction();

			if ($model->load(Yii::$app->request->post()) && $model->save()) 
			{
				$address_id = AddressModel::addressInsertWithCity($model->id,'vendor');
				
				$model->added_at = strtotime(date('Y-m-d H:i:s'));
				$model->update();

				//Vendor Add Contact
				$contact_id = ContactModel::contactInsert($model->id,'vendor', $address_id, true); //primary
				$contact = Contact::findOne($contact_id);

				//Create Vendor User to Login to Backend
				if(User::find()->where("email='".$contact->email."'")->count() > 0)
				{
					 return $this->redirect(['view', 'id' => $model->id,'error'=>Yii::t('app', 'User can not be Created Email Already Exists!')]);
				}
				else
				{
					$userModel = new User;
					$userModel->first_name = $contact->first_name;
					$userModel->last_name = $contact->last_name;
					$userModel->email = $contact->email;
					$userModel->username = $contact->email;
					$userModel->active = 1;
					$userModel->user_type_id = UserTypeSearch::getCompanyUserType('Vendor')->id;
					$userModel->entity_id = $model->id;
					$userModel->entity_type = 'vendor';
					$userModel->added_at = time();
					$new_password = Yii::$app->security->generateRandomString (8);
					$userModel->password_hash=Yii::$app->security->generatePasswordHash($new_password);
					$userModel->save();
					/*if(count($userModel->errors) >0){
						var_dump($userModel->errors);
					}*/
					$authModel = new AuthAssignment;
					$authModel->item_name = 'Vendor';
					$authModel->user_id = $userModel->id;
					$authModel->save();
					/*$img->loadImage('users/nophoto.jpg')->saveImage("users/".$userModel->id.".png");
					$img->loadImage('users/nophoto.jpg')->resize(30, 30)->saveImage("users/user_".$userModel->id.".png");*/

					if(!MulteModel::saveFileToServer('nophoto.jpg', $userModel->id.'.png', Yii::$app->params['web_folder']."/users"))
					{
						throw new \Exception (Yii::$app->session->getFlash('error'));
					}
					if(!MulteModel::saveFileToServer('nophoto.jpg', $model->id.'.png', Yii::$app->params['web_folder']."/vendors"))
					{
						throw new \Exception (Yii::$app->session->getFlash('error'));
					}

					SendEmail::sendNewUserEmail($userModel->email,$userModel->first_name." ".$userModel->last_name, $userModel->username,$new_password);
				}
				
				Yii::$app->session->setFlash('success', Yii::t('app', 'Registration completed!'));

				$transaction->commit();
				return $this->redirect(['view', 'id' => $model->id]);
			} 
			else 
			{
				return $this->render('create', [
					'model' => $model,
				]);
			}
		}
		catch (\Exception $e)
		{
			$transaction->rollback();
			Yii::$app->session->setFlash('error', $e->getMessage());
			$model = new Vendor;
			return $this->render('create', [
					'model' => $model,
				]);
		}
    }

    /**
     * Updates an existing Vendor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		if(!Yii::$app->user->can('Vendor.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
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
     * Deletes an existing Vendor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		if(!Yii::$app->user->can('Vendor.Delete')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

	public function actionActivate($id)
    {
		if(!Yii::$app->user->can('Vendor.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $result = $this->findModel($id);
		$result->active = 1;
		$result->updated_at = time();
		$result->save();

		$user = User::find()->where("entity_type='vendor' and entity_id=".$id)->one();
		$user->active = 1;
		$user->save();

		// Update all inventory items to active
		Inventory::updateAll(['active' => 1], ['=', 'vendor_id', $id]);

        return $this->redirect(['index']);
    }

	public function actionDeactivate($id)
    {
		if(!Yii::$app->user->can('Vendor.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $result = $this->findModel($id);
		$result->active = 0;
		$result->updated_at = time();
		$result->save();

		$user = User::find()->where("entity_type='vendor' and entity_id=".$id)->one();
		$user->active = 0;
		$user->save();

		// Update all inventory items to inactive
		Inventory::updateAll(['active' => 0], ['=', 'vendor_id', $id]);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Vendor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vendor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vendor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
