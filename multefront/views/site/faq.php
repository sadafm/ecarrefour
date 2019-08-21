<?php
use yii\helpers\Url;
use multebox\models\StaticPages;

$content = StaticPages::findOne(['page_name' => 'FAQ'])->content;
?>
<!-- Breadcrumb Start-->
  <ul class="breadcrumb">
	<li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
	<li><?=Yii::t('app', 'Frequently Asked Questions')?></li>
  </ul>
  <!-- Breadcrumb End-->
  <div class="main container">
	<?=$content?>
  </div>