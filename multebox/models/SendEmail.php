<?php
namespace multebox\models;
use Yii;
use yii\db\Query;
use multebox\models\EmailTemplate;
use multebox\models\Order;
use multebox\models\SubOrder;
use multebox\models\OrderStatus;
use multebox\models\User;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use multebox\models\search\MulteModel;
use yii\helpers\Url;

class SendEmail extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '';
    }

	public static function sendMultEmail($uemail, $body, $cc = false, $subject, $from_system = true, $attachment = false, $attachment_name = false) 
	{
		$email = new \PHPMailer(true);
		
		try
		{
			if ($from_system)
			{
				$email->From = Yii::$app->params['SYSTEM_EMAIL'];
				$email->FromName = Yii::$app->params['company']['company_name'];
			}
			else
			{
				$email->From = Yii::$app->user->identity->email;
				$email->FromName = Yii::$app->user->identity->first_name." ".Yii::$app->user->identity->last_name;
			}
			if (!empty($cc))
			{
				$cc = explode(',',$cc);
				foreach($cc as $email_id)
				{
					$email->AddCC($email_id);
				}
			}
			$uemail = explode(',',$uemail);
			$email->Subject = $subject;
			$email->Body = $body;
			foreach($uemail as $email_id)
			{
				$email->AddAddress($email_id);
			}
			if ($attachment)
			{
				$email->AddAttachment($attachment, $attachment_name?$attachment_name:'attachment');
			}
			if (Yii::$app->params['SMTP_AUTH']=='Yes')
			{
				$email->IsSMTP();
				$email->Host = Yii::$app->params['SMTP_HOST'];
				$email->SMTPAuth = true;
				$email->Port = Yii::$app->params['SMTP_PORT'];
				$email->Username = Yii::$app->params['SMTP_USERNAME'];
				$email->Password = MulteModel::multecrypt(Yii::$app->params['SMTP_PASSWORD'], 'd');
				
				if(Yii::$app->params['SMTP_ENCRYPTION'] == 'No')
				{
					$email->SMTPSecure = false;
					$email->SMTPAutoTLS = false;
				}
				else
				{
					$email->SMTPSecure = Yii::$app->params['SMTP_ENCRYPTION']; 
					$email->SMTPOptions = [
											Yii::$app->params['SMTP_ENCRYPTION'] => [
														'verify_peer' => false,
														'verify_peer_name' => false,
														'allow_self_signed' => true
													],
											];
				}		
			}
			$email->IsHTML(true); 
			$email->Send();
			return 0;
			//Yii::$app->session->setFlash('success', 'Email sent successfully!');
		}
		catch (\Exception $e)
		{
			return $e->errorMessage();
			//Yii::$app->session->setFlash('error', 'Send Email Failed: '.$e->errorMessage());
		}
	}

	public static function getCompanyDetail($body)
	{
		$from = array('COMPANY_NAME', 'COMPANY_ADDRESS', 'COMPANY_PHONE','COMPANY_FAX','COMPANY_EMAIL');
		$to   = array(Yii::$app->params['company']['company_name'], 
					nl2br(Yii::$app->params['address']['address_1']."\r\n".Yii::$app->params['address']['address_2']."\r\n".Yii::$app->params['address']['city']."\r\n".Yii::$app->params['address']['state']."\r\n".Yii::$app->params['address']['country']), 
					Yii::$app->params['company']['phone'], 
					Yii::$app->params['company']['fax'], 
					Yii::$app->params['company']['company_email']);
		
		return str_replace($from, $to, $body);
	}

	public static function sendResetPasswordEmail($email,$user_name,$password){
		$email_template = EmailTemplate::find()->where("template_name = 'RESET_PASSWORD'")->one();
		$from = array('NAME', 'PASSWORD');
		$to   = array($user_name,$password);
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to,$email_template->template_body);
		return SendEmail::sendMultEmail($email, $body,false,$email_template->template_subject);
	}

	public static function sendNewUserEmail($email,$user_name,$username,$password, $backend = false){
		if($backend)
		{
			$url = Yii::$app->params['backend_url'];
		}
		else
		{
			$url = Yii::$app->params['frontend_url'];
		}
		$email_template = EmailTemplate::find()->where("template_name = 'NEW_USER_EMAIL'")->one();
		$from = array('EMAIL','FIRST_NAME LAST_NAME', 'USERNAME','PASSWORD','LINK');
		$to   = array($email,$user_name,$username,$password,'<a href="'.$url.'">'.Yii::t('app', 'here').'</a>');
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to,$email_template->template_body);
		return SendEmail::sendMultEmail($email, $body,false, str_replace('EMAIL',$email,$email_template->template_subject));
	}

	public static function sendNewVendorEmailFromFrontend($email,$user_name,$username,$password){
		$email_template = EmailTemplate::find()->where("template_name = 'NEW_USER_EMAIL'")->one();
		$from = array('EMAIL','FIRST_NAME LAST_NAME', 'USERNAME','PASSWORD','LINK');
		$to   = array($email,$user_name,$username,$password,'<a href="'.Yii::$app->params['backend_url'].'">'.Yii::t('app', 'here').'</a>');
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to,$email_template->template_body);
		return SendEmail::sendMultEmail($email, $body,false, str_replace('EMAIL',$email,$email_template->template_subject));
	}

	public static function sendSubOrderStatusChangeEmail($sub_order_id)
	{
		$sub_order = SubOrder::findOne($sub_order_id);
		$order = Order::findOne($sub_order->order_id);
		$user = User::find()->where("entity_type='customer' and entity_id=".$order->customer_id)->one();
		$status = OrderStatus::getLabelByStatus($sub_order->sub_order_status);

		$email_template = EmailTemplate::find()->where("template_name = 'ORDER_STATUS_CHANGE_EMAIL'")->one();
		$from = array('FIRST_NAME', 'LAST_NAME', 'ORDER_NUMBER', 'STATUS', 'LINK');
		$to   = array($user->first_name, $user->last_name, $order->id, $status, '<a href="'.str_replace(Url::base(true), Yii::$app->params['frontend_url'], Url::to(['/order/default/information', 'order_id' => $sub_order->order_id], true)).'">'.Yii::t('app', 'here').'</a>');
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to, $email_template->template_body);
		
		return SendEmail::sendMultEmail($user->email, $body, false, $email_template->template_subject);
	}

	public static function sendVendorInvoiceEmail($vendor_id, $invoice)
	{
		$user = User::find()->where("entity_type='vendor' and entity_id=".$vendor_id)->one();

		$email_template = EmailTemplate::find()->where("template_name = 'VENDOR_INVOICE_EMAIL'")->one();
		$from = array('FIRST_NAME', 'LAST_NAME');
		$to   = array($user->first_name, $user->last_name);
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to, $email_template->template_body);
		
		return SendEmail::sendMultEmail($user->email, $body, false, $email_template->template_subject, true, $invoice, 'Invoice');
	}

	public static function sendOrderConfirmationEmail($order_id, $content)
	{
		$order = Order::findOne($order_id);
		$user = User::find()->where("entity_type='customer' and entity_id=".$order->customer_id)->one();

		$email_template = EmailTemplate::find()->where("template_name = 'ORDER_CONFIRMATION_EMAIL'")->one();
		$from = array('FIRST_NAME', 'LAST_NAME', 'CONTENT');
		$to   = array($user->first_name, $user->last_name, $content);
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to, $email_template->template_body);
		
		return SendEmail::sendMultEmail($user->email, $body, false, $email_template->template_subject.$order_id);
	}

	public static function sendOrderInProcessEmail($order_id)
	{
		$order = Order::findOne($order_id);
		$user = User::find()->where("entity_type='customer' and entity_id=".$order->customer_id)->one();

		$email_template = EmailTemplate::find()->where("template_name = 'ORDER_IN_PROCESS_EMAIL'")->one();
		$from = array('FIRST_NAME', 'LAST_NAME', 'ORDER_ID');
		$to   = array($user->first_name, $user->last_name, $order_id);
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to, $email_template->template_body);
		
		return SendEmail::sendMultEmail($user->email, $body, false, $email_template->template_subject.$order_id);
	}

	public static function sendVendorOrderNotificationEmail($suborder)
	{
		$user = User::find()->where("entity_type='vendor' and entity_id=".$suborder->vendor_id)->one();

		$email_template = EmailTemplate::find()->where("template_name = 'VENDOR_ORDER_NOTIFICATION_EMAIL'")->one();
		$from = array('FIRST_NAME', 'LAST_NAME', 'ORDER_ID', 'LINK');
		$to   = array($user->first_name, $user->last_name, $suborder->id, '<a href="'.str_replace(Url::base(true), Yii::$app->params['backend_url'], Url::to(['/order/sub-order/sub-order-view', 'id' => $suborder->id], true)).'">'.Yii::t('app', 'here').'</a>');
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to, $email_template->template_body);
		
		return SendEmail::sendMultEmail($user->email, $body, false, $email_template->template_subject.$suborder->id);
	}

	public static function sendResetRequestEmail($resetLink, $email, $firstname, $lastname)
	{
		$email_template = EmailTemplate::find()->where("template_name = 'PASSWORD_RESET_REQUEST'")->one();
		$from = array('FIRST_NAME', 'LAST_NAME', 'LINK');
		$to   = array($firstname, $lastname, '<a href="'.$resetLink.'">'.$resetLink.'</a>');
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to, $email_template->template_body);
		
		return SendEmail::sendMultEmail($email, $body, false, $email_template->template_subject);
	}

	public static function sendDigitalLinkEmail($digital_record)
	{
		$user = User::find()->where("entity_type='customer' and entity_id=".$digital_record->customer_id)->one();

		$email_template = EmailTemplate::find()->where("template_name = 'DIGITAL_LINK_EMAIL'")->one();

		$link = Url::to(['/order/default/download', 'did' => $digital_record->id, 'oid' => $digital_record->sub_order_id, 'token' => $digital_record->token], true);
		$from = array('FIRST_NAME', 'LAST_NAME', 'LINK');
		$to   = array($user->first_name, $user->last_name, $link);
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to, $email_template->template_body);
		
		return SendEmail::sendMultEmail($user->email, $body, false, $email_template->template_subject);
	}

	public static function sendLicenseKeyCodeAttachmentEmail($file, $customer_id, $name)
	{
		$user = User::find()->where("entity_type='customer' and entity_id=".$customer_id)->one();

		$email_template = EmailTemplate::find()->where("template_name = 'CODE_ATTACHMENT_EMAIL'")->one();
		$from = array('FIRST_NAME', 'LAST_NAME');
		$to   = array($user->first_name, $user->last_name);
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to, $email_template->template_body);
		
		return SendEmail::sendMultEmail($user->email, $body, false, $email_template->template_subject, true, $file, $name);
	}

	public static function sendLicenseKeyCodeTextEmail($code, $customer_id)
	{
		$user = User::find()->where("entity_type='customer' and entity_id=".$customer_id)->one();

		$email_template = EmailTemplate::find()->where("template_name = 'CODE_TEXT_EMAIL'")->one();
		$from = array('FIRST_NAME', 'LAST_NAME', 'CODE');
		$to   = array($user->first_name, $user->last_name, $code);
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to, $email_template->template_body);
		
		return SendEmail::sendMultEmail($user->email, $body, false, $email_template->template_subject);
	}

	public static function sendTicketAssignedEmail($email,$user_name,$url,$title,$desc)
	{
		$email_template = EmailTemplate::find()->where("template_name = 'NEW_TICKET_ASSIGNED'")->one();

		$from = array('FIRST_NAME LAST_NAME', 'LINK','DESCRIPTION');

		$to   = array($user_name,$url,$desc);
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to,$email_template->template_body);
		
		return SendEmail::sendMultEmail($email, $body,false, $title);

	}

	public static function sendCustomerTicketNotification($email,$user_name,$title,$status)
	{
		$email_template = EmailTemplate::find()->where("template_name = 'CUSTOMER_TICKET_NOTIFICATION'")->one();

		$from = array('FIRST_NAME LAST_NAME', 'TITLE','STATUS');

		$to   = array($user_name,$title,$status);
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to,$email_template->template_body);

		return SendEmail::sendMultEmail($email, $body,false, $title);

	}

	public static function sendEnquiryEmail($name,$email,$phone,$message)
	{
		$email_template = EmailTemplate::find()->where("template_name = 'CUSTOMER_ENQUIRY'")->one();

		$from = array('ENQUIRER_NAME', 'ENQUIRER_EMAIL', 'ENQUIRER_PHONE', 'ENQUIRER_MESSAGE');

		$to   = array($name,$email,$phone,$message);
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to, $email_template->template_body);
		
		return SendEmail::sendMultEmail(Yii::$app->params['company']['company_email'], $body, false, $email_template->template_subject);
	}

	public static function sendItemToFriendEmail($sendername, $friendname, $senderemail, $friendemail, $link, $message)
	{
		$email_template = EmailTemplate::find()->where("template_name = 'SEND_ITEM_TO_FRIEND'")->one();

		$from = array('SENDER_NAME', 'FRIEND_NAME', 'SENDER_EMAIL', 'LINK', 'MESSAGE');

		$to   = array($sendername, $friendname, $senderemail, $link, $message);
		$email_template->template_body = SendEmail::getCompanyDetail($email_template->template_body);
		$body = str_replace($from, $to, $email_template->template_body);
		
		return SendEmail::sendMultEmail($friendemail, $body, false, $email_template->template_subject);
	}
}
