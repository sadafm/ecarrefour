<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\CustomerType $model
 */

$this->title = Yii::t('app', 'Update Customer Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customer Types'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->type, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<script>
	$(document).ready(function(e) {
        $('#customertype-type').attr('disabled',true);
    });
</script>
<div class="customer-type-update">
	<div class="box box-default">
		<div class="box-header with-border">
			<div class="box-title">
				<h5> <?= Html::encode($this->title) ?></h5>
			</div>
			 <div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
			</div>
		</div>
		
		<div class="box-body">

		<?= $this->render('_form', [
			'model' => $model,
		]) ?>
		</div>
    </div>
</div>
