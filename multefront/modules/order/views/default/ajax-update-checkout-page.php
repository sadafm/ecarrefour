<?php
use multebox\models\Inventory;
use multebox\models\Cart;
use multebox\models\File;
use multebox\models\Vendor;
use multebox\models\search\MulteModel;
use yii\helpers\Url;

if(Yii::$app->user->isGuest)
{
	$cart_items = Cart::find()->where("session_id='".session_id()."'")->all();
}
else
{
	$cart_items = Cart::find()->where("user_id=".Yii::$app->user->identity->id)->all();
}
?>

	<?php
	$total_cart_price = 0;
	foreach($cart_items as $cart)
	{
		$inventory_item = Inventory::findOne($cart->inventory_id);
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
		<td class="text-right"><?=MulteModel::formatAmount(MulteModel::getInventoryActualPrice($inventory_item) - MulteModel::getInventoryDiscountAmount($inventory_item, $cart->total_items))?></td>
		<td class="text-right"><?=MulteModel::formatAmount($inventory_item->shipping_cost*$cart->total_items)?></td>
		<td class="text-right"><?=MulteModel::formatAmount($cart->global_discount_temp)?></td>
		<td class="text-right"><?=MulteModel::formatAmount($cart->coupon_discount_temp)?></td>
		<td class="text-right"><?=MulteModel::formatAmount(MulteModel::getInventoryTotalAmount($inventory_item, $cart->total_items)*$cart->total_items - $cart->global_discount_temp - $cart->coupon_discount_temp)?></td>

		<?php
		$total_cart_price += MulteModel::getInventoryTotalAmount($inventory_item, $cart->total_items)*$cart->total_items;
		?>
	  </tr>
	<?php
	}
	?>
	<?php
	$global_discount = MulteModel::getGlobalDiscount($cart_items, 0); // 0 since this is before order confirmation
	
	if ($global_discount > 0)
	{
	?>
	  <tr>
	    <input type="hidden" name="special_discount" value="<?=$global_discount?>">
		<td class="text-right" colspan="7"><strong><?=Yii::t('app', 'Total Special Discount')?>:</strong></td>
		<td class="text-right"><?=MulteModel::formatAmount($global_discount)?></td>
	  </tr>
	<?php
	}
	
	if ($coupon_discount > 0)
	{
	?>
	  <tr>
	    <input type="hidden" name="coupon_discount" value="<?=$coupon_discount?>">
		<td class="text-right" colspan="7"><strong><?=Yii::t('app', 'Total Coupon Discount')?> (<?=$discount_coupon?>):</strong></td>
		<td class="text-right"><?=MulteModel::formatAmount($coupon_discount)?></td>
	  </tr>
	<?php
	}
	?>
	  <tr>
	    <input type="hidden" name="total_cost" value="<?=$total_cart_price - $global_discount?>">
		<td class="text-right" colspan=7><strong><?=Yii::t('app', 'Total Cart Price')?>:</strong></td>
		<td class="text-right"><?=MulteModel::formatAmount($total_cart_price - $global_discount - $coupon_discount)?></td>
	  </tr>

