<?php
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\Address $model
 */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Address',
]) . ' ' . $model->address_1." ".$model->address_2;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Addresses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->address_1." ".$model->address_2, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<script>
function loadState(){
	$('#address-state_id').load("<?=Url::to(['/multeobjects/address/ajax-load-states', 'country_id' => $model->country_id, 'state_id' => $model->state_id])?>");
}
function loadCity(){
	$('#address-city_id').load("<?=Url::to(['/multeobjects/address/ajax-load-cities', 'state_id' => $model->state_id, 'city_id' => $model->city_id])?>");
}
$(document).ready(function(e) {

	$('#address-country_id').change(function(){
    $.post("<?=Url::to(['/multeobjects/address/ajax-load-states'])?>", { 'country_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#address-state_id').html(result);
					$('#address-city_id').html('<option value=""> --Select City--</option>');
				})
	})

	$('#address-state_id').change(function(){
    $.post("<?=Url::to(['/multeobjects/address/ajax-load-cities'])?>", { 'state_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#address-city_id').html(result);

	//Auto Load
	loadState();
	loadCity()
});


</script>
<div class="address-update">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5> <?=$this->title ?></h5>

            <div class="ibox-tools">

                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
               
                <a class="close-link">
                    <i class="fa fa-times"></i>
                </a>
            </div>
</div>
         <div class="ibox-content">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
		</div>
    </div>
</div>
