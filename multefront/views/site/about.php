<?php
use yii\helpers\Url;
use multebox\models\ProductBrand;
use multebox\models\BannerData;
use multebox\models\BannerType;
use multebox\models\File;
use multebox\models\Testimonial;
use multebox\models\StaticPages;

$content = StaticPages::findOne(['page_name' => 'ABOUT'])->content;
?>
<!-- Breadcrumb Start-->
  <ul class="breadcrumb">
	<li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
	<li><?=Yii::t('app', 'About Us')?></li>
  </ul>
  <!-- Breadcrumb End-->
  
  <!-- Main Container -->
  
  <div class="main container">
	<?=$content?>
  </div>