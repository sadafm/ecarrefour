<?php

namespace multefront\modules\order\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\Json;
use multebox\Controller;
use multebox\models\Cart;
use multebox\models\Inventory;
use multebox\models\DiscountCoupons;
use multebox\models\GlobalDiscount;
use multebox\models\search\MulteModel;
use multebox\models\Customer;
use multebox\models\Contact;
use multebox\models\Address;
use multebox\models\AddressModel;
use multebox\models\User;
use multebox\models\search\UserType as UserTypeSearch;
use multebox\models\Order;
use multebox\models\SubOrder;
use multebox\models\OrderStatus;
use multebox\models\ProductSubSubCategory;
use multebox\models\Tax;
use multebox\models\StateTax;
use multebox\models\PaymentMethods;
use \Exception;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use multebox\models\PaypalDetails;
use multebox\models\StripeDetails;
use multebox\models\PaypalRefundDetails;
use multebox\models\StripeRefundDetails;
use multebox\models\SendEmail;
use multebox\models\Invoice;
use multebox\models\DigitalRecords;
use multebox\models\LicenseKeyCode;
use multebox\models\BitcoinDetails;
use multebox\models\CurrencyConversion;
use multebox\models\Wishlist;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use multebox\models\RazorpayDetails;

/**
 * Default controller for the `order` module
 */
class DefaultController extends Controller
{
	/**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

	public function actionRazorpayRefund()
	{
		$order = Order::findOne($_REQUEST['order_id']);
		$suborder = SubOrder::findOne($_REQUEST['sub_order_id']);

		if($suborder->sub_order_status != OrderStatus::_RETURNED && $suborder->sub_order_status != OrderStatus::_CANCELED || $suborder->order_id != $order->id)
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}
		
		try
		{
			$connection = Yii::$app->db;
			$transaction = $connection->beginTransaction();

			MulteModel::issueRazorpayRefund($suborder);

			$transaction->commit();
		}
		catch (Exception $e)
		{
			$transaction->rollback();
			Yii::$app->session->setFlash('error', Yii::t('app', 'Razorpay refund encountered error: ').$e->getMessage());
		}

		return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
	}

	public function actionStripeRefund()
    {
		$order = Order::findOne($_REQUEST['order_id']);
		$suborder = SubOrder::findOne($_REQUEST['sub_order_id']);

		if($suborder->sub_order_status != OrderStatus::_RETURNED && $suborder->sub_order_status != OrderStatus::_CANCELED || $suborder->order_id != $order->id)
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}
		
		try
		{
			$connection = Yii::$app->db;
			$transaction = $connection->beginTransaction();

			MulteModel::issueStripeRefund($suborder);

			$transaction->commit();
		}
		catch (Exception $e)
		{
			$transaction->rollback();
			Yii::$app->session->setFlash('error', Yii::t('app', 'Stripe refund encountered error: ').$e->getMessage());
		}

		return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
    }

	public function actionPaypalRefund()
	{
		$order = Order::findOne($_REQUEST['order_id']);
		$suborder = SubOrder::findOne($_REQUEST['sub_order_id']);

		if($suborder->sub_order_status != OrderStatus::_RETURNED && $suborder->sub_order_status != OrderStatus::_CANCELED || $suborder->order_id != $order->id)
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}
		
		try
		{
			$connection = Yii::$app->db;
			$transaction = $connection->beginTransaction();

			MulteModel::issuePaypalRefund($suborder);

			$transaction->commit();
		}
		catch (Exception $e)
		{
			$transaction->rollback();
			Yii::$app->session->setFlash('error', Yii::t('app', 'Paypal refund encountered error: ').$e->getMessage());
		}

		return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
	}

	public function actionExecutePaypalPayment()
    {
		$apiContext = new \PayPal\Rest\ApiContext(
												  new \PayPal\Auth\OAuthTokenCredential(
													Yii::$app->params['PAYPAL_API_ID'],
													MulteModel::multecrypt(Yii::$app->params['PAYPAL_SECRET_ID'], 'd')
												  )
												);

        // Get payment object by passing paymentId
		$paymentId = $_GET['paymentId'];
		$payment = Payment::get($paymentId, $apiContext);
		$payerId = $_GET['PayerID'];

		// Execute payment with payer id
		$execution = new \PayPal\Api\PaymentExecution();
		$execution->setPayerId($payerId);

		try
		{
			// Execute payment
			$result = $payment->execute($execution, $apiContext);
			//var_dump($result);
		}
		catch (Exception $e) 
		{
			Yii::$app->session->setFlash('error', Yii::t('app', 'Paypal payment failed with error: ').$e->getMessage());
			return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
		}

		// Now update order status
		try
		{	
			$connection = Yii::$app->db;
			$transaction = $connection->beginTransaction();

			$order = Order::findOne($_REQUEST['order_id']);
			$order->order_status = OrderStatus::_CONFIRMED;
			$order->save();

			SubOrder::updateAll(['sub_order_status' => OrderStatus::_CONFIRMED], ['and', ['=', 'order_id', $order->id], ['=', 'sub_order_status', OrderStatus::_NEW]]);

			$transactions = $payment->getTransactions();
			$relatedResources = $transactions[0]->getRelatedResources();
			$sale = $relatedResources[0]->getSale();
			$saleId = $sale->getId();

			$paypaldetails = new PaypalDetails;
			$paypaldetails->order_id = $order->id;
			$paypaldetails->amount = $order->total_cost;
			$paypaldetails->result_json = strval($result);
			$paypaldetails->payment_id = $paymentId;
			$paypaldetails->payer_id = $payerId;
			$paypaldetails->sale_id = $saleId;
			$paypaldetails->added_at = time();

			$paypaldetails->save();

			$transaction->commit();
		}
		catch (Exception $e)
		{
			$transaction->rollback();
			Yii::$app->session->setFlash('error', Yii::t('app', 'Your payment is successful but we encountered some issue with order - Please contact customer support for further assistance - Error message:').$e->getMessage());
		}
		
		Yii::$app->session->setFlash('success', Yii::t('app', 'Payment successfully completed!'));

		SendEmail::sendOrderInProcessEmail($_REQUEST['order_id']);

		// Now send link to download digital products
		$this->CheckAndSendDigitalLinks($_REQUEST['order_id']);

		return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
    }

	public function actionCreateBitcoinPayment()
	{
		$connection = Yii::$app->db;

		if(isset($_SESSION['CONVERTED_CURRENCY_CODE']))
		{
			$currency_code = $_SESSION['CONVERTED_CURRENCY_CODE'];
		}
		else
		{
			$currency_code = Yii::$app->params['SYSTEM_CURRENCY'];
		}

		try
		{
			$transaction = $connection->beginTransaction();

			$order_id = $_REQUEST['order_id'];
			$order_cost = $_REQUEST['order_cost'];

			$privateKey = new \Bitpay\PrivateKey();
			$privateKey->setHex(MulteModel::multecrypt(Yii::$app->params['BITPAY_PRIVATE_KEY'], 'd'));

			$publicKey = new \Bitpay\PublicKey;
			$publicKey->setPrivateKey($privateKey);
			$publicKey->generate();

			$sin = \Bitpay\SinKey::create()->setPublicKey($publicKey)->generate();

			$client = new \Bitpay\Client\Client();
			
			if(Yii::$app->params['BITPAY_DEMO_MODE'] == 'Yes')
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

			$token = new \Bitpay\Token();
			$token->setToken(MulteModel::multecrypt(Yii::$app->params['BITPAY_TOKEN'], 'd'));

			$client->setToken($token);

			$invoice = new \Bitpay\Invoice();

			$item = new \Bitpay\Item();
			$item->setCode($order->id);
			$item->setDescription(Yii::t('app', 'Payment for Order').'#'.$order_id);
			$item->setPrice($order_cost);

			$invoice->setItem($item);
			$invoice->setRedirectUrl(Url::toRoute(['/order/default/execute-bitcoin-payment', 'order_id' => $_REQUEST['order_id']], true));
			//$invoice->setNotificationUrl("http://localhost/multecart/multeback/web/bitcoin4.php");

			$invoice->setCurrency(new \Bitpay\Currency($currency_code));

			$client->createInvoice($invoice);

			$bitcoin_detail = BitcoinDetails::find()->where("order_id=".$order_id)->one();

			if(!$bitcoin_detail)
			{
				$bitcoin_detail = new BitcoinDetails;
			}

			$bitcoin_detail->order_id = $order_id;
			$bitcoin_detail->amount = MulteModel::getConvertedCost($order_cost);
			$bitcoin_detail->status = 'New';
			$bitcoin_detail->invoice_id = $invoice->getId();
			$bitcoin_detail->added_at = time();
			$bitcoin_detail->save();

			$transaction->commit();

			return $this->redirect($invoice->getUrl());
		}
		catch (Exception $e) 
		{
			$transaction->rollback();
			Yii::$app->session->setFlash('error', Yii::t('app', 'Transaction failed with error: ').$e->getMessage());
			return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
		}
	}

	public function actionExecuteBitcoinPayment()
	{
		$connection = Yii::$app->db;
		$paid = false;

		try
		{
			$transaction = $connection->beginTransaction();

			$order_id = $_REQUEST['order_id'];

			$bitcoin_detail = BitcoinDetails::find()->where("order_id=".$order_id)->one();

			if($bitcoin_detail->status == 'PAID')
			{
				throw new Exception(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
			}

			$client = new \Bitpay\Client\Client();

			if(Yii::$app->params['BITPAY_DEMO_MODE'] == 'Yes')
			{
				$network = new \Bitpay\Network\Testnet();
			}
			else
			{
				$network = new \Bitpay\Network\Livenet();
			}

			$adapter = new \Bitpay\Client\Adapter\CurlAdapter();

			$client->setNetwork($network);
			$client->setAdapter($adapter);

			$invoice = $client->getInvoice ($bitcoin_detail->invoice_id);

			$status = $invoice->getStatus();

			if($status == 'paid' || $status == 'confirmed' || $status == 'complete')
			{
				$paid = true;
				$bitcoin_detail->status = 'PAID';
				$bitcoin_detail->update();

				$order = Order::findOne($_REQUEST['order_id']);
				$order->order_status = OrderStatus::_CONFIRMED;
				$order->update();

				SubOrder::updateAll(['sub_order_status' => OrderStatus::_CONFIRMED], ['and', ['=', 'order_id', $order->id], ['=', 'sub_order_status', OrderStatus::_NEW]]);
			}

			$transaction->commit();
		}
		catch (Exception $e) 
		{
			$transaction->rollback();
			Yii::$app->session->setFlash('error', Yii::t('app', 'Transaction failed with error: ').$e->getMessage());
			return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
		}
		
		if ($paid)
		{
			Yii::$app->session->setFlash('success', Yii::t('app', 'Payment successfully completed!'));

			SendEmail::sendOrderInProcessEmail($_REQUEST['order_id']);

			// Now send link to download digital products
			$this->CheckAndSendDigitalLinks($_REQUEST['order_id']);
		}
		else
		{
			Yii::$app->session->setFlash('info', Yii::t('app', 'Your Payment has not yet completed!'));
		}
		
		return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
	}

	public function actionCreatePaypalPayment()
    {
		if(isset($_SESSION['CONVERTED_CURRENCY_CODE']))
		{
			$currency_code = $_SESSION['CONVERTED_CURRENCY_CODE'];
		}
		else
		{
			$currency_code = Yii::$app->params['SYSTEM_CURRENCY'];
		}

		$apiContext = new \PayPal\Rest\ApiContext(
												  new \PayPal\Auth\OAuthTokenCredential(
													Yii::$app->params['PAYPAL_API_ID'],
													MulteModel::multecrypt(Yii::$app->params['PAYPAL_SECRET_ID'], 'd')
												  )
												);

		if(Yii::$app->params['IS_DEMO'] == 'No')
		{
			$apiContext->setConfig(['mode' => 'live']);
		}

		$order_id = $_REQUEST['order_id'];

		$order = Order::findOne($order_id);

		$zero_decimal_currencies = Yii::$app->params['zero_decimal_currencies'];

		/*if(in_array(Yii::$app->params['SYSTEM_CURRENCY'], $zero_decimal_currencies))
		{
			$order_cost = round($order->total_cost);
		}
		else
		{
			$order_cost = round($order->total_cost, 2);
		}*/

		try 
		{
			$inputFields = (new \PayPal\Api\InputFields())
						->setAllowNote(true)
						->setNoShipping(1) // Important step
						->setAddressOverride(0);

			$webProfile = (new \PayPal\Api\WebProfile())
						->setName(uniqid())
						->setInputFields($inputFields)
						->setTemporary(true);

			$createProfile = $webProfile->create($apiContext);

			// Create new payer and method
			$payer = new Payer();
			$payer->setPaymentMethod("paypal");

			// Set redirect urls
			$redirectUrls = new RedirectUrls();
			$redirectUrls->setReturnUrl(Url::toRoute(['/order/default/execute-paypal-payment', 'order_id' => $_REQUEST['order_id']], true))
						->setCancelUrl(Url::toRoute(['/order/default/information', 'order_id' => $_REQUEST['order_id']], true));
			
			$suborders = SubOrder::find()->where("order_id=".$order->id." and sub_order_status = '".OrderStatus::_NEW."'")->all();
			$order_cost = 0;
			foreach ($suborders as $row)
			{
				if(in_array($currency_code, $zero_decimal_currencies))
				{
					$order_cost += round(MulteModel::getConvertedCost($row->total_cost));
				}
				else
				{
					$order_cost += round(MulteModel::getConvertedCost($row->total_cost), 2);
				}
			}

			/*

			$arrayitems = [];
			$order_cost = 0;
			foreach ($suborders as $row)
			{
				$item = new Item();

				if(in_array(Yii::$app->params['SYSTEM_CURRENCY'], $zero_decimal_currencies))
				{
					$total_cost = round($row->total_cost);
					$order_cost += round($row->total_cost);
				}
				else
				{
					$total_cost = round($row->total_cost, 2);
					$order_cost += round($row->total_cost, 2);
				}

				$item->setName(Inventory::findOne($row->inventory_id)->product_name)
					->setCurrency(Yii::$app->params['SYSTEM_CURRENCY'])
					->setSku($row->id)
					->setQuantity($row->total_items)
					->setPrice($total_cost/$row->total_items);
					//->setPrice($total_cost);

				array_push($arrayitems, $item);
			}*/

			// Set payment amount
			$amount = new Amount();
			$amount->setCurrency($currency_code)
				->setTotal($order_cost);

//var_dump($arrayitems);exit;
			$item = new Item();
			$item->setName(Yii::$app->params['COMPANY_NAME'].' Order#'.$order->id)
				->setCurrency($currency_code)
				->setQuantity(1)
				->setPrice($order_cost);

			$itemList = new ItemList();
			$itemList->setItems(array($item));
			//$itemList->setItems($arrayitems);

			// Set transaction object
			$transaction = new Transaction();
			$transaction->setAmount($amount)
				->setItemList($itemList)
					->setDescription(Yii::t('app', 'Payment for Order').'#'.$order_id);

			// Create the full payment object
			$payment = new Payment();
			$payment->setIntent('sale')
					->setPayer($payer)
					->setRedirectUrls($redirectUrls)
					->setExperienceProfileId($createProfile->getId())
					->setTransactions(array($transaction));
		
			$payment->create($apiContext);

			// Get PayPal redirect URL and redirect user
			$approvalUrl = $payment->getApprovalLink();
			return $this->redirect($approvalUrl);
		} 
		catch (Exception $e) 
		{
			Yii::$app->session->setFlash('error', Yii::t('app', 'Transaction failed with error: ').$e->getMessage());
			return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
		}
    }

	public function actionCreateRazorpayPayment()
	{
		$connection = Yii::$app->db;

		/* Verify Payment if made */
		if (empty($_POST['razorpay_payment_id']) === false)
		{
			try
			{
				$transaction = $connection->beginTransaction();

				$success = true;
				$error = Yii::t('app', 'Payment Failed');

				$api = new Api(Yii::$app->params['RAZORPAY_API_KEY'], MulteModel::multecrypt(Yii::$app->params['RAZORPAY_SECRET_KEY'], 'd'));
				$razorpay_details = RazorpayDetails::findOne(['order_id' => $_REQUEST['order_id'], 'payment_confirmed' => 0]);

				try
				{
					$attributes = array(
						'razorpay_order_id' => $razorpay_details->razorpay_order_id,
						'razorpay_payment_id' => $_POST['razorpay_payment_id'],
						'razorpay_signature' => $_POST['razorpay_signature']
					);

					$api->utility->verifyPaymentSignature($attributes);
				}
				catch(SignatureVerificationError $e)
				{
					$success = false;
					$error = Yii::t('app', 'Razorpay Error : ') . $e->getMessage();
				}

				if ($success === true)
				{
					$razorpay_details->payment_confirmed = 1;
					$razorpay_details->razorpay_payment_id = $_POST['razorpay_payment_id'];
					$razorpay_details->razorpay_signature = $_POST['razorpay_signature'];
					if(!$razorpay_details->save())
					{
						throw new \Exception(Yii::t('app', 'Failed to save transaction details!'));
					}

					$order = Order::findOne($_REQUEST['order_id']);
					$order->order_status = OrderStatus::_CONFIRMED;
					if(!$order->update())
					{
						throw new \Exception(Yii::t('app', 'Failed to update order status!'));
					}

					SubOrder::updateAll(['sub_order_status' => OrderStatus::_CONFIRMED], ['and', ['=', 'order_id', $order->id], ['=', 'sub_order_status', OrderStatus::_NEW]]);

					Yii::$app->session->setFlash('success', Yii::t('app', 'Payment successfully completed!'));

					SendEmail::sendOrderInProcessEmail($_REQUEST['order_id']);

					// Now send link to download digital products
					$this->CheckAndSendDigitalLinks($_REQUEST['order_id']);

					$transaction->commit();

					return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
				}
				else
				{
					throw new \Exception(Yii::t('app', 'Failed to verify transaction details!'));
				}
			}
			catch (\Exception $e)
			{
				Yii::$app->session->setFlash('error', Yii::t('app', 'Transaction failed with error: ').$e->getMessage());
				return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
			}
		}
		/* Payment Verification Ends */

		if(Yii::$app->params['SYSTEM_CURRENCY'] !== 'INR' || (isset($_SESSION['CONVERTED_CURRENCY_CODE']) && $_SESSION['CONVERTED_CURRENCY_CODE'] !== 'INR'))
		{
			throw new NotFoundHttpException(Yii::t('app', 'Razorpay method is not supported for the currently set Currency Code!'));
		}

		$zero_decimal_currencies = Yii::$app->params['zero_decimal_currencies'];

		if(isset($_SESSION['CONVERTED_CURRENCY_CODE']))
		{
			$display_currency_code = $_SESSION['CONVERTED_CURRENCY_CODE'];
		}
		else
		{
			$display_currency_code = Yii::$app->params['SYSTEM_CURRENCY'];
		}

		if(in_array($currency_code, $zero_decimal_currencies))
		{
			$display_amount = round(MulteModel::getConvertedCost($_REQUEST['order_cost']/100));
		}
		else
		{
			$display_amount =  round(MulteModel::getConvertedCost($_REQUEST['order_cost']/100), 2);
		}

		try
		{
			$transaction = $connection->beginTransaction();

			$api = new Api(Yii::$app->params['RAZORPAY_API_KEY'], MulteModel::multecrypt(Yii::$app->params['RAZORPAY_SECRET_KEY'], 'd'));

			$orderData = [
							'receipt'         => $_REQUEST['order_id'],
							'amount'          => $_REQUEST['order_cost'],
							'currency'        => Yii::$app->params['SYSTEM_CURRENCY'], // Must be INR
							'payment_capture' => 1 // auto capture
						];

			$razorpayOrder = $api->order->create($orderData);

			//echo "<pre>";
			//print_r($razorpayOrder);
			//exit;

			$data = [
						"key"               => Yii::$app->params['RAZORPAY_API_KEY'],
						"amount"            => $_REQUEST['order_cost'],
						"name"              => Yii::$app->params['company']['company_name'],
						"description"       => Yii::t('app', 'Payment for Order').'#'.$_REQUEST['order_id'],
						"image"             => Url::base(true).'/image/logo.png',
						"prefill"           => [
												"name"              => Yii::$app->user->identity->first_name." ".Yii::$app->user->identity->last_name,
												"email"             => Yii::$app->user->identity->email,
												"contact"           => Contact::findOne(['entity_type' => 'customer', 'entity_id' => Yii::$app->user->identity->id])->mobile,
												],
						"notes"             => [
												"address"           => Yii::$app->params['company']['company_name'],
												"merchant_order_id" => $_REQUEST['order_id'],
												],
						"theme"             => [
												"color"             => "#F37254"
												],
						"order_id"          => $razorpayOrder['id'],
					];

			if ($display_currency_code !== 'INR')
			{
				$data['display_currency']  = $display_currency_code;
				$data['display_amount']    = $display_amount;
			}

			$json = Json::encode($data);
			
			$razorpay_details = RazorpayDetails::findOne(['order_id' => $_REQUEST['order_id'], 'payment_confirmed' => 0]);
			if($razorpay_details)
			{
				$razorpay_details->delete();
			}
			$razorpay_details = new RazorpayDetails();

			$razorpay_details->order_id = $_REQUEST['order_id'];
			$razorpay_details->json_data = $json;
			$razorpay_details->razorpay_order_id = $razorpayOrder['id'];
			$razorpay_details->amount = $_REQUEST['order_cost']/100;
			$razorpay_details->payment_confirmed = 0;

			if(!$razorpay_details->save())
			{
				throw new \Exception(Yii::t('app', 'Failed to save transaction details!'));
			}
			
			$transaction->commit();

			return $this->render('razorpay', ['json' => $json, 'order_id' => $_REQUEST['order_id']]);
		}
		catch (Exception $e) 
		{
			Yii::$app->session->setFlash('error', Yii::t('app', 'Transaction failed with error: ').$e->getMessage());
			return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
		}
	}

	public function actionPayment()
    {
		if(isset($_SESSION['CONVERTED_CURRENCY_CODE']))
		{
			$currency_code = $_SESSION['CONVERTED_CURRENCY_CODE'];
		}
		else
		{
			$currency_code = Yii::$app->params['SYSTEM_CURRENCY'];
		}

		$order_id = $_REQUEST['order_id'];

		$order = Order::findOne($order_id);
		$suborders = SubOrder::find()->where("order_id=".$order->id)->all();

		$zero_decimal_currencies = Yii::$app->params['zero_decimal_currencies'];
		
		if($order->order_status != OrderStatus::_NEW)
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}

		if(isset($_REQUEST['stripesubmit']))
		{
			$connection = Yii::$app->db;
			try
			{
				$transaction = $connection->beginTransaction();

				\Stripe\Stripe::setApiKey(MulteModel::multecrypt(Yii::$app->params['STRIPE_SECRET_KEY'], 'd'));
				
				$order_stripe_cost = 0;

				foreach ($suborders as $row)
				{
					if(in_array($currency_code, $zero_decimal_currencies))
					{
						$order_stripe_cost += round(MulteModel::getConvertedCost($row->total_cost));
					}
					else
					{
						$order_stripe_cost += round(MulteModel::getConvertedCost($row->total_cost)*100);
					}
				}

				$token  = $_POST['stripeToken'];
				$email  = $_POST['stripeEmail'];

				$customer = \Stripe\Customer::create(array(
					  'email' => $email,
					  'source'  => $token
				  ));

				$charge = \Stripe\Charge::create(array(
					  'customer' => $customer->id,
					  'amount'   => $order_stripe_cost,
					  'currency' => $currency_code
				  ));

				$order = Order::findOne($_POST['order_id']);
				$order->order_status = OrderStatus::_CONFIRMED;
				$order->save();

				SubOrder::updateAll(['sub_order_status' => OrderStatus::_CONFIRMED], ['and', ['=', 'order_id', $order->id], ['=', 'sub_order_status', OrderStatus::_NEW]]);

				$stripedetails = new StripeDetails;

				$stripedetails->order_id = $order->id;
				$stripedetails->amount = MulteModel::getConvertedCost($order->total_cost);
				$stripedetails->charge_id = $charge->id;
				$stripedetails->json_response = strval($charge->__toJSON());
				$stripedetails->added_at = time();
				$stripedetails->save();

				$transaction->commit();
				
				Yii::$app->session->setFlash('success', Yii::t('app', 'Payment successfully completed!'));

				SendEmail::sendOrderInProcessEmail($order_id);

				// Now send link to download digital products
				$this->CheckAndSendDigitalLinks($_REQUEST['order_id']);

				return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);

			}
			catch (Exception $e)
			{
				Yii::$app->session->setFlash('error', Yii::t('app', 'Transaction failed with error: ').$e->getMessage());
				$transaction->rollback();
				return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
			}

		}
		
		$payment_method = PaymentMethods::find()->where("method='".$order->payment_method."'")->one()->method;
		$order_cost = 0;

		if(in_array($currency_code, $zero_decimal_currencies))
		{
			$order_cost = round($order->total_cost);
		}
		else
		{
			if($payment_method == PaymentMethods::_STRIPE)
			{
				$order_cost = round(MulteModel::getConvertedCost($order->total_cost)*100);
			}
			else
			{
				$order_cost = round(MulteModel::getConvertedCost($order->total_cost), 2);
			}
		}

		if ($payment_method == PaymentMethods::_COD)
		{
			/*$order->order_status = OrderStatus::_CONFIRMED;
			$order->save();*/

			return $this->redirect(['/order/default/information', 'order_id' => $order->id]);
		}
		else
		if ($payment_method == PaymentMethods::_PAYPAL)
		{
			return $this->redirect(['create-paypal-payment', 'order_id' => $order->id]);
		}
		else
		if ($payment_method == PaymentMethods::_STRIPE)
		{			
			return $this->render('stripe', ['order' => $order, 'order_stripe_cost' => $order_cost]);
		}
		else
		if ($payment_method == PaymentMethods::_BITCOIN)
		{
			return $this->redirect(['create-bitcoin-payment', 'order_id' => $order->id, 'order_cost' => $order_cost]);
		}
		else
		if ($payment_method == PaymentMethods::_RAZORPAY)
		{
			$order_cost = round($order->total_cost*100); //Assuming RazorPay is called only for Base currency as INR
			return $this->redirect(['create-razorpay-payment', 'order_id' => $order->id, 'order_cost' => $order_cost]);
		}
    }

	public function actionHistory()
    {
		$order_list = Order::find()->where("customer_id='".Yii::$app->user->identity->entity_id."'")->orderBy('id desc')->all();
        return $this->render('history', ['order_list' => $order_list]);
    }

	public function actionInformation()
    {
		$order = Order::findOne($_REQUEST['order_id']);

		if($order->customer_id != Yii::$app->user->identity->entity_id)
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}
		
		if(isset($_REQUEST['cancel_id']))
		{
			// Cancel suborder
			$connection = Yii::$app->db;

			try
			{
				$transaction = $connection->beginTransaction(); 

				if ($order->payment_method == PaymentMethods::_BITCOIN)
				{
					// Cancelation not allowed for BitCoin Payments
					throw new Exception(Yii::t('app', 'Order cannot be canceled!'));
				}
				
				$sub_order = SubOrder::findOne($_REQUEST['cancel_id']);
				$cancel_status = OrderStatus::_CANCELED;  // Canceled
				
				$sub_order_old_status = $sub_order->sub_order_status;

				if($sub_order->sub_order_status == $cancel_status)
				{
					throw new Exception(Yii::t('app', 'Order already canceled!'));
				}

				if($sub_order->sub_order_status == OrderStatus::_RETURNED || $sub_order->sub_order_status == OrderStatus::_RETURN_REQUESTED || $sub_order->sub_order_status == OrderStatus::_COMPLETED || $sub_order->sub_order_status == OrderStatus::_REFUNDED || $sub_order->sub_order_status == OrderStatus::_READY_TO_SHIP || $sub_order->sub_order_status == OrderStatus::_SHIPPED || $sub_order->sub_order_status == OrderStatus::_DELIVERED)
				{
					throw new Exception(Yii::t('app', 'Order cannot be canceled!'));
				}

				$order->total_site_discount -= $sub_order->total_site_discount;
				$order->total_coupon_discount -= $sub_order->total_coupon_discount;
				$order->total_cost -= $sub_order->total_cost;

				$sub_order->sub_order_status = $cancel_status; 
				/*$sub_order->total_site_discount = 0;
				$sub_order->total_coupon_discount = 0;
				$sub_order->total_cost = 0;
				$sub_order->total_tax = 0;
				$sub_order->total_shipping = 0;*/

				$sub_order->save();

				if(!SubOrder::find()->where("order_id='".$_REQUEST['order_id']."' and sub_order_status != '".$cancel_status."'")->exists())
				{
					$order->order_status = $cancel_status; 
				}

				$order->save();

				// Release inventory stock
				MulteModel::releaseInventoryStock($sub_order);

				// Release Coupon Discount amount
				MulteModel::releaseCouponDiscount($sub_order);

				// Release Global Discount Amouont
				MulteModel::releaseGlobalDiscount($sub_order);

				$transaction->commit();

				Yii::$app->session->setFlash('success', Yii::t('app', 'Order Successfully Canceled!'));

				if(($sub_order->payment_method == PaymentMethods::_PAYPAL) && ($sub_order_old_status == OrderStatus::_CONFIRMED))
				{
					// Auto refund as order was not in process yet
					return $this->redirect(['/order/default/paypal-refund', 'order_id' => $order->id, 'sub_order_id' => $sub_order->id]);
				}
				else
				if(($sub_order->payment_method == PaymentMethods::_STRIPE) && ($sub_order_old_status == OrderStatus::_CONFIRMED))
				{
					// Auto refund as order was not in process yet
					return $this->redirect(['/order/default/stripe-refund', 'order_id' => $order->id, 'sub_order_id' => $sub_order->id]);
				}
				else
				if(($sub_order->payment_method == PaymentMethods::_RAZORPAY) && ($sub_order_old_status == OrderStatus::_CONFIRMED))
				{
					// Auto refund as order was not in process yet
					return $this->redirect(['/order/default/razorpay-refund', 'order_id' => $order->id, 'sub_order_id' => $sub_order->id]);
				}
			}
			catch (Exception $e)
			{
				Yii::$app->session->setFlash('error', $e->getMessage());
				$transaction->rollback();
				return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
			}
		}
		else
		if(isset($_REQUEST['return_id']))
		{
			// Return suborder
			$connection = Yii::$app->db;

			try
			{
				$transaction = $connection->beginTransaction(); 

				if ($order->payment_method == PaymentMethods::_BITCOIN)
				{
					// Returns not allowed for BitCoin Payments
					throw new Exception(Yii::t('app', 'Order cannot be returned!'));
				}

				$sub_order = SubOrder::findOne($_REQUEST['return_id']);
				$return_status = OrderStatus::_RETURNED;  // Returned
				$return_request = OrderStatus::_RETURN_REQUESTED;

				if($sub_order->sub_order_status == $return_status)
				{
					throw new Exception(Yii::t('app', 'Order already returned!'));
				}

				if(($sub_order->sub_order_status == OrderStatus::_CANCELED || $sub_order->sub_order_status == OrderStatus::_COMPLETED) || ($sub_order->sub_order_status != OrderStatus::_SHIPPED && $sub_order->sub_order_status != OrderStatus::_DELIVERED && $sub_order->sub_order_status != OrderStatus::_IN_PROCESS && $sub_order->sub_order_status != OrderStatus::_READY_TO_SHIP))
				{
					throw new Exception(Yii::t('app', 'Order cannot be returned!'));
				}

				$return_window = ProductSubSubCategory::findOne(Inventory::findOne($sub_order->inventory_id)->product->sub_subcategory_id)->return_window;
				
				$max_duration = $return_window*24*60*60 + $sub_order->updated_at;

				if ($max_duration < time())
				{
					throw new Exception(Yii::t('app', 'Order cannot be returned - Return Window Expired!'));
				}
				
				if ($sub_order->sub_order_status == OrderStatus::_IN_PROCESS || $sub_order->sub_order_status == OrderStatus::_SHIPPED || $sub_order->sub_order_status == OrderStatus::_DELIVERED || $sub_order->sub_order_status == OrderStatus::_READY_TO_SHIP)
				{
					$sub_order->sub_order_status = $return_request;
					Yii::$app->session->setFlash('success', Yii::t('app', 'Order Return Request Placed!'));
				}
				else
				{
					$sub_order->sub_order_status = $return_status;
					Yii::$app->session->setFlash('success', Yii::t('app', 'Order Successfully Returned!'));
				}

				$sub_order->save();

				if(!SubOrder::find()->where("order_id='".$_REQUEST['order_id']."' and sub_order_status != '".$return_status."'")->exists())
				{
					$order->order_status = $return_status; 
				}

				$order->total_site_discount -= $sub_order->total_site_discount;
				$order->total_coupon_discount -= $sub_order->total_coupon_discount;
				$order->total_cost -= $sub_order->total_cost;

				$order->save();

				// Release inventory stock
				$inventory = Inventory::findOne($sub_order->inventory_id);
				$inventory->stock++;
				$inventory->total_sale--;
				$inventory->save();

				// Release Coupon Discount amount and Global Discount Amouont
				if($sub_order->total_coupon_discount > 0)
				{
					$discount_coupon_row = DiscountCoupons::findOne($sub_order->discount_coupon_id);
					$discount_coupon_row->used_budget -= $sub_order->total_coupon_discount;
					$discount_coupon_row->save();
				}
				
				if($sub_order->total_site_discount > 0)
				{
					$global_discount_row = GlobalDiscount::findOne($sub_order->global_discount_id);
					$global_discount_row->used_budget -= $sub_order->total_site_discount;
					$global_discount_row->save();
				}

				$transaction->commit();
			}
			catch (Exception $e)
			{
				Yii::$app->session->setFlash('error', $e->getMessage());
				$transaction->rollback();
				return $this->redirect(['/order/default/information', 'order_id' => $_REQUEST['order_id']]);
			}
		}

        return $this->render('information', ['order' => $order]);
    }

	public function actionCart()
    {
		if(Yii::$app->user->isGuest)
		{
			$cart = Cart::find()->where("session_id='".session_id()."'")->all();
		}
		else
		{
			$cart = Cart::find()->where("user_id=".Yii::$app->user->identity->id)->all();
		}

        return $this->render('cart', ['cart_items' => $cart]);
    }

	public function actionCheckout()
    {
		if(isset($_SESSION['CONVERTED_CURRENCY_CODE']))
		{
			$currency_code = $_SESSION['CONVERTED_CURRENCY_CODE'];
			$currency_symbol = $_SESSION['CONVERTED_CURRENCY_SYMBOL'];
			$cur_cnv = CurrencyConversion::findOne(['from' => Yii::$app->params['SYSTEM_CURRENCY'], 'to' => $currency_code]);

			if($cur_cnv)
				$conversion_rate = $cur_cnv->conversion_rate;
			else
				$conversion_rate = 1;
		}
		else
		{
			$currency_code = Yii::$app->params['SYSTEM_CURRENCY'];
			$currency_symbol = Yii::$app->params['SYSTEM_CURRENCY_SYMBOL'];
			$conversion_rate = 1;
		}
		//print_r($this->renderPartial('order-for-email'));exit;
		$connection = Yii::$app->db;

		if(isset($_REQUEST['checkoutsubmit'])) // Confirm order button is pressed
		{
			//var_dump($_REQUEST);exit;
			if($_REQUEST['account'] == 'guest')
			{
				if(User::find()->where("username='".$_REQUEST['email']."'")->exists())
				{
					Yii::$app->session->setFlash('error', Yii::t('app', 'Email ID already in use - Please login with existing Email or choose another!'));
					return $this->redirect(['/order/default/checkout']);
				}
				
				$customer = new Customer;
				$contact = new Contact;
				$address = new Address;
				$user = new User;

				try
				{				
					// Open transaction
					$transaction = $connection->beginTransaction(); 

					// Create entry in tbl_customer for guest
					$customer->customer_name = $_REQUEST['email'];
					$customer->customer_type_id = 2; // Regular Customer
					$customer->added_by_id = 0; //System
					$customer->active = 1;
					$customer->added_at = time();

					if(!$customer->save())
					{
						throw new \Exception(Json::encode($customer->getErrors()));
					}

					// Create entry in tbl_address for guest
					$address->address_1 = $_REQUEST['address_1'];
					$address->address_2 = $_REQUEST['address_2'];
					$address->country_id = $_REQUEST['country_id'];
					$address->state_id = $_REQUEST['state_id'];
					//$address->city_id = $_REQUEST['city_id'];
					$address->city_id = AddressModel::getCityId($_REQUEST['country_id'], $_REQUEST['state_id'], $_REQUEST['city_id']);
					$address->zipcode = $_REQUEST['zipcode'];
					$address->entity_id = $customer->id;
					$address->entity_type = 'customer';
					$address->is_primary = 1;
					$address->added_at = time();

					if(!$address->save())
					{
						throw new \Exception(Json::encode($address->getErrors()));
					}

					// Create entry in tbl_contact for guest
					$contact->first_name = $_REQUEST['first_name'];
					$contact->last_name = $_REQUEST['last_name'];
					$contact->email = $_REQUEST['email'];
					$contact->mobile = $_REQUEST['mobile'];
					$contact->address_id = $address->id;
					$contact->entity_id = $customer->id;
					$contact->entity_type = 'customer';
					$contact->is_primary = 1;
					$contact->added_at = time();

					if(!$contact->save())
					{
						throw new \Exception(Json::encode($contact->getErrors()));
					}

					// Create entry in tbl_user for guest and send email
					$user->username = $contact->email;
					$user->email = $contact->email;
					$user->first_name = $contact->first_name;
					$user->last_name = $contact->last_name;
					$user->user_type_id = UserTypeSearch::getCompanyUserType('Customer')->id;
					$user->active = 1;
					$user->entity_id = $customer->id;
					$user->entity_type = 'customer';
					$user->added_at = time();

					MulteModel::createUserWithRolesAndSendMail($user, 'Customer');

					// Commit Transaction
					$transaction->commit();
				}
				catch (Exception $e)
				{
					Yii::$app->session->setFlash('error', $e->getMessage());
					$transaction->rollback();
					return $this->redirect(['/order/default/checkout']);
				}
			}
			
			$order = new Order;
			$discount_row = DiscountCoupons::find()->where("coupon_code='".$_REQUEST['coupon_code']."'")->one();
			$cart_items = Cart::find()->where("user_id=".Yii::$app->user->identity->id)->all();
			$added_by_user = User::findOne($discount_row->added_by_id);
			$newOrderStatus = OrderStatus::_NEW;

			try
			{
				// Create Order
				// First block the inventory items if available
				// Then update global discount if used
				// Then update discount coupons if used

				// Create suborder(s) for vendor(s)

				// Proceed to payment using selected method
				
				// Update main order and sub-order status according to payment status

				// Open transaction
				$transaction = $connection->beginTransaction();

				if(!$cart_items)
				{
					throw new Exception(Yii::t('app', 'Your cart is empty!'));
				}

				$address_data = $_REQUEST['account']=='returning'?Address::findOne($_REQUEST['shippingaddress']):Address::findOne($address->id);
				$contact_data = $_REQUEST['account']=='returning'?Contact::find()->where("entity_type='customer' and entity_id='".Yii::$app->user->identity->entity_id."' and address_id='".$address_data->id."'")->one():Contact::findOne($contact->id);
				
				// Begin creating order
				$order->customer_id = Yii::$app->user->identity->entity_id;
				$order->cart_snapshot = $cart_items?Json::encode($cart_items):'';
				$order->discount_coupon_snapshot = $discount_row?Json::encode($discount_row):'';
				$glb_dsc = GlobalDiscount::find()->All();
				$order->global_discount_snapshot = $glb_dsc?Json::encode($glb_dsc):'';
				$order->total_cost = $_REQUEST['total_cost'] - $_REQUEST['coupon_discount'];
				$order->total_site_discount = $_REQUEST['special_discount'];
				$order->total_coupon_discount = isset($_REQUEST['coupon_discount'])?$_REQUEST['coupon_discount']:0;
				$order->discount_coupon_type = $added_by_user?$added_by_user->entity_type=='vendor'?'V':'S':'';
				$order->address_snapshot = $address_data?Json::encode($address_data):'';
				//$order->contact_snapshot = Json::encode($_REQUEST['account']=='returning'?Contact::findOne($_REQUEST['contact_id']):Contact::findOne($contact->id));
				$order->contact_snapshot = $contact_data?Json::encode($contact_data):'';
				$order->delivery_method = $_REQUEST['shippingtype'];
				$order->payment_method = $_REQUEST['paymentmethod'];
				$order->order_status = $newOrderStatus;
				
				$order->order_currency_code = $currency_code;
				$order->order_currency_symbol = $currency_symbol;
				$order->total_converted_cost = MulteModel::getConvertedCost($order->total_cost);
				$order->conversion_rate = $conversion_rate;

				$order->added_at = time();

				$order->save();
				//print_r($order->getErrors());exit;

				// Begin creating sub-order(s), Update Global Discount
				foreach ($cart_items as $cart)
				{
					$suborder = new SubOrder;
					$inventory_item = Inventory::findOne($cart->inventory_id);
					$product_sub_subcategory = ProductSubSubCategory::findOne($cart->product->sub_subcategory_id);
					$global_discount_row = GlobalDiscount::findOne($cart->global_discount_id);
					$total_cost = MulteModel::getInventoryTotalAmount($inventory_item, $cart->total_items)*$cart->total_items;

					$suborder->order_id = $order->id;
					$suborder->vendor_id = $cart->inventory->vendor_id;
					$suborder->inventory_id = $cart->inventory_id;
					$suborder->total_items = $cart->total_items;
					$suborder->discount_coupon_id = $cart->discount_coupon_id;
					$suborder->global_discount_id = $cart->global_discount_id;
					$suborder->tax_id = $product_sub_subcategory->tax_id;
					$suborder->inventory_snapshot = $inventory_item?Json::encode($inventory_item):'';
					$suborder->discount_coupon_snapshot = $discount_row?Json::encode($discount_row):'';
					$suborder->global_discount_snapshot = $global_discount_row?Json::encode($global_discount_row):'';
					$suborder->total_cost = $total_cost - $cart->global_discount_temp - $cart->coupon_discount_temp;
					$suborder->order_currency_code = $currency_code;
					$suborder->order_currency_symbol = $currency_symbol;
					$suborder->total_converted_cost = MulteModel::getConvertedCost($suborder->total_cost);
					$suborder->conversion_rate = $conversion_rate;
					if($product_sub_subcategory->tax_ind == 1)  // Tax applicable on this item
					{
						$tax_row = Tax::findOne($product_sub_subcategory->tax_id);

						/* Begin State Tax Changes */
						$state_tax_row = StateTax::findOne(['tax_id' => $product_sub_subcategory->tax_id, 'state_id' => $address_data->state_id]);

						if($state_tax_row)
						{
							$tax_percentage = $state_tax_row->tax_percentage;
						}
						else
						{
							$tax_percentage = $tax_row->tax_percentage;
						}
						
						//$before_tax_amount = $suborder->total_cost/(1+$tax_row->tax_percentage/100);
						$before_tax_amount = $suborder->total_cost/(1+$tax_percentage/100);
						/* End State Tax Changes */

						$tax_amount = $suborder->total_cost - $before_tax_amount;
					}
					else
					{
						$tax_row='';
						$tax_amount=0;
					}
					$suborder->tax_snapshot = $tax_row?Json::encode($tax_row):'';

					/* Begin State Tax Changes */
					if($state_tax_row)
						$suborder->state_tax_snapshot = $state_tax_row?Json::encode($state_tax_row):'';
					/* End State Tax Changes */

					$suborder->total_shipping = $inventory_item->shipping_cost*$cart->total_items;
					$suborder->total_site_discount = $cart->global_discount_temp;
					$suborder->total_coupon_discount = $cart->coupon_discount_temp;
					$suborder->discount_coupon_type = $cart->coupon_discount_temp>0?$added_by_user->entity_type=='vendor'?'V':'S':'';
					$suborder->total_tax = $tax_amount;
					$suborder->delivery_method = $_REQUEST['shippingtype'];
					$suborder->payment_method = $_REQUEST['paymentmethod'];
					$suborder->sub_order_status = $newOrderStatus;
					$suborder->is_processed = 0; //  Not processed yet for vendor commission
					$suborder->added_at = time();

					$suborder->save();
					
					/* Create Invoice */
					$invoice = new Invoice;
					$invoice->sub_order_id = $suborder->id;
					$invoice->added_at = time();
					$invoice->save();

					// Reduce the inventory item stock
					$inventory = Inventory::findOne($cart->inventory_id);
					$inventory->stock = $inventory->stock - 1;
					if ($inventory->stock >= 0)
					{
						$inventory->total_sale++;
						$inventory->save();
					}
					else
					{
						throw new Exception(Yii::t('app', 'Few of the items that you were trying to order went out of stock - Please checkout again!'));
					}
					
					// Updating Global Discount
					if($cart->global_discount_temp > 0)
					{
						if($global_discount_row->max_budget > 0)
						{
							$left_budget = $global_discount_row->max_budget - $global_discount_row->used_budget - $cart->global_discount_temp;

							if($left_budget >= 0)
							{
								$global_discount_row->used_budget += $cart->global_discount_temp;
								$global_discount_row->save();
							}
							else
							{
								throw new Exception(Yii::t('app', 'Something went wrong with special discount - Please checkout again!'));
							}
						}
						else
						{
							$global_discount_row->used_budget += $cart->global_discount_temp;
							$global_discount_row->save();
						}
					}
				}

				// Update Discount Coupons
				if($discount_row)
				{
					if($discount_row->max_budget > 0)
					{
						$left_budget = $discount_row->max_budget - $discount_row->used_budget - $_REQUEST['coupon_discount'];

						if($left_budget >= 0)
						{
							$discount_row->used_budget += $_REQUEST['coupon_discount'];
							$discount_row->used_count++;
							$discount_row->save();
						}
						else
						{
							throw new Exception(Yii::t('app', 'Something went wrong with coupon code - Please checkout again!'));
						}
					}
				}
				// Now prepare the order email content
				$email_text = $this->renderPartial('order-for-email');

				// Empty the cart now
				Cart::deleteAll(['user_id' => Yii::$app->user->identity->id]);

				// Commit Transaction
				$transaction->commit();

				// Now send order confirmation email to customer
				SendEmail::sendOrderConfirmationEmail($order->id, $email_text);

				// Send email notifications to vendor(s)
				$vendor_orders = SubOrder::find()->where("order_id=".$order->id)->all();
				foreach($vendor_orders as $vorder)
				{
					SendEmail::sendVendorOrderNotificationEmail($vorder);
				}
			}
			catch (Exception $e)
			{
				//print_r($e->getMessage());exit;
				Yii::$app->session->setFlash('error', $e->getMessage());
				$transaction->rollback();
				return $this->redirect(['/order/default/checkout']);
			}

			// Now proceed to payment with selected method
			/*try
			{
				$transaction = $connection->beginTransaction();

				$transaction->commit();
			}
			catch (Exception $e)
			{
				Yii::$app->session->setFlash('error', $e->getMessage());
				$transaction->rollback();
			}*/

			return $this->redirect(['/order/default/payment', 'order_id' => $order->id]);
		}

		if(Yii::$app->user->isGuest)
		{
			Cart::updateAll(['coupon_discount_temp' => 0, 'discount_coupon_id' => 0], ['=', 'session_id', session_id()]);
		}
		else
		{
			Cart::updateAll(['coupon_discount_temp' => 0, 'discount_coupon_id' => 0], ['=', 'user_id', Yii::$app->user->identity->id]);
		}

		if(Yii::$app->user->isGuest)
		{
			$cart = Cart::find()->where("session_id='".session_id()."'")->all();
		}
		else
		{
			$cart = Cart::find()->where("user_id=".Yii::$app->user->identity->id)->all();
		}

		if ($cart)
		{
			$global_discount = MulteModel::getGlobalDiscount($cart, 0);
			/* Refresh Cart Array */
			if(Yii::$app->user->isGuest)
			{
				$cart = Cart::find()->where("session_id='".session_id()."'")->all();
			}
			else
			{
				$cart = Cart::find()->where("user_id=".Yii::$app->user->identity->id)->all();
			}
	        return $this->render('checkout', ['cart_items' => $cart, 'global_discount' => $global_discount]);
		}
		else
			return $this->redirect(['/site/index']);
    }

	public function actionAjaxAddToCart()
	{
		if(Yii::$app->user->isGuest)
		{
			$cart = Cart::find()->where("session_id='".session_id()."'")->andWhere('inventory_id='.$_REQUEST['inventory_id'])->one();
		}
		else
		{
			$cart = Cart::find()->where("user_id=".Yii::$app->user->identity->id)->andWhere('inventory_id='.$_REQUEST['inventory_id'])->one();
		}

		if ($cart)
		{
			$inventory = Inventory::find()->where('id='.$_REQUEST['inventory_id'])->one();
			if($inventory)
			{
				if (($cart->total_items + intval($_REQUEST['total_items'])) > $inventory->stock)
				{
					$cart->total_items = $inventory->stock;
				}
				else
				{
					$cart->total_items += intval($_REQUEST['total_items']);
				}

				$cart->updated_at = time();
				$cart->save();
		
				//$inventory->stock -= intval($_REQUEST['total_items']);
				//$inventory->save();
			}
		}
		else
		{
			$inventory = Inventory::find()->where('id='.$_REQUEST['inventory_id'])->one();
			$cart = new Cart;
			$cart->inventory_id = $_REQUEST['inventory_id'];

			if (intval($_REQUEST['total_items']) > $inventory->stock)
			{
				$cart->total_items = $inventory->stock;
			}
			else
			{
				$cart->total_items = intval($_REQUEST['total_items']);
			}

			$cart->session_id = session_id();

			if(!Yii::$app->user->isGuest)
			{
				$cart->user_id = Yii::$app->user->identity->id;
			}

			if($inventory)
			{
				$cart->added_at = time();
				$cart->save();

				//$inventory->stock -= intval($_REQUEST['total_items']);
				//$inventory->save();
			}
		}

		if(Yii::$app->user->isGuest)
		{
		  $cart_items = Cart::find()->where("session_id='".session_id()."'")->all();
		}
		else
		{
		  $cart_items = Cart::find()->where("user_id=".Yii::$app->user->identity->id)->all();
		}

		$total_items = 0;
		foreach($cart_items as $cart)
		{
		  $total_items += $cart->total_items;
		}
		
		/* If wishlist item then remove item from wishlist */
		if($_REQUEST['wish'] == 'true')
		{
			Wishlist::findOne(['customer_id' => Yii::$app->user->identity->entity_id, 'inventory_id' => $_REQUEST['inventory_id']])->delete();
		}
		
		return $this->renderPartial('ajax-update-cart', [
															'total_items' => $total_items,
															'cart_items'=>$cart_items,
															'remaining_stock' => $inventory->stock,
														]);
	}

	public function actionAjaxUpdateCart()
	{
		if($_REQUEST['new_count'] == '0')
		{
			Cart::findOne($_REQUEST['cart_id'])->delete();
		}
		else
		{
			$cart = Cart::findOne($_REQUEST['cart_id']);
			$inventory = Inventory::findOne($cart->inventory_id);
			
			if ($_REQUEST['new_count'] > $inventory->stock)
			{
				$cart->total_items = $inventory->stock;
			}
			else
			{
				$cart->total_items = $_REQUEST['new_count'];
			}
			
			$cart->updated_at = time();
			$cart->save();
		}

		if(Yii::$app->user->isGuest)
		{
			$cart_items = Cart::find()->where("session_id='".session_id()."'")->all();
		}
		else
		{
			$cart_items = Cart::find()->where("user_id=".Yii::$app->user->identity->id)->all();
		}

		return $this->renderPartial('ajax-update-cartpage', [
															'cart_items'=>$cart_items,
														]);
	}

	public function actionAjaxGetInventoryStock()
	{
		return Inventory::findOne($_REQUEST['inventory_id'])->stock;
	}

	public function actionAjaxApplyDiscount()
	{
		$discount_coupon = $_REQUEST['discount_coupon'];

		if(Yii::$app->user->isGuest)
		{
			Cart::updateAll(['coupon_discount_temp' => 0, 'discount_coupon_id' => 0], ['=', 'session_id', session_id()]);
		}
		else
		{
			Cart::updateAll(['coupon_discount_temp' => 0, 'discount_coupon_id' => 0], ['=', 'user_id', Yii::$app->user->identity->id]);
		}

		$coupon_discount = MulteModel::getCouponDiscount($discount_coupon);

		if(is_string($coupon_discount))
		{
			// Return Error code
			return $coupon_discount;
		}

		return $this->renderPartial('ajax-update-checkout-page', 
										[
											'coupon_discount' => $coupon_discount,
											'discount_coupon' => $discount_coupon,
										]);
	}

	public function actionAjaxRefreshCartpage()
	{
		if(Yii::$app->user->isGuest)
		{
			Cart::updateAll(['coupon_discount_temp' => 0, 'discount_coupon_id' => 0], ['=', 'session_id', session_id()]);
		}
		else
		{
			Cart::updateAll(['coupon_discount_temp' => 0, 'discount_coupon_id' => 0], ['=', 'user_id', Yii::$app->user->identity->id]);
		}

		return $this->renderPartial('ajax-update-checkout-page', 
										[
											'coupon_discount'=> '0',
											'discount_coupon' => '',
										]);
	}

	public function actionDownload()
	{
		$digital_record_id = $_REQUEST['did'];
		$sub_order_id = $_REQUEST['oid'];
		$token = $_REQUEST['token'];

		$digital_record = DigitalRecords::findOne($digital_record_id);

		if (Yii::$app->user->identity->entity_type != 'customer' || $digital_record->customer_id != Yii::$app->user->identity->entity_id)
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}

		if ($sub_order_id != $digital_record->sub_order_id || $token != $digital_record->token)
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}

		//$path = '../../multeback/web/uploads/'.$digital_record->file_name;
		$temp_file = 'temp/'.$digital_record->file_name;

		if(MulteModel::getFileFromServer($digital_record->file_name, 'digital_uploads', $temp_file))
		{
			if (file_exists($temp_file)) 
			{
				return Yii::$app->response->sendFile($temp_file, $digital_record->orig_name);
			}
			else
			{
				throw new NotFoundHttpException(Yii::t('app', 'File Not found - Please contact support!'));
			}
		}
		else
		{
			throw new NotFoundHttpException(Yii::t('app', 'File Download Failed - Please contact support!'));
		}
	}

	public function actionGetCode()
	{
		$license_key_code_id = $_REQUEST['lid'];
		$sub_order_id = $_REQUEST['oid'];

		$sub_order = SubOrder::findOne($sub_order_id);
		$order = Order::findOne($sub_order->order_id);

		if (Yii::$app->user->identity->entity_type != 'customer' || $order->customer_id != Yii::$app->user->identity->entity_id)
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}

		$licrec = LicenseKeyCode::find()->where("sub_order_id=".$sub_order_id)->one();
		
		if($license_key_code_id != $licrec->id)
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}

		$inventory = MulteModel::mapJsonToModel(Json::decode($sub_order->inventory_snapshot), new Inventory);

		if ($inventory->send_as_attachment)
		{
			$full_file_name = 'temp/'.uniqid($sub_order->id);

			$f = fopen($full_file_name, 'w');
			fwrite($f, $licrec->license_key_code);
			fclose($f);

			if ($inventory->attachment_file_name)
				return Yii::$app->response->sendFile($full_file_name, $inventory->attachment_file_name);
			else
				return Yii::$app->response->sendFile($full_file_name, 'file');
		}
		else
		{
			Yii::$app->session->setFlash('info', Yii::t('app', 'Please note down your code: ').$licrec->license_key_code);
			return $this->render('information', ['order' => $order]);
		}
	}

	function CheckAndSendDigitalLinks($order_id)
	{
		$connection = Yii::$app->db;
		
		$order = Order::findOne($order_id);
		$sub_orders = SubOrder::find()->where("order_id=".$order_id)->all();

		foreach ($sub_orders as $row)
		{
			try
			{			
				$inventory = Inventory::findOne($row['inventory_id']);
				if ($inventory->product->digital && !$inventory->product->license_key_code)
				{
					$transaction = $connection->beginTransaction();
					
					$suborder = SubOrder::findOne($row['id']);
					$suborder->sub_order_status = OrderStatus::_DELIVERED;
					$suborder->updated_at = time();

					$suborder->save();

					MulteModel::updateMainOrderStatus($suborder->id);
					
					$digital_record = new DigitalRecords;
					$digital_record->customer_id = $order->customer_id;
					$digital_record->sub_order_id = $suborder->id;
					$digital_record->token = hash('sha256', $order->customer_id.$suborder->id);
					$digital_record->file_name = $inventory->digital_file_name;
					$digital_record->orig_name = $inventory->digital_file;
					$digital_record->added_at = time();
					$digital_record->save();

					SendEmail::sendDigitalLinkEmail($digital_record);

					$transaction->commit();
				}
				else if ($inventory->product->digital && $inventory->product->license_key_code)
				{
					// Serials-Keys-Codes etc.
					$transaction = $connection->beginTransaction();
					
					$suborder = SubOrder::findOne($row['id']);
					$suborder->sub_order_status = OrderStatus::_DELIVERED;
					$suborder->updated_at = time();

					$suborder->save();

					MulteModel::updateMainOrderStatus($suborder->id);

					$lickeycode = LicenseKeyCode::find()->where("inventory_id=".$inventory->id." and used = 0")->one();
					$lickeycode->used = 1;
					$lickeycode->sub_order_id = $row['id'];
					$lickeycode->update();

					if($inventory->send_as_attachment)
					{
						if($inventory->attachment_file_name)
							$name = $inventory->attachment_file_name;
						else
							$name = 'file';
							
						$full_file_name = 'temp/'.uniqid($suborder->id);

						//Email code as attachment
						$f = fopen($full_file_name, 'w');
						fwrite($f, $lickeycode->license_key_code);
						fclose($f);

						SendEmail::sendLicenseKeyCodeAttachmentEmail($full_file_name, $order->customer_id, $name);

						unlink($full_file_name);
					}
					else
					{
						//Email code as inline Text
						SendEmail::sendLicenseKeyCodeTextEmail($lickeycode->license_key_code, $order->customer_id);
					}

					$transaction->commit();
				}
			}
			catch (Exception $e)
			{
				Yii::$app->session->setFlash('error', $e->getMessage());
				$transaction->rollback();
			}
		}
	}
}