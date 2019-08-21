<?php

/* @var $this \yii\web\View */
/* @var $content string */

use multefront\assets\AppAsset;
use multefront\assets\AppAssetRTL;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use multebox\widgets\Alert;
use multebox\models\ProductBrand;
use multebox\models\ProductCategory;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use multebox\models\Cart;
use multebox\models\Inventory;
use multebox\models\File;
use multebox\models\Social;
use multebox\models\Glocalization;
use multebox\models\CurrencyConversion;
use multebox\models\search\MulteModel;
use yii\helpers\Json;

if(Yii::$app->params['RTL_THEME'] == 'Yes' || $_SESSION['RTL_THEME'] == 'Yes')
	AppAssetRTL::register($this);
else
	AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<!-- Basic page needs -->
<meta charset="<?= Yii::$app->charset ?>">
<!--[if IE]>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <![endif]-->
<meta http-equiv="x-ua-compatible" content="ie=edge">
<?= Html::csrfMetaTags() ?>
<title><?= Html::encode(Yii::$app->params['APPLICATION_NAME']) ?></title>
<?php $this->head() ?>
<meta name="description" content="Mult-e-Cart: Multivendor ecommerce system">
<meta name="keywords" content="bootstrap, ecommerce, fashion, layout, responsive, multecart"/>
<!-- Mobile specific metas  , -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
if(Yii::$app->controller->route == 'product/default/detail')
{
	$invent = Inventory::findOne($_GET['inventory_id']);
	$fdet = File::find()->where("entity_type='product' and entity_id='$invent->product_id'")->orderBy("id asc")->all();
?>
<meta property="og:title" content="<?=$invent->product_name?>">
<meta property="og:image" content="<?=Yii::$app->params['web_url']?>/<?=$fdet[0]->new_file_name?>"> 
<meta property="og:image:type" content="image/jpeg/jpg/png"> 
<meta property="og:image:width" content="250"> 
<meta property="og:image:height" content="250">
<?php
}
?>
<!-- Favicon  -->
<link rel="shortcut icon" type="image/x-icon" href="<?=Yii::$app->params['web_url']?>/logo/front_favicon.ico">

<!-- Google Fonts -->
<link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700italic,700,400italic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Arimo:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Dosis:400,300,200,500,600,700,800' rel='stylesheet' type='text/css'>
<?php
$css_color= Yii::$app->params['FRONTEND_THEME_COLOR'];
?>
<?php include_once("script.php"); ?>
<?php include_once("css.php"); ?>
</head>

<body class="cms-index-index cms-home-page">
<?php $this->beginBody() ?>

<?php
if(Yii::$app->controller->route == 'site/index')
{
?>
<div class="se-pre-con"></div>
<?php
}
?>
<!--[if lt IE 8]>
      <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
  <![endif]--> 

<!-- mobile menu -->
<div id="mobile-menu">
  <ul>
  <?php
  $productCategoryList = ProductCategory::find()->where("active='1'")->all();
  foreach($productCategoryList as $productCategory)
  {
  ?>
    <li><a href="<?=Url::to(['/product/default/listing', 'category_id' => $productCategory->id])?>"><?=$productCategory->name?></a>
      <ul>
	  <?php
		$productSubCategoryList = ProductSubCategory::find()->where("parent_id = $productCategory->id and active='1'")->all();
		foreach($productSubCategoryList as $productSubCategory)
		{
			$productSubSubCategoryList = ProductSubSubCategory::find()->where("parent_id = $productSubCategory->id and active='1'")->all();
			if($productSubSubCategoryList)
			{
			?>
        <li> <a href="<?=Url::to(['/product/default/listing', 'category_id' => $productCategory->id, 'sub_category_id' => $productSubCategory->id])?>" class=""><?=$productSubCategory->name?></a>
          <ul class="level1">
		  <?php
			foreach($productSubSubCategoryList as $productSubSubCategory)
			{
		  ?>
            <li class="level2 nav-6-1-1"> <a href="<?=Url::to(['/product/default/listing', 'category_id' => $productCategory->id, 'sub_category_id' => $productSubCategory->id, 'sub_subcategory_id' => $productSubSubCategory->id])?>"><?=$productSubSubCategory->name?></a> </li>
		  <?php
			}
		  ?>
          </ul>
        </li>
		<?php
			}
		}
		?>
      </ul>
    </li>
	<?php
  }
	?>
  </ul>
</div>
<!-- end mobile menu -->
<div id="page"> 
 
  
  <!-- Header -->
  <header>
    <div class="header-container">
      <div class="header-top">
        <div class="container">
          <div class="row">
            <div class="col-lg-4 col-sm-4 hidden-xs"> 
              <!-- Default Welcome Message -->
              <div class="welcome-msg "><?=Yii::t('app', 'Welcome to ').Yii::$app->params['APPLICATION_NAME']?> </div>
              <span class="phone hidden-sm"><i class="fa fa-phone-square"></i>: <?=Yii::$app->params['company']['mobile']?></span> </div>
            
            <!-- top links -->
            <div class="headerlinkmenu col-lg-8 col-md-7 col-sm-8 col-xs-12">
              <div class="links">
                <!--<div class="myaccount"><a title="My Account" href="account_page.html"><i class="fa fa-user"></i><span class="hidden-xs">My Account</span></a></div>
                
                <div class="blog"><a title="Blog" href="blog.html"><i class="fa fa-rss"></i><span class="hidden-xs">Blog</span></a></div>-->

				<?php
				  if(Yii::$app->user->isGuest)
				  {
				  ?>
				  <div class="block block-currency">
					<div class="item-cur"> <a href="<?=Url::to(['/site/login'])?>"><i class="fa fa-unlock-alt"></i><span class="hidden-xs"><?=Yii::t('app', 'Login')?></span></a></div>
                  </div>
				<?php
				  }
				?>
				<div class="wishlist"><a title="<?=Yii::t('app', 'My Wishlist')?>" href="<?=Url::to(['/site/wishlist'])?>"><i class="fa fa-heart"></i><span class="hidden-xs"><?=Yii::t('app', 'Wishlist')?><span class="label label-primary pull-right mywishlistcount"><?=MulteModel::getCountWishlist()?></span></a></div>
				<div class="compare"><a title="<?=Yii::t('app', 'My Compare List')?>" href="<?=Url::to(['/site/compare'])?>"><i class="fa fa-signal"></i><span class="hidden-xs hidden-sm hidden-md"><?=Yii::t('app', 'Comparison')?><span class="label label-primary pull-right mycomparelistcount"><?=MulteModel::getCountComparelist()?></span></a></div>
              </div>
              <div class="language-currency-wrapper">
                <div class="inner-cl">
                  <div class="block block-language form-language">
                    <div class="lg-cur"> <span> &nbsp;<i class="fa fa-flag"></i>&nbsp; <span class="lg-fr"><?=Yii::t('app', 'Language')?></span> <i class="fa fa-angle-down"></i> </span> </div>
                    <ul>
					<?php
					$languages = Glocalization::find()->all();

					foreach($languages as $row)
					{
					?>
                      <li> <a href="<?=Url::to(['/site/convert-system-language', 'language' => $row['locale']])?>">  <span><?=$row['language']?></span> </a> </li>
					<?php
					}
					?>
                    </ul>
                  </div>
                  <div class="block block-currency">
                    <div class="item-cur"> <span><?=isset($_SESSION['CONVERTED_CURRENCY_CODE'])?$_SESSION['CONVERTED_CURRENCY_CODE'].' ('.$_SESSION['CONVERTED_CURRENCY_SYMBOL'].')':Yii::$app->params['SYSTEM_CURRENCY'].' ('.Yii::$app->params['SYSTEM_CURRENCY_SYMBOL'].')'?> </span> <i class="fa fa-angle-down"></i></div>
                    <ul>
                      <!--<li> <a href="#"><span class="cur_icon">€</span> EUR</a> </li>
                      <li> <a href="#"><span class="cur_icon">¥</span> JPY</a> </li>
                      <li> <a class="selected" href="#"><span class="cur_icon">$</span> USD</a> </li>-->
						<?php
						$currency = CurrencyConversion::find()->all();

						$currency_list = [];

						foreach($currency as $row)
						{
							array_push($currency_list, $row->to);
						}

						$currency_list = array_unique($currency_list);

						foreach ($currency_list as $row)
						{
						?>
						  <li><a href="<?=Url::to(['/site/convert-system-currency', 'currency' => $row])?>"><?=$row?></a></li>
						<?php
						}
						?>
                    </ul>
                  </div>
				  <?php
				  if(Yii::$app->user->isGuest)
				  {
				  ?>
				  <div class="block block-currency">
                    <div class="item-cur"> <span><?=Yii::t('app', 'Register')?> </span> <i class="fa fa-angle-down"></i></div>
                    <ul>
                      <li><a href="<?=Url::to(['/site/signup'])?>"><span><i class="fa fa-user"></i></span> <?=Yii::t('app', 'As User')?></a> </li>
                      <li><a href="<?=Url::to(['/site/vendor-signup'])?>"><span><i class="fa fa-male"></i></span> <?=Yii::t('app', 'As Vendor')?></a> </li>	
                    </ul>
                  </div>
				  <?php
				  }
				  else
				  {
				  ?>
				  <div class="block block-currency">
                    <div class="item-cur"> <i class="fa fa-user"></i> <div class="cur-user-name"><?=Yii::$app->user->identity->first_name?> <?=Yii::$app->user->identity->last_name?></div> </div>
                    <ul>
					  <li><a href="<?=Url::to(['/customer/default/account'])?>"><span><i class="fa fa-info-circle"></i></span>&nbsp;&nbsp;&nbsp;<?=Yii::t('app', 'My Account')?></a></li>
					  <li><a href="<?=Url::to(['/order/default/history'])?>"><span><i class="fa fa-history"></i></span>&nbsp;&nbsp;&nbsp;<?=Yii::t('app', 'Order History')?></a></li>
					  <li><a href="<?=Url::to(['/customer/default/support'])?>"><span><i class="fa fa-ticket"></i></span>&nbsp;&nbsp;&nbsp;<?=Yii::t('app', 'Report Issue')?></a></li>
					  <li><a href="<?=Url::to(['/site/logout'])?>" data-method="post"><span><i class="fa fa-lock"></i></span>&nbsp;&nbsp;&nbsp;<?=Yii::t('app', 'Logout')?></a></li>
                    </ul>
                  </div>
				  <?php
				  }
				  ?>
                </div>
                
                <!-- End Default Welcome Message --> 
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-sm-3 col-md-3 col-xs-12"> 
            <!-- Header Logo -->
            <div class="logo"><a title="<?=Yii::$app->params['APPLICATION_NAME']?>" href="<?=Url::to(['/site/index'])?>"><img alt="responsive theme logo" src="<?=Yii::$app->params['web_url']?>/logo/front_logo.png" class="img-logo"></a> </div>
            <!-- End Header Logo --> 
          </div>
          <div class="col-xs-9 col-sm-6 col-md-6"> 
            <!-- Search -->
            
            <div class="top-search">
              <div id="search">
			  <form method="post" action="<?=Url::to(['/product/default/search'])?>">
				  <div class="input-group">
					<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
					  <input id="filter_name" type="text" name="searchbox" placeholder="<?=Yii::t('app', 'Search')?>" class="form-control" />
					  <button type="submit" class="btn-search"><i class="fa fa-search"></i></button>
				  </div>
			  </form>
              </div>
            </div>
            
            <!-- End Search --> 
          </div>
          <!-- top cart -->
          <?php
		  if(Yii::$app->user->isGuest)
		  {
			  $cart_items = Cart::find()->where("session_id='".session_id()."'")->all();
		  }
		  else
		  {
			  $cart_items = Cart::find()->where("user_id=".Yii::$app->user->identity->id)->all();
		  }

		  $itemcount = 0;
		  foreach($cart_items as $cart)
		  {
			  $itemcount += $cart->total_items;
		  }
		  ?>
          <div class="col-lg-3 col-xs-3 top-cart">
            <div class="top-cart-contain">
              <div class="mini-cart">
                <div data-toggle="dropdown" data-hover="dropdown" class="basket dropdown-toggle"> <a href="#">
                  <div class="cart-icon"><i class="fa fa-shopping-cart mycart"></i></div>
                  <div class="shoppingcart-inner hidden-xs"><span class="cart-title"><?=Yii::t('app', 'Shopping Cart')?></span> <span class="cart-total cartcount"><?=$itemcount?> <?=Yii::t('app', 'Item(s)')?></span></div>
                  </a></div>
                <div>
                  <div class="top-cart-content">
                    <div class="block-subtitle hidden-xs cartdiv"><?=Yii::t('app', 'Recently added item(s)')?></div>
                    <ul id="cart-sidebar" class="mini-products-list">
					<input type="hidden" class="hiddencartvalue" value="">
					<input type="hidden" class="hiddenremainingstock" value="">
					<?php
					foreach($cart_items as $cart)
					{
						$inventory_item = Inventory::findOne($cart->inventory_id);
						$prod_title = $inventory_item->product_name;
						$fileDetails = File::find()->where("entity_type='product' and entity_id=".$inventory_item->product_id)->one();
					?>
                      <li class="item odd"> <a href="<?=Url::to(['/product/default/detail', 'inventory_id' => $cart->inventory_id])?>" class="product-image"><img src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$prod_title?>" title="<?=$prod_title?>" class="cart-img" ></a>
                        <div class="product-details"> 
						  <!--<a href="#" title="Remove This Item" class="remove-cart"><i class="icon-close"></i></a>-->
                          <p class="product-name"><a href="<?=Url::to(['/product/default/detail', 'inventory_id' => $cart->inventory_id])?>"><?=$prod_title?></a> </p>
                          <strong> x </strong><span class="price"><?=$cart->total_items?></span> </div>
                      </li>
					<?php
					}
					?>
                    </ul>
                    <!--<div class="top-subtotal">Subtotal: <span class="price">$520.00</span></div>-->
                    <div class="actions">
					  <a href="<?=Url::to(['/order/default/cart'])?>" class="btn btn-primary view-cart"><i class="fa fa-shopping-cart"></i> <?=Yii::t('app', 'View Cart')?></a>
					  <a href="<?=Url::to(['/order/default/checkout'])?>" class="btn btn-danger btn-checkout"><i class="fa fa-share"></i> <?=Yii::t('app', 'Checkout')?></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
  <!-- end header --> 
  <?= Alert::widget() ?>
  <!-- Navbar -->
  <nav>
    <div class="container">
      <div class="row">
        <div class="col-md-3 col-sm-4">
          <div class="mm-toggle-wrap">
            <div class="mm-toggle"> <i class="fa fa-align-justify"></i> </div>
            <span class="mm-label"><?=Yii::t('app', 'Categories')?></span> </div>
          <div class="mega-container visible-lg visible-md visible-sm">
            <div class="navleft-container">
              <div class="mega-menu-title">
                <h3><?=Yii::t('app', 'Categories')?></h3>
              </div>
              <div class="mega-menu-category">
                <ul class="nav">
				  <?php
				  $productCategoryList = ProductCategory::find()->where("active='1'")->all();
				  foreach($productCategoryList as $productCategory)
				  {
					  $productSubCategoryList = ProductSubCategory::find()->where("parent_id = $productCategory->id and active='1'")->all();
					
					  if($productSubCategoryList)
					  {
						  if(count($productSubCategoryList) <= 2)
						  {
							  $wrap = "column1";
							  $class = "col-md-12";
						  }
						  else
						  if(count($productSubCategoryList) > 2 && count($productSubCategoryList) <= 4)
						  {
							  $wrap = "column2";
							  $class = "col-sm-6";
						  }
						  else
						  {
							  $wrap = "";
							  $class = "col-md-4 col-sm-6";
						  }
				  ?>
                  <li> <a href="<?=Url::to(['/product/default/listing', 'category_id' => $productCategory->id])?>"><i class="icon fa fa-star fa-fw"></i> <?=$productCategory->name?></a>
                    <div class="wrap-popup <?=$wrap?>">
                      <div class="popup">
                        <div class="row">
						  <div class="<?=$class?>">
							<?php
							$catcount = 0;
							
							foreach($productSubCategoryList as $productSubCategory)
							{
								$catcount++;
							?>
                            <h3><?= Html::a($productSubCategory->name, ['/product/default/listing', 'category_id' => $productCategory->id, 'sub_category_id' => $productSubCategory->id]) ?></h3>
                            <ul class="nav">
							<?php
							$productSubSubCategoryList = ProductSubSubCategory::find()->where("parent_id = $productSubCategory->id and active='1'")->all();
							foreach($productSubSubCategoryList as $productSubSubCategory)
							{
							?>
                              <li><?= Html::a($productSubSubCategory->name, ['/product/default/listing', 'category_id' => $productCategory->id, 'sub_category_id' => $productSubCategory->id, 'sub_subcategory_id' => $productSubSubCategory->id]) ?></li>
							<?php
							}
							?>
                            </ul>
                            <br>
							<?php
								if ($catcount == 2)
								{
									$catcount = 0;
									?>
									</div>
									<div class="col-md-4 col-sm-6 has-sep">
									<?php
								}
							}
							?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
				  <?php
					  }
					  else
					  {
					  ?>
						<li class="nosub"><a href="<?=Url::to(['/product/default/listing', 'category_id' => $productCategory->id])?>"><i class="icon fa fa-star fa-fw"></i> <?=$productCategory->name?></a></li>
					  <?php
					  }
				  }
				  ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-9 col-sm-8">
          <div class="mtmegamenu">
            <ul>
              <li class="mt-root demo_custom_link_cms">
                <div class="mt-root-item">
				  <a href="<?=Url::to(['/site/index'])?>">
                    <div class="title title_font"><span class="title-text"><i class="icon fa fa-home"></i>&nbsp;<?=Yii::t('app', 'Home')?></span></div>
                  </a>
				</div>
              </li>

              
              <li class="mt-root">
                <div class="mt-root-item">
                  <div class="title title_font"><span class="title-text"><?=Yii::t('app', 'Best Seller')?></span></div>
                </div>
                <ul class="menu-items col-xs-12">
				<?php
					//$inventoryItemsList = Inventory::find()->where('stock > 0')->orderBy(['total_sale'=>SORT_DESC])->limit(3)->all();
					//$productList = Inventory::find()->select(['product_id', 'vendor_id'])->where('stock > 0 and active = 1')->orderBy(['total_sale'=>SORT_DESC])->limit(3)->distinct()->all();

					$all_prod_list = Inventory::find()->where('stock > 0 and active = 1')->orderBy(['total_sale'=>SORT_DESC])->limit(25)->all();
					$productList = MulteModel::getDistinctProdVendor($all_prod_list);

					//foreach($inventoryItemsList as $inventoryItem)
					foreach ($productList as $plist)
					{
					  $inventoryItem = Inventory::find()->where('stock > 0 and active = 1')->andWhere(['product_id' => $plist['product_id'], 'vendor_id' => $plist['vendor_id']])->orderBy(['total_sale'=>SORT_DESC])->limit(1)->one();

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
                  <li class="menu-item depth-1 product menucol-1-3 withimage">
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
                          <div class="pr-img-area"> <a title="<?=$inventoryItem->product_name?>" href="<?=$url?>">
                            <figure class=""> <img class="first-img mainlazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"> <img class="hover-img mainlazy" src="<?=Url::base()?>/loading.gif" data-src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$inventoryItem->product_name?>"></figure>
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
                  </li>
                  <?php
				  }
				  ?>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </nav>
  <!-- end nav --> 
  
  
  
  <!-- main container -->

<?php
if(Yii::$app->controller->route != 'site/index')
{
?>
   <div class="main-container col1-layout">
    <div class="container">
      <div class="row">
<?php
}
?>
        <?php  echo Breadcrumbs::widget ( [ 'links' => isset ( $this->params ['breadcrumbs'] ) ? $this->params ['breadcrumbs'] : [ ],
															'homeLink' => [
																			'label' => Yii::t('app', 'Home'),
																			'url' => Yii::$app->homeurl,
																			]
														]) ?>
        <?= $content ?>
<?php
if(Yii::$app->controller->route != 'site/index')
{
?>
</div></div></div>
<?php
}
?>
  <!-- end main container --> 
  
  
  
  <!-- Footer -->
  <br>
  <footer>
    <div class="footer-newsletter">
      <div class="container">
        <div class="row">
          <div class="col-md-8 col-sm-7">
            <form id="newsletter-validate-detail" method="post" action="<?=Url::to(['/site/news-signup'])?>">
			  <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
              <h3 class="hidden-sm"><?=Yii::t('app', 'Sign up for newsletter')?></h3>
              <div class="newsletter-inner">
                <input class="newsletter-email" name="newsemail" placeholder="<?=Yii::t('app', 'Enter Your Email')?>"/>
                <button class="button subscribe" type="submit" title="<?=Yii::t('app', 'Subscribe')?>"><?=Yii::t('app', 'Subscribe')?></button>
              </div>
            </form>
          </div>
		  <?php
		  $social = Social::find()->where("active = 1")->all();
		  ?>
          <div class="social col-md-4 col-sm-5 social-icons">
            <ul class="inline-mode">
			<?php
			foreach ($social as $row)
			{
				switch($row['id'])
				{
					case Social::_FACEBOOK:
						?>
						<li class="social-network fb"><a title="<?=Yii::t('app', 'Join us on Facebook')?>" data-toggle="tooltip" data-placement="bottom" target="_blank" href="<?=$row['link']?>"><i class="fa fa-facebook"></i></a></li>
						<?php
						break;
					case Social::_GOOGLE_PLUS:
						?>
						<li class="social-network googleplus"><a title="<?=Yii::t('app', 'Join us on Google')?>+" data-toggle="tooltip" data-placement="bottom" target="_blank" href="<?=$row['link']?>"><i class="fa fa-google-plus"></i></a></li>
						<?php
						break;
					case Social::_TWITTER:
						?>
						<li class="social-network tw"><a title="<?=Yii::t('app', 'Join us on Twitter')?>" data-toggle="tooltip" data-placement="bottom" target="_blank" href="<?=$row['link']?>"><i class="fa fa-twitter"></i></a></li>
						<?php
						break;
					case Social::_LINKEDIN:
						?>
						<li class="social-network linkedin"><a title="<?=Yii::t('app', 'Join us on LinkedIn')?>" data-toggle="tooltip" data-placement="bottom" target="_blank" href="<?=$row['link']?>"><i class="fa fa-linkedin"></i></a></li>
						<?php
						break;
					case Social::_YOUTUBE:
						?>
						<li class="social-network youtube"><a title="<?=Yii::t('app', 'Join us on YouTube')?>" data-toggle="tooltip" data-placement="bottom" target="_blank" href="<?=$row['link']?>"><i class="fa fa-youtube"></i></a></li>
						<?php
						break;
					case Social::_INSTAGRAM:
						?>
						<li class="social-network instagram"><a title="<?=Yii::t('app', 'Join us on Instagram')?>" data-toggle="tooltip" data-placement="bottom" target="_blank" href="<?=$row['link']?>"><i class="fa fa-instagram"></i></a></li>
						<?php
						break;
				}
			}
			?>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-sm-6 col-md-4 col-xs-12 col-lg-3">
          <div class="footer-logo"><a href="<?=Url::to(['/site/index'])?>"><img src="<?=Yii::$app->params['web_url']?>/logo/front_logo.png" alt="footer logo" class="img-footer"></a> </div>
          <p><?=Yii::t('app', 'Feel free to reach out to us in case of any queries')?>.</p>
          <div class="footer-content">
            <div class="email"> <i class="fa fa-envelope"></i>
              <p><a href="mailto:<?=Yii::$app->params['company']['company_email']?>"><?=Yii::$app->params['company']['company_email']?></a></p>
            </div>
            <div class="phone"> <i class="fa fa-phone"></i>
              <p><?=Yii::$app->params['company']['mobile']?></p>
            </div>
            <div class="address"> <i class="fa fa-map-marker"></i>
              <p> <?=Yii::$app->params['address']['address_1']?>, <?=Yii::$app->params['address']['address_2']?>, <?=Yii::$app->params['address']['city']?>, <?=Yii::$app->params['address']['state']?>, <?=Yii::$app->params['address']['country']?>-<?=Yii::$app->params['address']['zipcode']?></p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-3 col-xs-12 col-lg-3 collapsed-block">
          <div class="footer-links">
            <h3 class="links-title"><?=Yii::t('app', 'Information')?><a class="expander visible-xs" href="#TabBlock-1">+</a></h3>
            <div class="tabBlock" id="TabBlock-1">
              <ul class="list-links list-unstyled">
                <li><a href="<?=Url::to(['/site/delivery'])?>"><?=Yii::t('app', 'Delivery Information')?></a></li>
                <li><a href="<?=Url::to(['/site/returns'])?>"><?=Yii::t('app', 'Returns')?></a></li>
                <li><a href="<?=Url::to(['/site/privacy'])?>"><?=Yii::t('app', 'Privacy Policy')?></a></li>
                <li><a href="<?=Url::to(['/site/faq'])?>"><?=Yii::t('app', 'FAQs')?></a></li>
                <li><a href="<?=Url::to(['/site/tnc'])?>"><?=Yii::t('app', 'Terms and Conditions')?></a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-3 col-xs-12 col-lg-3 collapsed-block">
          <div class="footer-links">
            <h3 class="links-title"><?=Yii::t('app', 'Insider')?><a class="expander visible-xs" href="#TabBlock-3">+</a></h3>
            <div class="tabBlock" id="TabBlock-3">
              <ul class="list-links list-unstyled">
                <li> <a href="<?=Url::to(['/site/about'])?>"><?=Yii::t('app', 'About Us')?></a> </li>
                <li> <a href="<?=Url::to(['/site/contact'])?>"><?=Yii::t('app', 'Contact Us')?></a> </li>
                <li> <a href="<?=Url::to(['/order/default/history'])?>"><?=Yii::t('app', 'My Orders')?></a> </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-2 col-xs-12 col-lg-3 collapsed-block">
          <div class="footer-links">
            <h3 class="links-title"><?=Yii::t('app', 'Service')?><a class="expander visible-xs" href="#TabBlock-4">+</a></h3>
            <div class="tabBlock" id="TabBlock-4">
              <ul class="list-links list-unstyled">
                <li> <a href="<?=Url::to(['/customer/default/account'])?>"><?=Yii::t('app', 'Account')?></a> </li>
                <li> <a href="<?=Url::to(['/site/wishlist'])?>"><?=Yii::t('app', 'Wishlist')?></a> </li>
                <li> <a href="<?=Url::to(['/order/default/cart'])?>"><?=Yii::t('app', 'Shopping Cart')?></a> </li>
                <li> <a href="<?=Url::to(['/site/returns'])?>"><?=Yii::t('app', 'Return Policy')?></a> </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-coppyright">
      <div class="container">
        <div class="row">
          <div class="col-sm-6 col-xs-12 coppyright"> <?=Yii::$app->params['APPLICATION_NAME']?> <?=date('Y')?> | &copy; <a href="http://www.techraft.in/" target="_blank">TechRaft Solutions </a>&trade; </div>
          <div class="col-sm-6 col-xs-12">
            <div class="payment">
              <ul>
                <li><a href="#"><img title="Visa" data-toggle="tooltip" alt="Visa" src="<?=Url::base()?>/images/visa.png"></a></li>
                <li><a href="#"><img title="Paypal" data-toggle="tooltip" alt="Paypal" src="<?=Url::base()?>/images/paypal.png"></a></li>
                <li><a href="#"><img title="Master Card" data-toggle="tooltip" alt="Master Card" src="<?=Url::base()?>/images/master-card.png"></a></li>
				<li><a href="#"><img title="Razorpay" data-toggle="tooltip" alt="Razorpay" src="<?=Url::base()?>/images/razorpay.png"></a></li>
				<li><a href="#"><img title="BitPay" data-toggle="tooltip" alt="BitPay" src="<?=Url::base()?>/images/bitcoin.png"></a></li>
				<li><a href="#"><img title="Stripe" data-toggle="tooltip" alt="Stripe" src="<?=Url::base()?>/images/stripe.png"></a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <a href="#" class="totop"> </a> 
  <!-- End Footer --> 
  <!--Newsletter Popup Start--> 

 <!--End of Newsletter Popup-->   
  </div>


<!-- JS --> 
<?php $this->endBody() ?>   

  <script type="text/javascript">
  /* <![CDATA[ */   
  var mega_menu = '0';
  
  /* ]]> */
  </script> 

<!-- Revolution Slider --> 
<script type="text/javascript">
          jQuery(document).ready(function() {
			
			if ($('.mainindex').length == 0) {
			  jQuery('.mega-menu-category').slideUp();
			}
			
			<?php
				if(!isset($_SESSION['newspopup']))
				{
			?>
			jQuery('#myModal').modal('show');

            jQuery('#myModal').modal({
            keyboard: false,
           backdrop:false
          }); 
			<?php
				}
			?>

          });
</script> 
<div class="modal fade wishconfirmmodal" >
  <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-body">
          <h4><p class="text-center"><?=Yii::t('app', 'Added!')?>! <i class="glyphicon glyphicon-ok text-success"></i></p></h4>
        </div>
      </div>
  </div>
</div>
<div class="modal fade compareconfirmmodal" >
  <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-body">
          <h4><p class="text-center"><?=Yii::t('app', 'Added!')?>! <i class="glyphicon glyphicon-ok text-success"></i></p></h4>
        </div>
      </div>
  </div>
</div>
<div class="modal fade wishexistmodal" >
  <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-body">
          <h4><p class="text-center"><?=Yii::t('app', 'Already Exists!')?>! <i class="glyphicon glyphicon-exclamation-sign text-warning"></i></p></h4>
        </div>
      </div>
  </div>
</div>
<div class="modal fade compareexistmodal" >
  <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-body">
          <h4><p class="text-center"><?=Yii::t('app', 'Already Exists!')?>! <i class="glyphicon glyphicon-exclamation-sign text-warning"></i></p></h4>
        </div>
      </div>
  </div>
</div>
<div class="modal fade comparemaxmodal" >
  <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-body">
          <h4><p class="text-center"><?=Yii::t('app', 'Maximum Items Already Added!')?>! <i class="glyphicon glyphicon-exclamation-sign text-warning"></i></p></h4>
        </div>
      </div>
  </div>
</div>
</body>
</html>
<?php $this->endPage() ?>

<script>
function Add_Error(obj,msg){
	 $(obj).parents('.form-group').addClass('has-error');
	 $(obj).parents('.form-group').append('<div style="color:#D16E6C; clear:both" class="error"><i class="icon-remove-sign"></i> '+msg+'</div>');
	 return true;
}

function Remove_Error(obj){
	$(obj).parents('.form-group').removeClass('has-error');
	$(obj).parents('.form-group').children('.error').remove();
	return false;
}

function Add_ErrorTag(obj,msg){
	obj.css({'border':'1px solid #D16E6C'});
	
	obj.after('<div style="color:#D16E6C; clear:both" class="error"><i class="icon-remove-sign"></i> '+msg+'</div>');
	 return true;
}

function Remove_ErrorTag(obj){
	obj.removeAttr('style').next('.error').remove();
	return false;
}

 //$(document).ready(function () {
		//$(".add_to_wishlist").click(function() {
			$(document).on('click', '.add_to_wishlist', function() {
			$.post("<?=Url::to(['/site/add-to-wishlist'])?>", { 'id': $('input', this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
								if(result == -1)
								{
									window.location.href = '<?=Url::to(['/site/login'])?>';
								}
								else if(result == -2)
								{
									$('.wishexistmodal').modal('show');
									setTimeout(function() {$('.wishexistmodal').modal('hide');}, 1500);
								}
								else 
								{
									$('.mywishlistcount').html(result);
									$('.wishconfirmmodal').modal('show');
									setTimeout(function() {$('.wishconfirmmodal').modal('hide');}, 1500);
								}
				})
		});

		//$(".add_to_compare").click(function() {
		$(document).on('click', '.add_to_compare', function() {
			$.post("<?=Url::to(['/site/add-to-comparelist'])?>", { 'id': $('input', this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
								if(result == -1)
								{
									$('.compareexistmodal').modal('show');
									setTimeout(function() {$('.compareexistmodal').modal('hide');}, 1500);
								}
								else if(result == -2)
								{
									$('.comparemaxmodal').modal('show');
									setTimeout(function() {$('.comparemaxmodal').modal('hide');}, 1500);
								}
								else
								{
									$('.mycomparelistcount').html(result);
									$('.compareconfirmmodal').modal('show');
									setTimeout(function() {$('.compareconfirmmodal').modal('hide');}, 1500);
								}
				})
		});

		/*$( ".basket" ).click(function() {
			  setTimeout(function() {$( ".top-cart-content" ).trigger("click");}, 1);
			});*/

        $('.dropdown-toggle').dropdown();

		$('.multe-rating').rating({
				'showClear': false,
                'showCaption': true,
                'stars': '5',
                'min': '0',
                'max': '5',
                'step': '1',
                'size': 'xs',
                'starCaptions': {0: "<?=Yii::t('app', 'Not Rated')?>", 1: "<?=Yii::t('app', 'Poor')?>", 2: "<?=Yii::t('app', 'Fair')?>", 3: "<?=Yii::t('app', 'Good')?>", 4: "<?=Yii::t('app', 'Very Good')?>", 5: "<?=Yii::t('app', 'Excellent')?>"}
            });

		$('.multe-rating-nocap').rating({
				'showClear': false,
                'showCaption': false,
                'stars': '5',
                'min': '0',
                'max': '5',
                'step': '1',
                'size': 'xs'
            });
		
		$('.multe-rating-sm').rating({
				'showClear': false,
                'showCaption': true,
                'stars': '5',
                'min': '0',
                'max': '5',
                'step': '1',
                'size': 'xxs',
				'starCaptions': {0: "<?=Yii::t('app', 'Not Rated')?>", 1: "<?=Yii::t('app', 'Poor')?>", 2: "<?=Yii::t('app', 'Fair')?>", 3: "<?=Yii::t('app', 'Good')?>", 4: "<?=Yii::t('app', 'Very Good')?>", 5: "<?=Yii::t('app', 'Excellent')?>"}
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

    //});

	$(window).load(function() {
		// Animate loader off screen
		$(".se-pre-con").fadeOut("slow");;
	});

	$(".mainlazy").lazyload({
        event : "turnPage",
        effect : "fadeIn"
    });

</script>