<?php
use multebox\models\File;
use multebox\models\Vendor;
use multebox\models\search\MulteModel;
use multebox\models\ProductCategory;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;

include_once('../web/cart_script.php');
?>

<script type="text/javascript" src="<?=Url::base()?>/js/jquery-2.1.1.min.js"></script>

<script>
$(document).ready(function(){

 $(".mylazy").lazyload({
        event : "turnPage",
        effect : "fadeIn"
    });

$("div.holder").jPages({
        containerID : "itemContainer",
			//scrollBrowse   : true,
		perPage		: 20,
        animation   : "fadeInUp",
        callback    : function( pages,
        items ){
            /* lazy load current images */
        items.showing.find("img").trigger("turnPage");
        /* lazy load next page images */
        items.oncoming.find("img").trigger("turnPage");
        }
    });

$('#categoryfilter').change(function(){
	$('#subcategoryfilter').html('<option value="">--<?=Yii::t('app', 'Select')?>--</option>');
	$('#childcategoryfilter').html('<option value="">--<?=Yii::t('app', 'Select')?>--</option>');
   $.post("<?=Url::to(['/product/default/ajax-load-sub-category'])?>", { 'category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#subcategoryfilter').html(result);
					$('#childcategoryfilter').html('<option value="">--<?=Yii::t('app', 'Select')?>--</option>');
				})
	})

$('#subcategoryfilter').change(function(){
	$('#childcategoryfilter').html('<option value="">--<?=Yii::t('app', 'Select')?>--</option>');
    $.post("<?=Url::to(['/product/default/ajax-load-sub-sub-category'])?>", { 'sub_category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#childcategoryfilter').html(result);
				})
	})

$('#subcategoryfilter').load("<?=Url::to(['/product/default/ajax-load-sub-category', 'category_id' => $category_id, 'sub_category_id' => $sub_category_id])?>");

$('#childcategoryfilter').load("<?=Url::to(['/product/default/ajax-load-sub-sub-category', 'sub_category_id' => $sub_category_id, 'sub_subcategory_id' => $sub_subcategory_id])?>");

});
</script>

 <!-- Breadcrumb Start-->
      <ul class="breadcrumb">
	    <?php
	     $url1 = Url::to(['/site/index']);
		 $url2 = Url::to(['/product/default/listing', 'category_id' => $_GET['category_id']]);
		 $url3 = Url::to(['/product/default/listing', 'category_id' => $_GET['category_id'], 'sub_category_id' => $_GET['sub_category_id']]);
		 $url4 = Url::to(['/product/default/listing', 'category_id' => $_GET['category_id'], 'sub_category_id' => $_GET['sub_category_id'], 'sub_subcategory_id' => $_GET['sub_subcategory_id']]);
	    ?>
        <li><a href="<?=$url1?>"><i class="fa fa-home"></i></a></li>
		<?php
		$result_label = Yii::t('app', 'Result');

		if($_GET['category_id'] != '')
		{
		?>
		<li><a href="<?=$url2?>"><?=ProductCategory::findOne($_GET['category_id'])->name?></a></li>
		<?php
		}
		?>
		<?php
		if($_GET['category_id'] != '' && $_GET['sub_category_id'] != '')
		{
		?>
		<li><a href="<?=$url3?>"><?=ProductSubCategory::findOne($_GET['sub_category_id'])->name?></a></li>
		<?php
		}
		?>
		<?php
		if($_GET['category_id'] != '' && $_GET['sub_category_id'] != '' && $_GET['sub_subcategory_id'] != '')
		{
		?>
		<li><a href="<?=$url4?>"><?=ProductSubSubCategory::findOne($_GET['sub_subcategory_id'])->name?></a></li>
		<?php
		}
		?>

		<li><?=$result_label?></li>

      </ul>
      <!-- Breadcrumb End-->

  <!-- Main Container -->
  <div class="main-container col1-layout">
    <div class="container">
      <div class="row">
        <div class="col-main col-sm-12 col-xs-12">
          <div class="shop-inner">
		  <div class="toolbar column">
              <div class="sorter">
               <?php
				 if($showfilters == 'true')
				 {
				 ?>
				  <div class="product-filter">
					<div class="row">
					  <div class="col-md-12 col-sm-12 text-right">
						<button class="btn-info" data-toggle="collapse" data-target="#collapsible"><?=Yii::t('app', 'Show/Hide Options')?></button>
					  </div>
					</div><br>
				  </div>
					
				  <div class="collapse in" id="collapsible">
						<!-- Start -->
					<form method="post" action="<?=Url::to(['/product/default/filter'])?>">
					<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
					  <div class="product-filter">

						<div class="row">

						   <div class="col-md-2 col-sm-2 text-left"><?=Yii::t('app', 'Sort By')?>
								<select id="sortfilter" name="sortfilter" class="form-control col-sm-3">
								  <option value="" selected="selected">--<?=Yii::t('app', 'Select')?>--</option>
								  <option value="name_asc" <?=$sortfilter=='name_asc'?'selected':''?>><?=Yii::t('app', 'Name (A - Z)')?></option>
								  <option value="name_desc" <?=$sortfilter=='name_desc'?'selected':''?>><?=Yii::t('app', 'Name (Z - A)')?></option>
								  <option value="price_asc" <?=$sortfilter=='price_asc'?'selected':''?>><?=Yii::t('app', 'Price (Low to High)')?></option>
								  <option value="price_desc" <?=$sortfilter=='price_desc'?'selected':''?>><?=Yii::t('app', 'Price (High to Low)')?></option>
								</select>
						  </div>

						  <div class="col-md-2 col-sm-2 text-left"><?=Yii::t('app', 'Products Type')?>
								<select id="digitaltype" name="digitaltype" class="form-control col-sm-3">
								  <option value="" selected="selected">--<?=Yii::t('app', 'Select')?>--</option>
								  <option value="1" <?=$digital=='1'?'selected':''?>><?=Yii::t('app', 'Digital')?></option>
								  <option value="0" <?=$digital=='0'?'selected':''?>><?=Yii::t('app', 'Non-Digital')?></option>
								</select>
						  </div>

						  <div class="col-md-2 col-sm-2 text-left"><?=Yii::t('app', 'Category')?>
								<select id="categoryfilter" name="category_id" class="form-control col-sm-3">
								  <option value="" selected="selected">--<?=Yii::t('app', 'Select')?>--</option>
								  <?php
								  $categories = ProductCategory::find()->where("active=1 order by name")->all();
								  foreach ($categories as $row)
								  {
								  ?>
									<option value="<?=$row->id?>" <?=$category_id==$row->id?'selected':''?>><?=$row->name?></option>
								  <?php
								  }
								  ?>
								</select>
						  </div>

						  <div class="col-md-2 col-sm-2 text-left"><?=Yii::t('app', 'Sub Category')?>
								<select id="subcategoryfilter" name="sub_category_id" class="form-control col-sm-3">
								  <option value="" selected="selected">--<?=Yii::t('app', 'Select')?>--</option>
								</select>
						  </div>

						  <div class="col-md-2 col-sm-2 text-left"><?=Yii::t('app', 'Child Category')?>
								<select id="childcategoryfilter" name="sub_subcategory_id" class="form-control col-sm-3">
								  <option value="" selected="selected">--<?=Yii::t('app', 'Select')?>--</option>
								</select>
						  </div>

						  <div class="col-md-2 col-sm-2 text-left"><?=Yii::t('app', 'Vendor')?>
								<select id="vendorfilter" name="vendor_id" class="form-control col-sm-3">
								  <option value="" selected="selected">--<?=Yii::t('app', 'Select')?>--</option>
								  <?php
								  $vendors = Vendor::find()->where("active=1 order by vendor_name")->all();
								  foreach ($vendors as $row)
								  {
								  ?>
									<option value="<?=$row->id?>" <?=$vendor_id==$row->id?'selected':''?>><?=$row->vendor_name?></option>
								  <?php
								  }
								  ?>
								</select>
						  </div>
						</div> <!-- row -->

					  </div> <!-- product filter -->

					  <div class="product-filter"><br>
						<div class="row">
						  <div class="col-md-12 col-sm-12 text-right">
							<button class="btn-primary" type="submit"><?=Yii::t('app', 'Apply Selected Filter')?></button>
						  </div>
						</div>
					  </div>
					</form>
						<!-- End -->
				  </div>
				  <?php
				  }
				  ?>
				  <div class="visible-xs" id="xs-check"></div>
				  <script>
					if( $("#xs-check").is(":visible") )
						$("#collapsible").removeClass("in");
				  </script>

				  <div class="holder"></div>
              </div>
            </div>
            <div class="page-title">
              <h2> <?=$result_label?></h2>
            </div>
            
            <div class="product-grid-area">
              <ul class="products-grid" id="itemContainer">
				<?php
				foreach($itemsList as $inventoryItem)
				{
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
                                <figure class=""> <img class="first-img mylazy" src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem['product_name']?>"> <img class="hover-img mylazy" src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem['product_name']?>"></figure>
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
								  <input type="hidden" value="<?=$inventoryItem['id']?>">
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
                
              </ul>
            </div>
            <div class="pagination-area ">
              <div class="holder"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Main Container End --> 
  