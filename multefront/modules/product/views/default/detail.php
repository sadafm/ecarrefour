<?php
use multebox\models\File;
use multebox\models\Product;
use multebox\models\ProductCategory;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use multebox\models\ProductBrand;
use multebox\models\ProductAttributes;
use multebox\models\ProductAttributeValues;
use multebox\models\InventoryDetails;
use multebox\models\Inventory;
use multebox\models\Vendor;
use multebox\models\search\MulteModel;
use multebox\models\ProductReview;
use multebox\models\User;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\Html;


include_once('../web/cart_script.php');

$product = Product::findOne($inventory->product_id);
$fileDetails = File::find()->where("entity_type='product' and entity_id='$inventory->product_id'")->orderBy("id asc")->all();
$product_reviews = ProductReview::find()->where("product_id=".$inventory->product_id)->all();

$vendor_matching_inventories = Inventory::find()->where(['product_id' => $inventory->product_id, 'vendor_id' => $inventory->vendor_id])->all();
$ref_arr = [];

function getBigImage($name)
{
	return str_replace("_small.", ".", $name);
}

foreach ($vendor_matching_inventories as $rec)
{
	$inv_det = InventoryDetails::find()->where('inventory_id='.$rec->id)->all();
	foreach ($inv_det as $row)
	{
		$arr_rec = $row->attribute_id.$row->attribute_value;

		if(!in_array($arr_rec, $ref_arr))
		{
			array_push($ref_arr, $arr_rec);
		}
	}
}

?>

<script type="text/javascript" src="<?=Url::base()?>/js/jquery-2.1.1.min.js"></script>
<script>
$(document).ready(function()
{
	$('.sendemail').on('click', function () {
		$('.emailmodal').modal('show');
	});

	$('.attribute_dropdown').on('change', function () {
		//alert( this.value );

		var options = $('.attribute_dropdown option:selected');
		var blank = false;

		var values = $.map(options ,function(option)
		{
			if(option.value == '')
			{
				blank = true;
			}
			else
			{
				return option.value;
			}
		});
		
		if(!blank)
		{
			//alert(JSON.stringify(values));
			var val_arr = JSON.stringify(values);
			$.post("<?=Url::to(['/product/default/ajax-get-matching-item'])?>", { 'product_id': '<?=$inventory->product_id?>', 'vendor_id' : '<?=$inventory->vendor_id?>', 'attributes_list': val_arr, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					if(result == 'NRF')
					{
						/* Hide button */
						$("#addtocart2").attr("disabled", "true");
						$("#addtocart2").attr("class", "button pro-out-of-stock");
					}
					else
					{
						//$(".front_content").html(result);
						var response = JSON.parse (result);
						if(response[1] > 0)
						{
							$(".multe-sale").removeAttr("style");
							$(".multe-price").removeAttr("style");
						}
						else
						{
							$(".multe-sale").attr("style", "display:none");
							$(".multe-price").attr("style", "display:none");
						}
						
						$("#addtocart2").removeAttr("disabled");
						$("#addtocart2").attr("class", "button pro-add-to-cart addtocart2");

						$('.multe-actual-price').html(response[0]);
						$('.multe-discount-percent').html('-' + response[1] + '%');
						$('.multe-discounted-price').html(response[2]);
						$('.multe-product-code').html(response[3]);
						$('.addtocart2').attr("value", response[4]);
						$('.remainingstock').attr("value", response[5]);
						$('#qty').val(1);
					}
				})
		}
		else
		{
			$("#addtocart2").attr("disabled", "true");
			$("#addtocart2").attr("class", "button pro-out-of-stock");
		}

	});

	$('.addtocart2').on('click', function () {
        var cart = $('.mycart');
        var imgtodrag = $('.large-image').find("img").eq(0);
        if (imgtodrag) {
            var imgclone = imgtodrag.clone()
                .offset({
                top: imgtodrag.offset().top,
                left: imgtodrag.offset().left
            })
                .css({
                'opacity': '0.5',
                    'position': 'absolute',
                    'height': '150px',
                    'width': '150px',
                    'z-index': '100'
            })
                .appendTo($('body'))
                .animate({
                'top': cart.offset().top + 10,
                    'left': cart.offset().left + 10,
                    'width': 75,
                    'height': 75
            }, 1000, 'easeInOutExpo');
            
            imgclone.animate({
                'width': 0,
                    'height': 0
            }, function () {
                $(this).detach()
            });
        }

		$.post("<?=Url::to(['/order/default/ajax-add-to-cart'])?>", { 'inventory_id': $(this).val(), 'total_items' : $('#qty').val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					
					$('.mini-products-list').html(result);
					$('.cartcount').html($('.hiddencartvalue').val() + ' ' + "<?=Yii::t('app', 'Item(s)')?>");
					$('.remainingstock').val($('.hiddenremainingstock').val());
					$('.confirmmodal').modal('show');
					setTimeout(function() {$('.confirmmodal').modal('hide');}, 1500);
				})

	    });

	//var error='';
	$(".plus").click(function(event)
	{
		//var stock = <?=$inventory->stock?>;
		var stock = $('.remainingstock').val();
		//Remove_Error($(this));
		if(stock < parseInt($('#qty').val()))
		{
			//alert($('#input-quantity').val());
			//error+=Add_Error($(this),'<?=Yii::t('app','Not enough stock!')?>');
			$('#qty').val(stock);
			//event.preventDefault();
			return false;
		}
	});

	$(".minus").click(function(event)
	{
		if((new Number( $('#qty').val())) <= 0)
		{
			$('#qty').val('1');
			//event.preventDefault();
			return false;
		}
	});

	$('#qty').change(function(event)
	{
		//var stock = <?=$inventory->stock?>;
		var stock = $('.remainingstock').val();
		//Remove_Error($(this));
		if(stock < parseInt($('#qty').val()))
		{
			//alert("Not enough stock!");
			//error+=Add_Error($(this),'<?=Yii::t('app','Not enough stock!')?>');
			$('#qty').val(stock);
			//event.preventDefault();
			return false;
		}

		if((new Number( $('#qty').val())) <= 0)
		{
			$('#qty').val('1');
			//event.preventDefault();
			return false;
		}
	});
	
	//Remove_Error($(this));

	// Bind a click event to a Cloud Zoom instance.
        $('#zoom1').bind('click',function(){
            // On click, get the Cloud Zoom object,
            var cloudZoom = $(this).data('CloudZoom');
            // Close the zoom window (from 2.1 rev 1211291557)
            cloudZoom.closeZoom();                       
            // and pass Cloud Zoom's image list to Fancy Box.
            $.fancybox.open(cloudZoom.getGalleryList()); 
            return false;
        });

	$(document).on("click", '.send_email', function(event)
	{
		Remove_Error($('#modal-yourname'));
		Remove_Error($('#modal-friendname'));
		Remove_Error($('#modal-friendemail'));
		Remove_Error($('#modal-youremail'));

		if($('#modal-yourname').val() == '')
		{
			Add_Error($('#modal-yourname'),"<?=Yii::t('app','This field is required!')?>");
			event.preventDefault();
		}

		if($('#modal-friendname').val() == '')
		{
			Add_Error($('#modal-friendname'),"<?=Yii::t('app','This field is required!')?>");
			event.preventDefault();
		}

		if($('#modal-friendemail').val() == '')
		{
			Add_Error($('#modal-friendemail'),"<?=Yii::t('app','This field is required!')?>");
			event.preventDefault();
		}
		else
		{
			var atpos=$('#modal-friendemail').val().indexOf("@");
			var dotpos=$('#modal-friendemail').val().lastIndexOf(".");

			if (atpos<1 || dotpos<atpos+2 || dotpos+2>=$('#modal-friendemail').val().length)
			{
				Add_Error($('#modal-friendemail'),"<?=Yii::t('app','Please enter a valid email!')?>");
				event.preventDefault();
			}
		}

		if($('#modal-youremail').val() == '')
		{
			Add_Error($('#modal-youremail'),"<?=Yii::t('app','This field is required!')?>");
			event.preventDefault();
		}
		else
		{
			var atpos=$('#modal-youremail').val().indexOf("@");
			var dotpos=$('#modal-youremail').val().lastIndexOf(".");

			if (atpos<1 || dotpos<atpos+2 || dotpos+2>=$('#modal-youremail').val().length)
			{
				Add_Error($('#modal-youremail'),"<?=Yii::t('app','Please enter a valid email!')?>");
				event.preventDefault();
			}
		}
	})

});
</script>
<input type="hidden" class="remainingstock" value="<?=$inventory->stock?>">
<!-- Breadcrumb Start-->
  <ul class="breadcrumb">
  <?php
	$url1 = Url::to(['/site/index']);
	$url2 = Url::to(['/product/default/listing', 'category_id' => $product->category_id]);
	$url3 = Url::to(['/product/default/listing', 'category_id' => $product->category_id, 'sub_category_id' => $product->sub_category_id]);
	$url4 = Url::to(['/product/default/listing', 'category_id' => $product->category_id, 'sub_category_id' => $product->sub_category_id, 'sub_subcategory_id' => $product->sub_subcategory_id]);
  ?>
	<li><a href="<?=$url1?>"><i class="fa fa-home"></i></a></li>
	<li><a href="<?=$url2?>"><?=ProductCategory::findOne($product->category_id)->name?></a></li>
	<li><a href="<?=$url3?>"><?=ProductSubCategory::findOne($product->sub_category_id)->name?></a></li>
	<li><a href="<?=$url4?>"><?=ProductSubSubCategory::findOne($product->sub_subcategory_id)->name?></a></li>
  </ul>
  <!-- Breadcrumb End-->
  <!-- Main Container -->
  <div class="main-container col1-layout">
  <div class="container">
  <?php
	/*$inventoryPrice = floatval($inventory->price);
	if($inventory->price_type == 'B')
	{
	  if($inventory->attribute_price)
	  foreach(Json::decode($inventory->attribute_price) as $row)
	  {
		  $inventoryPrice += floatval($row);
	  }
	}

	if($inventory->discount_type == 'P')
	$inventoryDiscount = $inventory->discount;
	else
	{
		if($inventoryPrice > 0)
			$inventoryDiscount = round((floatval($inventory->discount)/$inventoryPrice)*100,2);
		else
			$inventoryDiscount = 0;
	}

	$inventoryDiscountedPrice = round($inventoryPrice - $inventoryPrice*$inventoryDiscount/100, 2);*/

	$inventoryPrice = MulteModel::getInventoryActualPrice ($inventory, 1);
	$inventoryDiscount = MulteModel::getInventoryDiscountPercentage ($inventory, 1);
	$inventoryDiscountedPrice = round($inventoryPrice - $inventoryPrice*$inventoryDiscount/100, 2);

	?>
    <div class="row">
      <div class="col-main">
        <div class="product-view-area">
          <div class="product-big-image col-xs-12 col-sm-5 col-lg-5 col-md-5">
           <?php
		   if($inventoryDiscount > 0)
		   {
		   ?>
			<div class="icon-sale-label sale-left multe-sale"><li class="multe-discount-percent">-<?=$inventoryDiscount?>%</li></div>
			<?php
		   }
		   else
		   {
		   ?>
			<div class="icon-sale-label sale-left multe-sale" style="display:none"><li class="multe-discount-percent">-<?=$inventoryDiscount?>%</li></div>
			<?php
		   }
		   ?>
            <div class="large-image"> <a href="javascript:void(0)" class="cloud-zoom" id="zoom1" rel="useWrapper: false, adjustY:0, adjustX:20"> <img class="zoom-img" src="<?=Yii::$app->params['web_url']?>/<?=getBigImage($fileDetails[0]->new_file_name)?>" title="<?=$fileDetails[0]->file_title?>" alt="<?=$fileDetails[0]->file_title?>" data-zoom-image="<?=Yii::$app->params['web_url']?>/<?=getBigImage($fileDetails[0]->new_file_name)?>" /> </a> </div>
            <div class="flexslider flexslider-thumb">
              <ul class="previews-list slides">
			    <?php
				foreach($fileDetails as $file)
				{
				?>
                <li><a href="#" class="cloud-zoom-gallery" rel="useZoom: 'zoom1', smallImage: '<?=Yii::$app->params['web_url']?>/<?=getBigImage($file->new_file_name)?>' "><img src="<?=Yii::$app->params['web_url']?>/<?=getBigImage($file->new_file_name)?>" class="thumb-detail" alt = "<?=$file->file_title?>"/></a></li>
				
				<?php
					if(count($fileDetails) == 2)
					{
						echo "<li></li>";
					}
				}
				?>
              </ul>
            </div>
            
            <!-- end: more-images --> 
            
          </div>
          <div class="col-xs-12 col-sm-7 col-lg-7 col-md-7 product-details-area">
       
              <div class="product-name">
                <?php
				  if($inventory->product->digital)
				  {
				  ?>
					<h1 class="title" itemprop="name"><?=$inventory->product_name?> <small style="text-transform:uppercase">(<?=Yii::t('app', 'Digital Product')?>)</small></h1>
				  <?php
				  }
				  else
				  {
				  ?>
					<h1 class="title" itemprop="name"><?=$inventory->product_name?></h1>
				  <?php
				  }
				  ?>
              </div>
              <div class="price-box">
			  <?php
			  if($inventoryDiscount > 0)
			  {
			  ?>
                <p class="special-price"> <span class="price-label"><?=Yii::t('app', 'Special Price')?>:</span> <span class="price multe-discounted-price"> <?=MulteModel::formatAmount($inventoryDiscountedPrice)?> </span> </p>
                <p class="old-price multe-price"> <span class="price-label"><?=Yii::t('app', 'Regular Price')?>:</span> <span class="price multe-actual-price"> <?=MulteModel::formatAmount($inventoryPrice)?> </span> </p>
			  <?php
			  }
			  else
			  {
			  ?>
				<p class="special-price"> <span class="price-label"><?=Yii::t('app', 'Special Price')?>:</span> <span class="price"> <?=MulteModel::formatAmount($inventoryDiscountedPrice)?> </span> </p>
				<p class="old-price multe-price" style="display:none"> <span class="price-label"><?=Yii::t('app', 'Regular Price')?>:</span> <span class="price"> <?=MulteModel::formatAmount($inventoryPrice)?> </span>
			  <?php
			  }
			  ?>
              </div>
              <div class="ratings">
                <div class="rating"> <input type="text" class="multe-rating-nocap-sm" value="<?=$inventory->product_rating?>" readonly> </div>
                <p class="rating-links"> <a href="javascript:void(0)"><?=$product_reviews?count($product_reviews):0?> <?=Yii::t('app', 'customer review(s)')?></a> </p>
                <?php
				if($inventory->stock > 0)
				{
				?>
				<p class="availability in-stock pull-right"><?=Yii::t('app', 'Availability')?>: <span><?=Yii::t('app', 'In Stock')?></span></p>
				<?php
				}
				else
				{
				?>
				<p class="availability out-of-stock pull-right"><?=Yii::t('app', 'Availability')?>: <span><?=Yii::t('app', 'Out Of Stock')?></span></p>
				<?php
				}
				?>
              </div>
			  <?php
				if($product->brand_id != '')
				{
				?>
                  <li><b><?=Yii::t('app', 'Brand')?>:</b> <a href="#"><span itemprop="brand"><?=ProductBrand::findOne($product->brand_id)->name?> </span></a></li>
				<?php
				}
				?>
                  <li><b><?=Yii::t('app', 'Product Code')?>:</b> <span itemprop="mpn" class="multe-product-code"><?='INVT'.str_pad($inventory->id, 9, "0", STR_PAD_LEFT)?></span></li>
				<li><b><?=Yii::t('app', 'Sold By')?>:</b> <span itemprop="mpn">
				<?= Html::a(Vendor::findOne($inventory->vendor_id)->vendor_name, Url::to(['/product/default/filter']), [
																'data'=>[
																	'method' => 'post',
																	'params'=>['vendor_id'=> $inventory->vendor_id],
																],
																'data-toggle' => 'tooltip',
																'title' => Yii::t('app', 'Click To View All Products By This Seller'),
															]) ?>
				</span></li>
				<div class="row">
				<div class="col-sm-4">
				<li><b><?=Yii::t('app', 'Seller Rating')?>:</b></li>
				</div>

				<div class="col-sm-8 text-left">
				<li><input type="text" class="multe-rating-nocap-sm" value="<?=$inventory->vendor_rating?>" readonly></li>
				</div>
				</div>
              
              <div class="product-color-size-area">
                <div class="short-description">
                <div id="product">
				  <form method="post" name="" action="<?=Url::to(['/product/default/result'])?>"  enctype="multipart/form-data">
				  <?php Yii::$app->request->enableCsrfValidation = true; ?>
				  <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
				  <input type="hidden" name="product_id" value="<?=$inventory->product_id?>">
				  <?php
				  if ($inventory->slab_discount_ind == 1)
				  {
				  ?>
				  <?=Yii::t('app', 'Bulk Discount')?>:
				  <div class="table-responsive">
					<table class="table table-bordered">
					  <tbody>
					    <tr>
						  <th><?=Yii::t('app', 'Minimum Quantity')?></th>
						  <?=$inventory->slab_1_range > 0?'<td>'.$inventory->slab_1_range.'</td>':''?>
						  <?=$inventory->slab_2_range > 0?'<td>'.$inventory->slab_2_range.'</td>':''?>
						  <?=$inventory->slab_3_range > 0?'<td>'.$inventory->slab_3_range.'</td>':''?>
						  <?=$inventory->slab_4_range > 0?'<td>'.$inventory->slab_4_range.'</td>':''?>
						</tr>
						<tr>
						  <th><?=Yii::t('app', 'Discount')?></th>
						  <?=$inventory->slab_1_range > 0?'<td>'.($inventory->slab_discount_type=='F'?MulteModel::formatAmount($inventory->slab_1_discount):$inventory->slab_1_discount.'%').'</td>':''?>
						  <?=$inventory->slab_2_range > 0?'<td>'.($inventory->slab_discount_type=='F'?MulteModel::formatAmount($inventory->slab_2_discount):$inventory->slab_2_discount.'%').'</td>':''?>
						  <?=$inventory->slab_3_range > 0?'<td>'.($inventory->slab_discount_type=='F'?MulteModel::formatAmount($inventory->slab_3_discount):$inventory->slab_3_discount.'%').'</td>':''?>
						  <?=$inventory->slab_4_range > 0?'<td>'.($inventory->slab_discount_type=='F'?MulteModel::formatAmount($inventory->slab_4_discount):$inventory->slab_4_discount.'%').'</td>':''?>
						</tr>
					  </tbody>
					</table>
				  </div>
				  <?php
				  }
				  ?>
                  
				  
				  <?php
				  /*$productAttributesList = ProductAttributes::find()->where('parent_id='.ProductSubSubCategory::findOne($product->sub_subcategory_id)->id.' and fixed=1 and active=1')->orderBy('id')->all();

				  if($productAttributesList)
				  {
					  ?>
					  <h3 class="subtitle"><?=Yii::t('app', 'Available Options')?></h3>
					  <?php
				  }
					
				  $i=0;
				  foreach($productAttributesList as $productAttributes)
				  {
					  $productAttributeValueList = ProductAttributeValues::findOne($productAttributes->fixed_id);
				  ?>
                  <div class="form-group required">
				    <!--<input type="hidden" name="attribute_ids[]" value="<?=$productAttributes->id?>">-->
				    <input type="hidden" name="attribute_names[]" value="<?=$productAttributeValueList->name?>">
                    <label class="control-label"><?=$productAttributeValueList->name?></label>
                    <select class="form-control attribute_dropdown" id="input-option" name="attribute_value[]">
                      <option value=""> --- <?=Yii::t('app', 'Please Select')?> --- </option>
					  <?php
					  foreach(Json::decode($productAttributeValueList->values) as $value)
					  {
						if(in_array($productAttributes->id.$value, $ref_arr))
						{
					  ?>
                      <option value="<?=htmlspecialchars($value)?>" <?=(Json::decode($inventory->attribute_values))[$i] == $value?'selected':''?> data-validation="required" mandatory-field><?=$value?> </option>
					  <?php
						}
					  }
					  ?>
                    </select>
                  </div>
				  <?php
				  $i++;
				  }*/
				  
				  $id_list = '';
				  foreach ($vendor_matching_inventories as $rec)
				  {
					$id_list = $id_list.$rec->id.",";
				  }

				  $id_list = substr ($id_list, 0, -1);
				  
				  $att_ids_sorted = InventoryDetails::find()->select('attribute_id')->distinct()->where("inventory_id in (".$id_list.")")->orderBy(['attribute_id' => SORT_ASC])->all();
				  
				  if($att_ids_sorted)
				  {
					  ?>
					  <h3 class="subtitle"><?=Yii::t('app', 'Available Options')?></h3>
					  <?php
				  }
					
				  $i=0;
				  
				  foreach($att_ids_sorted as $att_id)
				  {
					  $att_list_sorted = InventoryDetails::find()->select('attribute_value')->distinct()->where("inventory_id in (".$id_list.") and attribute_id=".$att_id->attribute_id)->orderBy(['attribute_value' => SORT_ASC])->all();

					  $att_name = ProductAttributes::findOne($att_id->attribute_id)->name;
				  ?>
                  <div class="form-group required">
				    <!--<input type="hidden" name="attribute_ids[]" value="<?=$productAttributes->id?>">-->
				    <input type="hidden" name="attribute_names[]" value="<?=$att_name?>">
                    <label class="control-label"><?=$att_name?></label>
                    <select class="form-control attribute_dropdown" id="input-option" name="attribute_value[]">
                      <option value=""> --- <?=Yii::t('app', 'Please Select')?> --- </option>
					  <?php
					  foreach($att_list_sorted as $value)
					  {
					  ?>
                      <option value="<?=htmlspecialchars($value->attribute_value)?>" <?=(Json::decode($inventory->attribute_values))[$i] == $value->attribute_value?'selected':''?> data-validation="required" mandatory-field><?=$value->attribute_value?> </option>
					  <?php
					  }
					  ?>
                    </select>
                  </div>
				  <?php
				  $i++;
				  }
				  ///////
				  if($i > 0)
				  {
				  ?>
				  <div>
				  <button type="submit" id="button-cart" class="btn btn-success btn-block ashish"><?=Yii::t('app', 'Find items with applied filter')?></button>
				  </div><br/>
				  <?php
				  }
				  ?>
				  </form>
                  
                </div>
               
              </div>
                
              </div>
              <div class="product-variation">
                <form action="#" method="post">
                  <div class="cart-plus-minus">
                    <label for="qty"><?=Yii::t('app', 'Quantity')?>:</label>
                    <div class="numbers-row">
                      <div onClick="var result = document.getElementById('qty'); var qty = result.value; if( !isNaN( qty ) &amp;&amp; qty &gt; 0 ) result.value--;return false;" class="dec qtybutton minus"><i class="fa fa-minus">&nbsp;</i></div>
                      <input type="text" class="qty" title="Qty" value="1" maxlength="12" id="qty" name="qty">
                      <div onClick="var result = document.getElementById('qty'); var qty = result.value; if( !isNaN( qty )) result.value++;return false;" class="inc qtybutton plus"><i class="fa fa-plus">&nbsp;</i></div>
                    </div>
                  </div>
				  <?php
				  if($inventory->stock > 0)
				  {
				  ?>
                  <button id="addtocart2" class="button pro-add-to-cart addtocart2" title="<?=Yii::t('app', 'Add to Cart')?>" type="button" value="<?=$inventory->id?>"><span><i class="fa fa-shopping-cart"></i> <?=Yii::t('app', 'Add to Cart')?></span></button>
				  <?php
				  }
				  else
				  {
				  ?>
				  <button class="button pro-out-of-stock" disabled title="<?=Yii::t('app', 'Out Of Stock')?>" type="button"><span><i class="fa fa-shopping-cart"></i> <?=Yii::t('app', 'Out Of Stock')?></span></button>
				  <?php
				  }
				  ?>
                </form>
              </div>
              <div class="product-cart-option">
                <ul>
                  <li>
					<a class="add_to_wishlist" href="javascript:void(0)">
						<i class="fa fa-heart"></i>
						<span><?=Yii::t('app', 'Add to Wishlist')?></span>
						<input type="hidden" value="<?=$inventory->id?>">
					</a>
				  </li>
                  <li>
					<a class="add_to_compare" href="javascript:void(0)">
						<i class="fa fa-signal"></i>
						<span><?=Yii::t('app', 'Add to Compare')?></span>
						<input type="hidden" value="<?=$inventory->id?>">
					</a>
				  </li>
                  <li><a class="sendemail" href="javascript:void(0)"><i class="fa fa-envelope"></i><span><?=Yii::t('app', 'Email to a Friend')?></span></a></li>
                </ul>
           
            </div>
          </div>
        </div>
      </div>
      <div class="product-overview-tab">
        <div class="container">
          <div class="row">
            <div class="col-xs-12"><div class="product-tab-inner"> 
              <ul id="product-detail-tab" class="nav nav-tabs product-tabs">
                <li class="active"> <a href="#description" data-toggle="tab"><?=Yii::t('app', 'Description')?></a> </li>
				<li> <a href="#specifications" data-toggle="tab"><?=Yii::t('app', 'Specifications')?></a> </li>
                <li><a href="#reviews" data-toggle="tab"><?=Yii::t('app', 'Reviews')?> (<?=$product_reviews?count($product_reviews):0?>)</a></li>
              </ul>
              <div id="productTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="description">
                  <div class="std">
                    <?=$product->description?>
                  </div>
                </div>
                
                
                <div id="specifications" class="tab-pane fade">
					<?php
					  $inventoryDetails = InventoryDetails::find()->where('inventory_id='.$inventory->id)->all();	
					  foreach($inventoryDetails as $inventoryDetail)
					  {
					  ?>
						<table class="table table-bordered">
						  <tbody>
							<tr>
							  <td width=50%><strong><?=ProductAttributes::findOne($inventoryDetail->attribute_id)->name?></strong></td>
							  <td width=50%><?=$inventoryDetail->attribute_value?></td>
							</tr>
						  </tbody>
						</table>
					  <?php
					  }
					  ?>			
				</div>
            
                <div class="tab-pane fade" id="reviews">
				<div id="review">
                    <div>
                      <table class="table table-striped table-bordered">
                        <tbody>
						<?php
						foreach($product_reviews as $review)
						{
							$user = User::find()->where("entity_type='customer' and entity_id=".$review->customer_id)->one();
						?>
                          <tr>
						    <td style="width: 20%;"><input type="text" class="multe-rating-nocap-sm" value="<?=$review->rating?>" readonly></td>
                            <td ><strong><span><?=$user->first_name?> <?=$user->last_name?></span></strong></td>
                            <td class="text-right"><span><?=date('M d, Y', $review->added_at)?></span></td>
                          </tr>
                          <tr>
                            <td colspan="3">
							  <p style="white-space: pre-wrap;"><?=$review->review?></p>
                            </td>
                          </tr>
						<?php
						}
						?>
                        </tbody>
                      </table>
                     
                    </div>
                    <div class="text-right"></div>
                  </div> <!-- -->

                </div>

              </div>
            </div>
          </div></div>
        </div>
      </div>
       
    </div>
  </div>
</div>

<!-- Main Container End --> 



<!-- Related Product Slider -->
  
  <div class="container">
  <div class="row">
  <div class="col-xs-12">
   <div class="related-product-area">       
 <div class="page-header">
        <h2><?=Yii::t('app', 'Related Products')?></h2>
      </div>
      <div class="related-products-pro">
                <div class="slider-items-products">
          <div id="special-products-slider" class="product-flexslider hidden-buttons">
            <div class="slider-items slider-width-col4">
			<?php
			  //$inventoryItemsList = Inventory::find()->where('stock > 0 and special = 1')->orderBy(['product_rating'=>SORT_DESC])->limit(15)->all();
			  $inventoryItemsList = Inventory::find()
								->alias('i')
								->joinWith('inventoryProducts p')
								->andWhere('p.category_id = '.Product::findOne($inventory->product_id)->category_id)
								->andWhere('p.sub_category_id = '.Product::findOne($inventory->product_id)->sub_category_id)
								->andWhere("i.id !=".$inventory->id)
								->orderBy('name')
								->limit(15)
								->asArray()
								->all();

			  foreach($inventoryItemsList as $inventoryItem)
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

				  $url = Url::to(['/product/default/detail', 'inventory_id' => $inventoryItem['id']]);
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
					  <div class="pr-img-area"> <a title="Item" href="<?=$url?>">
						<figure class=""> <img class="first-img" src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem['product_name']?>"> <img class="hover-img" src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem['product_name']?>"></figure>
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
						  <div class="mt-button"> <a class="add_to_wishlist" href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Wishlist')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-heart"></i> <input type="hidden" value="<?=$inventoryItem['id']?>"> </a> </div>
						  <div class="mt-button"> <a class="add_to_compare" href="javascript:void(0)" title="<?=Yii::t('app', 'Add to Comparelist')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-signal"></i> <input type="hidden" value="<?=$inventoryItem['id']?>"> </a> </div>
						  <div class="mt-button quick-view"> <a href="<?=$url?>" title="<?=Yii::t('app', 'View Item')?>" data-placement="top" data-container="body" data-toggle="tooltip"> <i class="fa fa-search"></i> </a> </div>
						</div>
					  </div>
					</div>
					<div class="item-info">
					  <div class="info-inner">
						<div class="item-title"> <a title="<?=$inventoryItem['product_name']?>" href="<?=$url?>"><?=$inventoryItem['product_name']?> </a> </div>
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
              </div>
<!-- Related Product Slider End --> 

 <div class="modal emailmodal" data-backdrop="static" data-keyboard="false">
 <form method="post" id="emailform" action=""  enctype="multipart/form-data">
 <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
 <input type="hidden" name="inventory_id" value="<?=$inventory->id?>">
 <input type="hidden" name="sendtofriend" value="1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" id="closemodal" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-book"></i> <?=Yii::t('app', 'Share Item With Your Friend')?></h4>
      </div>
      <div class="modal-body">
   <div class="panel panel-default">

	  <div class="panel-body">
			<fieldset id="sendemail" class="required">
			  <div class="form-group required">
				<label for="modal-yourname" class="control-label"><?=Yii::t('app', 'Your Name')?></label>
				<input type="text" class="form-control" id="modal-yourname" value="<?=!Yii::$app->user->isGuest?Yii::$app->user->identity->first_name.' '.Yii::$app->user->identity->last_name:''?>" placeholder="<?=Yii::t('app', 'Your Name')?>" name="modal_yourname">
			  </div>
			  <div class="form-group required">
				<label for="modal-friendname" class="control-label"><?=Yii::t('app', 'Friend Name')?></label>
				<input type="text" class="form-control" id="modal-friendname" placeholder="<?=Yii::t('app', 'Friend Name')?>" name="modal_friendname">
			  </div>
			  <div class="form-group required">
				<label for="modal-youremail" class="control-label"><?=Yii::t('app', 'Your Email')?></label>
				<input type="text" class="form-control" id="modal-youremail" value="<?=!Yii::$app->user->isGuest?Yii::$app->user->identity->email:''?>" placeholder="<?=Yii::t('app', 'Your Email')?>" name="modal_youremail">
			  </div>
			  <div class="form-group required">
				<label for="modal-friendemail" class="control-label"><?=Yii::t('app', 'Friend Email')?></label>
				<input type="text" class="form-control" id="modal-friendemail" placeholder="<?=Yii::t('app', 'Friend Email')?>" name="modal_friendemail">
			  </div>
			  <div class="form-group required">
				<label for="modal-message" class="control-label"><?=Yii::t('app', 'Message')?></label>
				<textarea rows="10" class="form-control" id="modal-message" placeholder="<?=Yii::t('app', 'Write Your Message')?>..." name="modal_message"></textarea>
			  </div>
			</fieldset>
		  </div>
		  </div>	
		    </div>
			<div class="modal-footer">
      	<button type="submit" class="btn btn-primary send_email">
        	<i class="fa fa-send"></i> <?=Yii::t('app', 'Send')?> </button>
      </div>
 </div>
  </div>
  </form>
 </div>