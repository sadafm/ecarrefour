<?php
use yii\helpers\Url;
use multebox\models\search\MulteModel;

if(isset($_SESSION['CONVERTED_CURRENCY_CODE']))
{
	$currency_code = $_SESSION['CONVERTED_CURRENCY_CODE'];
}
else
{
	$currency_code = Yii::$app->params['SYSTEM_CURRENCY'];
}
?>

<div id="container">
    <div class="container">
      <!-- Breadcrumb Start-->
      <ul class="breadcrumb">
        <li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
        <li><a href="<?=Url::to(['/order/default/information', 'order_id' => $order->id])?>"><?=Yii::t('app', 'Back to Order Details')?></a></li>
        <li><?=Yii::t('app', 'Stripe Payment')?></li>
      </ul>
      <!-- Breadcrumb End-->
    <div class="row">
        <!--Middle Part Start-->
      <div id="content" class="col-sm-12 text-center">
	    <h1 class="title"><?=Yii::t('app', 'Click button to make payment')?></h1>

		<div class="row">
		  <div id="content" class="col-sm-4">
		  </div>
		  
		  <div id="content" class="col-sm-4">
		  <form action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
			<input type="hidden" name="stripesubmit" value="">
			<input type="hidden" name="order_id" value="<?=$order->id?>">
			  <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
					  data-key="<?=Yii::$app->params['STRIPE_PUBLISHABLE_KEY']?>"
					  data-description="<?=Yii::t('app', 'Payment for Order')?># <?=$order->id?>"
					  data-amount="<?=$order_stripe_cost?>"
					  data-locale="auto"
					  data-currency="<?=$currency_code?>"
					  data-name="<?=Yii::$app->params['COMPANY_NAME']?>">
			  </script>
			</form>
		  </div>

		  <div id="content" class="col-sm-4">
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>
