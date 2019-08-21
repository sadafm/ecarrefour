<?php
use multebox\models\ProductBrand;
use multebox\models\ProductCategory;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use multebox\models\Inventory;
use multebox\models\search\MulteModel;
use multebox\models\Product;
use multebox\models\BannerData;
use multebox\models\BannerType;
use multebox\models\File;
use multebox\models\Testimonial;
use yii\helpers\Url;
use yii\helpers\Json;

include_once('../web/cart_script.php');

function getStyle($index)
{
	$style[0][0] = 'class="tp-caption very_big_white skewfromrightshort fadeout"
                          data-x="center"
                          data-y="100"
                          data-speed="500"
                          data-start="1200"
                          data-easing="Circ.easeInOut"
                          style=" font-size:70px; font-weight:800; color:#000000;"';
	$style[0][1] = 'class="tp-caption tp-caption medium_text skewfromrightshort fadeout"
                          data-x="center"
                          data-y="165"
                          data-hoffset="0"
                          data-voffset="-73"
                          data-speed="500"
                          data-start="1200"
                          data-easing="Power4.easeOut"
                          style=" font-size:20px; font-weight:500; color:#000000;"';
	$style[0][2] = 'class="tp-caption customin tp-resizeme rs-parallaxlevel-0"
                          data-x="center"
                          data-y="210"
                          data-hoffset="0"
                          data-voffset="98" 
                          data-customin="x:0;y:0;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:0;scaleY:0;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:50% 50%;"
                          data-speed="500"
                          data-start="1500"
                          data-easing="Power3.easeInOut"
                          data-splitin="none"
                          data-splitout="none"
                          data-elementdelay="0.1"
                          data-endelementdelay="0.1"
                          data-linktoslide="next"
                          style="z-index: 12; max-width: auto; max-height: auto; white-space: nowrap;"';
	

	$style[1][0] = 'class="tp-caption white_heavy_60 customin ltl tp-resizeme"
                          data-x="310"
                          data-y="140" 
                          data-customin="x:0;y:0;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:0;scaleY:0;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:50% 50%;"
                          data-speed="1200"
                          data-start="700"
                          data-easing="Power4.easeOut"
                          data-splitin="none"
                          data-splitout="none"
                          data-elementdelay="0.1"
                          data-endelementdelay="0.1"
                          data-endspeed="1000"
                          data-endeasing="Power4.easeIn"
                          style=" font-size:70px; font-weight:800; color:#333;"';
	$style[1][1] = 'class="tp-caption black_thin_blackbg_30 customin ltl tp-resizeme"
                          data-x="310"
                          data-y="220" 
                          data-customin="x:0;y:0;z:0;rotationX:90;rotationY:0;rotationZ:0;scaleX:1;scaleY:1;skewX:0;skewY:0;opacity:0;transformPerspective:200;transformOrigin:50% 0%;"
                          data-speed="1500"
                          data-start="1000"
                          data-easing="Power4.easeInOut"
                          data-splitin="none"
                          data-splitout="none"
                          data-elementdelay="0.01"
                          data-endelementdelay="0.1"
                          data-endspeed="1000"
                          data-endeasing="Power4.easeIn"
                          style="z-index: 3; max-width: auto; max-height: auto; white-space: nowrap; color:#34bcec; font-size:20px; font-weight:500;"';
	$style[1][2] = 'class="tp-caption lfb ltb start tp-resizeme"
                          data-x="310"
                          data-y="270"
                          data-customin="x:0;y:0;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:0;scaleY:0;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:50% 50%;"
                          data-speed="1500"
                          data-start="1600"
                          data-easing="Power3.easeInOut"
                          data-splitin="none"
                          data-splitout="none"
                          data-elementdelay="0.01"
                          data-endelementdelay="0.1"
                          data-linktoslide="next"
                          style="z-index: 12; max-width: auto; max-height: auto; white-space: nowrap;"';
	
	$style[2][0] = 'class="tp-caption big_100_white lft start fadeout"
                          data-x="310"
                          data-y="120"
                          data-speed="500"
                          data-start="1200"
                          data-easing="Circ.easeInOut"
                          style=" font-size:70px; font-weight:800; color:#fed700;"';
	$style[2][1] = 'class="tp-caption tp-caption medium_text lfb fadeout"
                          data-x="310"
                          data-y="200"
                          data-speed="500"
                          data-start="1200"
                          data-easing="Power4.easeOut"
                          style="z-index: 3; max-width: auto; max-height: auto; white-space: nowrap; color:#34bcec; font-size:20px; font-weight:500;"';
	$style[2][2] = 'class="tp-caption fade fadeout tp-resizeme"
                          data-x="310"
                          data-y="250"
                          data-hoffset="-100"
                          data-customin="x:0;y:0;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:0;scaleY:0;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:50% 50%;"
                          data-speed="1500"
                          data-start="800"
                          data-easing="Power3.easeInOut"
                          data-splitin="none"
                          data-splitout="none"
                          data-elementdelay="0.01"
                          data-endelementdelay="0.1"
                          data-linktoslide="next"
                          style="z-index: 12; max-width: auto; max-height: auto; white-space: nowrap;"';

	return $style[$index];
}

function getUrl($banner)
{
	if($banner->inventory_id > 0)
	{
	  $url = Url::to(['/product/default/detail', 'inventory_id' => $banner->inventory_id]);
	}
	else
	if($banner->product_id > 0)
	{
	  $url = Url::to(['/product/default/detail', 'attribute_value' => '', 'product_id' => $banner->product_id]);
	}
	else
	if($banner->sub_subcategory_id > 0)
	{
	  $url = Url::to(['/product/default/listing', 'category_id' => $banner->category_id, 'sub_category_id' => $banner->sub_category_id, 'sub_subcategory_id' => $banner->sub_subcategory_id]);
	}
	else
	if($banner->sub_category_id > 0)
	{
	  $url = Url::to(['/product/default/listing', 'category_id' => $banner->category_id, 'sub_category_id' => $banner->sub_category_id]);
	}
	else
	if($banner->category_id > 0)
	{
	  $url = Url::to(['/product/default/listing', 'category_id' => $banner->category_id]);
	}
	else
	{
	  $url = "javascript:void(0)";
	}

	return $url;
}

?>

<!-- Home Slider Start -->
  <div class="slider mainindex">
    <div class="tp-banner-container clearfix">
      <div class="tp-banner" >
        <ul>
          
			<?php
		      $bannerdata = BannerData::find()->where(['banner_type' => BannerType::_SLIDER])->all();
			  $index = 0;
			  foreach ($bannerdata as $banner)
			  {
				  if($index == 3) // As we have configured 3 styles
					  $index = 0;

				  $style = getStyle($index++);

				  $url = getUrl($banner);
			?>
			<!-- SLIDE 1 -->
            <li data-transition="slidehorizontal" data-slotamount="5" data-masterspeed="700" >

				<!-- MAIN IMAGE --> 
				<img src="<?=Yii::$app->params['web_url']?>/banner/<?=$banner->banner_new_name?>" alt="<?=$banner->banner_new_name?>" data-bgfit="cover" data-bgposition="center center" data-bgrepeat="no-repeat"> 
				<!-- LAYERS --> 
				<!-- LAYER NR. 1 -->
				<?php 
				if($banner->text_1 != '')
				{
				?>
				<div <?=$style[0]?>><?=$banner->text_1?></span> </div>
				<?php
				}

				if($banner->text_2 != '')
				{
				?>
				<!-- LAYER NR. 2 -->
				<div <?=$style[1]?>> <?=$banner->text_2?> </div>
				<?php
				}
				
				if($banner->text_3 != '')
				{
				?>
				<!-- LAYER NR. 3 -->
				<div <?=$style[2]?>><a href='<?=$url?>' class='largebtn solid'><?=$banner->text_3?></a> </div>
				<?php
				}
				?>
			</li>
			<?php
			  }
			?>
          
        </ul>
      </div>
    </div>
  </div>


<div class="main-container col1-layout">
    <div class="container">
      <div class="row">


  <!-- Home Tabs  -->
        <div class="col-sm-8 col-md-9 col-xs-12">
          <div class="home-tab">
            <ul class="nav home-nav-tabs home-product-tabs">
              <li class="active"><a href="#featured" data-toggle="tab" aria-expanded="false"><?=Yii::t('app', 'Featured products')?></a></li>
              <li class="divider"></li>
              <li> <a href="#top-sellers" data-toggle="tab" aria-expanded="false"><?=Yii::t('app', 'Top Sellers')?></a> </li>
            </ul>
            <div id="productTabContent" class="tab-content">
              <div class="tab-pane active in" id="featured">
                <div class="featured-pro">
                  <div class="slider-items-products">
                    <div id="featured-slider" class="product-flexslider hidden-buttons">
                      <div class="slider-items slider-width-col4">
					  <?php
					  $inventoryItemsList = Inventory::find()->where('stock > 0 and featured = 1 and active = 1')->orderBy(['id'=>SORT_DESC])->limit(15)->all();

					  foreach($inventoryItemsList as $inventoryItem)
					  {
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
					  ?>
					  <!-- Start Item -->
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
                              <div class="pr-img-area"> <a title="<?=$inventoryItem->product_name?>" href="<?=$url?>">
                                <figure class=""> <img class="first-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"> <img class="hover-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"></figure>
                                </a>
								<?php
								  if($inventoryItem->stock > 0)
								  {
								  ?>
                                <button type="button" class="add-to-cart-mt addtocart" value="<?=$inventoryItem->id?>"> <i class="fa fa-shopping-cart"></i><span> <?=Yii::t('app', 'Add to Cart')?></span> </button>
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
								  <input type="hidden" value="<?=$inventoryItem->id?>">
								  </div>
                                  <div class="mt-button add_to_compare"> <a href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Comparelist')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-signal"></i> </a> 
								  <input type="hidden" value="<?=$inventoryItem->id?>">
								  </div>
                                  <div class="mt-button quick-view"> <a href="<?=$url?>" title="<?=Yii::t('app', 'View Item')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-search"></i> </a> </div>
                                </div>
                              </div>
                            </div>
                            <div class="item-info">
                              <div class="info-inner">
                                <div class="item-title"> <a title="<?=$inventoryItem->product_name?>" href="<?=$url?>"><?=$inventoryItem->product_name?> </a> </div>
                                <div class="item-content">
                                  <div class="rating"> <input type="text" class="multe-rating-nocap-sm" value="<?=$inventoryItem->product_rating?>" readonly> </div>
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
						<!-- End Item -->
					  <?php
					  }
					  ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="top-sellers">
                <div class="top-sellers-pro">
                  <div class="slider-items-products">
                    <div id="top-sellers-slider" class="product-flexslider hidden-buttons">
                      <div class="slider-items slider-width-col4 ">
					  <?php
					  //$inventoryItemsList = Inventory::find()->where('stock > 0 and total_sale > 0')->orderBy(['total_sale'=>SORT_DESC])->limit(10)->all();
					  //$productList = Inventory::find()->select(['product_id', 'vendor_id'])->where('stock > 0 and total_sale > 0 and active = 1')->distinct()->limit(10)->all();

					  $all_prod_list = Inventory::find()->where('stock > 0 and total_sale > 0 and active = 1')->orderBy(['total_sale'=>SORT_DESC])->limit(25)->all();
					  $productList = MulteModel::getDistinctProdVendor($all_prod_list, 10);

					  //foreach($inventoryItemsList as $inventoryItem)
					  foreach ($productList as $plist)
					  {
						  $inventoryItem = Inventory::find()->where('stock > 0 and total_sale > 0 and active = 1')->andWhere(['product_id' => $plist['product_id'], 'vendor_id' => $plist['vendor_id']])->one();
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
					  ?>
						<!-- Start Product Item -->
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
                              <div class="pr-img-area"> <a title="<?=$inventoryItem->product_name?>" href="<?=$url?>">
                                <figure class=""> <img class="first-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"> <img class="hover-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"></figure>
                                </a>
								<?php
								  if($inventoryItem->stock > 0)
								  {
								  ?>
                                <button type="button" class="add-to-cart-mt addtocart" value="<?=$inventoryItem->id?>"> <i class="fa fa-shopping-cart"></i><span> <?=Yii::t('app', 'Add to Cart')?></span> </button>
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
								  <input type="hidden" value="<?=$inventoryItem->id?>">
								  </div>
                                  <div class="mt-button add_to_compare"> <a href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Comparelist')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-signal"></i> </a> 
								  <input type="hidden" value="<?=$inventoryItem->id?>">
								  </div>
                                  <div class="mt-button quick-view"> <a href="<?=$url?>" title="<?=Yii::t('app', 'View Item')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-search"></i> </a> </div>
                                </div>
                              </div>
                            </div>
                            <div class="item-info">
                              <div class="info-inner">
                                <div class="item-title"> <a title="<?=$inventoryItem->product_name?>" href="<?=$url?>"><?=$inventoryItem->product_name?> </a> </div>
                                <div class="item-content">
                                  <div class="rating"> <input type="text" class="multe-rating-nocap-sm" value="<?=$inventoryItem->product_rating?>" readonly> </div>
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
                       <!-- End Product Item-->
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
        </div>
        <!--Hot deal -->
		<?php
			$inventoryItem = Inventory::find()->where('stock > 0 and hot = 1 and active = 1')->one();
			
			if(!$inventoryItem)
			{
				$inventoryItem = Inventory::find()->where('stock > 0 and active = 1')->orderBy(['total_sale'=>SORT_DESC])->one();
			}

			if($inventoryItem)
			{
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
				
				if(intval(Yii::$app->params['HOT_DEAL_END_DATE']) - time() > 0)
					$url = Url::to(['/product/default/detail', 'inventory_id' => $inventoryItem->id]);
				else
					$url = 'javascript:void(0)';
			}
			

		?>
        <div class="col-md-3 col-sm-4 col-xs-12 hot-products">
          <div class="hot-deal"> <span class="title-text"><?=Yii::t('app', 'Hot deal')?></span>
		  <?php
		  if($inventoryItem)
		  {
		  ?>
            <ul class="products-grid">
              <li class="item">
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
                      <div class="icon-hot-label hot-right"><?=Yii::t('app', 'Hot')?></div>
                      <div class="pr-img-area"> <a title="<?=$inventoryItem->product_name?>" href="<?=$url?>">
                        <figure> <img class="first-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"> <img class="hover-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"></figure>
                        </a>
                        <?php
						if(intval(Yii::$app->params['HOT_DEAL_END_DATE']) - time() > 0)
						{
						  if($inventoryItem->stock > 0)
						  {
						  ?>
						<button type="button" class="add-to-cart-mt addtocart" value="<?=$inventoryItem->id?>"> <i class="fa fa-shopping-cart"></i><span> <?=Yii::t('app', 'Add to Cart')?></span> </button>
						<?php
						  }
						  else
						  {
						?>
						<button type="button" class="add-to-cart-mt btn-danger" disabled> <i class="fa fa-shopping-cart"></i><span> <?=Yii::t('app', 'Out Of Stock')?></span> </button>
						<?php
						  }
						}
						else
						{
						?>
						<button type="button" class="add-to-cart-mt btn-danger" disabled> <i class="fa fa-shopping-cart"></i><span> <?=Yii::t('app', 'Deal Expired!')?></span> </button>
						<?php
						}
						?>
                      </div>
                      <div class="jtv-box-timer">
                        <div class="countbox_1 jtv-timer-grid"></div>
                      </div>
                      <div class="pr-info-area">
                        <div class="pr-button">
                          <div class="mt-button add_to_wishlist"> <a href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Wishlist')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-heart"></i> </a> 
						  <input type="hidden" value="<?=$inventoryItem->id?>">
						  </div>
						  <div class="mt-button add_to_compare"> <a href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Comparelist')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-signal"></i> </a> 
						  <input type="hidden" value="<?=$inventoryItem->id?>">
						  </div>
						  <div class="mt-button quick-view"> <a href="<?=$url?>" title="<?=Yii::t('app', 'View Item')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-search"></i> </a> </div>
                        </div>
                      </div>
                    </div>
                    <div class="item-info">
                      <div class="info-inner">
                        <div class="item-title"> <a title="<?=$inventoryItem->product_name?>" href="<?=$url?>"><?=$inventoryItem->product_name?> </a> </div>
                        <div class="item-content">
                          <div class="rating"> <input type="text" class="multe-rating-nocap-sm" value="<?=$inventoryItem->product_rating?>" readonly> </div>
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
            </ul>
			<?php
		    }
			?>
          </div>
        </div>
      </div> <!-- row -->
    </div> <!-- container -->

	<!-- top banner -->
  <div class="container">
    <div class="row">
	<?php
	$banner = BannerData::findOne(['banner_type' => BannerType::_MIDDLE_LEFT_BANNER]);
	$url = getUrl($banner);
	?>
      <div class="col-sm-4 col-xs-12">
        <div class="jtv-banner-box banner-inner">
          <div class="image"> <a class="jtv-banner-opacity" href="<?=$url?>"><img class="mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/banner/<?=$banner->banner_new_name?>" alt="<?=$banner->banner_new_name?>"></a> </div>
          <div class="jtv-content-text">
            <h3 class="title"><?=$banner->text_1?></h3>
            <span class="sub-title"><?=$banner->text_2?></span> </div>
        </div>
      </div>

	  <?php
	  $banner = BannerData::findOne(['banner_type' => BannerType::_MIDDLE_CENTER_BANNER]);
	  $url = getUrl($banner);
	  ?>

      <div class="col-sm-5 col-xs-12">
        <div class="jtv-banner-box">
          <div class="image"> <a class="jtv-banner-opacity" href="javascript:void(0)"><img class="mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/banner/<?=$banner->banner_new_name?>" alt="<?=$banner->banner_new_name?>"></a> </div>
          <div class="jtv-content-text">
            <h3 class="title"><?=$banner->text_1?></h3>
            <span class="sub-title"><?=$banner->text_2?></span> <a href="<?=$url?>" class="button"><?=$banner->text_3?></a> </div>
        </div>
      </div>

	  <?php
	  $banner = BannerData::findOne(['banner_type' => BannerType::_MIDDLE_RIGHT_TOP_BANNER]);
	  $url = getUrl($banner);
	  ?>
      <div class="col-sm-3 col-xs-12">
        <div class="jtv-banner-box banner-inner">
          <div class="image"> <a class="jtv-banner-opacity" href="<?=$url?>"><img class="mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/banner/<?=$banner->banner_new_name?>" alt="<?=$banner->banner_new_name?>"></a> </div>
          <div class="jtv-content-text">
            <h3 class="title"><?=$banner->text_1?></h3>
          </div>
        </div>

		<?php
	    $banner = BannerData::findOne(['banner_type' => BannerType::_MIDDLE_RIGHT_BOTTOM_BANNNER]);
	    $url = getUrl($banner);
	    ?>
        <div class="jtv-banner-box banner-inner">
          <div class="image "> <a class="jtv-banner-opacity" href="<?=$url?>"><img class="mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/banner/<?=$banner->banner_new_name?>" alt="<?=$banner->banner_new_name?>"></a> </div>
          <div class="jtv-content-text">
            <h3 class="title"><?=$banner->text_1?></h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!--special-products-->
  
  <div class="container">
    <div class="special-products">
      <div class="page-header">
        <h2><?=Yii::t('app', 'Special Products')?></h2>
      </div>
      <div class="special-products-pro">
        <div class="slider-items-products">
          <div id="special-products-slider" class="product-flexslider hidden-buttons">
            <div class="slider-items slider-width-col4">
			<?php
			  $inventoryItemsList = Inventory::find()->where('stock > 0 and special = 1 and active = 1')->orderBy(['product_rating'=>SORT_DESC])->limit(15)->all();

			  foreach($inventoryItemsList as $inventoryItem)
			  {
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
			  ?>
			  <!-- Start Item -->
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
					  <div class="pr-img-area"> <a title="Ipsums Dolors Untra" href="<?=$url?>">
						<figure class=""> <img class="first-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"> <img class="hover-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"></figure>
						</a>
						<?php
						  if($inventoryItem->stock > 0)
						  {
						  ?>
						<button type="button" class="add-to-cart-mt addtocart" value="<?=$inventoryItem->id?>"> <i class="fa fa-shopping-cart"></i><span> <?=Yii::t('app', 'Add to Cart')?></span> </button>
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
						  <input type="hidden" value="<?=$inventoryItem->id?>">
						  </div>
						  <div class="mt-button add_to_compare"> <a href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Comparelist')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-signal"></i> </a> 
						  <input type="hidden" value="<?=$inventoryItem->id?>">
						  </div>
						  <div class="mt-button quick-view"> <a href="<?=$url?>" title="<?=Yii::t('app', 'View Item')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-search"></i> </a> </div>
						</div>
					  </div>
					</div>
					<div class="item-info">
					  <div class="info-inner">
						<div class="item-title"> <a title="<?=$inventoryItem->product_name?>" href="<?=$url?>"><?=$inventoryItem->product_name?> </a> </div>
						<div class="item-content">
						  <div class="rating"> <input type="text" class="multe-rating-nocap-sm" value="<?=$inventoryItem->product_rating?>" readonly> </div>
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
				<!-- End Item -->
			  <?php
			  }
			  ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-6"> 
        <!-- Testimonials Box -->
        <div class="testimonials">
          <div class="slider-items-products">
            <div id="testimonials-slider" class="product-flexslider hidden-buttons home-testimonials">
              <div class="slider-items slider-width-col4 ">
                <?php
				$testimonials = Testimonial::find()->all();

				foreach($testimonials as $testimonial)
				{
				?>
				<div class="holder">
                  <p><?=$testimonial->testimonial?> </p>
                  <div class="thumb"> <img class="mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/testimonial/<?=$testimonial->writer_new_image?>" alt="<?=$testimonial->writer_image?>"> </div>
                  <strong class="name"><?=$testimonial->writer_name?></strong> <strong class="designation"><?=$testimonial->writer_designation?></strong> 
				</div>
				<?php
				}
				?>
                
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- End Testimonials Box --> 
      <!-- our clients Slider -->
      <div class="col-md-6">
        <div class="our-clients">
          <div class="slider-items-products">
            <div id="our-clients-slider" class="product-flexslider hidden-buttons">
              <div class="slider-items slider-width-col6"> 

			    <?php
				$brands = ProductBrand::find()->where("active = 1")->all();
				if ($brands)
				{
					for ($i = 0; $i < count($brands); $i++)
					{
						if(($brands[$i]['brand_new_image']) || ($brands[$i+1]['brand_new_image']) || ($brands[$i+2]['brand_new_image']))
						{
						?>
						<!-- Item -->
						<div class="item"> 
						<?php
						if($brands[$i]['brand_new_image'])
						{
						?>
							<a href="javascript:void(0)"><img class="brand-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/brand/<?=$brands[$i]['brand_new_image']?>" alt="<?=$brands[$i]['name']?>"></a>

						<?php
						}
						?>
						<?php
						if($brands[$i+1] && $brands[$i+1]['brand_new_image'])
						{
						?>
							<br><a href="javascript:void(0)"><img class="brand-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/brand/<?=$brands[$i+1]['brand_new_image']?>" alt="<?=$brands[$i+1]['name']?>"></a>
						<?php
						}
						?>
						<?php
						if($brands[$i+2]  && $brands[$i+2]['brand_new_image'])
						{
						?>
						<br><a href="javascript:void(0)"><img class="brand-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/brand/<?=$brands[$i+2]['brand_new_image']?>" alt="<?=$brands[$i+2]['name']?>"></a>
						<?php
						}
						?>
						
						</div>
						<!-- End Item --> 
						<?php
							$i = $i+2;
						}
					}
				}
				?>
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Latest News Start -->
	
  <!-- Latest News End -->
  
  <!-- Bottom Banner Start -->
  
  <div class="bottom-banner-section">
    <div class="container">
      <div class="row">
	  <?php
	  $banner = BannerData::findOne(['banner_type' => BannerType::_BOTTOM_LEFT_BANNER]);
	  $url = getUrl($banner);
	  ?>
        <div class="col-md-4 col-sm-4"> <a href="<?=$url?>" class="bottom-banner-img"><img class="mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/banner/<?=$banner->banner_new_name?>" alt="<?=$banner->banner_new_name?>"> <span class="banner-overly"></span>
          <div class="bottom-img-info">
            <h3><?=$banner->text_1?></h3>
            <h6><?=$banner->text_2?></h6>
            <span class="shop-now-btn"><?=$banner->text_3?></span> </div>
          </a> </div>
	  <?php
	  $banner = BannerData::findOne(['banner_type' => BannerType::_BOTTOM_RIGHT_BANNER]);
	  $url = getUrl($banner);
	  ?>
        <div class="col-md-8 col-sm-8"> <a href="<?=$url?>" class="bottom-banner-img last"><img class="mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/banner/<?=$banner->banner_new_name?>" alt="<?=$banner->banner_new_name?>"> <span class="banner-overly last"></span>
          <div class="bottom-img-info last">
            <h3><?=$banner->text_1?></h3>
            <h6><?=$banner->text_2?></h6>
            <span class="shop-now-btn"><?=$banner->text_3?></span> </div>
          </a> </div>
      </div>
    </div>
  </div>
  
  <!-- category area start -->
  <div class="jtv-category-area">
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-sm-6">
          <div class="jtv-single-cat">
            <h2 class="cat-title"><?=Yii::t('app', 'Top Rated')?></h2>
			<?php
			  //$inventoryItemsList = Inventory::find()->where('stock > 0')->orderBy(['product_rating'=>SORT_DESC])->limit(3)->all();
			  //$productList = Inventory::find()->select(['product_id', 'vendor_id'])->where('stock > 0 and active = 1')->orderBy(['product_rating'=>SORT_DESC])->limit(3)->distinct()->all();
			  
			  $all_prod_list = Inventory::find()->where('stock > 0 and active = 1')->orderBy(['total_sale'=>SORT_DESC])->limit(25)->all();
			  $productList = MulteModel::getDistinctProdVendor($all_prod_list);

			  //foreach($inventoryItemsList as $inventoryItem)
			  foreach ($productList as $plist)
			  {
				  $inventoryItem = Inventory::find()->where('stock > 0 and active = 1')->andWhere(['product_id' => $plist['product_id'], 'vendor_id' => $plist['vendor_id']])->orderBy(['product_rating'=>SORT_DESC])->limit(1)->one();
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
			  ?>
            <!-- Start -->
            <div class="jtv-product jtv-cat-margin">
              <div class="product-img"> <a href="<?=$url?>"> <img class="primary-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"> <img class="secondary-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"> </a> </div>
              <div class="jtv-product-content">
                <h3><a href="<?=$url?>"><?=$inventoryItem->product_name?></a></h3>
                <div class="price-box">
				  <?php
				  if($inventoryDiscount == 0)
				  {
				  ?>
				  <p class="regular-price"> <span class="price"><?=MulteModel::formatAmount($inventoryPrice)?></span> </p>
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
                <div class="jtv-product-action">
                  <div class="jtv-extra-link">
                    <div class="button-cart">
                      <button class="addtocartmini" value="<?=$inventoryItem->id?>" title="<?=Yii::t('app', 'Add to Cart')?>" data-placement="top" data-container="body" data-toggle="tooltip"><i class="fa fa-shopping-cart"></i></button>
                    </div>
                    <a class="add_to_compare" href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Comparelist')?>" data-placement="top" data-container="body" data-toggle="tooltip">
						<i class="fa fa-signal"></i>
						<input type="hidden" value="<?=$inventoryItem->id?>">
					</a>
					
					<a class="add_to_wishlist" href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Wishlist')?>" data-placement="top" data-container="body" data-toggle="tooltip">
						<i class="fa fa-heart"></i>
						<input type="hidden" value="<?=$inventoryItem->id?>">
					</a> 
				  </div>
                </div>
              </div>
            </div>
            <!-- End -->
			<?php
			 }
			?>
          </div>
        </div>
        <div class="col-md-4 col-sm-6">
          <div class="jtv-single-cat">
            <h2 class="cat-title"><?=Yii::t('app', 'On Sale')?></h2>
            
			<?php
			  //$inventoryItemsList = Inventory::find()->where('stock > 0 and discount > 0')->orderBy(['discount'=>SORT_DESC])->limit(3)->all();
			  //$productList = Inventory::find()->select(['product_id', 'vendor_id'])->where('stock > 0 and discount > 0 and active = 1')->orderBy(['discount'=>SORT_DESC])->limit(3)->distinct()->all();

			  $all_prod_list = Inventory::find()->where('stock > 0 and discount > 0 and active = 1')->orderBy(['total_sale'=>SORT_DESC])->limit(25)->all();
			  $productList = MulteModel::getDistinctProdVendor($all_prod_list);

			  //foreach($inventoryItemsList as $inventoryItem)
			  foreach ($productList as $plist)
			  {
				  $inventoryItem = Inventory::find()->where('stock > 0 and discount > 0 and active = 1')->andWhere(['product_id' => $plist['product_id'], 'vendor_id' => $plist['vendor_id']])->orderBy(['discount'=>SORT_DESC])->limit(1)->one();
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
			  ?>
            <!-- Start -->
            <div class="jtv-product jtv-cat-margin">
              <div class="product-img"> <a href="<?=$url?>"> <img class="primary-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"> <img class="secondary-img mylazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"> </a> </div>
              <div class="jtv-product-content">
                <h3><a href="<?=$url?>"><?=$inventoryItem->product_name?></a></h3>
                <div class="price-box">
				  <?php
				  if($inventoryDiscount == 0)
				  {
				  ?>
				  <p class="regular-price"> <span class="price"><?=MulteModel::formatAmount($inventoryPrice)?></span> </p>
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
                <div class="jtv-product-action">
                  <div class="jtv-extra-link">
                    <div class="button-cart">
                      <button class="addtocartmini" value="<?=$inventoryItem->id?>" title="<?=Yii::t('app', 'Add to Cart')?>" data-placement="top" data-container="body" data-toggle="tooltip"><i class="fa fa-shopping-cart"></i></button>
                    </div>
                    <a class="add_to_compare" href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Comparelist')?>" data-placement="top" data-container="body" data-toggle="tooltip">
						<i class="fa fa-signal"></i>
						<input type="hidden" value="<?=$inventoryItem->id?>">
					</a>
					
					<a class="add_to_wishlist" href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Wishlist')?>" data-placement="top" data-container="body" data-toggle="tooltip">
						<i class="fa fa-heart"></i>
						<input type="hidden" value="<?=$inventoryItem->id?>">
					</a> 
				  </div>
                </div>
              </div>
            </div>
			<!-- End -->
			<?php
			 }
			?>
          </div>
        </div>
        
        <!-- service area start -->
        <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="jtv-service-area"> 
            
            <!-- jtv-single-service start -->
            
            <div class="jtv-single-service">
              <div class="service-icon"> <img alt="<?=Yii::t('app', '24/7 Customer Service')?>" src="<?=Url::base()?>/images/customer-service-icon.png"> </div>
              <div class="service-text">
                <h2><?=Yii::t('app', '24/7 Customer Service')?></h2>
                <p><span><?=Yii::t('app', 'Call us 24/7')?></span></p>
              </div>
            </div>
            <div class="jtv-single-service">
              <div class="service-icon"> <img alt="<?=Yii::t('app', 'Free Shipping Worldwide')?>" src="<?=Url::base()?>/images/shipping-icon.png"> </div>
              <div class="service-text">
                <h2><?=Yii::t('app', 'Free Shipping Worldwide')?></h2>
                <p><span><?=Yii::t('app', 'On Applicable Orders')?></span></p>
              </div>
            </div>
            <div class="jtv-single-service">
              <div class="service-icon"> <img alt="<?=Yii::t('app', 'Money Back Guarantee!')?>" src="<?=Url::base()?>/images/guaratee-icon.png"> </div>
              <div class="service-text">
                <h2><?=Yii::t('app', 'Money Back Guarantee!')?></h2>
                <p><span><?=Yii::t('app', 'For Returnable Products')?></span></p>
              </div>
            </div>
            
            <!-- jtv-single-service end --> 
            
          </div>
        </div>
      </div>
    
  <!-- category-area end --> 
		<div id="myModal" class="modal fade">
			<div class="modal-dialog newsletter-popup">
				<div class="modal-content"> <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<div class="modal-body"> 
						<h4 class="modal-title"><?=Yii::t('app', 'NEWSLETTER SIGNUP')?></h4>
						 <form id="newsletter-form" method="post" action="<?=Url::to(['/site/news-signup'])?>">
						  <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
						  <div class="content-subscribe">
							<div class="form-subscribe-header">
							  <label><?=Yii::t('app', 'Subscribe to be the first to know about Sales, Events, and Exclusive Offers!')?></label>
							</div>
							<div class="input-box">
							  <input type="text" class="input-text newsletter-subscribe" title="Sign up for our newsletter" name="newsemail" placeholder="Enter your email address">
							</div>
							<div class="actions">
							  <button class="button-subscribe" title="Subscribe" type="submit"><?=Yii::t('app', 'Subscribe')?></button>
							</div>
						  </div>
						</form>
						<div class="subscribe-bottom">
						  <input name="notshowpopup" id="notshowpopup" type="checkbox">
						  <?=Yii::t('app', 'Do not show this popup again')?>
						</div>
					</div>	
				</div>
			</div>
		</div>

      </div>
    </div>
  </div>

<script>
$(document).ready(function()
{

$(".mylazy").lazyload({
        event : "turnPage",
        effect : "fadeIn"
    });

$('#notshowpopup').change(function() {
	//if($(this).is(':checked'))
		$.post("<?=Url::to(['/site/ajax-unset-news-popup'])?>", { '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					})
	
});

jQuery('.tp-banner').revolution(
                  {
                      delay:9000,
                      startwidth:1170,
                      startheight:530,
                      hideThumbs:10,

                      navigationType:"bullet",							
                      navigationStyle:"preview1",

                      hideArrowsOnMobile:"on",
                      
                      touchenabled:"on",
                      onHoverStop:"on",
                      spinner:"spinner4"
                  });

  if(<?=intval(Yii::$app->params['HOT_DEAL_END_DATE']) - time()?> > 0)
	CountBack_slider(<?=intval(Yii::$app->params['HOT_DEAL_END_DATE']) - time()?>,"countbox_1", 1);

})
</script>
