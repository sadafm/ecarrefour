<?php

namespace multebox\models\search;
use Yii;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\ConfigItem as configItemModel;
use multebox\models\File as FileModel;
use multebox\models\Note as NoteModel;
use multebox\models\History as HistoryModel;
use multebox\models\Address as AddressModel;
use multebox\models\Contact;
use multebox\models\User;
use multebox\models\Vendor;
use multebox\models\Product;
use multebox\models\ProductBrand;
use multebox\models\ProductCategory;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use multebox\models\ProductAttributes;
use multebox\models\DiscountCoupons;
use multebox\models\Inventory;
use multebox\models\Cart;
use multebox\models\GlobalDiscount;
use multebox\models\SendEmail;
use multebox\models\AuthAssignment;
use multebox\models\Order;
use multebox\models\SubOrder;
use multebox\models\OrderStatus;
use multebox\models\PaypalDetails;
use multebox\models\StripeDetails;
use multebox\models\RazorpayDetails;
use multebox\models\PaypalRefundDetails;
use multebox\models\RazorpayRefundDetails;
use multebox\models\StripeRefundDetails;
use multebox\models\PaymentMethods;
use multebox\models\CommissionDetails;
use multebox\models\VendorReview;
use multebox\models\LicenseKeyCode;
use multebox\models\TicketStatus;
use multebox\models\Wishlist;
use multebox\models\Comparison;
use multebox\models\Ticket as TicketModel;
use multebox\models\CurrencyConversion;
use Razorpay\Api\Api;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use multebox\models\ImageUpload;

use yii\helpers\Url;
use yii\helpers\Json;

/**
 * MulteModel represents the model behind the search form about multebox\models\MulteModel.
 */
class MulteModel extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    /*public static function tableName()
    {
        return '';
    }*/

	public static function getCountWishlist()
	{
		return Yii::$app->user->isGuest?0:Wishlist::find()->where(['customer_id' => Yii::$app->user->identity->entity_id])->count();
	}

	public static function getCountComparelist()
	{
		if(Yii::$app->user->isGuest)
		{
			$list = Comparison::find()->where(['session_id' => session_id()])->one();
		}
		else
		{
			$list = Comparison::find()->where(['customer_id' => Yii::$app->user->identity->entity_id])->one();
		}

		if($list)
		{
			$items = Json::decode($list->inventory_list);

			return $items?count($items):0;
		}
		else
		{
			return 0;
		}
	}
	
	public static function getOrdersForDashboard()
	{
		return Order::find()->where("order_status not in ('".OrderStatus::_CANCELED."', '".OrderStatus::_REFUNDED."', '".OrderStatus::_RETURNED."')")->orderBy('id desc')->limit(7)->all();
	}

	public static function getVendorOrdersForDashboard()
	{
		return SubOrder::find()->where("vendor_id = ".Yii::$app->user->identity->entity_id." and sub_order_status not in ('".OrderStatus::_CANCELED."', '".OrderStatus::_REFUNDED."', '".OrderStatus::_RETURNED."')")->orderBy('id desc')->limit(7)->all();
	}

	public static function getInventoriesForDashboard()
	{
		return Inventory::find()->orderBy('id desc')->limit(4)->all();
	}

	public static function getVendorInventoriesForDashboard()
	{
		return Inventory::find()->where("vendor_id = ".Yii::$app->user->identity->entity_id)->orderBy('id desc')->limit(4)->all();
	}

	public static function getTotalOrderCount($months)
	{
		$oldtimestamp = strtotime("-$months month 00:00:00");
		$newtimestamp = strtotime('last day of this month 23:59:59', time());

		return Order::find()->where("added_at > ".$oldtimestamp." and added_at <= ".$newtimestamp)->count();
	}

	public static function getTotalVendorOrderCount($months)
	{
		$oldtimestamp = strtotime("-$months month 00:00:00");
		$newtimestamp = strtotime('last day of this month 23:59:59', time());

		return SubOrder::find()->where("vendor_id = ".Yii::$app->user->identity->entity_id." and added_at > ".$oldtimestamp." and added_at <= ".$newtimestamp)->count();
	}

	public static function getTotalSaleAmount($months)
	{
		$oldtimestamp = strtotime("-$months month 00:00:00");
		$newtimestamp = strtotime('last day of this month 23:59:59', time());

		return MulteModel::formatAmount(Order::find()->where("added_at > ".$oldtimestamp." and added_at <= ".$newtimestamp)->sum('total_cost'));
	}

	public static function getTotalVendorSaleAmount($months)
	{
		$oldtimestamp = strtotime("-$months month 00:00:00");
		$newtimestamp = strtotime('last day of this month 23:59:59', time());

		return MulteModel::formatAmount(SubOrder::find()->where("vendor_id = ".Yii::$app->user->identity->entity_id." and added_at > ".$oldtimestamp." and added_at <= ".$newtimestamp)->sum('total_cost'));
	}

	public static function getTotalCommission($months)
	{
		$oldtimestamp = strtotime("-$months month 00:00:00");
		$newtimestamp = strtotime('last day of this month 23:59:59', time());

		return MulteModel::formatAmount(CommissionDetails::find()->where("added_at > ".$oldtimestamp." and added_at <= ".$newtimestamp)->sum('commission'));
	}

	public static function getTotalVendorIncome($months)
	{
		$oldtimestamp = strtotime("-$months month 00:00:00");
		$newtimestamp = strtotime('last day of this month 23:59:59', time());

		return MulteModel::formatAmount(CommissionDetails::find()->where("vendor_id = ".Yii::$app->user->identity->entity_id." and added_at > ".$oldtimestamp." and added_at <= ".$newtimestamp)->sum('sub_order_total - commission'));
	}

	public static function getTotalVendors($months)
	{
		$oldtimestamp = strtotime("-$months month 00:00:00");
		$newtimestamp = strtotime('last day of this month 23:59:59', time());

		return Vendor::find()->where("added_at > ".$oldtimestamp." and added_at <= ".$newtimestamp)->count();
	}

	public static function getAverageVendorRating($months)
	{
		$oldtimestamp = strtotime("-$months month 00:00:00");
		$newtimestamp = strtotime('last day of this month 23:59:59', time());

		return VendorReview::find()->where("vendor_id = ".Yii::$app->user->identity->entity_id." and added_at > ".$oldtimestamp." and added_at <= ".$newtimestamp)->average('rating');
	}

	public static function getCurrentMonthOrderCount()
	{
		return Order::find()->where("added_at >= ".strtotime('first day of this month 00:00:00', time()))->count();
	}

	public static function getCurrentMonthVendorOrderCount()
	{
		return SubOrder::find()->where("vendor_id = ".Yii::$app->user->identity->entity_id." and added_at >= ".strtotime('first day of this month 00:00:00', time()))->count();
	}

	public static function getCurrentMonthSaleAmount()
	{
		return MulteModel::formatAmount(Order::find()->where("added_at >= ".strtotime('first day of this month 00:00:00', time()))->sum('total_cost'));
	}

	public static function getCurrentMonthVendorSaleAmount()
	{
		return MulteModel::formatAmount(SubOrder::find()->where("vendor_id = ".Yii::$app->user->identity->entity_id." and added_at >= ".strtotime('first day of this month 00:00:00', time()))->sum('total_cost'));
	}

	public static function getCurrentMonthCommission()
	{
		return MulteModel::formatAmount(CommissionDetails::find()->where("added_at >= ".strtotime('first day of this month 00:00:00', time()))->sum('commission'));
	}

	public static function getCurrentMonthVendorIncome()
	{
		return MulteModel::formatAmount(CommissionDetails::find()->where("vendor_id = ".Yii::$app->user->identity->entity_id." and added_at >= ".strtotime('first day of this month 00:00:00', time()))->sum('sub_order_total - commission'));
	}

	public static function getCurrentMonthVendors()
	{
		return Vendor::find()->where("added_at >= ".strtotime('first day of this month 00:00:00', time()))->count();
	}

	public static function getCurrentMonthVendorRating()
	{
		return VendorReview::find()->where("added_at >= ".strtotime('first day of this month 00:00:00', time()))->average('rating');
	}

	public static function formatAmount($amount, $currency_symbol = false)
	{
		$roundval = round($amount, 2);
		
		if($currency_symbol)
			return $currency_symbol.number_format($roundval, 2);
		else
		{
			if(isset($_SESSION['CONVERTED_CURRENCY_CODE']))
			{
				$currency_conversion = CurrencyConversion::findOne(['from' => Yii::$app->params['SYSTEM_CURRENCY'], 'to' => $_SESSION['CONVERTED_CURRENCY_CODE']]);

				if($currency_conversion)
				{
					return $_SESSION['CONVERTED_CURRENCY_SYMBOL'].number_format(round($amount*$currency_conversion->conversion_rate, 2), 2);
				}
				else
				{
					return Yii::$app->params['SYSTEM_CURRENCY_SYMBOL'].number_format($roundval, 2);
				}
			}
			else
				return Yii::$app->params['SYSTEM_CURRENCY_SYMBOL'].number_format($roundval, 2);
		}
	}

	public static function getConvertedCost($amount)
	{
		if(isset($_SESSION['CONVERTED_CURRENCY_CODE']))
		{
			$currency_conversion = CurrencyConversion::findOne(['from' => Yii::$app->params['SYSTEM_CURRENCY'], 'to' => $_SESSION['CONVERTED_CURRENCY_CODE']]);

			if($currency_conversion)
			{
				return $amount*$currency_conversion->conversion_rate;
			}
			else
			{
				return $amount;
			}
		}
		else
			return $amount;
	}

	/*********************************************************
	 * Takes as input sub_order_id and updates its main
	 * order status to most relevant status according to
	 * sub_order item(s) status
	 *********************************************************/
	public static function updateMainOrderStatus ($suborderid)
	{
		$suborder = SubOrder::findone($suborderid);
		$order = Order::findOne ($suborder->order_id);
		$suborders = SubOrder::find()->where("order_id=".$order->id)->all();
		
		$match = true;
		
		if($suborder->sub_order_status != OrderStatus::_IN_PROCESS)
		{
			foreach ($suborders as $row)
			{
				if ($row->sub_order_status == $suborder->sub_order_status || $row->sub_order_status == OrderStatus::_CANCELED)
				{
					continue;
				}
				else
				{
					$match = false;
					break;
				}
			}
		}
		
		$statuscount = 0;
		$savedstatus = '';
		foreach ($suborders as $row)
		{
			if ($row->sub_order_status == OrderStatus::_CANCELED || $row->sub_order_status == OrderStatus::_DELIVERED || $row->sub_order_status == OrderStatus::_REFUNDED)
			{
				$statuscount++;
				if ($row->sub_order_status == OrderStatus::_DELIVERED)
				{
					$savedstatus = OrderStatus::_DELIVERED;
				}
				else
				if ($row->sub_order_status == OrderStatus::_REFUNDED)
				{
					if ($savedstatus != OrderStatus::_DELIVERED)
						$savedstatus = OrderStatus::_REFUNDED;
				}
				else
				if ($row->sub_order_status == OrderStatus::_CANCELED)
				{
					if ($savedstatus != OrderStatus::_DELIVERED && $savedstatus != OrderStatus::_REFUNDED)
						$savedstatus = OrderStatus::_CANCELED;
				}
			}
		}

		if ($suborders && ($statuscount == count($suborders)))
		{
			$order->order_status = $savedstatus;
			$order->save();
		}
		else
		if ($match)
		{
			$order->order_status = $suborder->sub_order_status;
			$order->save();
		}
	}
	
	/****************************************************
	 * Encrypts and decrypts input string
	 * $action = 'e' for encryption
	 * $action = 'd' for decryption
	 ****************************************************/
	public static function multecrypt ($string, $action = 'e') 
	{
		$secret_key = 'multecrypt_key';
		$secret_iv = 'multecrypt_iv';

		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash ('sha256', $secret_key);
		$iv = substr (hash ('sha256', $secret_iv), 0, 16);

		if ($action == 'e')
		{
			$output = base64_encode (openssl_encrypt ($string, $encrypt_method, $key, 0, $iv));
		}
		else 
		if($action == 'd')
		{
			$output = openssl_decrypt (base64_decode ($string), $encrypt_method, $key, 0, $iv);
		}

		return $output;
	}
	
	public static  function searchAttachments($params, $entity_id,$entity_type)
	{
		$query = FileModel::find ()->where ( [ 
				'entity_type' => $entity_type,
				'entity_id' => $entity_id 
		] );
		
		$dataProvider = new ActiveDataProvider ( [ 
				'query' => $query 
		] );
		
		return $dataProvider;
	}
	
	public static  function searchNotes($params, $entity_id,$entity_type)
	{
		$query = NoteModel::find ()->where ( [ 
				'entity_type' => $entity_type,
				'entity_id' => $entity_id 
		] )->all();
		
		$dataProvider = new ActiveDataProvider ( [ 
				'query' => $query 
		] );
		
		return $query;
	}

	public static  function searchHistory($params, $entity_id,$entity_type)
	{
		$query = HistoryModel::find ()->where ( [ 
				'entity_type' => $entity_type,
				'entity_id' => $entity_id 
		] )->orderBy('id desc');
		
		$dataProvider = new ActiveDataProvider ( [ 
				'query' => $query 
		] );
		
		return $dataProvider;
	}
	
	public static  function searchAddresses($params, $entity_id,$entity_type)
	{
		$query = AddressModel::find()->where("entity_id=$entity_id and entity_type='$entity_type'");
		
		$dataProvider = new ActiveDataProvider ( [ 
				'query' => $query 
		] );
		
		return $dataProvider;
	}

	public static  function searchContacts($params, $entity_id,$entity_type)
	{
		$query = Contact::find()->where("entity_id=$entity_id and entity_type='$entity_type'");
		
		$dataProvider = new ActiveDataProvider ( [ 
				'query' => $query 
		] );
		
		return $dataProvider;
	}

	public static  function searchProductSubCategory($params, $id)
	{
		$query = ProductSubCategory::find()->where("parent_id=$id");
		
		$dataProvider = new ActiveDataProvider ( [ 
				'query' => $query 
		] );
		
		return $dataProvider;
	}

	public static  function searchProductSubSubCategory($params, $id)
	{
		$query = ProductSubSubCategory::find()->where("parent_id=$id");
		
		$dataProvider = new ActiveDataProvider ( [ 
				'query' => $query 
		] );
		
		return $dataProvider;
	}

	public static  function searchProductAttributes($params, $id)
	{
		$query = ProductAttributes::find()->where("parent_id=$id");
		
		$dataProvider = new ActiveDataProvider ( [ 
				'query' => $query 
		] );
		
		return $dataProvider;
	}

	public static function getInventoryActualPrice($inventory_item)
	{
		$inventoryPrice = floatval($inventory_item->price);
		if($inventory_item->price_type == 'B')
		{
			if($inventory_item->attribute_price)
			foreach(Json::decode($inventory_item->attribute_price) as $row)
			{
				$inventoryPrice += floatval($row);
			}
		}

		return $inventoryPrice;
	}

	public static function getInventoryDiscountPercentage($inventory_item, $total_items)
	{
		$inventoryPrice = MulteModel::getInventoryActualPrice($inventory_item);
		$normal = 0;

		if ($inventory_item->slab_discount_ind == 1) // Slab discount applicable
		{
			if ($inventory_item->slab_4_range != 0 && $inventory_item->slab_4_range != '' && $total_items >= $inventory_item->slab_4_range)
			{
				$discount = $inventory_item->slab_4_discount;
				$discount_type = $inventory_item->slab_discount_type;
			}
			else if ($inventory_item->slab_3_range != 0 && $inventory_item->slab_3_range != '' && $total_items >= $inventory_item->slab_3_range)
			{
				$discount = $inventory_item->slab_3_discount;
				$discount_type = $inventory_item->slab_discount_type;
			}
			else if($inventory_item->slab_2_range != 0 && $inventory_item->slab_2_range != '' && $total_items >= $inventory_item->slab_2_range)
			{
				$discount = $inventory_item->slab_2_discount;
				$discount_type = $inventory_item->slab_discount_type;
			}
			else if ($inventory_item->slab_1_range != 0 && $inventory_item->slab_1_range != '' && $total_items >= $inventory_item->slab_1_range)
			{
				$discount = $inventory_item->slab_1_discount;
				$discount_type = $inventory_item->slab_discount_type;
			}
			else
			{
				$normal = 1;
				$discount = $inventory_item->discount;
				$discount_type = $inventory_item->discount_type;
			}
		}
		else
		{
			$normal = 1;
			$discount = $inventory_item->discount;
			$discount_type = $inventory_item->discount_type;
		}
		/*var_dump($inventory_item->discount);
		var_dump($discount);
		var_dump($discount_type);exit;*/

		if($discount_type == 'P')
		{
			$inventoryDiscount = $discount;
		}
		else if ($normal)
		{
			if($inventoryPrice > 0)
				$inventoryDiscount = round((floatval($discount)/$inventoryPrice)*100,2);
			else
				$inventoryDiscount = 0;
		}
		else
		{
			if($inventoryPrice > 0)
				$inventoryDiscount = (floatval($discount)/($inventoryPrice*$total_items))*100;
			else
				$inventoryDiscount = 0;
		}

		return $inventoryDiscount;
	}

	public static function getInventoryDiscountedPrice($inventory_item, $total_items)
	{
		$inventoryPrice = MulteModel::getInventoryActualPrice($inventory_item);

		$inventoryDiscount = MulteModel::getInventoryDiscountPercentage($inventory_item, $total_items);

		$inventoryDiscountedPrice = round($inventoryPrice - $inventoryPrice*$inventoryDiscount/100, 2);

		return $inventoryDiscountedPrice;
	}

	public static function getInventoryDiscountAmount($inventory_item, $total_items)
	{
		$inventoryPrice = MulteModel::getInventoryActualPrice($inventory_item);

		$inventoryDiscount = MulteModel::getInventoryDiscountPercentage($inventory_item, $total_items);

		$inventoryDiscountAmount = round($inventoryPrice*$inventoryDiscount/100, 2);

		return $inventoryDiscountAmount;
	}

	public static function getInventoryTotalAmount($inventory_item, $total_items)
	{
		return MulteModel::getInventoryDiscountedPrice($inventory_item, $total_items) + $inventory_item->shipping_cost;
	}

	public static function getCartItemCostSingleUnitWithoutShipping($cart_item)
	{
		$inventory_item = Inventory::findOne($cart_item->inventory_id);

		$inventoryPrice = floatval($inventory_item->price);

		$inventoryDiscount = MulteModel::getInventoryDiscountPercentage($inventory_item, $cart_item->total_items);
			
		$inventoryDiscountedPrice = round($inventoryPrice - $inventoryPrice*$inventoryDiscount/100, 2);

		return $inventoryDiscountedPrice;
	}

	public static function getCartItemCostWithoutShipping($cart_item)
	{
		$inventoryDiscountedPrice = MulteModel::getCartItemCostSingleUnitWithoutShipping($cart_item);

		$cart_item_total_price = ($inventoryDiscountedPrice) * $cart_item->total_items;
		
		return $cart_item_total_price;
	}

	public static function getCartItemCostWithShipping($cart_item)
	{
		$inventory_item = Inventory::findOne($cart_item->inventory_id);

		$inventoryDiscountedPrice = MulteModel::getCartItemCostSingleUnitWithoutShipping($cart_item);

		$cart_item_total_price = ($inventoryDiscountedPrice + $inventory_item->shipping_cost) * $cart_item->total_items;
		
		return $cart_item_total_price;
	}

	/***************************************************
	* Return Values
	* a - Invalid Coupon
	* b - Expired Coupon
	* c - Not applicable on cart items
	* d - Not applicable on cart amount
	* e - Coupon code not issued to current customer
	* f - Coupon code max budget exhausted
	* Rest - HTML result to replace checkout page cart
	***************************************************/
	public static function getCouponDiscount($discount_coupon)
	{
		$discount_row = DiscountCoupons::find()->where("coupon_code='".$discount_coupon."'")->one();
		$site_level_coupon = false;
		$category_level_coupon = false;
		$sub_category_level_coupon = false;
		$sub_subcategory_level_coupon = false;

		if (!$discount_row)
		{
			return "a"; //Invalid Coupon
		}

		if (($discount_row->customer_id > 0) && ($discount_row->customer_id != Yii::$app->user->identity->entity_id))
		{
			return "e"; //Coupon code not issued to current customer
		}

		if (($discount_row->used_count == $discount_row->max_uses) || ($discount_row->expiry_datetime <= time()))
		{
			return "b"; //Coupon expired
		}

		if (($discount_row->max_budget > 0) && ($discount_row->used_budget >= $discount_row->max_budget))
		{
			return "f"; //Coupon budget exhausted
		}

		if(Yii::$app->user->isGuest)
		{
			$cart_items = Cart::find()->where("session_id='".session_id()."'")->all();
		}
		else
		{
			$cart_items = Cart::find()->where("user_id=".Yii::$app->user->identity->id)->all();
		}

		$total_cart_cost = 0;
		$vendor_coupon = false;
		$coupon_added_by = User::findOne($discount_row->added_by_id);

		if ($coupon_added_by->entity_type == 'vendor')
		{
			$vendor_coupon = true;
		}

		if ($discount_row->category_id == '' || $discount_row->category_id == 0)
		{
			$site_level_coupon = true;
			/* Site level coupon */
			foreach ($cart_items as $cart)
			{
				if ($vendor_coupon)
				{
					if(Inventory::findOne($cart->inventory_id)->vendor_id == $coupon_added_by->entity_id)
					{
						$total_cart_cost += MulteModel::getCartItemCostWithoutShipping($cart);
					}
				}
				else
				{
					$total_cart_cost += MulteModel::getCartItemCostWithoutShipping($cart);
				}
			}
		}
		else if ($discount_row->sub_category_id == '' || $discount_row->sub_category_id == 0)
		{
			$category_level_coupon = true;
			/* Category level coupon */
			foreach ($cart_items as $cart)
			{
				if($cart->product->category_id == $discount_row->category_id)
				{
					if ($vendor_coupon)
					{
						if(Inventory::findOne($cart->inventory_id)->vendor_id == $coupon_added_by->entity_id)
						{
							$total_cart_cost += MulteModel::getCartItemCostWithoutShipping($cart);
						}
					}
					else
					{
						$total_cart_cost += MulteModel::getCartItemCostWithoutShipping($cart);
					}
				}
			}
		}
		else if ($discount_row->sub_subcategory_id == '' || $discount_row->sub_subcategory_id == 0)
		{
			$sub_category_level_coupon = true;
			/* Sub_category level coupon */
			foreach ($cart_items as $cart)
			{
				if(($cart->product->category_id == $discount_row->category_id) && ($cart->product->sub_category_id == $discount_row->sub_category_id))
				{
					if ($vendor_coupon)
					{
						if(Inventory::findOne($cart->inventory_id)->vendor_id == $coupon_added_by->entity_id)
						{
							$total_cart_cost += MulteModel::getCartItemCostWithoutShipping($cart);
						}
					}
					else
					{
						$total_cart_cost += MulteModel::getCartItemCostWithoutShipping($cart);
					}
				}
			}
		}
		else
		{
			$sub_subcategory_level_coupon = true;
			/* Sub_subcategory level coupon */
			foreach ($cart_items as $cart)
			{
				if(($cart->product->category_id == $discount_row->category_id) && ($cart->product->sub_category_id == $discount_row->sub_category_id) && ($cart->product->sub_subcategory_id == $discount_row->sub_subcategory_id))
				{
					if($discount_row->inventory_id == '' || $discount_row->inventory_id == 0 || $discount_row->inventory_id == $cart->inventory_id)
					{
						if ($vendor_coupon)
						{
							if(Inventory::findOne($cart->inventory_id)->vendor_id == $coupon_added_by->entity_id)
							{
								$total_cart_cost += MulteModel::getCartItemCostWithoutShipping($cart);
							}
						}
						else
						{
							$total_cart_cost += MulteModel::getCartItemCostWithoutShipping($cart);
						}
					}
				}
			}
		}

		if($total_cart_cost == 0)
		{
			return "c"; //Not applicable on cart items
		}

		if($total_cart_cost < $discount_row->min_cart_amount)
		{
			return "d"; //Not applicable on cart amount
		}

		$coupon_discount = 0;
		if($discount_row->discount_type == 'P')
		{
			$coupon_discount = $total_cart_cost * $discount_row->discount/100;

			if(($discount_row->max_discount > 0) && ($coupon_discount > $discount_row->max_discount))
			{
				$coupon_discount = $discount_row->max_discount;
			}
		}
		else
		{
			if($discount_row->discount > $total_cart_cost) // TODO
				$coupon_discount = $total_cart_cost;
			else
				$coupon_discount = $discount_row->discount;
		}

		if (($discount_row->max_budget > 0) && ($coupon_discount > ($discount_row->max_budget - $discount_row->used_budget)))
		{
			$coupon_discount = $discount_row->max_budget - $discount_row->used_budget;
		}
		
		/* Now update Cart rows with individual discount */
		$coupon_percent = $coupon_discount/$total_cart_cost;

		if ($site_level_coupon)
		{
			foreach($cart_items as $cart)
			{
				if ($vendor_coupon && Inventory::findOne($cart->inventory_id)->vendor_id == $coupon_added_by->entity_id || !$vendor_coupon)
				{
					$cart_item_cost = MulteModel::getCartItemCostWithoutShipping($cart);
					$cart_item_discount = $cart_item_cost * $coupon_percent;

					if($cart_item_discount + $cart->global_discount_temp > $cart_item_cost)
					{
						$cart_item_discount -= $cart->global_discount_temp;
						$coupon_discount -= $cart->global_discount_temp;
					}
					
					$cart->coupon_discount_temp = $cart_item_discount;
					$cart->discount_coupon_id = $discount_row->id;
					$cart->save();

					/*Cart::updateAll(['coupon_discount_temp' => MulteModel::getCartItemCostWithoutShipping($cart)*$coupon_percent, 'discount_coupon_id' => $discount_row->id], ['=', 'id', $cart->id]);*/
				}
			}
		}
		else if ($category_level_coupon)
		{
			foreach ($cart_items as $cart)
			{
				if($cart->product->category_id == $discount_row->category_id)
				{
					if ($vendor_coupon && Inventory::findOne($cart->inventory_id)->vendor_id == $coupon_added_by->entity_id || !$vendor_coupon)
					{
						$cart_item_cost = MulteModel::getCartItemCostWithoutShipping($cart);
						$cart_item_discount = $cart_item_cost * $coupon_percent;

						if($cart_item_discount + $cart->global_discount_temp > $cart_item_cost)
						{
							$cart_item_discount -= $cart->global_discount_temp;
							$coupon_discount -= $cart->global_discount_temp;
						}
						
						$cart->coupon_discount_temp = $cart_item_discount;
						$cart->discount_coupon_id = $discount_row->id;
						$cart->save();

						/*Cart::updateAll(['coupon_discount_temp' => MulteModel::getCartItemCostWithoutShipping($cart)*$coupon_percent, 'discount_coupon_id' => $discount_row->id], ['=', 'id', $cart->id]);*/
					}
				}
			}
		}
		else if ($sub_category_level_coupon)
		{
			foreach ($cart_items as $cart)
			{
				if(($cart->product->category_id == $discount_row->category_id) && ($cart->product->sub_category_id == $discount_row->sub_category_id))
				{
					if ($vendor_coupon && Inventory::findOne($cart->inventory_id)->vendor_id == $coupon_added_by->entity_id || !$vendor_coupon)
					{
						$cart_item_cost = MulteModel::getCartItemCostWithoutShipping($cart);
						$cart_item_discount = $cart_item_cost * $coupon_percent;

						if($cart_item_discount + $cart->global_discount_temp > $cart_item_cost)
						{
							$cart_item_discount -= $cart->global_discount_temp;
							$coupon_discount -= $cart->global_discount_temp;
						}

						$cart->coupon_discount_temp = $cart_item_discount;
						$cart->discount_coupon_id = $discount_row->id;
						$cart->save();

						/*Cart::updateAll(['coupon_discount_temp' => MulteModel::getCartItemCostWithoutShipping($cart)*$coupon_percent, 'discount_coupon_id' => $discount_row->id], ['=', 'id', $cart->id]);*/
					}
				}
			}
		}
		else if ($sub_subcategory_level_coupon)
		{
			foreach ($cart_items as $cart)
			{
				if(($cart->product->category_id == $discount_row->category_id) && ($cart->product->sub_category_id == $discount_row->sub_category_id) && ($cart->product->sub_subcategory_id == $discount_row->sub_subcategory_id))
				{
					if($discount_row->inventory_id == '' || $discount_row->inventory_id == 0 || $discount_row->inventory_id == $cart->inventory_id)
					{
						if ($vendor_coupon && Inventory::findOne($cart->inventory_id)->vendor_id == $coupon_added_by->entity_id || !$vendor_coupon)
						{
							$cart_item_cost = MulteModel::getCartItemCostWithoutShipping($cart);
							$cart_item_discount = $cart_item_cost * $coupon_percent;

							if($cart_item_discount + $cart->global_discount_temp > $cart_item_cost)
							{
								$cart_item_discount -= $cart->global_discount_temp;
								$coupon_discount -= $cart->global_discount_temp;
							}

							$cart->coupon_discount_temp = $cart_item_discount;
							$cart->discount_coupon_id = $discount_row->id;
							$cart->save();

							/*Cart::updateAll(['coupon_discount_temp' => MulteModel::getCartItemCostWithoutShipping($cart)*$coupon_percent, 'discount_coupon_id' => $discount_row->id], ['=', 'id', $cart->id]);*/
						}
					}
				}
			}
		}

		return $coupon_discount;
	}

	public static function getGlobalDiscountSingle($cart, $discount_row, $indicator)
	{
		$global_discount = 0;
		$total_cart_cost = MulteModel::getCartItemCostWithoutShipping($cart);

		if(
			($discount_row->category_id == '' || $discount_row->category_id == 0) || 
			(($discount_row->category_id == $cart->product->category_id) && ($discount_row->sub_category_id == '' || $discount_row->sub_category_id == 0)) || 
			(($discount_row->category_id == $cart->product->category_id && $discount_row->sub_category_id == $cart->product->sub_category_id) && ($discount_row->sub_subcategory_id == '' || $discount_row->sub_subcategory_id == 0)) ||
			($discount_row->category_id == $cart->product->category_id && $discount_row->sub_category_id == $cart->product->sub_category_id && $discount_row->sub_subcategory_id == $cart->product->sub_subcategory_id)
		  )
		{
			//print_r($cart->id."<br> <br>");
			//print_r($discount_row->id."<br><br>");
			if($discount_row->discount_type == 'P')
			{
				$global_discount = $total_cart_cost * $discount_row->discount/100;

				if(($discount_row->max_discount > 0) && ($global_discount > $discount_row->max_discount))
				{
					$global_discount = $discount_row->max_discount;
				}
			}
			else
			{
				$global_discount = $discount_row->discount;
			}

			if ($discount_row->max_budget > 0)
			{
				if($indicator == 0) // Before order confirmation
				{
					if($global_discount > ($discount_row->max_budget - $discount_row->used_budget_temp))
					{
						$global_discount = $discount_row->max_budget - $discount_row->used_budget_temp;
					}
					
					$discount_row->used_budget_temp += $global_discount;
				}
				else
				{
					if($global_discount > ($discount_row->max_budget - $discount_row->used_budget))
					{
						$global_discount = $discount_row->max_budget - $discount_row->used_budget;
					}
					
					$discount_row->used_budget += $global_discount;
				}
			}
		}

		//$cart_item_cost = MulteModel::getCartItemCostWithoutShipping($cart);

		if($global_discount > $total_cart_cost)
		{
			$global_discount = $total_cart_cost;
		}

		if($indicator == 0)
			$discount_row->used_budget_temp = $discount_row->used_budget_temp + $global_discount;
		else
			$discount_row->used_budget = $discount_row->used_budget + $global_discount;

		$discount_row->save();

		Cart::updateAll(['global_discount_temp' => $global_discount, 'global_discount_id' => $discount_row->id], ['=', 'id', $cart->id]);

		return $global_discount;
	}
	
	/*********************************************************
	 * First matching global discount entry will be 
	 * used to calculate the discount. Exact match of
	 * sub_subcategory_id, sub_category_id, category_id
	 * in above order will take precedence
	 * $cart_items - list of cart items
	 * $indicator: 
	 *            0 if before order confirmation
	 *            1 if on order confirmation - !!!Not in Use!!!
	 *********************************************************/
	public static function getGlobalDiscount($cart_items, $indicator)
	{
		$global_discount = GlobalDiscount::find()->orderBy('sub_subcategory_id desc, sub_category_id desc, category_id desc')->all();
		
		if ($indicator == 0) // If before order confirmation
		{
			foreach($global_discount as $discount_row)
			{
				$discount_row->used_budget_temp = $discount_row->used_budget;
				//$discount_row->save();
			}
		}

		$selected_row = new GlobalDiscount;

		$cart_discount = 0;

		foreach($cart_items as $cart)
		{
			Cart::updateAll(['global_discount_temp' => 0, 'global_discount_id' => 0], ['=', 'id', $cart->id]);
			foreach($global_discount as $discount_row)
			{
				if ($discount_row->category_id == '' || $discount_row->category_id == 0)
				{
					$cart_discount += MulteModel::getGlobalDiscountSingle($cart, $discount_row, $indicator);
					break;
				}
				else if (($discount_row->sub_category_id == '' || $discount_row->sub_category_id == 0) && ($discount_row->category_id == $cart->product->category_id))
				{
					$cart_discount += MulteModel::getGlobalDiscountSingle($cart, $discount_row, $indicator);
					break;
				}
				else if (($discount_row->sub_subcategory_id == '' || $discount_row->sub_subcategory_id == 0) && (($discount_row->category_id == $cart->product->category_id && $discount_row->sub_category_id == $cart->product->sub_category_id)))
				{
					$cart_discount += MulteModel::getGlobalDiscountSingle($cart, $discount_row, $indicator);
					break;
				}
				else if ($discount_row->category_id == $cart->product->category_id && $discount_row->sub_category_id == $cart->product->sub_category_id && $discount_row->sub_subcategory_id == $cart->product->sub_subcategory_id)
				{
					$cart_discount += MulteModel::getGlobalDiscountSingle($cart, $discount_row, $indicator);
					break;
				}
			}
		}

		//exit;
		return $cart_discount;
	}

	/*************************************************
	 * Takes all user data except password.
	 * Password gets generated dynamically and
	 * sent to user in email. Assign roles.
	 *************************************************/
	public static function createUserWithRolesAndSendMail($user, $role)
	{
		$length = 8;
		$new_password = Yii::$app->security->generateRandomString ($length);
		
		$user->password_hash=Yii::$app->security->generatePasswordHash ($new_password);

		if(!$user->save())
		{
			throw new \Exception(Json::encode($user->getErrors()));
		}

		$emailObj = new SendEmail;

		SendEmail::sendNewUserEmail($user->email, $user->first_name." ".$user->last_name, $user->username, $new_password);
		
		$rolemodel = new AuthAssignment;
		$rolemodel->item_name = $role;
		$rolemodel->user_id = $user->id;
		$rolemodel->save();

		$old_session_id = session_id();
		if(Yii::$app->user->login(User::findByUsername($user->username), 3600 * 24 * 30))
		{
			// No need to check each existing cart item for old_session as this is a fresh user and hence
			// no old item can be found in cart of logged in user
			Cart::updateAll(['session_id' => session_id(), 'user_id' => Yii::$app->user->identity->id], ['=', 'session_id', $old_session_id]);
		}

		return $user;
	}

	public static function mapJsonToModel($array_row, \yii\db\ActiveRecord $model)
	{
		$all_attributes = $model->getAttributes();

		foreach($all_attributes as $key=>$value)
		{
			$model->$key = $array_row[$key];	
		}

		return $model;
	}

	public static function mapJsonArrayToModelArray($array, \yii\db\ActiveRecord $model)
	{
		$all_attributes = $model->getAttributes();
		$model_array = [];
		
		foreach($array as $array_row)
		{
			$model_temp = new $model;
			foreach($all_attributes as $key=>$value)
			{
				$model_temp->$key = $array_row[$key];	
			}

			array_push($model_array, $model_temp);
		}

		return $model_array;
	}

	public static function getPriceBeforeTax($price, $tax_percent)
	{
		return $price/(1+$tax_percent/100);
	}

	public static function getCommission($suborder, $rule)
	{
		if($rule->commission_type == 'P')
		{
			$commission = MulteModel::getSubOrderVendorCost($suborder) * $rule->commission/100;
		}
		else
		{
			$commission = $rule->commission;
		}

		return $commission;
	}

	public static function getSubOrderVendorCost($suborder)
	{
		 $coupon = DiscountCoupons::findOne($suborder['discount_coupon_id']);

		 if($coupon)
		 {
			 $added_by = User::findOne($coupon->added_by_id);
			 if($added_by->entity_type == 'vendor')
			 {
				 $cost = $suborder['total_cost'] + $suborder['total_site_discount'];
			 }
			 else
			 {
				$cost = $suborder['total_cost'] + $suborder['total_site_discount'] + $suborder['total_coupon_discount'];
			 }
		 }
		 else
		 {
			 $cost = $suborder['total_cost'] + $suborder['total_site_discount'];
		 }
		 
		 return round($cost, 2);
	}

	public static function releaseInventoryStock($suborder)
	{
		$inventory = Inventory::findOne($suborder->inventory_id);
		$inventory->stock++;
		$inventory->total_sale--;
		$inventory->save();
	}

	public static function releaseCouponDiscount($suborder)
	{
		if($suborder->total_coupon_discount > 0)
		{
			$discount_coupon_row = DiscountCoupons::findOne($suborder->discount_coupon_id);
			$discount_coupon_row->used_budget -= $suborder->total_coupon_discount;
			$discount_coupon_row->save();
		}
	}

	public static function releaseGlobalDiscount($suborder)
	{
		if($suborder->total_site_discount > 0)
		{
			$global_discount_row = GlobalDiscount::findOne($suborder->global_discount_id);
			$global_discount_row->used_budget -= $suborder->total_site_discount;
			$global_discount_row->save();
		}
	}

	public static function issueStripeRefund($suborder)
    {
        \Stripe\Stripe::setApiKey(MulteModel::multecrypt(Yii::$app->params['STRIPE_SECRET_KEY'], 'd'));
		$order = Order::findOne($suborder->order_id);
		$stripedetails = StripeDetails::find()->where("order_id=".$order->id)->one();

		if($suborder->sub_order_status != OrderStatus::_RETURNED && $suborder->sub_order_status != OrderStatus::_CANCELED)
		{
			Yii::$app->session->setFlash('error', 'Refund for current order not allowed!');
			return 0;
		}

		$zero_decimal_currencies = Yii::$app->params['zero_decimal_currencies'];

		//if(in_array(Yii::$app->params['SYSTEM_CURRENCY'], $zero_decimal_currencies))
		if(in_array($suborder->order_currency_code, $zero_decimal_currencies))
		{
			$suborder_cost = round($suborder->total_converted_cost);
		}
		else
		{
			$suborder_cost = round($suborder->total_converted_cost*100);
		}

		try
		{
			$refundedSale = \Stripe\Refund::create(array(
							"charge" => $stripedetails->charge_id,
							"amount" => $suborder_cost,
							//"refund_application_fee" => true,
							//"reverse_transfer" => true
						));

			Yii::$app->session->setFlash('success', 'Refund Successful! Please note down your refund reference number: '.$refundedSale->id);
		}
		catch (Exception $e)
		{
			Yii::$app->session->setFlash('error', 'Stripe refund failed with error: '.$e->getMessage());
			return 0;
		}

		try
		{
			// Capture refund details in system
			$refunddetails = new StripeRefundDetails;
			$refunddetails->order_id = $order->id;
			$refunddetails->sub_order_id = $suborder->id;
			$refunddetails->amount = $suborder->total_converted_cost;
			$refunddetails->result_json = $refundedSale->__toJSON();
			$refunddetails->refund_id = $refundedSale->id;
			$refunddetails->added_at = time();

			$refunddetails->save();

			// Update sub order status and order status (if applicable)
			$suborder->sub_order_status = OrderStatus::_REFUNDED;
			$suborder->save();

			$suborders = SubOrder::find()->where("order_id=".$order->id)->all();
			$safe = true;

			foreach($suborders as $row)
			{
				if($row->sub_order_status != OrderStatus::_REFUNDED && $row->sub_order_status != OrderStatus::_CANCELED)
				{
					$safe = false;
					break;
				}
			}
			
			if($safe)
			{
				$order->order_status = OrderStatus::_REFUNDED;
				$order->save();
			}
		}
		catch (Exception $e)
		{
			Yii::$app->session->setFlash('error', 'Your refund is successful but we enountered some issue with order. Please contact customer support for further assistance! Error message:'.$e->getMessage());
		}

		return 0;
    }

	public static function issuePaypalRefund($suborder)
	{
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
		
		$order = Order::findOne($suborder->order_id);
		$paypaldetails = PaypalDetails::find()->where("order_id=".$order->id)->one();

		if($suborder->sub_order_status != OrderStatus::_RETURNED && $suborder->sub_order_status != OrderStatus::_CANCELED)
		{
			Yii::$app->session->setFlash('error', 'Refund for current order not allowed!');
			return 0;
		}

		$zero_decimal_currencies = Yii::$app->params['zero_decimal_currencies'];

		//if(in_array(Yii::$app->params['SYSTEM_CURRENCY'], $zero_decimal_currencies))
		if(in_array($suborder->order_currency_code, $zero_decimal_currencies))
		{
			$suborder_cost = round($suborder->total_converted_cost);
		}
		else
		{
			$suborder_cost = round($suborder->total_converted_cost, 2);
		}

		$amt = new \PayPal\Api\Amount();
		$amt->setTotal($suborder_cost)
		  //->setCurrency(Yii::$app->params['SYSTEM_CURRENCY']);
			->setCurrency($suborder->order_currency_code);

		$refund = new \PayPal\Api\Refund();
		$refund->setAmount($amt);

		$sale = new \PayPal\Api\Sale();
		$sale->setId($paypaldetails->sale_id);

		try 
		{
			$refundedSale = $sale->refund($refund, $apiContext);

			Yii::$app->session->setFlash('success', 'Refund Successful! Please note down your refund reference number: '.$refundedSale->getId());
		}
		catch (Exception $e) 
		{
			Yii::$app->session->setFlash('error', 'Paypal refund failed with error: '.$e->getMessage());
			return 0;
		}
		
		try
		{
			// Capture refund details in system
			$refunddetails = new PaypalRefundDetails;
			$refunddetails->order_id = $order->id;
			$refunddetails->sub_order_id = $suborder->id;
			$refunddetails->amount = $suborder->total_converted_cost;
			$refunddetails->result_json = strval($refundedSale);
			$refunddetails->refund_id = $refundedSale->getId();
			$refunddetails->added_at = time();

			$refunddetails->save();

			// Update sub order status and order status (if applicable)
			$suborder->sub_order_status = OrderStatus::_REFUNDED;
			$suborder->save();

			$suborders = SubOrder::find()->where("order_id=".$order->id)->all();
			$safe = true;

			foreach($suborders as $row)
			{
				if($row->sub_order_status != OrderStatus::_REFUNDED && $row->sub_order_status != OrderStatus::_CANCELED)
				{
					$safe = false;
					break;
				}
			}
			
			if($safe)
			{
				$order->order_status = OrderStatus::_REFUNDED;
				$order->save();
			}
		}
		catch (Exception $e)
		{
			Yii::$app->session->setFlash('error', 'Your refund is successful but we enountered some issue with order. Please contact customer support for further assistance! Error message:'.$e->getMessage());
		}

		return 0;
	}

	public static function issueRazorpayRefund($suborder)
	{
		$api = new Api(Yii::$app->params['RAZORPAY_API_KEY'], MulteModel::multecrypt(Yii::$app->params['RAZORPAY_SECRET_KEY'], 'd'));

		$order = Order::findOne($suborder->order_id);

		$razorpay_details = RazorpayDetails::findOne(['order_id' => $order->id, 'payment_confirmed' => 1]);		

		if($suborder->sub_order_status != OrderStatus::_RETURNED && $suborder->sub_order_status != OrderStatus::_CANCELED)
		{
			Yii::$app->session->setFlash('error', 'Refund for current order not allowed!');
			return 0;
		}

		$refund_amount = round($suborder->total_cost*100, 2);

		try 
		{
			$refund = $api->refund->create(array('payment_id' => $razorpay_details->razorpay_payment_id, 'amount'=>$refund_amount));

			Yii::$app->session->setFlash('success', 'Refund Successful! Please note down your refund reference number: '.$refund['id']);
		}
		catch (Exception $e) 
		{
			Yii::$app->session->setFlash('error', 'Razorpay refund failed with error: '.$e->getMessage());
			return 0;
		}
		
		try
		{
			// Capture refund details in system
			$refunddetails = new RazorpayRefundDetails;
			$refunddetails->order_id = $order->id;
			$refunddetails->sub_order_id = $suborder->id;
			$refunddetails->amount = $suborder->total_cost; // valid only for INR
			$refunddetails->result_json = Json::encode($refund);
			$refunddetails->refund_id = $refund['id'];
			$refunddetails->added_at = time();

			$refunddetails->save();

			// Update sub order status and order status (if applicable)
			$suborder->sub_order_status = OrderStatus::_REFUNDED;
			$suborder->save();

			$suborders = SubOrder::find()->where("order_id=".$order->id)->all();
			$safe = true;

			foreach($suborders as $row)
			{
				if($row->sub_order_status != OrderStatus::_REFUNDED && $row->sub_order_status != OrderStatus::_CANCELED)
				{
					$safe = false;
					break;
				}
			}
			
			if($safe)
			{
				$order->order_status = OrderStatus::_REFUNDED;
				$order->save();
			}
		}
		catch (Exception $e)
		{
			Yii::$app->session->setFlash('error', 'Your refund is successful but we enountered some issue with order. Please contact customer support for further assistance! Error message:'.$e->getMessage());
		}

		return 0;
	}

	public static function releaseAndRefund($suborder)
	{
		// Release inventory stock
		MulteModel::releaseInventoryStock($suborder);

		// Release Coupon Discount amount
		MulteModel::releaseCouponDiscount($suborder);

		// Release Global Discount Amouont
		MulteModel::releaseGlobalDiscount($suborder);

		if ($suborder->payment_method != PaymentMethods::_COD)
		{
			// Process refund
			if(($suborder->payment_method == PaymentMethods::_PAYPAL))
			{
				MulteModel::issuePaypalRefund($suborder);
			}
			else
			if(($suborder->payment_method == PaymentMethods::_STRIPE))
			{
				MulteModel::issueStripeRefund($suborder);
			}
			else
			if(($suborder->payment_method == PaymentMethods::_RAZORPAY))
			{
				MulteModel::issueRazorpayRefund($suborder);
			}
		}
	}

	public static function getTimezoneList()
	{
		static $regions = array(
			\DateTimeZone::AFRICA,
			\DateTimeZone::AMERICA,
			\DateTimeZone::ANTARCTICA,
			\DateTimeZone::ASIA,
			\DateTimeZone::ATLANTIC,
			\DateTimeZone::AUSTRALIA,
			\DateTimeZone::EUROPE,
			\DateTimeZone::INDIAN,
			\DateTimeZone::PACIFIC,
		);

		$timezones = array();
		foreach( $regions as $region )
		{
			$timezones = array_merge( $timezones, \DateTimeZone::listIdentifiers( $region ) );
		}

		$timezone_offsets = array();
		foreach( $timezones as $timezone )
		{
			$tz = new \DateTimeZone($timezone);
			$timezone_offsets[$timezone] = $tz->getOffset(new \DateTime);
		}

		// sort timezone by offset
		asort($timezone_offsets);

		$timezone_list = array();
		foreach( $timezone_offsets as $timezone => $offset )
		{
			$offset_prefix = $offset < 0 ? '-' : '+';
			$offset_formatted = gmdate( 'H:i', abs($offset) );

			$pretty_offset = "UTC${offset_prefix}${offset_formatted}";

			$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
		}

		return $timezone_list;
	}

	public static function readInputSheet($inventory_id, $inputFileName)
	{
		try
		{
			//$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);

			/**  Create a new Reader of the type that has been identified  **/
			//$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

			/**  Load $inputFileName to a Spreadsheet Object  **/
			$spreadsheet = $reader->load($inputFileName);
			
			$sheetData = $spreadsheet->getSheet(0)->toArray();
			//print_r($sheetData);exit;
			$count = 0;
			foreach ($sheetData as $row)
			{
				$license_model = new LicenseKeyCode;
				$license_model->inventory_id = $inventory_id;
				$license_model->license_key_code = $row[0];
				$license_model->used = 0;
				$license_model->save();
				$count++;
			}

			return $count;
		}
		catch (\Exception $e)
		{
			throw new \Exception(Yii::t('app', 'Error reading input file.').' '.Yii::t('app', 'Make sure it is an excel sheet in xlsx format.').' '.Yii::t('app', 'All data need to be in 1st column of 1st sheet of workbook.').' '.Yii::t('app', 'There should be one row for each record!'));
		}
	}

	public static function getPendingTicketCountLabel()
	{
		return TicketModel::find()->where("user_assigned_id=".Yii::$app->user->identity->id." and (ticket_status='".TicketStatus::_NEEDSACTION."' or ticket_status='".TicketStatus::_INPROCESS."' or ticket_status='".TicketStatus::_REOPENED."')")->count();
	}

	public static function saveFileToServer($file, $new_file_name, $folder, $private = false)
	{
		if(Yii::$app->params['aws'])
		{
			try
			{
				$s3Client = new S3Client([
					'region' => Yii::$app->params['aws_region'],
					'version' => 'latest',
					'credentials' => [
						'key'    => Yii::$app->params['aws_user_key'],
						'secret' => Yii::$app->params['aws_user_secret'],
					],
				]);

				$result = $s3Client->putObject([
					'Bucket'     => Yii::$app->params['aws_s3_bucket'],
					'Key'        => $folder.'/'.$new_file_name,
					'SourceFile' => $file,
					'ACL'		 => $private?'private':'public-read',
				]);

				Yii::$app->session->setFlash('success', Yii::t('app', 'File Uploaded Successfully!'));
				return 1;
			}
			catch (S3Exception $e)
			{
				Yii::$app->session->setFlash('error', Yii::t('app', 'File Upload Failed!').$e->getMessage());
				return 0;
			}
		}
		else if(Yii::$app->params['ftp'])
		{
			$dataFile      = $file;
			$protocol      = Yii::$app->params['ftp_protocol'];
			$sftpServer    = Yii::$app->params['ftp_url'];
			$sftpUsername  = Yii::$app->params['ftp_user'];
			$sftpPassword  = Yii::$app->params['ftp_password'];
			$sftpPort      = Yii::$app->params['ftp_port'];
			$sftpRemoteDir = "/".$folder;
			 
			$ch = curl_init($protocol."://".$sftpServer . ':' . $sftpPort . $sftpRemoteDir . "/".$new_file_name);
			 
			$fh = fopen($dataFile, 'r');
			 
			if ($fh) {
				curl_setopt($ch, CURLOPT_USERPWD, $sftpUsername . ':' . $sftpPassword);
				curl_setopt($ch, CURLOPT_UPLOAD, true);
				curl_setopt($ch, CURLOPT_FTP_CREATE_MISSING_DIRS, true);
				if($protocol == 'sftp')
				{
					curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
				}
				curl_setopt($ch, CURLOPT_INFILE, $fh);
				curl_setopt($ch, CURLOPT_INFILESIZE, filesize($dataFile));
				curl_setopt($ch, CURLOPT_VERBOSE, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
			 
				$verbose = fopen('php://temp', 'w+');
				curl_setopt($ch, CURLOPT_STDERR, $verbose);
			 
				$response = curl_exec($ch);
				$error = curl_error($ch);
				curl_close($ch);
			 
				if ($response) {
					Yii::$app->session->setFlash('success', Yii::t('app', 'File Uploaded Successfully!'));
					return 1;
				} else {
					rewind($verbose);
					$verboseLog = stream_get_contents($verbose);
					Yii::$app->session->setFlash('error', Yii::t('app', 'File Upload Failed!'));
					return 0;
				}
			}
			else
			{
				return 0;
			}
		}
		else	// Local Storage
		{
			if(!file_exists($folder))
			{
				mkdir($folder, 0777, true);
			}

			if(@copy($file, $folder.'/'.$new_file_name))
			{
				Yii::$app->session->setFlash('success', Yii::t('app', 'File Uploaded Successfully!'));
				return 1;
			}
			else
			{
				Yii::$app->session->setFlash('error', Yii::t('app', 'File Upload Failed!'));
				return 0;
			}
		}
	}

	public static function getFileFromServer($file_name, $remote_folder, $temp_file)
	{
		if(Yii::$app->params['aws'])
		{
			try
			{
				$s3Client = new S3Client([
					'region' => Yii::$app->params['aws_region'],
					'version' => 'latest',
					'credentials' => [
						'key'    => Yii::$app->params['aws_user_key'],
						'secret' => Yii::$app->params['aws_user_secret'],
					],
				]);

				$result = $s3Client->getObject([
					'Bucket' => Yii::$app->params['aws_s3_bucket'],
					'Key'    => $remote_folder.'/'.$file_name,
					'SaveAs' => $temp_file
				]);

				return 1;
			}
			catch (S3Exception $e)
			{
				//Yii::$app->session->setFlash('error', Yii::t('app', 'File Retrieval Failed!').$e->getMessage());
				return 0;
			}
		}
		else if(Yii::$app->params['ftp'])
		{
			$protocol      = Yii::$app->params['ftp_protocol'];
			$sftpServer    = Yii::$app->params['ftp_url'];
			$sftpUsername  = Yii::$app->params['ftp_user'];
			$sftpPassword  = Yii::$app->params['ftp_password'];
			$sftpPort      = Yii::$app->params['ftp_port'];
			 
			$ch = curl_init($protocol."://".$sftpUsername . ':' . $sftpPassword . '@' . $sftpServer . ':' . $sftpPort . '/' . $remote_folder . '/' . $file_name);

			//$c = curl_init("sftp://$user:$pass@someserver.com/$filename");

			$fh = fopen($temp_file, 'w');
			
			if($protocol == 'sftp')
			{
				curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
			}

			curl_setopt($ch, CURLOPT_FILE, $fh);

			$response = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);
		 
			if ($response) 
			{
				return 1;
			}
			else
			{
				//print_r($error);exit;
				return 0;
			}
		}
		else	// Local Storage
		{
			return @copy ('../../multeback/web/'.$remote_folder.'/'.$file_name, $temp_file);
		}
	}

	public static function deleteFileFromServer($file_name, $remote_folder)
	{
		if(Yii::$app->params['aws'])
		{
			try
			{
				$s3Client = new S3Client([
					'region' => Yii::$app->params['aws_region'],
					'version' => 'latest',
					'credentials' => [
						'key'    => Yii::$app->params['aws_user_key'],
						'secret' => Yii::$app->params['aws_user_secret'],
					],
				]);

				$result = $s3Client->deleteObject([
					'Bucket' => Yii::$app->params['aws_s3_bucket'],
					'Key' => $remote_folder.'/'.$file_name,
				]);

				return 1;
			}
			catch (S3Exception $e)
			{
				//Yii::$app->session->setFlash('error', Yii::t('app', 'File Deletion Failed!').$e->getMessage());
				return 0;
			}
		}
		else if(Yii::$app->params['ftp'])
		{
			$protocol      = Yii::$app->params['ftp_protocol'];
			$sftpServer    = Yii::$app->params['ftp_url'];
			$sftpUsername  = Yii::$app->params['ftp_user'];
			$sftpPassword  = Yii::$app->params['ftp_password'];
			$sftpPort      = Yii::$app->params['ftp_port'];

			$ch = curl_init();
			//curl_setopt($ch, CURLOPT_URL, "sftp://xdocsUser:test123@192.168.5.229");
			curl_setopt($ch, CURLOPT_URL, $protocol. "://" . $sftpUsername . ':' . $sftpPassword . '@' . $sftpServer . ':' . $sftpPort);
			
			if($protocol == 'sftp')
			{
				curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
			}

			curl_setopt($ch, CURLOPT_USERPWD, "$sftpUsername:$sftpPassword");
			if($protocol == 'sftp')
			{
				curl_setopt($ch, CURLOPT_QUOTE, array('RM /' . $remote_folder.'/'.$file_name));
			}
			else
			{
				curl_setopt($ch, CURLOPT_QUOTE, array('DELE /' . $remote_folder.'/'.$file_name));
			}
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);
		 
			if ($response) 
			{
				return 1;
			}
			else
			{
				//print_r($error);exit;
				return 0;
			}
		}
		else // Local Storage
		{
			return @unlink ('../../multeback/web/'.$remote_folder.'/'.$file_name);			
		}
	}

	public static function fileExists($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_exec($ch);
		$retCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if($retCode == '200')
			return true;
		else
			return false;
	}

	public static function initiateInventoryCreateTemplate()
	{
		/* Begin initiate Inventory list */
		$alphalist = range("A", "Z");

		$max_col_count = count($alphalist);
		$max_row_count = 1000000;

		$alpha_index = 0;
		$row_index = 1;

		$spreadsheet_inventory = new Spreadsheet();

		$myWorkSheet = new Worksheet($spreadsheet_inventory, 'Data');
		$spreadsheet_inventory->addSheet($myWorkSheet, 1);

		$inventory_sheet = $spreadsheet_inventory->setActiveSheetIndex(1);

		$attribute_values = ProductAttributeValues::find()->all();

		foreach ($attribute_values as $list)
		{
			$start_range = $alphalist[$alpha_index].$row_index;
			$list_array = Json::decode($list->values);
			if(count($list_array) > ($max_row_count - $row_index))
			{
				$alpha_index++;
				if($alpha_index > $max_col_count)
				{
					throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many columns!')); 
				}
				$row_index = 1;
			}
			$columnArray = array_chunk($list_array, 1);
			$spreadsheet_inventory->getActiveSheet()->fromArray($columnArray,	NULL, $alphalist[$alpha_index].$row_index);

			$row_index += count($list_array);

			$end_range = $alphalist[$alpha_index].($row_index-1);
			$spreadsheet_inventory->addNamedRange( new NamedRange('ATTRIBUTELIST'.$list->id, $spreadsheet_inventory->getActiveSheet(), $start_range.':'.$end_range) );
		}

		$yesnolist = ["Yes", "No"];

		if(count($yesnolist) > $max_row_count)
		{
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many rows!')); 
		}

		if(count($yesnolist) > ($max_row_count - $row_index))
		{
			$alpha_index++;
			if($alpha_index > $max_col_count)
			{
				throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many columns!')); 
			}
			$row_index = 1;
		}

		$start_range = $alphalist[$alpha_index].$row_index;
		foreach ($yesnolist as $row)
		{
			$inventory_sheet->setCellValue ($alphalist[$alpha_index].$row_index, $row);

			$row_index++;
		}
		$end_range = $alphalist[$alpha_index].($row_index-1);

		$spreadsheet_inventory->addNamedRange( new NamedRange('YESNOLIST', $spreadsheet_inventory->getActiveSheet(), $start_range.':'.$end_range) );

		$discounttypelist = ["Flat", "Percent"];

		if(count($discounttypelist) > $max_row_count)
		{
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many rows!')); 
		}

		if(count($discounttypelist) > ($max_row_count - $row_index))
		{
			$alpha_index++;
			if($alpha_index > $max_col_count)
			{
				throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many columns!')); 
			}
			$row_index = 1;
		}

		$start_range = $alphalist[$alpha_index].$row_index;
		foreach ($discounttypelist as $row)
		{
			$inventory_sheet->setCellValue ($alphalist[$alpha_index].$row_index, $row);

			$row_index++;
		}
		$end_range = $alphalist[$alpha_index].($row_index-1);

		$spreadsheet_inventory->addNamedRange( new NamedRange('DISCOUNTTYPELIST', $spreadsheet_inventory->getActiveSheet(), $start_range.':'.$end_range) );

		///////

		$pricetypelist = ["Flat", "Base"];

		if(count($pricetypelist) > $max_row_count)
		{
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many rows!')); 
		}

		if(count($pricetypelist) > ($max_row_count - $row_index))
		{
			$alpha_index++;
			if($alpha_index > $max_col_count)
			{
				throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many columns!')); 
			}
			$row_index = 1;
		}

		$start_range = $alphalist[$alpha_index].$row_index;
		foreach ($pricetypelist as $row)
		{
			$inventory_sheet->setCellValue ($alphalist[$alpha_index].$row_index, $row);

			$row_index++;
		}
		$end_range = $alphalist[$alpha_index].($row_index-1);

		$spreadsheet_inventory->addNamedRange( new NamedRange('PRICETYPELIST', $spreadsheet_inventory->getActiveSheet(), $start_range.':'.$end_range) );


		///////

		$spreadsheet_inventory->getActiveSheet()->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

		$inventory_sheet = $spreadsheet_inventory->setActiveSheetIndex(0);

		$inventory_sheet->setCellValue ('A1', 'PRODUCT-ID');
		$inventory_sheet->setCellValue ('B1', 'CATEGORY');
		$inventory_sheet->setCellValue ('C1', 'SUB-CATEGORY');
		$inventory_sheet->setCellValue ('D1', 'SUB-SUB-CATEGORY');
		$inventory_sheet->setCellValue ('E1', 'PRODUCT-NAME');
		$inventory_sheet->setCellValue ('F1', 'STOCK');
		$inventory_sheet->setCellValue ('G1', 'PRICE-TYPE');
		$inventory_sheet->setCellValue ('H1', 'PRICE');
		$inventory_sheet->setCellValue ('I1', 'DISCOUNT-TYPE');
		$inventory_sheet->setCellValue ('J1', 'DISCOUNT');
		$inventory_sheet->setCellValue ('K1', 'SHIPPING-COST');
		$inventory_sheet->setCellValue ('L1', 'ENABLE-SLAB-DISCOUNT');
		$inventory_sheet->setCellValue ('M1', 'SLAB-DISCOUNT-TYPE');
		$inventory_sheet->setCellValue ('N1', 'SLAB-1-RANGE');
		$inventory_sheet->setCellValue ('O1', 'SLAB-1-DISCOUNT');
		$inventory_sheet->setCellValue ('P1', 'SLAB-2-RANGE');
		$inventory_sheet->setCellValue ('Q1', 'SLAB-2-DISCOUNT');
		$inventory_sheet->setCellValue ('R1', 'SLAB-3-RANGE');
		$inventory_sheet->setCellValue ('S1', 'SLAB-3-DISCOUNT');
		$inventory_sheet->setCellValue ('T1', 'SLAB-4-RANGE');
		$inventory_sheet->setCellValue ('U1', 'SLAB-4-DISCOUNT');
		$inventory_sheet->setCellValue ('V1', 'SEARCH-TAGS');

		$inventory_sheet->setCellValue ('W1', 'LENGTH');
		$inventory_sheet->setCellValue ('X1', 'WIDTH');
		$inventory_sheet->setCellValue ('Y1', 'HEIGHT');
		$inventory_sheet->setCellValue ('Z1', 'WEIGHT');
		$inventory_sheet->setCellValue ('AA1', 'WARRANTY');
		
		$inventory_row_index = 2;
		/* End Inventory Sheet */

		return $spreadsheet_inventory;
	}

	public static function writeInventoryCreateTemplateRecord($spreadsheet_inventory, $product, $sheet_index, $inventory_row_index)
	{
		$inventory_sheet = $spreadsheet_inventory->setActiveSheetIndex($sheet_index);

		//Create file for inventory creation
		$inventory_sheet->setCellValue ('A'.$inventory_row_index, $product->id);
		$validation = $spreadsheet_inventory->getActiveSheet()->getCell('A'.$inventory_row_index)->getDataValidation();
		$validation->setErrorStyle(DataValidation::STYLE_WARNING );
		$validation->setAllowBlank(false);
		$validation->setShowInputMessage(true);
		$validation->setPrompt(Yii::t('app', 'Please Do not change this value!'));

		$inventory_sheet->setCellValue ('B'.$inventory_row_index, ProductCategory::findOne($product->category_id)->name);
		$inventory_sheet->setCellValue ('C'.$inventory_row_index, ProductSubCategory::findOne($product->sub_category_id)->name);
		$inventory_sheet->setCellValue ('D'.$inventory_row_index, ProductSubSubCategory::findOne($product->sub_subcategory_id)->name);
		$inventory_sheet->setCellValue ('E'.$inventory_row_index, $product->name);

		$validation = $spreadsheet_inventory->getActiveSheet()->getCell('G'.$inventory_row_index)->getDataValidation();
		$validation->setType(DataValidation::TYPE_LIST );
		$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
		$validation->setAllowBlank(false);
		$validation->setShowDropDown(true);
		$validation->setFormula1('=PRICETYPELIST');

		$validation = $spreadsheet_inventory->getActiveSheet()->getCell('I'.$inventory_row_index)->getDataValidation();
		$validation->setType(DataValidation::TYPE_LIST );
		$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
		$validation->setAllowBlank(false);
		$validation->setShowDropDown(true);
		$validation->setFormula1('=DISCOUNTTYPELIST');

		$validation = $spreadsheet_inventory->getActiveSheet()->getCell('L'.$inventory_row_index)->getDataValidation();
		$validation->setType(DataValidation::TYPE_LIST );
		$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
		$validation->setAllowBlank(false);
		$validation->setShowDropDown(true);
		$validation->setFormula1('=YESNOLIST');

		$validation = $spreadsheet_inventory->getActiveSheet()->getCell('M'.$inventory_row_index)->getDataValidation();
		$validation->setType(DataValidation::TYPE_LIST );
		$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
		$validation->setAllowBlank(false);
		$validation->setShowDropDown(true);
		$validation->setFormula1('=DISCOUNTTYPELIST');

		$validation = $spreadsheet_inventory->getActiveSheet()->getCell('V'.$inventory_row_index)->getDataValidation();
		$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
		$validation->setAllowBlank(false);
		$validation->setShowInputMessage(true);
		$validation->setPrompt(Yii::t('app', 'Enter comma separated tags'));

		$invalphalist = ['AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ','AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE'];
		$invalpha_index = 0;

		$product_attributes = ProductAttributes::find()->where(['parent_id' => $product->sub_subcategory_id])->all();

		if($product_attributes)
		{
			$start_alpha = $invalpha_index;
			foreach ($product_attributes as $attribute_row)
			{
				if($attribute_row->fixed == 1)
				{
					$validation = $spreadsheet_inventory->getActiveSheet()->getCell($invalphalist[$invalpha_index].$inventory_row_index)->getDataValidation();
					$validation->setType(DataValidation::TYPE_LIST );
					$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
					$validation->setAllowBlank(false);
					$validation->setShowDropDown(true);
					$validation->setShowInputMessage(true);
					$validation->setPromptTitle(Yii::t('app', 'Select Attribute Name'));
					$validation->setPrompt($attribute_row->name);
					$validation->setFormula1('=ATTRIBUTELIST'.$attribute_row->fixed_id);
				}
				else
				{
					$validation = $spreadsheet_inventory->getActiveSheet()->getCell($invalphalist[$invalpha_index].$inventory_row_index)->getDataValidation();
					$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
					$validation->setAllowBlank(false);
					$validation->setShowInputMessage(true);
					$validation->setPromptTitle(Yii::t('app', 'Fill Attribute Name'));
					$validation->setPrompt($attribute_row->name);
				}

				$invalpha_index++;

				$validation = $spreadsheet_inventory->getActiveSheet()->getCell($invalphalist[$invalpha_index].$inventory_row_index)->getDataValidation();
				$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
				$validation->setAllowBlank(false);
				$validation->setShowInputMessage(true);
				$validation->setPromptTitle(Yii::t('app', 'Fill Attribute Price'));
				$validation->setPrompt($attribute_row->name);

				$invalpha_index++;
			}
			$end_alpha = $invalpha_index-1;

			$spreadsheet_inventory->getActiveSheet()
							->getStyle($invalphalist[$start_alpha].$inventory_row_index.':'.$invalphalist[$end_alpha].$inventory_row_index)
							->getFill()
							->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
							->getStartColor()->setARGB('FFFF0000');
		}
		
		return $spreadsheet_inventory;
	}

	/* Update */
	public static function initiateInventoryUpdateTemplate()
	{
		/* Begin initiate Inventory list */
		$alphalist = range("A", "Z");

		$max_col_count = count($alphalist);
		$max_row_count = 1000000;

		$alpha_index = 0;
		$row_index = 1;

		$spreadsheet_inventory = new Spreadsheet();

		$myWorkSheet = new Worksheet($spreadsheet_inventory, 'Data');
		$spreadsheet_inventory->addSheet($myWorkSheet, 1);

		$inventory_sheet = $spreadsheet_inventory->setActiveSheetIndex(1);

		$yesnolist = ["Yes", "No"];

		if(count($yesnolist) > $max_row_count)
		{
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many rows!')); 
		}

		if(count($yesnolist) > ($max_row_count - $row_index))
		{
			$alpha_index++;
			if($alpha_index > $max_col_count)
			{
				throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many columns!')); 
			}
			$row_index = 1;
		}

		$start_range = $alphalist[$alpha_index].$row_index;
		foreach ($yesnolist as $row)
		{
			$inventory_sheet->setCellValue ($alphalist[$alpha_index].$row_index, $row);

			$row_index++;
		}
		$end_range = $alphalist[$alpha_index].($row_index-1);

		$spreadsheet_inventory->addNamedRange( new NamedRange('YESNOLIST', $spreadsheet_inventory->getActiveSheet(), $start_range.':'.$end_range) );

		$discounttypelist = ["Flat", "Percent"];

		if(count($discounttypelist) > $max_row_count)
		{
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many rows!')); 
		}

		if(count($discounttypelist) > ($max_row_count - $row_index))
		{
			$alpha_index++;
			if($alpha_index > $max_col_count)
			{
				throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many columns!')); 
			}
			$row_index = 1;
		}

		$start_range = $alphalist[$alpha_index].$row_index;
		foreach ($discounttypelist as $row)
		{
			$inventory_sheet->setCellValue ($alphalist[$alpha_index].$row_index, $row);

			$row_index++;
		}
		$end_range = $alphalist[$alpha_index].($row_index-1);

		$spreadsheet_inventory->addNamedRange( new NamedRange('DISCOUNTTYPELIST', $spreadsheet_inventory->getActiveSheet(), $start_range.':'.$end_range) );

		///////

		$pricetypelist = ["Flat", "Base"];

		if(count($pricetypelist) > $max_row_count)
		{
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many rows!')); 
		}

		if(count($pricetypelist) > ($max_row_count - $row_index))
		{
			$alpha_index++;
			if($alpha_index > $max_col_count)
			{
				throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many columns!')); 
			}
			$row_index = 1;
		}

		$start_range = $alphalist[$alpha_index].$row_index;
		foreach ($pricetypelist as $row)
		{
			$inventory_sheet->setCellValue ($alphalist[$alpha_index].$row_index, $row);

			$row_index++;
		}
		$end_range = $alphalist[$alpha_index].($row_index-1);

		$spreadsheet_inventory->addNamedRange( new NamedRange('PRICETYPELIST', $spreadsheet_inventory->getActiveSheet(), $start_range.':'.$end_range) );


		///////

		$spreadsheet_inventory->getActiveSheet()->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

		$inventory_sheet = $spreadsheet_inventory->setActiveSheetIndex(0);
		
		$inventory_sheet->setCellValue ('A1', 'INVENTORY-ID');
		$inventory_sheet->setCellValue ('B1', 'PRODUCT-ID');
		$inventory_sheet->setCellValue ('C1', 'CATEGORY');
		$inventory_sheet->setCellValue ('D1', 'SUB-CATEGORY');
		$inventory_sheet->setCellValue ('E1', 'SUB-SUB-CATEGORY');
		$inventory_sheet->setCellValue ('F1', 'PRODUCT-NAME');
		$inventory_sheet->setCellValue ('G1', 'STOCK');
		$inventory_sheet->setCellValue ('H1', 'PRICE-TYPE');
		$inventory_sheet->setCellValue ('I1', 'PRICE');
		$inventory_sheet->setCellValue ('J1', 'DISCOUNT-TYPE');
		$inventory_sheet->setCellValue ('K1', 'DISCOUNT');
		$inventory_sheet->setCellValue ('L1', 'SHIPPING-COST');
		$inventory_sheet->setCellValue ('M1', 'ENABLE-SLAB-DISCOUNT');
		$inventory_sheet->setCellValue ('N1', 'SLAB-DISCOUNT-TYPE');
		$inventory_sheet->setCellValue ('O1', 'SLAB-1-RANGE');
		$inventory_sheet->setCellValue ('P1', 'SLAB-1-DISCOUNT');
		$inventory_sheet->setCellValue ('Q1', 'SLAB-2-RANGE');
		$inventory_sheet->setCellValue ('R1', 'SLAB-2-DISCOUNT');
		$inventory_sheet->setCellValue ('S1', 'SLAB-3-RANGE');
		$inventory_sheet->setCellValue ('T1', 'SLAB-3-DISCOUNT');
		$inventory_sheet->setCellValue ('U1', 'SLAB-4-RANGE');
		$inventory_sheet->setCellValue ('V1', 'SLAB-4-DISCOUNT');
		$inventory_sheet->setCellValue ('W1', 'SEARCH-TAGS');

		$inventory_sheet->setCellValue ('X1', 'LENGTH');
		$inventory_sheet->setCellValue ('Y1', 'WIDTH');
		$inventory_sheet->setCellValue ('Z1', 'HEIGHT');
		$inventory_sheet->setCellValue ('AA1', 'WEIGHT');
		$inventory_sheet->setCellValue ('AB1', 'WARRANTY');
		
		$inventory_row_index = 2;
		/* End Inventory Sheet */

		return $spreadsheet_inventory;
	}

	public static function writeInventoryUpdateTemplateRecord($spreadsheet_inventory, $inventory, $sheet_index, $inventory_row_index)
	{
		$inventory_sheet = $spreadsheet_inventory->setActiveSheetIndex($sheet_index);

		//Create file for inventory creation
		$inventory_sheet->setCellValue ('A'.$inventory_row_index, $inventory->id);
		$validation = $spreadsheet_inventory->getActiveSheet()->getCell('A'.$inventory_row_index)->getDataValidation();
		$validation->setErrorStyle(DataValidation::STYLE_WARNING );
		$validation->setAllowBlank(false);
		$validation->setShowInputMessage(true);
		$validation->setPrompt(Yii::t('app', 'Please Do not change this value!'));

		$inventory_sheet->setCellValue ('B'.$inventory_row_index, $inventory->product_id);

		$inventory_sheet->setCellValue ('C'.$inventory_row_index, ProductCategory::findOne($inventory->product->category_id)->name);
		$inventory_sheet->setCellValue ('D'.$inventory_row_index, ProductSubCategory::findOne($inventory->product->sub_category_id)->name);
		$inventory_sheet->setCellValue ('E'.$inventory_row_index, ProductSubSubCategory::findOne($inventory->product->sub_subcategory_id)->name);
		$inventory_sheet->setCellValue ('F'.$inventory_row_index, $inventory->product_name);

		$inventory_sheet->setCellValue ('G'.$inventory_row_index, $inventory->stock);

		$inventory_sheet->setCellValue ('H'.$inventory_row_index, $inventory->price_type=='F'?'Flat':'Base');

		$validation = $spreadsheet_inventory->getActiveSheet()->getCell('H'.$inventory_row_index)->getDataValidation();
		$validation->setType(DataValidation::TYPE_LIST );
		$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
		$validation->setAllowBlank(false);
		$validation->setShowDropDown(true);
		$validation->setFormula1('=PRICETYPELIST');

		$inventory_sheet->setCellValue ('I'.$inventory_row_index, $inventory->price);
		
		$inventory_sheet->setCellValue ('J'.$inventory_row_index, $inventory->price_type=='F'?'Flat':'Percent');

		$validation = $spreadsheet_inventory->getActiveSheet()->getCell('J'.$inventory_row_index)->getDataValidation();
		$validation->setType(DataValidation::TYPE_LIST );
		$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
		$validation->setAllowBlank(false);
		$validation->setShowDropDown(true);
		$validation->setFormula1('=DISCOUNTTYPELIST');

		$inventory_sheet->setCellValue ('K'.$inventory_row_index, $inventory->discount);
		$inventory_sheet->setCellValue ('L'.$inventory_row_index, $inventory->shipping_cost);

		$inventory_sheet->setCellValue ('M'.$inventory_row_index, $inventory->slab_discount_ind=='1'?'Yes':'No');

		$validation = $spreadsheet_inventory->getActiveSheet()->getCell('M'.$inventory_row_index)->getDataValidation();
		$validation->setType(DataValidation::TYPE_LIST );
		$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
		$validation->setAllowBlank(false);
		$validation->setShowDropDown(true);
		$validation->setFormula1('=YESNOLIST');

		$inventory_sheet->setCellValue ('N'.$inventory_row_index, $inventory->price_type=='F'?'Flat':'Percent');

		$validation = $spreadsheet_inventory->getActiveSheet()->getCell('N'.$inventory_row_index)->getDataValidation();
		$validation->setType(DataValidation::TYPE_LIST );
		$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
		$validation->setAllowBlank(false);
		$validation->setShowDropDown(true);
		$validation->setFormula1('=DISCOUNTTYPELIST');

		$inventory_sheet->setCellValue ('O'.$inventory_row_index, $inventory->slab_1_range);
		$inventory_sheet->setCellValue ('P'.$inventory_row_index, $inventory->slab_1_discount);

		$inventory_sheet->setCellValue ('Q'.$inventory_row_index, $inventory->slab_2_range);
		$inventory_sheet->setCellValue ('R'.$inventory_row_index, $inventory->slab_2_discount);

		$inventory_sheet->setCellValue ('S'.$inventory_row_index, $inventory->slab_3_range);
		$inventory_sheet->setCellValue ('T'.$inventory_row_index, $inventory->slab_3_discount);

		$inventory_sheet->setCellValue ('U'.$inventory_row_index, $inventory->slab_4_range);
		$inventory_sheet->setCellValue ('V'.$inventory_row_index, $inventory->slab_4_discount);
		
		$connection = \Yii::$app->db;
		$query = "select a.tag from tbl_tags a, tbl_inventory_tags b
						where b.inventory_id = ".$inventory->id."
						and a.id = b.tag_id;";
		$mdl = $connection->createCommand($query);

		$result = $mdl->queryAll();
		$tags = '';

		foreach ($result as $row)
		{
			$tags = $tags.$row['tag'].",";
		}
		$tags = substr($tags, 0, -1);
		
		$inventory_sheet->setCellValue ('W'.$inventory_row_index, $tags);

		$validation = $spreadsheet_inventory->getActiveSheet()->getCell('W'.$inventory_row_index)->getDataValidation();
		$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
		$validation->setAllowBlank(false);
		$validation->setShowInputMessage(true);
		$validation->setPrompt(Yii::t('app', 'Enter comma separated tags'));

		$invalphalist = ['AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ','AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF'];
		$invalpha_index = 0;
		
		if($inventory->attribute_values)
		{
			$product_attributes = Json::decode($inventory->attribute_values);
			$product_attributes_price = Json::decode($inventory->attribute_price);
		}

		if($product_attributes)
		{
			$start_alpha = $invalpha_index;
			//foreach ($product_attributes as $attribute_name)
			for ($i = 0; $i < count($product_attributes); $i++)
			{
				$attribute_name = $product_attributes[$i];
				$attribute_price = $product_attributes_price[$i];

				$inventory_sheet->setCellValue ($invalphalist[$invalpha_index].$inventory_row_index, $attribute_name);

				$validation = $spreadsheet_inventory->getActiveSheet()->getCell($invalphalist[$invalpha_index].$inventory_row_index)->getDataValidation();
				$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
				$validation->setAllowBlank(false);
				$validation->setShowInputMessage(true);
				$validation->setPrompt(Yii::t('app', 'Do not change this value!'));

				$invalpha_index++;

				$inventory_sheet->setCellValue ($invalphalist[$invalpha_index].$inventory_row_index, $attribute_price);

				$validation = $spreadsheet_inventory->getActiveSheet()->getCell($invalphalist[$invalpha_index].$inventory_row_index)->getDataValidation();
				$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
				$validation->setAllowBlank(false);
				$validation->setShowInputMessage(true);
				$validation->setPrompt(Yii::t('app', 'Change Attribute Price If Needed'));
				$validation->setPromptTitle($attribute_name);

				$invalpha_index++;
			}

		}
		
		return $spreadsheet_inventory;
	}

	public static function getAndAddFileToProduct($url, $product_id)
	{
		try
		{
			$temp_name = uniqid($product_id).time();
			$temp_file = "temp/".$temp_name;
			$ch = curl_init ($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
			$raw=curl_exec($ch);
			curl_close ($ch);
			if(file_exists($temp_file)){
				unlink($temp_file);
			}
			$fp = fopen($temp_file,'x');
			fwrite($fp, $raw);
			fclose($fp);

			$typeInt = exif_imagetype($temp_file);
			switch($typeInt) {
			  case IMAGETYPE_GIF:
				$typeString = 'image/gif';
				$extension = 'gif';
				break;
			  case IMAGETYPE_JPG:
				$typeString = 'image/jpg';
				$extension = 'jpg';
				break;
			  case IMAGETYPE_JPEG:
				$typeString = 'image/jpeg';
				$extension = 'jpeg';
				break;
			  case IMAGETYPE_PNG:
				$typeString = 'image/png';
				$extension = 'png';
				break;
			  default: 
				throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Unsupported image file!')); 
			}

			$addFile= new File();
			$addFile->entity_id=$product_id;
			$addFile->entity_type='product';
			$addFile->file_title='Image';
			$addFile->file_name=$temp_name.".".$extension;
			$addFile->file_path='multeback/web/attachments';
			$addFile->file_type=$typeString;
			$addFile->added_by_user_id =Yii::$app->user->identity->id;
			$addFile->added_at=time();
			if(!$addFile->save())
			{
				throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Failed to save file!')); 
			}
			$aid=$addFile->id;

			if(!MulteModel::saveFileToServer($temp_file, $aid.".".$extension, Yii::$app->params['web_folder']))
			{
				throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Failed to save file!')); 
			}
			else
			{
				$addFile->new_file_name=$aid.".".$extension;
				if(!$addFile->update())
				{
					throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Failed to save file!')); 
				}
			}
		}
		catch (\Exception $e)
		{
			unlink($temp_file);
			throw $e;
		}
	}

	public static function getAndAddFileToBrand($url, $brand_id)
	{
		try
		{
			$temp_name = uniqid($brand_id).time();
			$temp_file = "temp/".$temp_name;
			$ch = curl_init ($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
			$raw=curl_exec($ch);
			curl_close ($ch);
			if(file_exists($temp_file)){
				unlink($temp_file);
			}
			$fp = fopen($temp_file,'x');
			fwrite($fp, $raw);
			fclose($fp);

			$typeInt = exif_imagetype($temp_file);
			switch($typeInt) {
			  case IMG_GIF:
				$typeString = 'image/gif';
				$extension = 'gif';
				break;
			  case IMG_JPG:
				$typeString = 'image/jpg';
				$extension = 'jpg';
				break;
			  case IMG_JPEG:
				$typeString = 'image/jpeg';
				$extension = 'jpeg';
				break;
			  case IMG_PNG:
				$typeString = 'image/png';
				$extension = 'png';
				break;
			  default: 
				throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Unsupported image file!')); 
			}

			$brand = ProductBrand::fineOne($brand_id);
			$brand->brand_image=$temp_name.".".$extension;
			$brand->brand_new_image=$temp_name.".".$extension;

			if(!$brand->save())
			{
				throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Failed to save file!')); 
			}

			if(!MulteModel::saveFileToServer($temp_file, $temp_name.".".$extension, Yii::$app->params['web_folder']."/brand"))
			{
				throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Failed to save file!')); 
			}
		}
		catch (\Exception $e)
		{
			unlink($temp_file);
			throw $e;
		}
	}

	public static function getDistinctProdVendor ($inventories, $max_count = 3)
	{
		$full_list = [];
		$count == 0;

		foreach ($inventories as $row)
		{
			if($count == $max_count)
				break;

			if($count == 0)
			{
				$record = [];

				$record['product_id'] = $row->product_id;
				$record['vendor_id'] = $row->vendor_id;
				array_push($full_list, $record);
				$count++;
				continue;
			}
			
			for($i = 0; $i < $count; $i++)
			{
				if($full_list[$i]['product_id'].$full_list[$i]['vendor_id'] != $row->product_id.$row->vendor_id)
				{
					$record = [];

					$record['product_id'] = $row->product_id;
					$record['vendor_id'] = $row->vendor_id;
					array_push($full_list, $record);
					$count++;
					break;
				}
			}
		}
//echo "<pre>";
//print_r($full_list);exit;
		return $full_list;
	}

	public static function resizeImage ($original, $new, $maxWidth=250, $maxHeight=290)
	{
		$img = new ImageUpload();

		list($width, $height, $type, $attr) = getimagesize($original); 

		$ratio = min($maxHeight / $height, $maxWidth / $width); 
		$newHeight = ceil($height * $ratio); 
		$newWidth = ceil($width * $ratio);

		$img->loadImage($original)->resize($newWidth, $newHeight)->saveImage($new);
	}
}