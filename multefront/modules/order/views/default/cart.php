<?php
use multebox\models\Cart;
use multebox\models\Inventory;
use multebox\models\File;
use multebox\models\Vendor;
use multebox\models\search\MulteModel;
use yii\helpers\Url;
?>

<script type="text/javascript" src="<?=Url::base()?>/js/jquery-2.1.1.min.js"></script>
<script>
$(".top-cart").hide();

$(document).on("click", '.cartrefresh', function(event)
{
	$('.tooltip-inner').remove();
	$('.tooltip-arrow').remove();

	var new_count = $(this).closest('div').find('input').val();
	var cart_id = $(this).val();

	$.post("<?=Url::to(['/order/default/ajax-update-cart'])?>", { 'cart_id': cart_id, 'new_count' : new_count, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
				//alert(result);
				$('.cartcontents tbody').html(result);
			})
	
	$('body').tooltip({
		selector: '[data-toggle="tooltip"]'
	});
});

$(document).on("click", '.cartremove', function(event)
{
	if (confirm('Are you sure!'))
	{
		$('.tooltip-inner').remove();
		$('.tooltip-arrow').remove();

		var cart_id = $(this).val();
		$.post("<?=Url::to(['/order/default/ajax-update-cart'])?>", { 'cart_id': cart_id, 'new_count' : 0, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					//alert(result);
					$('.cartcontents tbody').html(result);
				})

		$('body').tooltip({
			selector: '[data-toggle="tooltip"]'
		});
	}
});

</script>

<section class="main-container col1-layout">
<div class="main container">
<div class="site-error">
    
      <!-- Breadcrumb Start-->
      <ul class="breadcrumb">
        <li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
        <li><?=Yii::t('app', 'Shopping Cart')?></li>
      </ul>
      <!-- Breadcrumb End-->
      <div class="row">
        <!--Middle Part Start-->
        <div id="content" class="col-sm-12">
		  <?php
		  if(!$cart_items)
		  {
		  ?>
			<div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
					<td><h1 class="title"><?=Yii::t('app', 'Your cart is empty - Why not take a look at huge number of wonderful products available!')?></h1></td>
				  </tr>
				</thead>
			  </table>
			</div>
			<div class="buttons">
            <div class="pull-left"><a href="<?=Url::to(['/site/index'])?>" class="btn btn-default"><?=Yii::t('app', 'Continue Shopping')?></a></div>
          </div>
		  <?php
		  }
		  else
		  {
		  ?>
		   <h1 class="title"><?=Yii::t('app', 'Shopping Cart')?></h1>
            <div class="table-responsive">
              <table class="table table-bordered cartcontents">
                <thead>
                  <tr>
                    <td class="text-center" style="width:20%;"><?=Yii::t('app', 'Image')?></td>
                    <td class="text-left"><?=Yii::t('app', 'Product Name')?></td>
                    <td class="text-left"><?=Yii::t('app', 'Quantity')?></td>
                    <td class="text-right"><?=Yii::t('app', 'Unit Price')?></td>
                    <td class="text-right"><?=Yii::t('app', 'Shipping')?></td>
                    <td class="text-right"><?=Yii::t('app', 'Total')?></td>
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
                </tbody>
              </table>
            </div>
          <div class="buttons">
            <div class="pull-left"><a href="<?=Url::to(['/site/index'])?>" class="btn btn-default"><?=Yii::t('app', 'Continue Shopping')?></a></div>
            <div class="pull-right"><a href="<?=Url::to(['/order/default/checkout'])?>" class="btn btn-primary"><?=Yii::t('app', 'Checkout')?></a></div>
          </div>
		  <?php
		  }
		  ?>
        </div>
        <!--Middle Part End -->
      </div>
    
</div>
</div>
</section>