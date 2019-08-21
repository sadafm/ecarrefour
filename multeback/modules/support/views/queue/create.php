<?php
use yii\helpers\Html;
use yii\helpers\Url;
/**
 * @var yii\web\View $this
 * @var multebox\models\Queue $model
 */
$this->title = Yii::t('app', 'Create Queue');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Queue'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="queue-create">
     <div class="box box-default">
		 <div class="box-header with-border">
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
			</div>
			<div class="box-title">
				<h5> <?=$this->title ?></h5>
			</div>
		</div>

		<div class="box-body">
			<?= $this->render('_form', [
				'model' => $model,
			]) ?>

		</div>
	</div>
</div>
