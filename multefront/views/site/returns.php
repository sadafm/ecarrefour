<?php
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use multebox\models\StaticPages;

$content = StaticPages::findOne(['page_name' => 'RETURNS'])->content;

$this->title = Yii::t('app', 'Returns');
?>
<section class="main-container col1-layout">
<div class="main container">
<div class="site-returns">

	<!-- Breadcrumb Start-->
	  <ul class="breadcrumb">
		<li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
		<li><?=Yii::t('app', 'Returns')?></li>
	  </ul>
	<!-- Breadcrumb End-->

  <div class="main container">
	<?=$content?>
  </div>
 </div>
</div>
</section>