<?php
use multebox\models\Inventory;
use multebox\models\File;
use yii\helpers\Url;
?>

<input type="hidden" class="hiddencartvalue" value="<?=$total_items?>">
<input type="hidden" class="hiddenremainingstock" value="<?=$remaining_stock?>">

<?php

foreach($cart_items as $cart)
{
	$inventory_item = Inventory::findOne($cart->inventory_id);
	$prod_title = $inventory_item->product_name;
	$fileDetails = File::find()->where("entity_type='product' and entity_id=".$inventory_item->product_id)->one();
?>
  <li class="item odd"> <a href="<?=Url::to(['/product/default/detail', 'inventory_id' => $cart->inventory_id])?>" class="product-image"><img src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$prod_title?>" title="<?=$prod_title?>" class="cart-img"></a>
	<div class="product-details"> 
	  <!--<a href="#" title="Remove This Item" class="remove-cart"><i class="icon-close"></i></a>-->
	  <p class="product-name"><a href="<?=Url::to(['/product/default/detail', 'inventory_id' => $cart->inventory_id])?>"><?=$prod_title?></a> </p>
	  <strong> x </strong><span class="price"><?=$cart->total_items?></span> </div>
  </li>
<?php
}

?>