<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var multebox\models\Customer $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Customer',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<script>
$(document).ready(function(e) {

	$('#country_id').change(function(){
    $.post("<?=Url::to(['/multeobjects/address/ajax-load-states'])?>", { 'country_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#state_id').html(result);
					$('#city_id').html('<option value=""> --Select--</option>');
				})
	})

	$('#state_id').change(function(){
    /*$.post("<?=Url::to(['/multeobjects/address/ajax-load-cities'])?>", { 'state_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#city_id').html(result);
				})*/
	$.post("<?=Url::to(['/multeobjects/address/ajax-load-cities-array'])?>", { 'state_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#city_id').autocomplete({"source": $.parseJSON(result)});
				})
	})

	$('#customer-customer_name').blur(function(){
		Remove_ErrorTag($('#customer-customer_name'));
	var username = $('#customer-customer_name').val();
	 if($('#customer-customer_name').val()==''){
		 
	 }
	else if(/^[a-zA-Z0-9]*$/.test(username) == false) 
	{
		Add_ErrorTag($('#customer-customer_name'), '<?=Yii::t('app', 'Username contains illegal characters!')?>');
		$('#customer-customer_name').focus();
	}
	 else{
	 $.post("<?=Url::to(['/customer/customer/ajax-customer-username'])?>", { 'username': username, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(data){
					if(data)
					{
						//alert(data);
						Add_ErrorTag($('#customer-customer_name'),data);
						//event.preventDefault();
						$('#customer-customer_name').focus();
					}
					else
					{
						Remove_ErrorTag($('#customer-customer_name'));
					}
				});
	 }
	})
});
</script>

<div class="customer-create">
   <!-- <div class="page-header">
       <h1><?= Html::encode($this->title) ?></h1> 
    </div> -->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
