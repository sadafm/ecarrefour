<?php
use multebox\models\Inventory;
use multebox\models\File;
use multebox\models\Vendor;
use multebox\models\search\MulteModel;
use yii\helpers\Url;
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
	  <div class="input-group btn-block quantity">
		<input type="text" value="<?=$cart->total_items?>" size="1" class="form-control itemquantity" />
		<span class="input-group-btn">
		  <button type="button" data-toggle="tooltip" title="<?=Yii::t('app', 'Update')?>" value="<?=$cart->id?>" class="btn btn-yellow cartrefresh"><i class="fa fa-refresh"></i></button>
		  <button type="button" data-toggle="tooltip" title="<?=Yii::t('app', 'Remove')?>" value="<?=$cart->id?>" class="btn btn-red cartremove" onClick=""><i class="fa fa-times-circle"></i></button>
		</span>
	  </div>
	</td>
	<td class="text-right"><?=MulteModel::formatAmount(MulteModel::getInventoryActualPrice($inventory_item) - MulteModel::getInventoryDiscountAmount($inventory_item, $cart->total_items))?></td>
	<td class="text-right"><?=MulteModel::formatAmount($inventory_item->shipping_cost*$cart->total_items)?></td>
	<td class="text-right"><?=MulteModel::formatAmount(MulteModel::getInventoryTotalAmount($inventory_item, $cart->total_items)*$cart->total_items)?></td>

	<?php
	$total_cart_price += MulteModel::getInventoryTotalAmount($inventory_item, $cart->total_items)*$cart->total_items;
	?>
  </tr>
<?php
}
?>
  <tr>
	<td class="text-right" colspan=5><?=Yii::t('app', 'Total Cart Price')?>:</td>
	<td class="text-right"><?=MulteModel::formatAmount($total_cart_price)?></td>
  </tr>