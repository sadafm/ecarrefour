<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var multebox\models\Inventory $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Inventory',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Inventories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
include_once("script.php");
?>

<script>
$(document).ready(function(){
	//$(".digital-template").hide();

	$('#inventory-product_id').change(function(){
	var product_id = $(this).val();
	$('#mytable').remove();
	$("#inventory-digital_file").attr('type', 'hidden');
	$(".field-inventory-send_as_attachment").hide();
	$(".field-inventory-attachment_file_name").hide();
	$(".digital-template").hide();
	
	$.post("<?=Url::to(['/inventory/inventory/ajax-get-product-type'])?>", { 'product_id': product_id, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					if(result == '1')
					{
						$("#inventory-digital_file").attr('type', 'file');
						$.post("<?=Url::to(['/inventory/inventory/ajax-load-attributes'])?>", { 'product_id': product_id, 'stock_ind': 1, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
							if($('#mytable').length > '0')
								$('#mytable').remove();
							
							$('#mystable').after(result);
						})
					}
					else if(result == '2')
					{
						$("#inventory-digital_file").attr('type', 'file');
						$(".field-inventory-send_as_attachment").show();
						$(".field-inventory-attachment_file_name").show();
						$(".digital-template").show();
						$.post("<?=Url::to(['/inventory/inventory/ajax-load-attributes'])?>", { 'product_id': product_id, 'stock_ind': 0, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
							if($('#mytable').length > '0')
								$('#mytable').remove();
							
							$('#mystable').after(result);
						})
					}
					else
					{
						$.post("<?=Url::to(['/inventory/inventory/ajax-load-attributes'])?>", { 'product_id': product_id, 'stock_ind': 1, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
							if($('#mytable').length > '0')
								$('#mytable').remove();
							
							$('#mystable').after(result);
						})
					}
				})

    /*$.post("<?=Url::to(['/inventory/inventory/ajax-load-attributes'])?>", { 'product_id': product_id, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					if($('#mytable').length > '0')
						$('#mytable').remove();
					
					$('#mystable').after(result);
				})*/
	})
});
</script>

<div class="inventory-create">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div> -->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
