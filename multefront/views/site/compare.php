<?php
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\Html;
use multebox\models\Comparison;
use multebox\models\Inventory;
use multebox\models\ProductBrand;
use multebox\models\Product;
use multebox\models\File;
use multebox\models\search\MulteModel;

include_once('../web/cart_script.php');

function getItemDetails($inventory_id)
{
	$inventoryItem = Inventory::findOne($inventory_id);
	$inventoryPrice = floatval($inventoryItem->price);
	if($inventoryItem->price_type == 'B')
	{
	  if($inventoryItem->attribute_price)
	  foreach(Json::decode($inventoryItem->attribute_price) as $row)
	  {
		  $inventoryPrice += floatval($row);
	  }
	}

	if($inventoryItem->discount_type == 'P')
	$inventoryDiscount = $inventoryItem->discount;
	else
	{
	if($inventoryPrice > 0)
		$inventoryDiscount = round((floatval($inventoryItem->discount)/$inventoryPrice)*100,2);
	else
		$inventoryDiscount = 0;
	}

	$inventoryDiscountedPrice = round($inventoryPrice - $inventoryPrice*$inventoryDiscount/100, 2);

	$fileDetails = File::find()->where("entity_type='product' and entity_id=$inventoryItem->product_id")->one();

	$url = Url::to(['/product/default/detail', 'inventory_id' => $inventoryItem->id]);
	
	$details['inventory_item'] = $inventoryItem;
	$details['regular_price'] = $inventoryPrice;
	$details['discounted_price'] = $inventoryDiscountedPrice;
	$details['file_details'] = $fileDetails;
	$details['url'] = $url;
	$details['brand'] = ProductBrand::findOne(Product::findOne($inventoryItem->product_id)->brand_id)->name;

	return $details;
}
?>
<!-- Breadcrumb Start-->
<ul class="breadcrumb">
	<li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
	<li><?=Yii::t('app', 'Comparison List')?></li>
</ul>
<!-- Breadcrumb End-->

 <!-- Main Container --> 
  <section class="main-container col1-layout">
    <div class="main container">
      <div class="col-main">
        <div class="compare-list">
          <div class="page-title">
            <h2><?=Yii::t('app', 'Compare Products')?></h2>
          </div>

		  <?php
			if(Yii::$app->user->isGuest)
			{
				$comparelist = Comparison::findOne(['session_id' => session_id()]);
			}
			else
			{
				$comparelist = Comparison::findOne(['customer_id' => Yii::$app->user->identity->entity_id]);
			}

			$inventory_list = Json::decode($comparelist->inventory_list);

			if($inventory_list[0])
			{
				$inv1 = getItemDetails($inventory_list[0]);
			}

			if($inventory_list[1])
			{
				$inv2 = getItemDetails($inventory_list[1]);
			}

			if($inventory_list[2])
			{
				$inv3 = getItemDetails($inventory_list[2]);
			}

			if($inventory_list[3])
			{
				$inv4 = getItemDetails($inventory_list[3]);
			}
		  ?>

          <div class="table-responsive">
   
              <table class="table table-bordered table-compare">
                <tr>
                  <td class="compare-label"><?=Yii::t('app', 'Product Image')?></td>
                  <td width="20%">
				  <?php
				  if($inv1)
				  {
				  ?>
					<a href="<?=$inv1['url']?>"><img src="<?=Yii::$app->params['web_url']?>/<?=$inv1['file_details']->new_file_name?>" class="compare-img" alt="<?=$inv1['inventory_item']->product_name?>"></a>
				  <?php
				  }
				  ?>
				  </td>
                  <td width="20%">
				  <?php
				  if($inv2)
				  {
				  ?>
					<a href="<?=$inv2['url']?>"><img src="<?=Yii::$app->params['web_url']?>/<?=$inv2['file_details']->new_file_name?>" class="compare-img" alt="<?=$inv2['inventory_item']->product_name?>"></a>
				  <?php
				  }
				  ?>
				  </td>
                  <td width="20%">
				  <?php
				  if($inv3)
				  {
				  ?>
					<a href="<?=$inv3['url']?>"><img src="<?=Yii::$app->params['web_url']?>/<?=$inv3['file_details']->new_file_name?>" class="compare-img" alt="<?=$inv3['inventory_item']->product_name?>"></a>
				  <?php
				  }
				  ?>
				  </td>
                  <td width="20%">
				  <?php
				  if($inv4)
				  {
				  ?>
					<a href="<?=$inv4['url']?>"><img src="<?=Yii::$app->params['web_url']?>/<?=$inv4['file_details']->new_file_name?>" class="compare-img" alt="<?=$inv4['inventory_item']->product_name?>"></a>
				  <?php
				  }
				  ?>
				  </td>
                </tr>
                <tr>
                  <td class="compare-label"><?=Yii::t('app', 'Product Name')?></td>
                  <td><a href="<?=$inv1['url']?>"><?=$inv1['inventory_item']->product_name?></a></td>
                  <td><a href="<?=$inv2['url']?>"><?=$inv2['inventory_item']->product_name?></a></td>
                  <td><a href="<?=$inv3['url']?>"><?=$inv3['inventory_item']->product_name?></a></td>
                  <td><a href="<?=$inv4['url']?>"><?=$inv4['inventory_item']->product_name?></a></td>
                </tr>
                <tr>
                  <td class="compare-label"><?=Yii::t('app', 'Rating')?></td>
                  <td>
				  <?php
				  if($inv1)
				  {
				  ?>
					<div class="rating"> <input type="text" class="multe-rating-nocap-sm" value="<?=$inv1['inventory_item']->product_rating?>" readonly> </div>
				  <?php
				  }
				  ?>
				  </td>
				  <td>
				  <?php
				  if($inv2)
				  {
				  ?>
					<div class="rating"> <input type="text" class="multe-rating-nocap-sm" value="<?=$inv2['inventory_item']->product_rating?>" readonly> </div>
				  <?php
				  }
				  ?>
				  </td>
				  <td>
				  <?php
				  if($inv3)
				  {
				  ?>
					<div class="rating"> <input type="text" class="multe-rating-nocap-sm" value="<?=$inv3['inventory_item']->product_rating?>" readonly> </div>
				  <?php
				  }
				  ?>
				  </td>
				  <td>
				  <?php
				  if($inv4)
				  {
				  ?>
					<div class="rating"> <input type="text" class="multe-rating-nocap-sm" value="<?=$inv4['inventory_item']->product_rating?>" readonly> </div>
				  <?php
				  }
				  ?>
				  </td>
                </tr>
                <tr>
                  <td class="compare-label"><?=Yii::t('app', 'Regular Price')?></td>
                  <td class="price">
				  <?php
				  if($inv1)
				  {
				  ?>
					<?=MulteModel::formatAmount($inv1['regular_price'])?>
				  <?php
				  }
				  ?>
				  </td>
				  <td class="price">
				  <?php
				  if($inv2)
				  {
				  ?>
					<?=MulteModel::formatAmount($inv2['regular_price'])?>
				  <?php
				  }
				  ?>
				  </td>
				  <td class="price">
				  <?php
				  if($inv3)
				  {
				  ?>
					<?=MulteModel::formatAmount($inv3['regular_price'])?>
				  <?php
				  }
				  ?>
				  </td>
				  <td class="price">
				  <?php
				  if($inv4)
				  {
				  ?>
					<?=MulteModel::formatAmount($inv4['regular_price'])?>
				  <?php
				  }
				  ?>
				  </td>
                </tr>
				<tr>
                  <td class="compare-label"><?=Yii::t('app', 'Discounted Price')?></td>
                  <td class="price">
				  <?php
				  if($inv1)
				  {
				  ?>
					<?=MulteModel::formatAmount($inv1['discounted_price'])?>
				  <?php
				  }
				  ?>
				  </td>
				  <td class="price">
				  <?php
				  if($inv2)
				  {
				  ?>
					<?=MulteModel::formatAmount($inv2['discounted_price'])?>
				  <?php
				  }
				  ?>
				  </td>
				  <td class="price">
				  <?php
				  if($inv3)
				  {
				  ?>
					<?=MulteModel::formatAmount($inv3['discounted_price'])?>
				  <?php
				  }
				  ?>
				  </td>
				  <td class="price">
				  <?php
				  if($inv4)
				  {
				  ?>
					<?=MulteModel::formatAmount($inv4['discounted_price'])?>
				  <?php
				  }
				  ?>
				  </td>
                </tr>
                <tr>
                  <td class="compare-label"><?=Yii::t('app', 'Brand')?></td>
                  <td><?=$inv1['brand']?></td>
                  <td><?=$inv2['brand']?></td>
                  <td><?=$inv3['brand']?></td>
                  <td><?=$inv4['brand']?></td>
                </tr>
                <tr>
                  <td class="compare-label"><?=Yii::t('app', 'Availability')?></td>
                  <td>
				  <?php
				  if($inv1)
				  {
					if($inv1['inventory_item']->stock > 0)
					{
						echo "In Stock";
					}
					else
				    {
						echo "Out of Stock";
				    }
				  }
				  ?>
				  </td>
				  <td>
				  <?php
				  if($inv2)
				  {
					if($inv2['inventory_item']->stock > 0)
					{
						echo "In Stock";
					}
					else
				    {
						echo "Out of Stock";
				    }
				  }
				  ?>
				  </td>
				  <td>
				  <?php
				  if($inv3)
				  {
					if($inv3['inventory_item']->stock > 0)
					{
						echo "In Stock";
					}
					else
				    {
						echo "Out of Stock";
				    }
				  }
				  ?>
				  </td>
				  <td>
				  <?php
				  if($inv4)
				  {
					if($inv4['inventory_item']->stock > 0)
					{
						echo "In Stock";
					}
					else
				    {
						echo "Out of Stock";
				    }
				  }
				  ?>
				  </td>
                </tr>
               
                <tr>
                  <td class="compare-label"><?=Yii::t('app', 'Properties')?></td>
                  <td><?=$inv1['inventory_item']->attribute_values?></td>
                  <td><?=$inv2['inventory_item']->attribute_values?></td>
                  <td><?=$inv3['inventory_item']->attribute_values?></td>
                  <td><?=$inv4['inventory_item']->attribute_values?></td>
                </tr>
                <tr>
                  <td class="compare-label"><?=Yii::t('app', 'Action')?></td>
                  <td class="action" style="text-align:center">
				  <?php
				  if($inv1)
				  {
				  ?>
					<button class="button button-sm compare-add-to-cart" value="<?=$inv1['inventory_item']->id?>"><i class="fa fa-shopping-cart"></i></button>
                    <a href="<?=Url::to(['/site/add-to-wishlist', 'id' => $inv1['inventory_item']->id])?>" class="my-button button-sm"><i class="fa fa-heart"></i></a>
                    <a href="<?=Url::to(['/site/delete-compare', 'id' => $inv1['inventory_item']->id])?>" class="my-button button-sm"><i class="fa fa-close"></i></a>
				  <?php
				  }
				  ?>
				  </td>

				  <td class="action" style="text-align:center">
				  <?php
				  if($inv2)
				  {
				  ?>
					<button class="button button-sm compare-add-to-cart" value="<?=$inv2['inventory_item']->id?>"><i class="fa fa-shopping-cart"></i></button>
                    <a href="<?=Url::to(['/site/add-to-wishlist', 'id' => $inv2['inventory_item']->id])?>" class="my-button button-sm"><i class="fa fa-heart"></i></a>
                    <a href="<?=Url::to(['/site/delete-compare', 'id' => $inv2['inventory_item']->id])?>" class="my-button button-sm"><i class="fa fa-close"></i></a>
				  <?php
				  }
				  ?>
				  </td>

				  <td class="action" style="text-align:center">
				  <?php
				  if($inv3)
				  {
				  ?>
					<button class="button button-sm compare-add-to-cart" value="<?=$inv3['inventory_item']->id?>"><i class="fa fa-shopping-cart"></i></button>
                    <a href="<?=Url::to(['/site/add-to-wishlist', 'id' => $inv3['inventory_item']->id])?>" class="my-button button-sm"><i class="fa fa-heart"></i></a>
                    <a href="<?=Url::to(['/site/delete-compare', 'id' => $inv3['inventory_item']->id])?>" class="my-button button-sm"><i class="fa fa-close"></i></a>
				  <?php
				  }
				  ?>
				  </td>

				  <td class="action" style="text-align:center">
				  <?php
				  if($inv4)
				  {
				  ?>
					<button class="button button-sm compare-add-to-cart" value="<?=$inv4['inventory_item']->id?>"><i class="fa fa-shopping-cart"></i></button>
                    <a href="<?=Url::to(['/site/add-to-wishlist', 'id' => $inv4['inventory_item']->id])?>" class="my-button button-sm"><i class="fa fa-heart"></i></a>
                    <a href="<?=Url::to(['/site/delete-compare', 'id' => $inv4['inventory_item']->id])?>" class="my-button button-sm"><i class="fa fa-close"></i></a>
				  <?php
				  }
				  ?>
				  </td>
                </tr>
              </table>
    
          </div>
        </div>
      </div>
    </div>
  </section>