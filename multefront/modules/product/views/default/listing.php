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

include_once('../web/cart_script.php');
?>
<style type="text/css">
        .ajax-load{
            background: #e1e1e1;
            padding: 10px 0px;
            width: 100%;
        }

		.ajax-end{
            background: #e1e1e1;
            width: 100%;
        }
</style>

<script type="text/javascript" src="<?=Url::base()?>/js/jquery-2.1.1.min.js"></script>

<script>
var taskFired  = false;
    $(window).scroll(function() {
        if(Math.round($(window).scrollTop()) + $(window).height() >= $(document).height()-450 && !$(".ajax-end").is(":visible")) {
			//$(window).off('scroll');
            var last_id = $(".ajax-data:last").val();
            //alert(last_id);
			if(!taskFired){
				taskFired = true;
				setTimeout(loadMoreData(last_id), 1500);
			}
        }
    });

    function loadMoreData(last_id){
		var postData = {
        "category_id" : '<?=$category_id?>',
        "sub_category_id" : '<?=$sub_category_id?>',
        "sub_subcategory_id" : '<?=$sub_subcategory_id?>',
        "last_id" : last_id,
		"_csrf"	: '<?=Yii::$app->request->csrfToken?>'
		};

      $.ajax(
            {
                url: '<?=Url::to(['/product/default/re-listing'])?>',
                type: "POST",
				data: postData, 
                beforeSend: function()
                {
                    $('.ajax-load').show();
                }
            })
            .done(function(data)
            {
				$('.ajax-load').hide();

				if(data == '0')
				{
					$('.ajax-end').show();
					return;
				}

                $("#itemContainer").append(data);
				
				$(".mylazy").lazyload({
					event : "turnPage",
					effect : "fadeIn"
				});

				$('.multe-rating-nocap-sm').rating({
                'showClear': false,
                'showCaption': false,
                'stars': '5',
                'min': '0',
                'max': '5',
                'step': '1',
                'size': 'xxs'
				});

				taskFired  = false;
            })
            .fail(function(jqXHR, ajaxOptions, thrownError)
            {
                  alert('<?=Yii::t('app', 'Server not responding!')?>');
            });
    }

$(document).ready(function(){
var list = <?=empty($itemsList)?'0':'1'?>;

if(list == 0)
{
	$('.ajax-end').show();
}

 $(".mylazy").lazyload({
        event : "turnPage",
        effect : "fadeIn"
    });

//$("div.holder").jPages({
 //       containerID : "itemContainer",
			//scrollBrowse   : true,
//		perPage		: 20,
//        animation   : "fadeInUp",
 //       callback    : function( pages,
 //       items ){
            /* lazy load current images */
  //      items.showing.find("img").trigger("turnPage");
        /* lazy load next page images */
  //      items.oncoming.find("img").trigger("turnPage");
 //       }
//    });

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
								<select id="sortfilter" name="sortfilter" class="form-control">
								  <option value="" selected="selected">--<?=Yii::t('app', 'Select')?>--</option>
								  <option value="name_asc" <?=$sortfilter=='name_asc'?'selected':''?>><?=Yii::t('app', 'Name (A - Z)')?></option>
								  <option value="name_desc" <?=$sortfilter=='name_desc'?'selected':''?>><?=Yii::t('app', 'Name (Z - A)')?></option>
								  <option value="price_asc" <?=$sortfilter=='price_asc'?'selected':''?>><?=Yii::t('app', 'Price (Low to High)')?></option>
								  <option value="price_desc" <?=$sortfilter=='price_desc'?'selected':''?>><?=Yii::t('app', 'Price (High to Low)')?></option>
								</select>
						  </div>

						  <div class="col-md-2 col-sm-2 text-left"><?=Yii::t('app', 'Products Type')?>
								<select id="digitaltype" name="digitaltype" class="form-control">
								  <option value="" selected="selected">--<?=Yii::t('app', 'Select')?>--</option>
								  <option value="1" <?=$digital=='1'?'selected':''?>><?=Yii::t('app', 'Digital')?></option>
								  <option value="0" <?=$digital=='0'?'selected':''?>><?=Yii::t('app', 'Non-Digital')?></option>
								</select>
						  </div>

						  <div class="col-md-2 col-sm-2 text-left"><?=Yii::t('app', 'Category')?>
								<select id="categoryfilter" name="category_id" class="form-control">
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
								<select id="subcategoryfilter" name="sub_category_id" class="form-control">
								  <option value="" selected="selected">--<?=Yii::t('app', 'Select')?>--</option>
								</select>
						  </div>

						  <div class="col-md-2 col-sm-2 text-left"><?=Yii::t('app', 'Child Category')?>
								<select id="childcategoryfilter" name="sub_subcategory_id" class="form-control">
								  <option value="" selected="selected">--<?=Yii::t('app', 'Select')?>--</option>
								</select>
						  </div>

						  <div class="col-md-2 col-sm-2 text-left"><?=Yii::t('app', 'Vendor')?>
								<select id="vendorfilter" name="vendor_id" class="form-control">
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

				  <!--<div class="holder"></div>-->
              </div>
            </div>
            <div class="page-title">
              <h2> <?=$result_label?></h2>
            </div>
            
            <div class="product-grid-area">
              <ul class="products-grid" id="itemContainer">
				<?php include("data.php")?>                
              </ul>
            </div>
            <!--<div class="pagination-area ">
              <div class="holder"></div>
            </div>-->
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Main Container End -->
 <br>
  <div class="ajax-load text-center" style="display:none">
    <p><img src="<?=Url::base()?>/loader.gif"><?=Yii::t('app', 'Loading more items')?></p>
</div>

<div class="ajax-end text-center" style="display:none">
    <p><?=Yii::t('app', 'No more items to display!')?></p>
</div>
