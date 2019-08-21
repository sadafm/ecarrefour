<?php
use multebox\models\Order;
use multebox\models\OrderStatus;
use multebox\models\City;
use multebox\models\State;
use multebox\models\Country;
use multebox\models\SubOrder;
use multebox\models\PaymentMethods;
use multebox\models\search\MulteModel;
use multebox\models\Inventory;
use multebox\models\DigitalRecords;
use multebox\models\File;
use multebox\models\Vendor;
use multebox\models\Cart;
use multebox\models\ShippingDetail;
use multebox\models\LicenseKeyCode;
use yii\helpers\Url;
use yii\helpers\Json;

?>

<script type="text/javascript" src="<?=Url::base()?>/js/jquery-2.1.1.min.js"></script>
<script>
/*$(document).on("click", '#cancelitem', function(event)
{
	if (confirm('Are you sure You want to cancel this item from the order?'))
	{
		$('.tooltip-inner').remove();
		$('.tooltip-arrow').remove();

		var sub_order_id = $(this).val();
		$.post("<?=Url::to(['/order/default/cancel-sub-order'])?>", { 'sub_order_id': sub_order_id, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					//alert(result);
					$('.orderitems tbody').html(result);
				})

		$('body').tooltip({
			selector: '[data-toggle="tooltip"]'
		});
	}
});*/
</script>

  <div id="container">
    
      <!-- Breadcrumb Start-->
      <ul class="breadcrumb">
        <li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
        <li><a href="<?=Url::to(['/customer/default/account'])?>"><?=Yii::t('app', 'Account')?></a></li>
        <li><a href="<?=Url::to(['/order/default/history'])?>"><?=Yii::t('app', 'Order History')?></a></li>
        <li><?=Yii::t('app', 'Order Information')?></li>
      </ul>
      <!-- Breadcrumb End-->
      <div class="row">
        <!--Middle Part Start-->
        <div id="content" class="col-sm-12">
        <div class="pull-left"><h1 class="title"><?=Yii::t('app', 'Order')?> #<?=$order->id?> - <?=Yii::t('app', $order->status->label)?></h1></div>

		<div class="pull-right">
		<?php
		if($order->order_status == OrderStatus::_NEW && $order->payment_method != PaymentMethods::_COD)
		{
		?>
		<a href="<?=Url::to(['/order/default/payment', 'order_id' => $order->id])?>" class="btn btn-danger"><?=Yii::t('app', 'Retry Payment')?></a>
        <?php
		}
		
		if($order->order_status == OrderStatus::_NEW && $order->payment_method == PaymentMethods::_BITCOIN)
		{
		?>
		<a href="<?=Url::to(['/order/default/execute-bitcoin-payment', 'order_id' => $order->id])?>" class="btn btn-success"><?=Yii::t('app', 'Check Payment Status')?></a>
        <?php
		}
		?>
		</div>

        <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <td colspan="3" class="text-left"><?=Yii::t('app', 'Order Details')?></td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="width: 33%;" class="text-left">
			  <b><?=Yii::t('app', 'Order ID')?>:</b> #<?=$order->id?><br>
              <b><?=Yii::t('app', 'Order Date')?>:</b> <?=date('M d, Y, H:i', $order->added_at)?>
			</td>
            <td style="width: 33%;" class="text-left">
			  <b><?=Yii::t('app', 'Payment Method')?>:</b> <?=Yii::t('app', PaymentMethods::find()->where("method='".$order->payment_method."'")->one()->label)?><br>
              <b><?=Yii::t('app', 'Shipping Method')?>:</b> <?=Yii::t('app', 'Flat Rate Shipping')?>
			</td>
			<td style="width: 33%;" class="text-left">
			<?php
			$address = Json::decode($order->address_snapshot);
			$contact = Json::decode($order->contact_snapshot);
			?>
			  <b><?=Yii::t('app', 'Shipping Address')?></b><br>
			  <?=$contact['first_name']?> <?=$contact['last_name']?><br>
			  <?=$address['address_1']?><br>
			  <?=$address['address_2']?><br>
			  <?=City::findOne($address['city_id'])->city?><br/>
			  <?=State::findOne($address['state_id'])->state?><br/>
			  <?=Country::findOne($address['country_id'])->country?> - <?=$address['zipcode']?><br/>
			  <?=Yii::t('app', 'Phone')?>: <?=$contact['mobile']?><br/>
			</td>
          </tr>
        </tbody>
      </table>
      
      <div class="table-responsive">
        <table class="table table-bordered table-hover orderitems">
          <thead>
            <tr>
              <td class="text-center" style="width:10%;"><?=Yii::t('app', 'Image')?></td>
			  <td class="text-left"><?=Yii::t('app', 'Product Name')?></td>
			  <td class="text-right"><?=Yii::t('app', 'Quantity')?></td>
			  <td class="text-right"><?=Yii::t('app', 'Unit Price')?></td>
			  <td class="text-right"><?=Yii::t('app', 'Shipping')?></td>
			  <td class="text-right"><?=Yii::t('app', 'Special Discount')?></td>
		  	  <td class="text-right"><?=Yii::t('app', 'Coupon Discount')?></td>
		  	  <td class="text-right"><?=Yii::t('app', 'Total')?></td>
			  <td class="text-right"><?=Yii::t('app', 'Status')?></td>
			  <td class="text-left"><?=Yii::t('app', 'Tracking Details')?></td>
              <td style="width: 20px;"></td>
            </tr>
          </thead>
          <tbody>
		  <?php
		  $cart_items = MulteModel::mapJsonArrayToModelArray(Json::decode($order->cart_snapshot), new Cart);
		  foreach($cart_items as $cart)
		  {
			//$cart = MulteModel::mapJsonToModel($cart, new Cart);
			//var_dump($cart);exit;
			$sub_order = SubOrder::find()->where('order_id='.$order->id.' and inventory_id='.$cart->inventory_id)->one();

			//if($sub_order->sub_order_status == OrderStatus::_CANCELED)
				//continue;

			$inventory_json = $sub_order->inventory_snapshot;
			$inventory_item = MulteModel::mapJsonToModel(Json::decode($inventory_json), new Inventory);//Inventory::findOne($cart->inventory_id);
			$prod_title = $inventory_item->product_name;
			$fileDetails = File::find()->where("entity_type='product' and entity_id=".$inventory_item->product_id)->one();
		  ?>
		  <tr>
			<td class="text-center"><a href="<?=Url::to(['/product/default/detail', 'inventory_id' => $cart->inventory_id])?>"><img src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$prod_title?>" title="<?=$prod_title?>" class="checkout-img" /></a></td>
			<td class="text-left"><a href="<?=Url::to(['/product/default/detail', 'inventory_id' => $cart->inventory_id])?>"><?=$prod_title?></a><br />
			  <small><?=Yii::t('app', 'Sold By')?>: <?=Vendor::findOne($inventory_item)->vendor_name?></small></td>
			<td class="text-left">
			  <div class="text-right">
				<?=$cart->total_items?>
			  </div>
			</td>
			<td class="text-right"><?=MulteModel::formatAmount((MulteModel::getInventoryActualPrice($inventory_item) - MulteModel::getInventoryDiscountAmount($inventory_item, $cart->total_items))*$sub_order->conversion_rate, $sub_order->order_currency_symbol)?></td>
			<td class="text-right"><?=MulteModel::formatAmount(($inventory_item->shipping_cost*$cart->total_items)*$sub_order->conversion_rate, $sub_order->order_currency_symbol)?></td>
			<td class="text-right"><?=MulteModel::formatAmount($cart->global_discount_temp*$sub_order->conversion_rate, $sub_order->order_currency_symbol)?></td>
			<td class="text-right"><?=MulteModel::formatAmount($cart->coupon_discount_temp*$sub_order->conversion_rate, $sub_order->order_currency_symbol)?></td>
			<td class="text-right"><?=MulteModel::formatAmount((MulteModel::getInventoryTotalAmount($inventory_item, $cart->total_items)*$cart->total_items - $cart->global_discount_temp - $cart->coupon_discount_temp)*$sub_order->conversion_rate, $sub_order->order_currency_symbol)?></td>

			<?php
			if($sub_order->sub_order_status == OrderStatus::_CANCELED)
			{
				$label_color = 'label-danger';
			}
			else if($sub_order->sub_order_status == OrderStatus::_RETURNED)
			{
				$label_color = 'label-warning';
			}
			else if($sub_order->sub_order_status == OrderStatus::_REFUNDED)
			{
				$label_color = 'label-success';
			}
			else
			{
				$label_color = 'label-info';
			}
			?>
			<td class="text-right"><span class="label <?=$label_color?>"><?=Yii::t('app', $sub_order->status->label)?></span></td>
			<td>
			<?php
			if($sub_order->sub_order_status == OrderStatus::_SHIPPED || $sub_order->sub_order_status == OrderStatus::_DELIVERED)
			{
				$shipmodel = ShippingDetail::findOne(['sub_order_id' => $sub_order->id]);
				if ($shipmodel)
				{
					echo "<b>".Yii::t('app', 'Carrier').":</b> ".$shipmodel->carrier."<br>";
					echo "<b>".Yii::t('app', 'Number').":</b> ".$shipmodel->tracking_number."<br>";
					echo "<b>".Yii::t('app', 'URL').":</b> <a href='".$shipmodel->tracking_url."' target='_blank'>".Yii::t('app', 'Click Here')."</a>";
				}
				else
				{
					echo Yii::t('app', 'No Shipping Information');
				}
			}
			else
			{
				echo Yii::t('app', 'No Shipping Information');
			}
			?>
			</td>
			<?php
			if($sub_order->sub_order_status != OrderStatus::_REFUNDED && $sub_order->sub_order_status != OrderStatus::_CANCELED)
			{
				$total_cart_price += MulteModel::getInventoryTotalAmount($inventory_item, $cart->total_items)*$cart->total_items;
			}
			?>
							  
              <td style="white-space: nowrap;" class="text-center">
				<a href="<?=Url::to(['/order/default/information', 'order_id' => $order->id, 'cancel_id' => $sub_order->id])?>" data-toggle="tooltip" title="<?=Yii::t('app', 'Cancel Item')?>" id="cancelitem" onclick="return confirm('<?=Yii::t ('app','Are you Sure!')?>')"><i class="fa fa-times-circle"></i></a>
                <a href="<?=Url::to(['/order/default/information', 'order_id' => $order->id, 'return_id' => $sub_order->id])?>" data-toggle="tooltip" title="<?=Yii::t('app', 'Return Item')?>" id="returnitem" onclick="return confirm('<?=Yii::t ('app','Are you Sure!')?>')"><i class="fa fa-reply"></i></a>
				<?php
				if($inventory_item->product->digital && $sub_order->sub_order_status == OrderStatus::_DELIVERED)
				{
					if(!$inventory_item->product->license_key_code)
					{
						$digitalrecord = DigitalRecords::find()->where("customer_id=".Yii::$app->user->identity->entity_id." and sub_order_id=".$sub_order->id)->one();
					?>
						<a href="<?=Url::to(['/order/default/download', 'did' => $digitalrecord->id, 'oid' => $sub_order->id, 'token' => $digitalrecord->token])?>" data-toggle="tooltip" title="<?=Yii::t('app', 'Download Item')?>" id="downloaditem"><i class="fa fa-download"></i></a>
					<?php
					}
					else
					{
						$licrec = LicenseKeyCode::find()->where("sub_order_id=".$sub_order->id)->one();
					?>
						<a href="<?=Url::to(['/order/default/get-code', 'lid' => $licrec->id, 'oid' => $sub_order->id])?>" data-toggle="tooltip" title="<?=Yii::t('app', 'Get Code')?>" id="getcode"><i class="fa fa-download"></i></a>
					<?php
					}
				}
				?>
			  </td>
            </tr>
	      <?php
		  }

		  $global_discount = $order->total_site_discount; 
		  $coupon_discount = $order->total_coupon_discount;
							
			if ($global_discount > 0)
			{
			?>
			  <tr>
				<input type="hidden" name="special_discount" value="<?=$global_discount?>">
				<td class="text-right" colspan="10"><strong><?=Yii::t('app', 'Total Special Discount')?>:</strong></td>
				<td class="text-right"><?=MulteModel::formatAmount($global_discount*$order->conversion_rate, $order->order_currency_symbol)?></td>
			  </tr>
			<?php
			}
			
			if ($coupon_discount > 0)
			{
			?>
			  <tr>
				<input type="hidden" name="coupon_discount" value="<?=$coupon_discount?>">
				<td class="text-right" colspan="10"><strong><?=Yii::t('app', 'Total Coupon Discount')?> (<?=Json::decode($order->discount_coupon_snapshot)['coupon_code']?>):</strong></td>
				<td class="text-right"><?=MulteModel::formatAmount($coupon_discount*$order->conversion_rate, $order->order_currency_symbol)?></td>
			  </tr>
			<?php
			}
			?>

			  <tr>
				<input type="hidden" name="coupon_discount" value="0">
				<input type="hidden" name="total_cost" value="<?=$total_cart_price - $global_discount?>">
				<td class="text-right" colspan=10><strong><?=Yii::t('app', 'Total Order Amount')?>:</strong></td>
				<td class="text-right"><?=MulteModel::formatAmount(($total_cart_price - $global_discount - $coupon_discount)*$order->conversion_rate, $order->order_currency_symbol)?></td>
			  </tr>


          </tbody>
        </table>
      </div>
                 
      <div class="buttons clearfix">
        <div class="pull-right"><a class="btn btn-primary" href="<?=Url::to(['/site/index'])?>"><?=Yii::t('app', 'Continue')?></a></div>
      </div>
              
            
               
        </div>
        <!--Middle Part End -->
      </div>
    
  </div>