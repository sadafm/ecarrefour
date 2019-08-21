<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var multebox\models\Vendor $model
 */

$this->title = Yii::t('app', 'Vendor Signup');
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="text/javascript" src="<?=Url::base()?>/js/jquery-2.1.1.min.js"></script>

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
});

$(document).on("click", '.vendor_submit', function(event){
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

</script>

<div class="vendor-create">
   <!-- <div class="page-header">
       <h1><?= Html::encode($this->title) ?></h1> 
    </div> -->
    <?= $this->render('_vendorform', [
        'model' => $model,
    ]) ?>

</div>
