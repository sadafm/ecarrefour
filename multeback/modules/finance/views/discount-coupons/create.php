<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var multebox\models\DiscountCoupons $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Discount Coupons',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Discount Coupons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
include_once("script.php");
?>

<script>
$(document).ready(function(e) {
	$('#discountcoupons-coupon_code').blur(function(){
		Remove_ErrorTag($('#discountcoupons-coupon_code'));
	var couponcode = $('#discountcoupons-coupon_code').val();
	 if($('#discountcoupons-coupon_code').val()==''){
		 
	 }else{
	 $.post("<?=Url::to(['/finance/discount-coupons/ajax-discount-coupons'])?>", { 'coupon_code': couponcode, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(data){
					if(data)
					{
						//alert(data);
						Add_ErrorTag($('#discountcoupons-coupon_code'),data);
						//event.preventDefault();
						$('#discountcoupons-coupon_code').focus();
					}
					else
					{
						Remove_ErrorTag($('#discountcoupons-coupon_code'));
					}
				});
	 }
	})
});
</script>

<div class="discount-coupons-create">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
