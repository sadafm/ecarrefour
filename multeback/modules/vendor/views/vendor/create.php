<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var multebox\models\Vendor $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Vendor',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vendors'), 'url' => ['index']];
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
});
</script>

<div class="vendor-create">
   <!-- <div class="page-header">
       <h1><?= Html::encode($this->title) ?></h1> 
    </div> -->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
