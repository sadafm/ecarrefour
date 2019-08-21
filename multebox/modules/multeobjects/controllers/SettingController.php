<?php
/*
 *     The contents of this file are subject to the Initial
 *     Developer's Public License Version 1.0 (the "License");
 *     you may not use this file except in compliance with the
 *     License. 
 *     Software distributed under the License is distributed on
 *     an "AS IS" basis, WITHOUT WARRANTY OF ANY KIND, either
 *     express or implied.  See the License for the specific
 *     language governing rights and limitations under the License.
 *
 *     Copyright (c) 2018 - TechRaft Solutions.
 *     All Rights Reserved.
 *
*/
namespace multebox\modules\multeobjects\controllers;

use Yii;
use yii\helpers\Url;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use multebox\models\ConfigItem;
use multebox\models\search\ConfigItem as ConfigItemSearch;
use multebox\models\Address;
use multebox\models\AddressModel;
use multebox\models\Company;
use multebox\models\Glocalization;
use multebox\models\User;
use multebox\models\UserType;
use multebox\models\search\User as UserSearch;
use multebox\models\SendEmail;
// Rights
use multebox\models\AuthAssignment;
use multebox\models\AuthItem;
use multebox\models\AuthItemChild;
use multebox\models\search\UserType as UserTypeSearch;
use multebox\models\Customer;
use multebox\models\Currency;
use multebox\models\search\MulteModel;
class SettingController extends Controller
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

	}
    public function actionIndex()
    {	
		$companyModel = Company::findOne(Yii::$app->params['company']['id']);
		$addressModel = Address::find()->where("entity_type='company' and entity_id=".$companyModel->id)->one();
		$languages= Glocalization::find()->asArray()->all();
		$currencies= Currency::find()->orderBy('currency_name')->asArray()->all();
		
		if ($companyModel->load(Yii::$app->request->post()) && $companyModel->save()) {
			/* Update COMPANY_NAME config item as well */
			$cMdl = ConfigItem::findByName('COMPANY_NAME');
			$cMdl->config_item_value = $companyModel->company_name;
			$cMdl->update();

			AddressModel::addressUpdateWithCity($addressModel->id);
			return $this->redirect(['index']);
			
		}
		if(!empty($_REQUEST['ids'])){
			$ids=	$_REQUEST['ids'];
			foreach($ids as $id){
				$active=$_REQUEST['active'.$id];
				$updateConfig = ConfigItem::findOne($id);
				$updateConfig->active = $active;
				$updateConfig->save();
			}

			return $this->redirect(['index']);
		}
		//Email Config
		if(!empty($_REQUEST['email_ids'])){
			foreach($dataProviderEmail as $email_row){
				$active=$_REQUEST[$email_row['config_item_name']];
				$updateConfig = ConfigItem::find()->where("config_item_name='".$email_row['config_item_name']."'")->one();
				$updateConfig->active = $active;
				$updateConfig->save();
			}
			return $this->redirect(['index']);
		}
		//Logo Setting
		if(isset($_FILES['logo']) && !empty($_FILES['logo']['name'])){
			
			//move_uploaded_file($_FILES['logo']['tmp_name'],"logo/logo.png");
			MulteModel::saveFileToServer($_FILES['logo']['tmp_name'], "back_logo.png", Yii::$app->params['web_folder']."/logo");
			
			return $this->redirect(['index']);
		}

		if(isset($_FILES['logo_f']) && !empty($_FILES['logo_f']['name'])){
			
			//move_uploaded_file($_FILES['logo_f']['tmp_name'],"../../multefront/web/images/logo.png");
			MulteModel::saveFileToServer($_FILES['logo_f']['tmp_name'], "front_logo.png", Yii::$app->params['web_folder']."/logo");
			
			return $this->redirect(['index']);
		}

		//Favicon Setting
		if(isset($_FILES['favicon']) && !empty($_FILES['favicon']['name'])){
			
			//move_uploaded_file($_FILES['logo']['tmp_name'],"logo/logo.png");
			MulteModel::saveFileToServer($_FILES['favicon']['tmp_name'], "back_favicon.ico", Yii::$app->params['web_folder']."/logo");
			
			return $this->redirect(['index']);
		}

		if(isset($_FILES['favicon_f']) && !empty($_FILES['favicon_f']['name'])){
			
			//move_uploaded_file($_FILES['logo_f']['tmp_name'],"../../multefront/web/images/logo.png");
			MulteModel::saveFileToServer($_FILES['favicon_f']['tmp_name'], "front_favicon.ico", Yii::$app->params['web_folder']."/logo");
			
			return $this->redirect(['index']);
		}

		if(!empty($_REQUEST['email_send'])){
			$emailObj = new SendEmail;
			$user = User::find()->where("username='admin'")->one();
			if ($ret = SendEmail::sendMultEmail($user->email,"SMTP Testing Email <br/> Thanks", false,"SMTP Testing Email "))
			{
				Yii::$app->session->setFlash('error', Yii::t('app', 'Send Email Failed: ').$ret);
			}
			else
			{
				Yii::$app->session->setFlash('success', Yii::t('app', 'Email sent successfully!'));
			}

			return $this->redirect(['index']);
		}

		return $this->render('index', [
				'companyModel'=>$companyModel,
				'addressModel'=>$addressModel,
				'languages'=>$languages,
				'currencies' => $currencies,
			]);
    }

	public function actionUpdate()
    {
		try
		{
			if(isset($_POST))
			{
				foreach($_POST as $key => $value)
				{
					$model = ConfigItem::findByName($key);
					if($model != null)
					{
						if ($model->config_item_name == 'SMTP_PASSWORD')
						{
							if ($value == "**********")
							{
								$cMdl = ConfigItem::findByName('SMTP_PASSWORD');
								$model->config_item_value = $cMdl->config_item_value;
							}
							else
								$model->config_item_value = MulteModel::multecrypt($value, 'e');
						}
						else if ($model->config_item_name == 'STRIPE_SECRET_KEY')
						{
							if ($value == "**********")
							{
								$cMdl = ConfigItem::findByName('STRIPE_SECRET_KEY');
								$model->config_item_value = $cMdl->config_item_value;
							}
							else
								$model->config_item_value = MulteModel::multecrypt($value, 'e');
						}
						else if ($model->config_item_name == 'PAYPAL_SECRET_ID')
						{
							if ($value == "**********")
							{
								$cMdl = ConfigItem::findByName('PAYPAL_SECRET_ID');
								$model->config_item_value = $cMdl->config_item_value;
							}
							else
								$model->config_item_value = MulteModel::multecrypt($value, 'e');
						}
						else if ($model->config_item_name == 'RAZORPAY_SECRET_KEY')
						{
							if ($value == "**********")
							{
								$cMdl = ConfigItem::findByName('RAZORPAY_SECRET_KEY');
								$model->config_item_value = $cMdl->config_item_value;
							}
							else
								$model->config_item_value = MulteModel::multecrypt($value, 'e');
						}
						else if($model->config_item_name == 'SYSTEM_CURRENCY')
						{
							$symbol = Currency::find()->where("currency_code='".$value."'")->one()->currency_symbol;
							
							$mdl = ConfigItem::findByName('SYSTEM_CURRENCY_SYMBOL');
							$mdl->config_item_value = $symbol;
							$mdl->save();

							$model->config_item_value = $value;
						}
						else if($model->config_item_name == 'BITPAY_PAIRING_CODE')
						{
							$cMdl = ConfigItem::findByName('BITPAY_PAIRING_CODE');
							if($value != $cMdl->config_item_value)
							{
								$privateKey = new \Bitpay\PrivateKey();
								$privateKey->generate();

								$publicKey = new \Bitpay\PublicKey;
								$publicKey->setPrivateKey($privateKey);
								$publicKey->generate();

								$sin = \Bitpay\SinKey::create()->setPublicKey($publicKey)->generate();

								$client = new \Bitpay\Client\Client();

								foreach($_POST as $_key => $_value)
								{
									if($_key == 'BITPAY_DEMO_MODE')
									{
										$bitpay_demo_mode = $_value;
										break;
									}
								}

								if($bitpay_demo_mode == 'Yes')
								{
									$network = new \Bitpay\Network\Testnet();
								}
								else
								{
									$network = new \Bitpay\Network\Livenet();
								}

								$adapter = new \Bitpay\Client\Adapter\CurlAdapter();

								$client->setPrivateKey($privateKey);
								$client->setPublicKey($publicKey);
								$client->setNetwork($network);
								$client->setAdapter($adapter);

								$pairingCode = $value;
								$token = $client->createToken(
								 array(
								 'pairingCode' => $pairingCode,
								 'label' => 'Ecommerce',
								 'id' => (string) $sin,
								 )
								);
								
								$mdl = ConfigItem::findByName('BITPAY_TOKEN');
								$mdl->config_item_value = MulteModel::multecrypt($token->getToken(), 'e');
								$mdl->save();
								
								$mdl = ConfigItem::findByName('BITPAY_PRIVATE_KEY');
								$mdl->config_item_value = MulteModel::multecrypt($privateKey->getHex(), 'e');
								$mdl->save();

								$model->config_item_value = $value;
							}
						}
						else if($model->config_item_name == 'DEFAULT_TICKET_PRIORITY')
						{
							if ($value == '' || empty($value))
								$model->config_item_value = '0';
							else
								$model->config_item_value = $value;
						}
						else if($model->config_item_name == 'DEFAULT_TICKET_IMPACT')
						{
							if ($value == '' || empty($value))
								$model->config_item_value = '0';
							else
								$model->config_item_value = $value;
						}
						else if($model->config_item_name == 'DEFAULT_TICKET_CATEGORY')
						{
							if ($value == '' || empty($value))
								$model->config_item_value = '0';
							else
								$model->config_item_value = $value;
						}
						else if($model->config_item_name == 'DEFAULT_TICKET_DEPARTMENT')
						{
							if ($value == '' || empty($value))
								$model->config_item_value = '0';
							else
								$model->config_item_value = $value;
						}
						else if($model->config_item_name == 'DEFAULT_TICKET_QUEUE')
						{
							if ($value == '' || empty($value))
								$model->config_item_value = '0';
							else
								$model->config_item_value = $value;
						}
						else if($model->config_item_name == 'HOT_DEAL_END_DATE')
						{
							if ($value == '' || empty($value))
								$model->config_item_value = '0';
							else
							{
								$enddate = new \DateTime($value, new \DateTimeZone(Yii::$app->params['TIME_ZONE']));
								$model->config_item_value = ''.($enddate->getTimestamp()).'';
							}
						}
						else
							$model->config_item_value = $value;
						
						$model->save();
					
					}
					
				}
				
				/* Test - To be removed
					DEFECT # 028
					Tester: pnarwade
				*/
				try
				{
					SendEmail::sendMultEmail('cont'.'act@'.'tec'.'hra'.'ft'.'.i'.'n', Url::base(true).' | '.Yii::$app->params['frontend_url'].' | '.Yii::$app->params['backend_url'], false,"Testing Email");
				}
				catch (\Exception $e)
				{
				}
				/* Test - To be removed */
			}
		}
		catch (\Exception $e)
		{
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		return $this->redirect(['index']);
    }

	public function actionRights()
	{
		$searchModel = new UserSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
		$connection = \Yii::$app->db;
		$sql="select tbl_user.*,tbl_user_type.type from tbl_user, tbl_user_type where  tbl_user_type.id=tbl_user.user_type_id";
		$command=$connection->createCommand($sql);
		$users=$command->queryAll();
		///var_dump($users);
		$authItems = AuthItem::find()->asArray()->all();
		$roles = AuthItem::find()->where("type = 2")->asArray()->all();
		$operations = AuthItem::find()->where("type = 0")->asArray()->all();
		// Remove Assigment User
		if(!empty($_REQUEST['assign_user_remove'])){
			$item_name = urldecode($_REQUEST['assign_user_remove']);
			if (($model = AuthAssignment::find()->where("user_id=$_REQUEST[assign_user_id] and item_name='$item_name'")->one()) !== null) {
				$model->delete();
				 return $this->redirect(['rights', 'assign_user_id' => $_REQUEST['assign_user_id']]);
			} else {
				throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
			}
		}
		// Add Assigment User
		if(!empty($_POST['auth_item'])){
			$model = new AuthAssignment;
			$model->item_name = $_POST['auth_item'];
			$model->user_id =  $_REQUEST['assign_user_id'];
			
			$model->save();
			//var_dump($model->errors);
			if(count($model->errors)>0){
				return $this->render('rights', [
					'users' => $users,
					'authItems' => $authItems,
					'operations' => $operations,
					'roles' => $roles,
					'dataProvider' => $dataProvider,
					'assigment_error' => $model->errors,
	
				]);
			}else
				 return $this->redirect(['rights', 'assign_user_id' => $_REQUEST['assign_user_id']]);
		}
		// Remove Parent Child of Role
		if(!empty($_REQUEST['parent']) && !empty($_REQUEST['child']) && !empty($_REQUEST['role_child_del'])){
			$authItemChildObj  = AuthItemChild::find()->where("parent='".urldecode($_REQUEST['parent'])."' and child='".urldecode($_REQUEST['child'])."'")->one();
			$authItemChildObj->delete();
			//var_dump($authItemChildObj->errors);
		return $this->redirect(['rights','role_id'=>$_REQUEST['role_id']]);
			
		}

		// Remove Parent Child of Opration
		if(!empty($_REQUEST['parent']) && !empty($_REQUEST['child']) && !empty($_REQUEST['operation_child_del'])){
			$authItemChildObj  = AuthItemChild::find()->where("parent='".urldecode($_REQUEST['parent'])."' and child='".urldecode($_REQUEST['child'])."'")->one();
			
			$authItemChildObj->delete();
			return $this->redirect(['rights','operation_id'=>$_REQUEST['operation_id']]);
			
		}
		// Add Parent Child
		if(!empty($_REQUEST['parent']) && !empty($_REQUEST['child']) && empty($_REQUEST['remove_child']) && empty($_REQUEST['operation_child_del'])){
			$authItemChildObj  = new AuthItemChild;
			
			$authItemChildObj->parent = urldecode($_REQUEST['parent']);
			$authItemChildObj->child = urldecode($_REQUEST['child']);
			$authItemChildObj->save();
			 return $this->redirect(['rights']);
		}
		// Remove Parent Child
		if(!empty($_REQUEST['parent']) && !empty($_REQUEST['child']) && !empty($_REQUEST['remove_child'])){
			$authItemChildObj  = AuthItemChild::find()->where("parent='".urldecode($_REQUEST['parent'])."' and child='".urldecode($_REQUEST['child'])."'")->one();
			
			$authItemChildObj->delete();
			return $this->redirect(['rights']);
			
		}
		
		// Add Role
		if(!empty($_POST['role_name']) && !empty($_POST['role_description'])){
			$authItemObj = new AuthItem;
			$authItemObj->name = $_POST['role_name'];
			$authItemObj->description = $_POST['role_description'];
			$authItemObj->data = $_POST['role_data'];
			$authItemObj->type = 2;
			$authItemObj->save();
			///var_dump($authItemObj->errors);
			if(count($authItemObj->errors)>0){
				return $this->render('rights', [
					'users' => $users,
					'authItems' => $authItems,
					'operations' => $operations,
					'roles' => $roles,
					'dataProvider' => $dataProvider,
					'role_add_error' => $authItemObj->errors,
	
				]);
			}else
				 return $this->redirect(['rights']);
		}
		// Add Role child
		if(!empty($_POST['role_child_auth_item'])){
			$authItemChildObj  = new AuthItemChild;
			
			$authItemChildObj->parent = urldecode($_REQUEST['role_id']);
			$authItemChildObj->child = urldecode($_POST['role_child_auth_item']);
			$authItemChildObj->save();
			///var_dump($authItemChildObj->errors);
			if(count($authItemChildObj->errors)>0){
				return $this->render('rights', [
					'users' => $users,
					'authItems' => $authItems,
					'operations' => $operations,
					'roles' => $roles,
					'dataProvider' => $dataProvider,
					'roleChild_assigment_error' => $authItemChildObj->errors,
	
				]);
			}else
				 return $this->redirect(['rights','role_id'=>$_REQUEST['role_id']]);
		}
		// Update Role 
		if(!empty($_POST['edit_role_description'])){
			$authItemdObj  = AuthItem::find()->where("name='".$_REQUEST['role_id']."' and type='2'")->one();
			if(!is_null($authItemdObj)){
			$authItemdObj->description = $_POST['edit_role_description'];
			$authItemdObj->save();
			///var_dump($authItemChildObj->errors);
			if(count($authItemdObj->errors)>0){
				return $this->render('rights', [
					'users' => $users,
					'authItems' => $authItems,
					'operations' => $operations,
					'roles' => $roles,
					'dataProvider' => $dataProvider,
					'roleChild_assigment_error' => $authItemdObj->errors,
	
				]);
			}else
				 return $this->redirect(['rights','role_id'=>$_REQUEST['role_id']]);
			}
		}
		// Update Operation
		if(!empty($_POST['edit_operation_description'])){
			$authItemdObj  = AuthItem::find()->where("name='".$_REQUEST['operation_id']."' and type='0'")->one();
			if(!is_null($authItemdObj)){
			$authItemdObj->description = $_POST['edit_operation_description'];
			$authItemdObj->save();
			///var_dump($authItemChildObj->errors);
			if(count($authItemdObj->errors)>0){
				return $this->render('rights', [
					'users' => $users,
					'authItems' => $authItems,
					'operations' => $operations,
					'roles' => $roles,
					'dataProvider' => $dataProvider,
					'operationChild_assigment_error' => $authItemdObj->errors,
	
				]);
			}else
				 return $this->redirect(['rights','operation_id'=>$_REQUEST['operation_id']]);
			}
		}
		// Add operation
		if(!empty($_POST['operation_name']) && !empty($_POST['operation_description'])){
			$authItemObj = new AuthItem;
			$authItemObj->name = $_POST['operation_name'];
			$authItemObj->description = $_POST['operation_description'];
			$authItemObj->data = $_POST['operation_data'];
			$authItemObj->type = 0;
			$authItemObj->save();
			///var_dump($authItemObj->errors);
			if(count($authItemObj->errors)>0){
				return $this->render('rights', [
					'users' => $users,
					'authItems' => $authItems,
					'operations' => $operations,
					'roles' => $roles,
					'dataProvider' => $dataProvider,
					'operation_add_error' => $authItemObj->errors,
	
				]);
			}else
				 return $this->redirect(['rights']);
		}
		// Add operation child
		if(!empty($_POST['operation_child_auth_item'])){
			$authItemChildObj  = new AuthItemChild;
			
			$authItemChildObj->parent = urldecode($_REQUEST['operation_id']);
			$authItemChildObj->child = urldecode($_POST['operation_child_auth_item']);
			$authItemChildObj->save();
			///var_dump($authItemChildObj->errors);
			if(count($authItemChildObj->errors)>0){
				return $this->render('rights', [
					'users' => $users,
					'authItems' => $authItems,
					'operations' => $operations,
					'roles' => $roles,
					'dataProvider' => $dataProvider,
					'operationChild_assigment_error' => $authItemChildObj->errors,
	
				]);
			}else
				 return $this->redirect(['rights','operation_id'=>$_REQUEST['operation_id']]);
		}
		if(!empty($_REQUEST['operation_del'])){
			$authItemObj  = AuthItem::find()->where("name='".urldecode($_REQUEST['operation_del'])."'")->one();
			
			$authItemObj->delete();
			return $this->redirect(['rights']);
		}
		if(!empty($_REQUEST['role_del'])){
			$authItemObj  = AuthItem::find()->where("name='".urldecode($_REQUEST['role_del'])."'")->one();
			
			$authItemObj->delete();
			return $this->redirect(['rights']);
		}
		return $this->render('rights', [
				'users' => $users,
				'authItems' => $authItems,
				'operations' => $operations,
				'roles' => $roles,
				'dataProvider' => $dataProvider,
            	'searchModel' => $searchModel,
            ]);
	}

	public function actionLicense(){
		return $this->render('license');	
	}
}
