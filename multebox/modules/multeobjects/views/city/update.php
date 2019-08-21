<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\City $model
 */

$this->title = Yii::t('app', 'Update City : ', [
    'modelClass' => 'City',
]) . ' ' . $model->city;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->city, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<script>
function loadState(){
$('#city-state_id').load("<?=Url::to(['/multeobjects/address/ajax-load-states', 'country_id' => $model->country_id, 'state_id' => $model->state_id])?>");
}
$(document).ready(function(e) {
	$('#city-country_id').change(function(){
    $.post("<?=Url::to(['/multeobjects/address/ajax-load-states'])?>", { 'country_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#city-state_id').html(result);
				})
	})

	//Auto Load
	loadState();
});


</script>
<div class="city-update">
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

</div></div></div>
