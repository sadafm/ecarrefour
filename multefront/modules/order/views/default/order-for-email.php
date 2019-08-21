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

<table style="border: 1px solid black; border-spacing: 0;">
	<thead>
	  <tr>
		<td style="text-align:center; border: 1px solid black;"><?=Yii::t('app', 'Image')?></td>
		<td style="text-align:left; border: 1px solid black;"><?=Yii::t('app', 'Product Name')?></td>
		<td style="text-align:right; border: 1px solid black;"><?=Yii::t('app', 'Quantity')?></td>
		<td style="text-align:right; border: 1px solid black;"><?=Yii::t('app', 'Unit Price')?></td>
		<td style="text-align:right; border: 1px solid black;"><?=Yii::t('app', 'Shipping')?></td>
		<td style="text-align:right; border: 1px solid black;"><?=Yii::t('app', 'Special Discount')?></td>
		<td style="text-align:right; border: 1px solid black;"><?=Yii::t('app', 'Coupon Discount')?></td>
		<td style="text-align:right; border: 1px solid black;"><?=Yii::t('app', 'Total')?></td>
	  </tr>
	</thead>
	<tbody>
	<?php
	$total_cart_price = 0;
	foreach($cart_items as $cart)
	{
		$inventory_item = Inventory::findOne($cart->inventory_id);
		$prod_title = $inventory_item->product_name;
		$fileDetails = File::find()->where("entity_type='product' and entity_id=".$inventory_item->product_id)->one();
	?>
	  <tr>
		<td style="text-align:center; border: 1px solid black;"><a href="<?=Url::to(['/product/default/detail', 'inventory_id' => $cart->inventory_id], true)?>"><img src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$prod_title?>" title="<?=$prod_title?>" style="width:50px;" /></a></td>
		<td style="text-align:left; border: 1px solid black;"><a href="<?=Url::to(['/product/default/detail', 'inventory_id' => $cart->inventory_id], true)?>"><?=$prod_title?></a><br />
		  <small><?=Yii::t('app', 'Sold By')?>: <?=Vendor::findOne($inventory_item)->vendor_name?></small></td>
		<td style="text-align:left; border: 1px solid black;">
		  <div style="text-align:right">
			<?=$cart->total_items?>
		  </div>
		</td>
		<td style="text-align:right; border: 1px solid black;"><?=MulteModel::formatAmount(MulteModel::getInventoryActualPrice($inventory_item) - MulteModel::getInventoryDiscountAmount($inventory_item, $cart->total_items))?></td>
		<td style="text-align:right; border: 1px solid black;"><?=MulteModel::formatAmount($inventory_item->shipping_cost*$cart->total_items)?></td>
		<td style="text-align:right; border: 1px solid black;"><?=MulteModel::formatAmount($cart->global_discount_temp)?></td>
		<td style="text-align:right; border: 1px solid black;"><?=MulteModel::formatAmount($cart->coupon_discount_temp)?></td>
		<td style="text-align:right; border: 1px solid black;"><?=MulteModel::formatAmount(MulteModel::getInventoryTotalAmount($inventory_item, $cart->total_items)*$cart->total_items - $cart->global_discount_temp - $cart->coupon_discount_temp)?></td>

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
		<td style="text-align:right; border: 1px solid black;" colspan="7"><strong><?=Yii::t('app', 'Total Special Discount')?>:</strong></td>
		<td style="text-align:right; border: 1px solid black;"><?=MulteModel::formatAmount($global_discount)?></td>
	  </tr>
	<?php
	}
	
	if ($coupon_discount > 0)
	{
	?>
	  <tr>
		<td style="text-align:right; border: 1px solid black;" colspan="7"><strong><?=Yii::t('app', 'Total Coupon Discount')?> (<?=$discount_coupon?>):</strong></td>
		<td style="text-align:right; border: 1px solid black;"><?=MulteModel::formatAmount($coupon_discount)?></td>
	  </tr>
	<?php
	}
	?>
	  <tr>
		<td style="text-align:right; border: 1px solid black;" colspan=7><strong><?=Yii::t('app', 'Total Order Amount')?>:</strong></td>
		<td style="text-align:right; border: 1px solid black;"><?=MulteModel::formatAmount($total_cart_price - $global_discount - $coupon_discount)?></td>
	  </tr>
	</tbody>
</table>
