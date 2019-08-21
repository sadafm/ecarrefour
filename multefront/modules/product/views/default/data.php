<?php
use multebox\models\File;
use multebox\models\Vendor;
use multebox\models\search\MulteModel;
use multebox\models\Inventory;
use multebox\models\ProductCategory;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;

				
//foreach($itemsList as $inventoryItem)

foreach ($itemsList as $plist)
{
	// uncomment below for distinct search and comment one below
	//$inventoryItem = Inventory::find()->where(['product_id' => $plist['product_id'], 'vendor_id' => $plist['vendor_id']])->limit(1)->one();
	$inventoryItem = Inventory::findOne($plist['id']);

	$inventoryPrice = floatval($inventoryItem['price']);
	if($inventoryItem['price_type'] == 'B')
	{
		if($inventoryItem['attribute_price'])
		foreach(Json::decode($inventoryItem['attribute_price']) as $row)
		{
			$inventoryPrice += floatval($row);
		}
	}

	if($inventoryItem['discount_type'] == 'P')
	  $inventoryDiscount = $inventoryItem['discount'];
	else
	{
	  if($inventoryPrice > 0)
		  $inventoryDiscount = round((floatval($inventoryItem['discount'])/$inventoryPrice)*100,2);
	  else
		  $inventoryDiscount = 0;
	}

	$inventoryDiscountedPrice = round($inventoryPrice - $inventoryPrice*$inventoryDiscount/100, 2);

	$fileDetails = File::find()->where("entity_type='product' and entity_id=".$inventoryItem['product_id'])->one();

	$url1 = Url::to(['/product/default/detail', 'inventory_id' => $inventoryItem['id']]);
?>
<li class="item col-lg-3 col-md-4 col-sm-6 col-xs-6 ">
   <div class="product-item">
		  <div class="item-inner">
			<div class="product-thumbnail">
			<?php
			if($inventoryDiscount > 0)
			{
			?>
			  <div class="icon-sale-label sale-left">-<?=$inventoryDiscount?>%</div>
			<?php
			}
			?>
			  <!--<div class="icon-new-label new-right">New</div>-->
			  <div class="pr-img-area"> <a title="<?=$inventoryItem['product_name']?>" href="<?=$url1?>">
				<figure class=""> <img class="first-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem['product_name']?>"> <img class="hover-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem['product_name']?>"></figure>
				</a>
				<?php
				  if($inventoryItem['stock'] > 0)
				  {
				  ?>
				<button type="button" class="add-to-cart-mt addtocart" value="<?=$inventoryItem['id']?>"> <i class="fa fa-shopping-cart"></i><span> <?=Yii::t('app', 'Add to Cart')?></span> </button>
				<?php
				  }
				  else
				  {
				?>
				<button type="button" class="add-to-cart-mt btn-danger" disabled> <i class="fa fa-shopping-cart"></i><span> <?=Yii::t('app', 'Out Of Stock')?></span> </button>
				<?php
				  }
				?>
			  </div>
			  <div class="pr-info-area">
				<div class="pr-button">
				  <div class="mt-button add_to_wishlist"> <a href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Wishlist')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-heart"></i> </a> 
				  <input type="hidden" value="<?=$inventoryItem['id']?>">
				  </div>
				  <div class="mt-button add_to_compare"> <a href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Comparelist')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-signal"></i> </a> 
				  <input type="hidden" class="ajax-data" value="<?=$inventoryItem['id']?>">
				  </div>
				  <div class="mt-button quick-view"> <a href="<?=$url1?>" title="<?=Yii::t('app', 'View Item')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-search"></i> </a> </div>
				</div>
			  </div>
			</div>
			<div class="item-info">
			  <div class="info-inner">
				<div class="item-title"> <a title="<?=$inventoryItem['product_name']?>" href="<?=$url1?>"><?=$inventoryItem['product_name']?> </a> </div>
				<div class="item-content">
				  <div class="rating"> <input type="text" class="multe-rating-nocap-sm" value="<?=$inventoryItem['product_rating']?>" readonly> </div>
				  <div class="item-price">
					<div class="price-box">
					  <?php
					  if($inventoryDiscount == 0)
					  {
					  ?>
					  <span class="regular-price"> <span class="price"><?=MulteModel::formatAmount($inventoryPrice)?></span> </span>
					  <?php
					  }
					  else
					  {
					  ?>
					  <p class="special-price"> <span class="price-label"><?=Yii::t('app', 'Special Price')?></span> <span class="price"> <?=MulteModel::formatAmount($inventoryDiscountedPrice)?> </span> </p>
					  <p class="old-price"> <span class="price-label"><?=Yii::t('app', 'Regular Price')?>:</span> <span class="price"> <?=MulteModel::formatAmount($inventoryPrice)?> </span> </p>
					  <?php
					  }
					  ?>
					</div>
				  </div>
				</div>
			  </div>
			</div>
		  </div>
		</div>
</li>

<?php
}
?>