<?php

use yii\helpers\Html;
use multebox\models\Product;

/**
 * @var yii\web\View $this
 * @var multebox\models\Inventory $model
 */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Inventory',
]) . ' ' . Product::findOne($model->product_id)->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Inventories'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => Product::findOne($model->product_id)->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<script>
$(document).ready(function(){
	if($('#inventory-slab_discount_ind').val() == '0')
	{
		$('#inventory-slab_discount_type').attr('disabled',true);
		$('#inventory-slab_1_range').attr('disabled',true);
		$('#inventory-slab_1_discount').attr('disabled',true);
		$('#inventory-slab_2_range').attr('disabled',true);
		$('#inventory-slab_2_discount').attr('disabled',true);
		$('#inventory-slab_3_range').attr('disabled',true);
		$('#inventory-slab_3_discount').attr('disabled',true);
		$('#inventory-slab_4_range').attr('disabled',true);
		$('#inventory-slab_4_discount').attr('disabled',true);
	}

	$('#inventory-slab_discount_ind').change(function()
	{
		if($('#inventory-slab_discount_ind').val() != '1')
		{
			$('#inventory-slab_discount_type').attr('disabled',true);
			$('#inventory-slab_1_range').attr('disabled',true);
			$('#inventory-slab_1_discount').attr('disabled',true);
			$('#inventory-slab_2_range').attr('disabled',true);
			$('#inventory-slab_2_discount').attr('disabled',true);
			$('#inventory-slab_3_range').attr('disabled',true);
			$('#inventory-slab_3_discount').attr('disabled',true);
			$('#inventory-slab_4_range').attr('disabled',true);
			$('#inventory-slab_4_discount').attr('disabled',true);
		}
		else
		{
			$('#inventory-slab_discount_type').attr('disabled',false);
			$('#inventory-slab_1_range').attr('disabled',false);
			$('#inventory-slab_1_discount').attr('disabled',false);
			$('#inventory-slab_2_range').attr('disabled',false);
			$('#inventory-slab_2_discount').attr('disabled',false);
			$('#inventory-slab_3_range').attr('disabled',false);
			$('#inventory-slab_3_discount').attr('disabled',false);
			$('#inventory-slab_4_range').attr('disabled',false);
			$('#inventory-slab_4_discount').attr('disabled',false);
		}
	})

	$('.inventory_submit').click(function(event){
		var error='';
		if($('#inventory-slab_discount_ind').val() == '1')
		{
			Remove_Error($('#inventory-slab_discount_type'));
			if($('#inventory-slab_discount_type').val() == '')
			{
				error+=Add_Error($('#inventory-slab_discount_type'),'<?=Yii::t('app','This Field is Required!')?>');
				event.preventDefault();
				return false;
			}
			
			/* Check if slab values are not consistant */
			Remove_Error($('#inventory-slab_4_range'));
			if($('#inventory-slab_4_range').val() > 0 && ($('#inventory-slab_3_range').val() == 0 || $('#inventory-slab_2_range').val() == 0 || $('#inventory-slab_1_range').val() == 0))
			{
				error+=Add_Error($('#inventory-slab_4_range'),'<?=Yii::t('app','Please dont skip slabs!')?>');
				event.preventDefault();
				return false;
			}

			Remove_Error($('#inventory-slab_3_range'));
			if($('#inventory-slab_3_range').val() > 0 && ($('#inventory-slab_2_range').val() == 0 || $('#inventory-slab_1_range').val() == 0))
			{
				error+=Add_Error($('#inventory-slab_3_range'),'<?=Yii::t('app','Please dont skip slabs!')?>');
				event.preventDefault();
				return false;
			}

			Remove_Error($('#inventory-slab_2_range'));
			if($('#inventory-slab_2_range').val() > 0 && ($('#inventory-slab_1_range').val() == 0))
			{
				error+=Add_Error($('#inventory-slab_2_range'),'<?=Yii::t('app','Please dont skip slabs!')?>');
				event.preventDefault();
				return false;
			}
			
			Remove_Error($('#inventory-slab_4_range'));
			if(parseInt($('#inventory-slab_4_range').val()) > 0 && parseInt($('#inventory-slab_4_range').val()) <= parseInt($('#inventory-slab_3_range').val()))
			{
				error+=Add_Error($('#inventory-slab_4_range'),'<?=Yii::t('app','Slab 4 must be greater than slab 3!')?>');
				event.preventDefault();
				return false;
			}

			Remove_Error($('#inventory-slab_3_range'));
			if(parseInt($('#inventory-slab_3_range').val()) > 0 && parseInt($('#inventory-slab_3_range').val()) <= parseInt($('#inventory-slab_2_range').val()))
			{
				error+=Add_Error($('#inventory-slab_3_range'),'<?=Yii::t('app','Slab 3 must be greater than slab 2!')?>');
				event.preventDefault();
				return false;
			}

			Remove_Error($('#inventory-slab_2_range'));
			if(parseInt($('#inventory-slab_2_range').val()) > 0 && parseInt($('#inventory-slab_2_range').val()) <= parseInt($('#inventory-slab_1_range').val()))
			{
				error+=Add_Error($('#inventory-slab_2_range'),'<?=Yii::t('app','Slab 2 must be greater than slab 1!')?>');
				event.preventDefault();
				return false;
			}
		}
		else
			return true;
	})
});

</script>

<div class="inventory-update">

    <!--<h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
		'inventoryDetails' => $inventoryDetails,
		'tags' => $tags
    ]) ?>

</div>
