<?php

namespace multefront\modules\customer\controllers;

use multebox\Controller;
use Yii;
use multebox\models\User;
use multebox\models\Contact;
use multebox\models\Address;
use multebox\models\AddressModel;
use multebox\models\Ticket;
use multebox\models\TicketSla;
use multebox\models\TicketStatus;
use multebox\models\Note;
use multebox\models\SendEmail;
use yii\web\NotFoundHttpException;
use \Exception;


/**
 * Default controller for the `customer` module
 */
class DefaultController extends Controller
{
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

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

	public function actionSupport()
    {
		if(Yii::$app->user->isGuest && Yii::$app->user->identity->entity_id != 'customer')
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}

		$connection = Yii::$app->db;
		$model = new Ticket;
		
		try
		{
			$transaction = $connection->beginTransaction();

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

					$transaction->commit();
					
					$ticketStatusModel = TicketStatus::find()->where("status='".$model->ticket_status."'")->one();
					
					if($model->user_assigned_id && $model->user_assigned_id != 0)
					{
						SendEmail::sendTicketAssignedEmail($this->getUserEmail($model->user_assigned_id), $this->getUserFullName($model->user_assigned_id),'<a href="'.Url::to(['/support/ticket/update', 'id' => $model->id], true).'">'.$stringId.'</a>', $model->ticket_title, $model->ticket_description);
					}

					SendEmail::sendCustomerTicketNotification($this->getCustomerEmail($model->ticket_customer_id),$this->getCustomerFullName($model->ticket_customer_id), $model->ticket_title, $ticketStatusModel->label);
					
					Yii::$app->session->setFlash('success', Yii::t('app', 'Ticket Submitted Successfully!'));

					return $this->redirect(['/site/index']);
				}
				else
				{
					$transaction->rollback();
					return $this->render('report-issue');
				}
			}
			else 
			{
				$transaction->rollback();
				return $this->render('report-issue');
			}
		}
		catch (\Exception $e)
		{
			$transaction->rollback();
			Yii::$app->session->setFlash('error', $e->getMessage());
			return $this->render('report-issue');
		}
    }

	public function actionAccount()
    {
		$redirect = false;
		$address = isset($_REQUEST['address_modal'])?$_REQUEST['address_modal']:'';
		$contact = isset($_REQUEST['contact_modal'])?$_REQUEST['contact_modal']:'';
		$connection = Yii::$app->db;
		$addedit = false;

		try
		{
			$transaction = $connection->beginTransaction();
			$isnew = false;
			
			if(isset($_REQUEST['oldpassword']))
			{
				$user = User::findOne(Yii::$app->user->identity->id);

				if(!$user->validatePassword($_REQUEST['oldpassword']))
				{
					throw new Exception (Yii::t('app', 'Incorrect old password provided!'));
				}

				$user->password_hash = Yii::$app->security->generatePasswordHash ($_REQUEST['newpassword']);

				$user->save();

				$transaction->commit();  // Commiting the transaction here as we need to now logout!
				Yii::$app->user->logout();
				Yii::$app->session->setFlash('success', Yii::t('app', 'Password Changed Successfully!'));
				return $this->redirect(['/site/login']);
			}
			else
			if(isset($_REQUEST['firstname']))
			{
				$user = User::findOne(Yii::$app->user->identity->id);
				
				$user->first_name = $_REQUEST['firstname'];
				$user->last_name = $_REQUEST['lastname'];

				if(User::find()->where("email='".$_REQUEST['useremail']."' and id != '".Yii::$app->user->identity->id."'")->exists())
				{
					throw new Exception (Yii::t('app', 'Email address already in use - Please use another!'));
				}

				$user->email = $_REQUEST['useremail'];
				$user->username = $_REQUEST['useremail'];

				$user->save();

				Contact::updateAll(['email' => $_REQUEST['useremail']], ['and', ['=', 'entity_type', 'customer'], ['=', 'entity_id', Yii::$app->user->identity->entity_id]]);

				/*if($change)
				{
					$transaction->commit();  // Commiting the transaction here as we need to now logout!
					Yii::$app->user->logout();
					Yii::$app->session->setFlash('success', Yii::t('app', 'Details updated Successfully!'));
					return $this->redirect(['/site/login']);
				}*/

				Yii::$app->session->setFlash('success', Yii::t('app', 'Details updated Successfully!'));
				$redirect = true;
			}
			else
			if(isset($_REQUEST['modal_address_id']))
			{
				if($_REQUEST['modal_address_id'] == '')
				{
					// Add new details
					$isnew = true;
					$address_modal = new Address;
					$contact_modal = new Contact;
				}
				else
				{
					// Update existing
					$address_modal = Address::findOne($_REQUEST['modal_address_id']);
					$contact_modal = Contact::findOne($_REQUEST['modal_contact_id']);
				}

				$address_modal->address_1 = $_REQUEST['address_1'];
				$address_modal->address_2 = $_REQUEST['address_2'];
				$address_modal->country_id = $_REQUEST['country_id'];
				$address_modal->state_id = $_REQUEST['state_id'];
				$address_modal->city_id = AddressModel::getCityId($_REQUEST['country_id'], $_REQUEST['state_id'], $_REQUEST['city_id']);
				$address_modal->zipcode = $_REQUEST['zipcode'];
				$address_modal->entity_id = Yii::$app->user->identity->entity_id;
				$address_modal->entity_type = 'customer';
				
				if ($isnew)
				{
					if(Address::find()->where("entity_type='customer' and entity_id='".Yii::$app->user->identity->entity_id."'")->count() > 0)
						$address_modal->is_primary = '0';
					else
						$address_modal->is_primary = '1';
					$address_modal->added_at = time();
				}
				else
				{
					$address_modal->updated_at = time();
				}

				$address_modal->save();

				$contact_modal->first_name = $_REQUEST['modal_firstname'];
				$contact_modal->last_name = $_REQUEST['modal_lastname'];
				$contact_modal->email = Yii::$app->user->identity->email;
				$contact_modal->mobile = $_REQUEST['mobile'];
				$contact_modal->address_id = $address_modal->id;
				$contact_modal->entity_id = Yii::$app->user->identity->entity_id;
				$contact_modal->entity_type = 'customer';
				
				if ($isnew)
				{
					if(Contact::find()->where("entity_type='customer' and entity_id='".Yii::$app->user->identity->entity_id."'")->count() > 0)
						$contact_modal->is_primary = '0';
					else
						$contact_modal->is_primary = '1';
					$contact_modal->added_at = time();
				}
				else
					$contact_modal->updated_at = time();

				$contact_modal->save();

				if($isnew)
					Yii::$app->session->setFlash('success', Yii::t('app', 'Address added Successfully!'));
				else
					Yii::$app->session->setFlash('success', Yii::t('app', 'Address updated Successfully!'));
			}
			else
			if(isset($_REQUEST['edit_address_id'])) // Edit address
			{
				$addedit = true;
				$address = Address::find()->where("id = '".$_REQUEST['edit_address_id']."' and entity_type='customer' and entity_id = '".Yii::$app->user->identity->entity_id."'")->one();
				$contact = Contact::find()->where("address_id = '".$_REQUEST['edit_address_id']."' and entity_type='customer' and entity_id = '".Yii::$app->user->identity->entity_id."'")->one();
			}
			else
			if(isset($_REQUEST['del_address_id'])) // Delete address
			{
				$redirect = true;
				$address = Address::find()->where("id = '".$_REQUEST['del_address_id']."' and entity_type='customer' and entity_id = '".Yii::$app->user->identity->entity_id."'")->one();
				$contact = Contact::find()->where("address_id = '".$_REQUEST['del_address_id']."' and entity_type='customer' and entity_id = '".Yii::$app->user->identity->entity_id."'")->one();

				if($address && $contact)
				{
					$address->delete();
					$contact->delete();
				}
				else
				{
					throw new Exception(Yii::t('app', 'Address cannot be deleted!'));
				}

				Yii::$app->session->setFlash('success', Yii::t('app', 'Address deleted Successfully!'));
			}
			else
			if(isset($_REQUEST['def_address_id'])) // Make default
			{
				$redirect = true;
				$address = Address::find()->where("id = '".$_REQUEST['def_address_id']."' and entity_type='customer' and entity_id = '".Yii::$app->user->identity->entity_id."'")->one();
				$contact = Contact::find()->where("address_id = '".$_REQUEST['def_address_id']."' and entity_type='customer' and entity_id = '".Yii::$app->user->identity->entity_id."'")->one();

				if($address && $contact)
				{
					$address->is_primary = 1;
					$contact->is_primary = 1;

					$address->save();
					$contact->save();

					Address::updateAll(['is_primary' => 0], ['and', ['=', 'entity_type', 'customer'], ['=', 'entity_id', Yii::$app->user->identity->entity_id], ['!=', 'id', $address->id]]);

					Contact::updateAll(['is_primary' => 0], ['and', ['=', 'entity_type', 'customer'], ['=', 'entity_id', Yii::$app->user->identity->entity_id], ['!=', 'id', $contact->id]]);

					Yii::$app->session->setFlash('success', Yii::t('app', 'Address set as default!'));
				}
				else
				{
					throw new Exception(Yii::t('app', 'Address cannot be set as default!'));
				}
			}

			$transaction->commit();
		}
		catch (Exception $e)
		{
			Yii::$app->session->setFlash('error', $e->getMessage());
			$transaction->rollback();
			return $this->redirect('account');
		}
		
		if($redirect)
			return $this->redirect('account');
		else
			return $this->render('account', ['address_modal' => $address, 'contact_modal' => $contact]);
    }
}
