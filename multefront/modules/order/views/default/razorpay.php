<?php
use yii\helpers\Url;
?>

<div id="container">
    <div class="container">
      <!-- Breadcrumb Start-->
      <ul class="breadcrumb">
        <li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
        <li><a href="<?=Url::to(['/order/default/information', 'order_id' => $_REQUEST['order_id']])?>"><?=Yii::t('app', 'Back to Order Details')?></a></li>
        <li><?=Yii::t('app', 'Razorpay Payment')?></li>
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
			<button id="rzp-button1" class="btn btn-primary"><?=Yii::t('app', 'Pay with Razorpay')?></button>
			<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
			<form name='razorpayform' action="" method="POST">
				<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
				<input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
				<input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
				<input type="hidden" name="order_id"  value="<?=$_REQUEST['order_id']?>" >
			</form>
		  </div>

		  <div id="content" class="col-sm-4">
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>

<script>
// Checkout details as a json
var options = <?php echo $json?>;

/**
 * The entire list of Checkout fields is available at
 * https://docs.razorpay.com/docs/checkout-form#checkout-fields
 */
options.handler = function (response){
    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
    document.getElementById('razorpay_signature').value = response.razorpay_signature;
    document.razorpayform.submit();
};

// Boolean whether to show image inside a white frame. (default: true)
options.theme.image_padding = false;

options.modal = {
    ondismiss: function() {
        console.log("This code runs when the popup is closed");
    },
    // Boolean indicating whether pressing escape key 
    // should close the checkout form. (default: true)
    escape: true,
    // Boolean indicating whether clicking translucent blank
    // space outside checkout form should close the form. (default: false)
    backdropclose: false
};

var rzp = new Razorpay(options);

document.getElementById('rzp-button1').onclick = function(e){
    rzp.open();
    e.preventDefault();
}
</script>