<?php
use yii\helpers\Url;
use yii\helpers\Html;
use multebox\models\Wishlist;
use multebox\models\Inventory;
use multebox\models\File;
use multebox\models\search\MulteModel;
use yii\helpers\Json;

include_once('../web/cart_script.php');
?>
<!-- Breadcrumb Start-->
<ul class="breadcrumb">
	<li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
	<li><?=Yii::t('app', 'Wishlist')?></li>
</ul>
<!-- Breadcrumb End-->

  <!-- Main Container -->
  <section class="main-container col2-right-layout">
    <div class="main container">
      <div class="row">
        <div class="col-main col-sm-12 col-xs-12">
          <div class="my-account">
            <div class="page-title">
              <h2><?=Yii::t('app', 'My Wishlist')?></h2>
            </div>
            <div class="wishlist-item table-responsive">
              <table class="col-md-12">
                <thead>
                  <tr>
                    <th class="th-delate"><?=Yii::t('app', 'Remove')?></th>
                    <th class="th-product"><?=Yii::t('app', 'Images')?></th>
                    <th class="th-details" style="text-align:left"><?=Yii::t('app', 'Product Name')?></th>
                    <th class="th-price"><?=Yii::t('app', 'Unit Price')?></th>
                    <th class="th-total th-add-to-cart"><?=Yii::t('app', 'Add to Cart')?> </th>
                  </tr>
                </thead>
                <tbody>
				<?php
				$wishlist = Wishlist::find()->where(['customer_id' => Yii::$app->user->identity->entity_id])->all();

				if(!$wishlist)
				{
				?>
					<tr>
						<td colspan="5"><h2><?=Yii::t('app', 'You do not have any wishlist items!')?></h2></td>
					</tr>
				<?php
				}
				else
				foreach($wishlist as $wishrow)
				{
				  $inventoryItem = Inventory::findOne($wishrow->inventory_id);
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

				  if($inventoryDiscount == 0)
				  {
					  $displayPrice = $inventoryPrice;
				  }
				  else
				  {
					  $displayPrice = $inventoryDiscountedPrice;
				  }
				?>
                  <tr>
                    <!--<td class="th-delate"><a href="<?=Url::to(['/site/delete-wishlist', 'id' => $wishrow->id])?>">X</a></td>-->
					<td class="th-delate"><?=Html::a('<span class="glyphicon glyphicon-remove"></span>',
							 Yii::$app->urlManager->createUrl(['/site/delete-wishlist', 'id' => $wishrow->id]),
								['title' => Yii::t('app', 'Delete From Wishlist'), 'data-confirm' => Yii::t('app', 'Are you sure you want to remove this item from wishlist?'),]
							)?></td>
                    <td class="th-product"><a href="<?=$url?>"><img class="wishlist-img" src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"></a></td>
                    <td class="th-details"><h2><a href="<?=$url?>"><?=$inventoryItem->product_name?></a></h2></td>
                    <td class="th-price"><?=MulteModel::formatAmount($displayPrice)?></td>
                    <th class="td-add-to-cart"><button class="btn btn-primary wish-add-to-cart" value="<?=$wishrow->inventory_id?>"> <?=Yii::t('app', 'Add to Cart')?></button></th>
                  </tr>
				<?php
				}
				?>
                </tbody>
              </table>
              <!--<a href="checkout.html" class="all-cart"><?=Yii::t('app', 'Add All To Cart')?></a>--> </div>
          </div>
        </div>

      </div>
    </div>
  </section>
  