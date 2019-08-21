<?php

/* @var $this \yii\web\View */
/* @var $content string */
use multebox\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;
use multeback\assets\AppAsset;
use multebox\models\search\MulteModel;

//AppAsset::register($this);

function activeParentMenu($array)
{
	return in_array(Yii::$app->controller->route,$array)?'active':'';	
}

function activeMenu($link)
{
	return Yii::$app->controller->route==$link?'active':'';	
}

function activeEstimateMenu($entity_type)
{
	return ($_REQUEST['entity_type'] == $entity_type ) ? 'active' : '';
}

function activeSubMenu($action, $entity_type)
{
	$path = parse_url( $_SERVER['REQUEST_URI']);
	$route = Yii::$app->controller->route;
	$route = explode( "/", trim( $route, "/" ) );
	return ( $action == $route[2] && $_REQUEST['entity_type'] == $entity_type ) ? 'active' : '';
}

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?=Yii::$app->params['web_url']?>/logo/back_favicon.ico" rel="icon" />
	<!-- jQuery 3 -->
<script src="<?=Url::base()?>/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?=Url::base()?>/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Fix Bootstrap Dropdown problem -->
<script>
    $(document).ready(function () {
        $('.dropdown-toggle').dropdown();
    });
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?=Url::base()?>/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=Url::base()?>/dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->

	<link rel="stylesheet" href="<?=Url::base()?>/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=Url::base()?>/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?=Url::base()?>/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <?php
  if(Yii::$app->params['RTL_THEME'] == 'No')
  {
  ?>
  <link rel="stylesheet" href="<?=Url::base()?>/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?=Url::base()?>/dist/css/skins/_all-skins.min.css">
  <?php
  }
  else
  {
  ?>
  <link rel="stylesheet" href="<?=Url::base()?>/dist/css/AdminLTE-rtl.min.css">
  <link rel="stylesheet" href="<?=Url::base()?>/dist/css/skins/_all-skins-rtl.min.css">
  <?php
  }
  ?>
 
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

	<?php include_once("script.php"); ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">

<?php $this->beginBody() ?>

<div class="wrapper">
  <header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b><?=Yii::$app->params['APPLICATION_SHORT_NAME']?></b></span>
      <!-- logo for regular state and mobile devices -->
      <!--<span class="logo-lg"><b>Mult</b>-e-Cart</span>-->
	  <span class="logo-lg"><b><?=Yii::$app->params['APPLICATION_NAME']?></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only"><?=Yii::t('app', 'Toggle navigation')?></span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
         
        
         
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?=Yii::$app->params['web_url']?>/users/<?=Yii::$app->user->identity->id?>.png" class="user-image" alt="">
              <span class="hidden-xs"><?=Yii::$app->user->identity->first_name?> <?=Yii::$app->user->identity->last_name?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?=Yii::$app->params['web_url']?>/users/<?=Yii::$app->user->identity->id?>.png" class="img-circle" alt="">

                <p>
                  <?=Yii::$app->user->identity->first_name?> <?=Yii::$app->user->identity->last_name?>
                  <small>(<?=Yii::$app->user->identity->username?>)</small>
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row">
                  <div class="text-center">
					  <?php
					    $url1 = Url::to(['/user/user/change-password']);
					  ?>
                    <a href="<?=$url1?>"><i class="fa fa-exchange"></i> <small><?=Yii::t('app', 'Change Password')?></small></a>
                  </div>
                </div>
                <!-- /.row -->
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
					<?php
					    $url1 = Url::to(['/user/user/view', 'id' => Yii::$app->user->getId()]);
					?>
				  <a href="<?=$url1?>" class="btn btn-default btn-flat"><i class="fa fa-user"></i> <?=Yii::t('app', 'Profile')?></a>
                </div>
                <div class="pull-right">
				  <a href="<?= Url::to(['/site/logout'])?>" data-method="post" class="btn btn-default btn-flat"><i class="fa fa-sign-out"></i> <?=Yii::t('app', 'Sign out')?></a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
		  <?php
		  if(Yii::$app->user->can('GlobalSettings.Index'))
		  {
		  ?>
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
		  <?php
		  }
		  ?>
        </ul>
      </div>
    </nav>
  </header>

  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?=Yii::$app->params['web_url']?>/logo/back_logo.png" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>
            <?=Yii::$app->user->identity->first_name?> <?=Yii::$app->user->identity->last_name?>
          </p>
          <a href="#"><i class="fa fa-circle text-success"></i> <?=Yii::t('app', 'Online')?></a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="<?=Yii::t('app', 'Search')?>">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header"><?=Yii::t('app', 'MAIN NAVIGATION')?></li>

		<li class="<?=isset(Yii::$app->controller->route)?activeParentMenu(['site/index']):'active'?>">
          <a href="<?=Url::to(['/site/index'])?>">
            <i class="fa fa-dashboard text-yellow"></i> <span><?=Yii::t('app', 'Dashboard')?></span>
          </a>
        </li>
		
		<!-- Begin Vendor Menu -->
		<?php
		$vendor_menu=array('vendor/vendor/create','vendor/vendor/index','vendor/vendor/view', 'vendor/vendor/update', 'finance/vendor-invoices/create','finance/vendor-invoices/index','finance/vendor-invoices/view', 'finance/vendor-invoices/update', 'finance/vendor-invoices/get-invoice');
		if((Yii::$app->user->can('Vendor.Index') || Yii::$app->user->can('Vendor.Create')))
		{
		?>
        <li class="treeview <?=activeParentMenu($vendor_menu)?>">
          <a href="#">
            <i class="fa fa-cubes text-green"></i>
            <span><?=Yii::t('app', 'Vendors')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php 
			if(Yii::$app->user->can('Vendor.Create'))
			{
			?>
				<li class="<?=activeMenu('vendor/vendor/create')?>"><a href="<?=Url::to(['/vendor/vendor/create'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Add Vendor')?></a></li>
			<?php
			}
			
			if(Yii::$app->user->can('Vendor.Index'))
			{
			?>
				<li class="<?=activeMenu('vendor/vendor/index')?>"><a href="<?=Url::to(['/vendor/vendor/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Vendors')?></a></li>
            <?php
			}
			?>

			<?php
		
			if(Yii::$app->user->can('VendorInvoices.Index'))
			{
			?>
				<li class="<?=activeMenu('finance/vendor-invoices/index')?>"><a href="<?=Url::to(['/finance/vendor-invoices/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'View Vendor Invoices')?></a></li>
			<?php
			}
			?>

          </ul>
        </li>

		<?php
		}
		?>
		<!-- End Vendor Menu -->

		<!-- Begin Customer Menu -->
		<?php
		$customer_menu=array('customer/customer/create','customer/customer/index','customer/customer/view', 'customer/customer/update');
		if((Yii::$app->user->can('Customer.Index') || Yii::$app->user->can('Customer.Create')))
		{
		?>
        <li class="treeview <?=activeParentMenu($customer_menu)?>">
          <a href="#">
            <i class="fa fa-users text-yellow"></i>
            <span><?=Yii::t('app', 'Customers')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php 
			if(Yii::$app->user->can('Customer.Create'))
			{
			?>
				<li class="<?=activeMenu('customer/customer/create')?>"><a href="<?=Url::to(['/customer/customer/create'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Add Customer')?></a></li>
			<?php
			}
			
			if(Yii::$app->user->can('Customer.Index'))
			{
			?>
				<li class="<?=activeMenu('customer/customer/index')?>"><a href="<?=Url::to(['/customer/customer/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Customers')?></a></li>
            <?php
			}
			?>
          </ul>
        </li>
		<?php
		}
		?>
		<!-- End Customer Menu -->

		<!-- Begin Product Category Menu -->
		<?php
		if((Yii::$app->user->can('ProductCategory.Index') || Yii::$app->user->can('ProductCategory.Create')))
		{
		$product_menu=array('product/product-category/create','product/product-category/index','product/product-category/view', 'product/product-category/update', 'product/product-sub-category/view', 'product/product-sub-sub-category/view', 'product/product-attributes/create');
		?>
		<li class="treeview <?=activeParentMenu($product_menu)?>">
          <a href="#">
            <i class="fa fa-object-group text-info"></i> <span><?=Yii::t('app', 'Product Categories')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php
			if(Yii::$app->user->can('ProductCategory.Create'))
			{ 
			?>
				<li class="<?=activeMenu('product/product-category/create')?>"><a href="<?=Url::to(['/product/product-category/create'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Add Product Category')?></a></li>
			<?php
			}
			
			if(Yii::$app->user->can('ProductCategory.Index'))
			{
			?>
				<li class="<?=activeMenu('product/product-category/index')?>"><a href="<?=Url::to(['/product/product-category/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Product Categories')?></a></li>
			<?php
			}
			?>
          </ul>
        </li>
		<?php
		}
		?>
		<!-- End Product Category Menu -->

		<!-- Begin Commission Menu -->
		<?php
		if((Yii::$app->user->can('Commission.Index') || Yii::$app->user->can('Commission.Create')))
		{
		$commission_menu=array('finance/commission/create','finance/commission/index','finance/commission/view', 'finance/commission/update', 'finance/commission-details/index');
		?>
		<li class="treeview <?=activeParentMenu($commission_menu)?>">
          <a href="#">
            <i class="fa fa-dollar text-red"></i> <span><?=Yii::t('app', 'Commission')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php
			if(Yii::$app->user->can('Commission.Create'))
			{ 
			?>
				<li class="<?=activeMenu('finance/commission/create')?>"><a href="<?=Url::to(['/finance/commission/create'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Create Commission Rule')?></a></li>
			<?php
			}
			
			if(Yii::$app->user->can('Commission.Index'))
			{
			?>
				<li class="<?=activeMenu('finance/commission/index')?>"><a href="<?=Url::to(['/finance/commission/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Commission Rules')?></a></li>
				<li class="<?=activeMenu('finance/commission-details/index')?>"><a href="<?=Url::to(['/finance/commission-details/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'View Commission Details')?></a></li>
			<?php
			}
			?>
          </ul>
        </li>
		<?php
		}
		?>
		<!-- End Commission Menu -->

		<!-- Begin Global Discount Menu -->
		<?php
		if((Yii::$app->user->can('GlobalDiscount.Index') || Yii::$app->user->can('GlobalDiscount.Create')))
		{
		$global_discount_menu=array('finance/global-discount/create','finance/global-discount/index','finance/global-discount/view', 'finance/global-discount/update');
		?>
		<li class="treeview <?=activeParentMenu($global_discount_menu)?>">
          <a href="#">
            <i class="fa fa-diamond text-yellow"></i> <span><?=Yii::t('app', 'Global Discount')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php
			if(Yii::$app->user->can('GlobalDiscount.Create'))
			{ 
			?>
				<li class="<?=activeMenu('finance/global-discount/create')?>"><a href="<?=Url::to(['/finance/global-discount/create'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Create Global Discount')?></a></li>
			<?php
			}
			
			if(Yii::$app->user->can('GlobalDiscount.Index'))
			{
			?>
				<li class="<?=activeMenu('finance/global-discount/index')?>"><a href="<?=Url::to(['/finance/global-discount/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Global Discounts')?></a></li>
			<?php
			}
			?>
          </ul>
        </li>
		<?php
		}
		?>
		<!-- End Global Discount  Menu -->

		<!-- Begin Products Menu -->
		<?php
		if((Yii::$app->user->can('Product.Index') || Yii::$app->user->can('Product.Create')))
		{
		$product_menu=array('product/product/create','product/product/index','product/product/view', 'product/product/update', 'product/product-brand/create','product/product-brand/index','product/product-brand/view', 'product/product-brand/update');
		?>
		<li class="treeview <?=activeParentMenu($product_menu)?>">
          <a href="#">
            <i class="fa fa-hdd-o text-white"></i> <span><?=Yii::t('app', 'Products')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php
			if(Yii::$app->user->can('Product.Create'))
			{ 
			?>
				<li class="<?=activeMenu('product/product/create')?>"><a href="<?=Url::to(['/product/product/create'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Add Product')?></a></li>
			<?php
			}
			
			if(Yii::$app->user->can('Product.Index'))
			{
			?>
				<li class="<?=activeMenu('product/product/index')?>"><a href="<?=Url::to(['/product/product/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Product')?></a></li>
			<?php
			}

			if(Yii::$app->user->can('ProductBrand.Create'))
			{ 
			?>
				<li class="<?=activeMenu('product/product-brand/create')?>"><a href="<?=Url::to(['/product/product-brand/create'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Add Product Brand')?></a></li>
			<?php
			}
			
			if(Yii::$app->user->can('ProductBrand.Index'))
			{
			?>
				<li class="<?=activeMenu('product/product-brand/index')?>"><a href="<?=Url::to(['/product/product-brand/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Product Brands')?></a></li>
			<?php
			}
			?>
          </ul>
        </li>
		<?php
		}

		/*if((Yii::$app->user->can('ProductBrand.Index') || Yii::$app->user->can('ProductBrand.Create')))
		{
		$product_brand_menu=array('product/product-brand/create','product/product-brand/index','product/product-brand/view', 'product/product-brand/update');
		?>
		<li class="treeview <?=activeParentMenu($product_brand_menu)?>">
          <a href="#">
            <i class="fa fa-heart-o text-red"></i> <span><?=Yii::t('app', 'Product Brand')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php
			if(Yii::$app->user->can('ProductBrand.Create'))
			{ 
			?>
				<li class="<?=activeMenu('product/product-brand/create')?>"><a href="<?=Url::to(['/product/product-brand/create'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Add Product Brand')?></a></li>
			<?php
			}
			
			if(Yii::$app->user->can('ProductBrand.Index'))
			{
			?>
				<li class="<?=activeMenu('product/product-brand/index')?>"><a href="<?=Url::to(['/product/product-brand/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Product Brands')?></a></li>
			<?php
			}
			?>
          </ul>
        </li>
		<?php
		}*/

		if((Yii::$app->user->can('AttributeValues.Index') || Yii::$app->user->can('AttributeValues.Create')))
		{
		$product_attributesvalues_menu=array('product/product-attribute-values/create','product/product-attribute-values/index','product/product-attribute-values/view', 'product/product-attribute-values/update');
		?>
		<li class="treeview <?=activeParentMenu($product_attributesvalues_menu)?>">
          <a href="#">
            <i class="fa fa-plus-square text-info"></i> <span><?=Yii::t('app', 'Product Attribute Values')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php
			if(Yii::$app->user->can('AttributeValues.Create'))
			{ 
			?>
				<li class="<?=activeMenu('product/product-attribute-values/create')?>"><a href="<?=Url::to(['/product/product-attribute-values/create'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Create Attribute Values List')?></a></li>
			<?php
			}
			
			if(Yii::$app->user->can('AttributeValues.Index'))
			{
			?>
				<li class="<?=activeMenu('product/product-attribute-values/index')?>"><a href="<?=Url::to(['/product/product-attribute-values/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Attribute Values List')?></a></li>
			<?php
			}
			?>
          </ul>
        </li>
		<?php
		}
		?>
		<!-- End Products Menu -->

		<!-- Begin Inventory Menu -->
		<?php
		if((Yii::$app->user->can('Inventory.Index') || Yii::$app->user->can('Inventory.Create')))
		{
		$inventory_menu=array('inventory/inventory/create','inventory/inventory/index','inventory/inventory/view', 'inventory/inventory/update');
		?>
		<li class="treeview <?=activeParentMenu($inventory_menu)?>">
          <a href="#">
            <i class="fa fa-cart-plus text-green"></i> <span><?=Yii::t('app', 'Inventory')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php
			if(Yii::$app->user->can('Inventory.Create'))
			{ 
			?>
				<li class="<?=activeMenu('inventory/inventory/create')?>"><a href="<?=Url::to(['/inventory/inventory/create'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Add Inventory Item')?></a></li>
			<?php
			}
			
			if(Yii::$app->user->can('Inventory.Index'))
			{
			?>
				<li class="<?=activeMenu('inventory/inventory/index')?>"><a href="<?=Url::to(['/inventory/inventory/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Inventory')?></a></li>
			<?php
			}
			?>
          </ul>
        </li>
		<?php
		}
		?>
		<!-- End Inventory Menu -->

		<!-- Begin Bulk Upload Products/Inventories Menu -->
		<?php
		if(Yii::$app->user->can('Inventory.Create') || Yii::$app->user->can('Product.Create') ||  Yii::$app->user->can('ProductCategory.Create'))
		{
		$inventory_menu=array('bulk/default/bulk-upload-categories', 'bulk/default/bulk-upload-products', 'bulk/default/bulk-upload-inventories', 'bulk/default/bulk-upload-combined');
		?>
		<li class="treeview <?=activeParentMenu($inventory_menu)?>">
          <a href="#">
            <i class="fa fa-upload text-red"></i> <span><?=Yii::t('app', 'Bulk Upload')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php

			if(Yii::$app->user->can('ProductCategory.Create'))
			{ 
			?>
				<li class="<?=activeMenu('bulk/default/bulk-upload-categories')?>"><a href="<?=Url::to(['/bulk/default/bulk-upload-categories'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Categories')?></a></li>
			<?php
			}

			if(Yii::$app->user->can('Product.Create'))
			{ 
			?>
				<li class="<?=activeMenu('bulk/default/bulk-upload-products')?>"><a href="<?=Url::to(['/bulk/default/bulk-upload-products'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Products')?></a></li>
			<?php
			}

			if(Yii::$app->user->can('Inventory.Create'))
			{ 
			?>
				<li class="<?=activeMenu('bulk/default/bulk-upload-inventories')?>"><a href="<?=Url::to(['/bulk/default/bulk-upload-inventories'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Inventories')?></a></li>
			<?php
			}

			if(Yii::$app->user->can('Inventory.Create') && Yii::$app->user->can('Product.Create'))
			{ 
			?>
				<li class="<?=activeMenu('bulk/default/bulk-upload-combined')?>"><a href="<?=Url::to(['/bulk/default/bulk-upload-combined'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Products and Inventories')?></a></li>
			<?php
			}
			?>
			
          </ul>
        </li>
		<?php
		}
		?>
		<!-- End Bulk Upload Products/Inventories Menu -->

		<!-- Begin Discount Coupons Menu -->
		<?php
		if((Yii::$app->user->can('DiscountCoupons.Index') || Yii::$app->user->can('DiscountCoupons.Create')))
		{
		$discount_coupons_menu=array('finance/discount-coupons/create','finance/discount-coupons/index','finance/discount-coupons/view', 'finance/discount-coupons/update');
		?>
		<li class="treeview <?=activeParentMenu($discount_coupons_menu)?>">
          <a href="#">
            <i class="fa fa-flash text-white"></i> <span><?=Yii::t('app', 'Discount Coupons')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php
			if(Yii::$app->user->can('DiscountCoupons.Create'))
			{ 
			?>
				<li class="<?=activeMenu('finance/discount-coupons/create')?>"><a href="<?=Url::to(['/finance/discount-coupons/create'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Add Discount Coupons')?></a></li>
			<?php
			}
			
			if(Yii::$app->user->can('DiscountCoupons.Index'))
			{
			?>
				<li class="<?=activeMenu('finance/discount-coupons/index')?>"><a href="<?=Url::to(['/finance/discount-coupons/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Discount Coupons')?></a></li>
			<?php
			}
			?>
          </ul>
        </li>
		<?php
		}
		?>
		<!-- End Discount Coupons Menu -->

		<!-- Begin Order Menu -->
		<?php
		$order_menu=array('order/order/index','order/order/view', 'order/order/update', 'order/sub-order/view-order', 'order/sub-order/sub-order-view', 'order/sub-order/get-invoice');
		if(Yii::$app->user->can('Order.Index'))
		{
		?>
        <li class="treeview <?=activeParentMenu($order_menu)?>">
          <a href="#">
            <i class="fa fa-gift text-teal"></i>
            <span><?=Yii::t('app', 'Orders')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php 
			if(Yii::$app->user->can('Order.Index'))
			{
			?>
				<li class="<?=activeMenu('order/order/index')?>"><a href="<?=Url::to(['/order/order/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Orders')?></a></li>
            <?php
			}
			?>
          </ul>
        </li>
		<?php
		}
		?>
		<!-- End Order Menu -->

		<!-- Begin Vendor Order Menu -->
		<?php
		if (Yii::$app->user->identity->entity_type = 'vendor')
		{
			$vendor_order_menu=array('order/sub-order/vendor-index','order/sub-order/view', 'order/sub-order/update', 'order/sub-order/view-order', 'order/sub-order/sub-order-view', 'order/sub-order/get-invoice');
			if(Yii::$app->user->can('SubOrder.Index'))
			{
			?>
			<li class="treeview <?=activeParentMenu($vendor_order_menu)?>">
			  <a href="#">
				<i class="fa fa-gift text-teal"></i>
				<span><?=Yii::t('app', 'Vendor Orders')?></span>
				<span class="pull-right-container">
				  <i class="fa fa-angle-left pull-right"></i>
				</span>
			  </a>
			  <ul class="treeview-menu">
				<?php 
				if(Yii::$app->user->can('SubOrder.Index'))
				{
				?>
					<li class="<?=activeMenu('order/sub-order/vendor-index')?>"><a href="<?=Url::to(['/order/sub-order/vendor-index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Vendor Orders')?></a></li> 
				<?php
				}
				?>
			  </ul>
			</li>
			<?php
			}
		}
		?>
		<!-- End Vendor Order Menu -->

		<!-- Begin Support/Ticket Menu -->
		<?php
		$ticket_menu = array('support/ticket/create','support/ticket/my-tickets','support/ticket/index','support/ticket/update');
		if((Yii::$app->user->can('Ticket.Index') || Yii::$app->user->can('Ticket.Create') || Yii::$app->user->can('Ticket.MyTicket'))) 
		{
		?>
			<li class="treeview <?= activeParentMenu($ticket_menu)?>">
			  <a href="#">
				<i class="fa fa-ticket text-red"></i>
				<span><?=Yii::t('app', 'Tickets')?></span>
				<span class="pull-right-container">
				  <i class="fa fa-angle-left pull-right"></i>
				</span>
			  </a>
			  <ul class="treeview-menu">
					<?php 
					if(Yii::$app->user->can('Ticket.Create'))
					{ ?>
						<li class="<?=activeMenu('support/ticket/create')?>"><a href="<?=Url::to(['/support/ticket/create'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Create Ticket')?></a></li>
					<?php 
					} 
					
					if(Yii::$app->user->can('Ticket.MyTicket'))
					{ ?>
						<li class="<?=activeMenu('support/ticket/my-tickets')?>"><a href="<?=Url::to(['/support/ticket/my-tickets'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'My Tickets')?><span class="label label-warning pull-right livecrm-skin"><?=MulteModel::getPendingTicketCountLabel()?></span></a></li>
					<?php 
					} 
					
					if(Yii::$app->user->can('Ticket.Index'))
					{ ?> 
						<li class="<?=activeMenu('support/ticket/index')?>"><a href="<?=Url::to(['/support/ticket/index'])?>"><i class="fa fa-circle-o text-orange"></i><?=Yii::t('app', 'Manage Tickets')?></a></li>
					<?php 
					}  ?> 
			  </ul>
			</li>
			<?php 
		} ?>
		<!-- End Support/Ticket Menu -->

	      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

    <div class="content-wrapper">
			<?= Alert::widget() ?>
			     <section class="content-header">
					<h4><?= Html::encode($this->title) ?></h4>
				  <ol class="breadcrumb">
					<?php  echo Breadcrumbs::widget ( [ 'links' => isset ( $this->params ['breadcrumbs'] ) ? $this->params ['breadcrumbs'] : [ ],
															'homeLink' => [
																			'label' => Yii::t('app', 'Home'),
																			'url' => Yii::$app->homeurl,
																			]
														]) ?>
				  </ol>
				</section>

		<!-- Main content -->
		<section class="content">
			<?= $content ?>
		</section>
    </div>

<footer class="main-footer">
    <div class="container">
        <p class="pull-left">&copy; <a href="http://www.techraft.in">TechRaft Solutions</a> <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php
if(Yii::$app->user->can('GlobalSettings.Index'))
{
?>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li class="active" title="<?=Yii::t('app', 'System Settings')?>"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-gear"></i></a></li>
	  <li title="<?=Yii::t('app', 'Support Settings')?>"><a href="#control-sidebar-support-tab" data-toggle="tab"><i class="fa fa-support"></i></a></li>
	  <li title="<?=Yii::t('app', 'Other Settings')?>"><a href="#control-sidebar-other-tab" data-toggle="tab"><i class="fa fa-diamond"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane active" id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading"><?=Yii::t('app', 'System Settings')?></h3>
        <ul class="control-sidebar-menu">
		<?php
		if(Yii::$app->user->can('Settings.Index'))
		{
		?>
          <li>
            <a href="<?=Url::to(['/multeobjects/setting'])?>">
              <i class="menu-icon fa fa-plus-circle bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Advanced System Settings')?></h4>

                <p><?=Yii::t('app', 'Advanced system settings')?></p>
              </div>
            </a>
          </li>

		  <li>
            <a href="<?=Url::to(['/product/banner-data'])?>">
              <i class="menu-icon fa fa-flag bg-green"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Banner Settings')?></h4>

                <p><?=Yii::t('app', 'Add/Edit Frontend Banners')?></p>
              </div>
            </a>
          </li>

		  <li>
            <a href="<?=Url::to(['/user/testimonial'])?>">
              <i class="menu-icon fa fa-edit bg-purple"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Testimonials')?></h4>

                <p><?=Yii::t('app', 'Add/Edit User Testimonials')?></p>
              </div>
            </a>
          </li>

		   <li>
            <a href="<?=Url::to(['/multeobjects/social'])?>">
              <i class="menu-icon fa fa-connectdevelop bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Social Links')?></h4>

                <p><?=Yii::t('app', 'View/Update various social media links')?></p>
              </div>
            </a>
          </li>

		  <li>
            <a href="<?=Url::to(['/multeobjects/static-pages'])?>">
              <i class="menu-icon fa fa-paint-brush bg-green"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Static Pages')?></h4>

                <p><?=Yii::t('app', 'View/Update various footer static pages')?></p>
              </div>
            </a>
          </li>
		<?php
		}
		
		if(Yii::$app->user->can('RBAC.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/multeobjects/setting/rights'])?>">
              <i class="menu-icon fa fa-magic bg-yellow"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'RBAC Settings')?></h4>

                <p><?=Yii::t('app', 'Define role based access control for system')?></p>
              </div>
            </a>
          </li>
		<?php
		}

		if(Yii::$app->user->can('UserSessions.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/user/user/user-sessions'])?>">
              <i class="menu-icon fa fa-history bg-purple"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Session History')?></h4>

                <p><?=Yii::t('app', 'Browse session history of different users')?></p>
              </div>
            </a>
          </li>
		<?php
		}
		?>

		<?php
		if(Yii::$app->user->can('EmailTemplates.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/multeobjects/email-template'])?>">
              <i class="menu-icon fa fa-envelope bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Email Templates')?></h4>

                <p><?=Yii::t('app', 'Add/Update various email templates')?></p>
              </div>
            </a>
          </li>
          
		<?php
		}
		?>

		<?php
		if(Yii::$app->user->can('Users.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/user/user'])?>">
              <i class="menu-icon fa fa-group bg-purple"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Users')?></h4>

                <p><?=Yii::t('app', 'Add/Update various system users')?></p>
              </div>
            </a>
          </li>
		<?php
		}
		?>

		  <li>
            <a href="<?=Url::to(['/multeobjects/setting/license'])?>">
              <i class="menu-icon fa fa-copyright bg-green"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'License')?></h4>

                <p><?=Yii::t('app', 'View License')?></p>
              </div>
            </a>
          </li>

        </ul>
        <!-- /.control-sidebar-menu -->
      </div>
      <!-- /.tab-pane -->
	  
	  <!-- Support Settings -->
	  <div class="tab-pane" id="control-sidebar-support-tab">
        <h3 class="control-sidebar-heading"><?=Yii::t('app', 'Support Settings')?></h3>
        <ul class="control-sidebar-menu">
		<?php
		if(Yii::$app->user->can('TicketStatus.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/support/ticket-status/index'])?>">
              <i class="menu-icon fa fa-ticket bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Ticket Status')?></h4>

                <p><?=Yii::t('app', 'Change Label Of Various Ticket Status')?></p>
              </div>
            </a>
          </li>
		<?php
		}

		if(Yii::$app->user->can('TicketImpact.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/support/ticket-impact/index'])?>">
              <i class="menu-icon fa fa-ticket bg-orange"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Ticket Impact')?></h4>

                <p><?=Yii::t('app', 'Add/Update Ticket Impact')?></p>
              </div>
            </a>
          </li>
		<?php
		}

		if(Yii::$app->user->can('TicketPriority.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/support/ticket-priority/index'])?>">
              <i class="menu-icon fa fa-ticket bg-purple"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Ticket Priority')?></h4>

                <p><?=Yii::t('app', 'Add/Update Ticket Priority')?></p>
              </div>
            </a>
          </li>
		<?php
		}

		if(Yii::$app->user->can('TicketSla.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/support/ticket-sla/index'])?>">
              <i class="menu-icon fa fa-ticket bg-yellow"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Ticket SLA')?></h4>

                <p><?=Yii::t('app', 'Define Ticket SLA')?></p>
              </div>
            </a>
          </li>
		<?php
		}

		if(Yii::$app->user->can('Department.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/support/department/index'])?>">
              <i class="menu-icon fa fa-ticket bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Departments')?></h4>

                <p><?=Yii::t('app', 'Add/Update Various Support Departments')?></p>
              </div>
            </a>
          </li>
		<?php
		}

		if(Yii::$app->user->can('TicketCategory.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/support/ticket-category/index'])?>">
              <i class="menu-icon fa fa-ticket bg-green"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Ticket Category')?></h4>

                <p><?=Yii::t('app', 'Add/Update Ticket Categories')?></p>
              </div>
            </a>
          </li>
		<?php
		}

		if(Yii::$app->user->can('Queue.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/support/queue/index'])?>">
              <i class="menu-icon fa fa-ticket bg-orange"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Queues')?></h4>

                <p><?=Yii::t('app', 'Add/Update Various Support Queues')?></p>
              </div>
            </a>
          </li>

		<?php
		}
		?>
        </ul>
      </div>

	  <!-- Other Settings tab content -->
      <div class="tab-pane" id="control-sidebar-other-tab">
        <h3 class="control-sidebar-heading"><?=Yii::t('app', 'Other Settings')?></h3>
        <ul class="control-sidebar-menu">
		<?php
		if(Yii::$app->user->can('CustomerType.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/customer/customer-type'])?>">
              <i class="menu-icon fa fa-group bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Customer Type')?></h4>

                <p><?=Yii::t('app', 'Add/Update various customer types')?></p>
              </div>
            </a>
          </li>
		<?php
		}

		if(Yii::$app->user->can('VendorType.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/vendor/vendor-type'])?>">
              <i class="menu-icon fa fa-cubes bg-yellow"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Vendor Type')?></h4>

                <p><?=Yii::t('app', 'Add/Update various vendor types')?></p>
              </div>
            </a>
          </li>
		<?php
		}

		if(Yii::$app->user->can('Tax.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/finance/tax'])?>">
              <i class="menu-icon fa fa-dollar bg-green"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Tax')?></h4>

                <p><?=Yii::t('app', 'Define various tax parameters')?></p>
              </div>
            </a>
          </li>
		<?php
		}
		if(Yii::$app->user->can('PaymentMethods.Index'))
		{
		?>
		  <li>
            <a href="<?=Url::to(['/finance/payment-methods'])?>">
              <i class="menu-icon fa fa-money bg-purple"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Payment Methods')?></h4>

                <p><?=Yii::t('app', 'Enable/Disable Various Payment Methods')?></p>
              </div>
            </a>
          </li>

		  <li>
            <a href="<?=Url::to(['/finance/currency-conversion'])?>">
              <i class="menu-icon fa fa-exchange bg-orange"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading"><?=Yii::t('app', 'Currency Conversion')?></h4>

                <p><?=Yii::t('app', 'Define Currency Conversion Rate')?></p>
              </div>
            </a>
          </li>
		<?php
		}
		?>
		</ul>
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
<?php
}
?>
</div>
<?php $this->endBody() ?>

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

  $(document).ready(function(e) {
	$('#w0').submit(function(event){
	var error='';

	$('[data-validation="required"]').each(function(index, element) 
	{
		Remove_Error($(this));
		
		var e=$(this).val();

		if($(this).val() == '' && !$(this).is("[mandatory-field]"))
		{
			Remove_Error($(this));
		}
		else if($(this).val() == '' && $(this).is("[mandatory-field]"))
		{
			error+=Add_Error($(this),"<?=Yii::t('app','This Field is Required!')?>");
		}
		else if($(this).is("[email-validation]"))
		{
			var atpos=e.indexOf("@");
			var dotpos=e.lastIndexOf(".");

			if (atpos<1 || dotpos<atpos+2 || dotpos+2>=e.length)
			{
				error+=Add_Error($(this),"<?=Yii::t('app','Email Address Not Valid!')?>");
			}
			else
			{
				Remove_Error($(this));
			}	
		}
		else if($(this).is("[num-validation]"))
		{
			if (!e.match(/^\d+$/))
			{
				error+=Add_Error($(this),"<?=Yii::t('app','Please enter a valid number!')?>");
			}
			else
			{
				Remove_Error($(this));
			}	
		}
		else if($(this).is("[num-validation-float]"))
		{
			//if (!e.match(/^\d+$/))
			//if (!e.match(/^[-+]?[0-9]*\.?[0-9]+$/))
			if (!e.match(/^[]?[0-9]*\.?[0-9]+$/))
			{
				error+=Add_Error($(this),"<?=Yii::t('app','Please enter a valid number!')?>");
			}
			else
			{
				Remove_Error($(this));
			}	
		}
		else if($(this).val() == '')
		{
			error+=Add_Error($(this),"<?=Yii::t ('app','This Field is Required!')?>");
		}
		else
		{
			Remove_Error($(this));
		}	

		if(error !='')
		{
			event.preventDefault();
		}
		else
		{
			return true;
		}
		});
	});
	$('a[data-toggle="tab"]').bind('click', function () {
		//alert("a");
        localStorage.setItem('lastTab_leadview', $(this).attr('href'));
    });
    //go to the latest tab, if it exists:
    var lastTab_leadview = localStorage.getItem('lastTab_leadview');

    if ($('a[href="' + lastTab_leadview + '"]').length > 0) {
        $('a[href="' + lastTab_leadview + '"]').tab('show');
    }
    else
    {
        // Set the first tab if cookie do not exist
        $('a[data-toggle="tab"]:first').tab('show');
    }
});
</script>
</body>
</html>

<?php
  $this->endPage();
?>