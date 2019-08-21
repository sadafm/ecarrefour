<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\SubOrder $model
 */

$this->title = Yii::t('app', 'View {modelClass}: ', [
    'modelClass' => 'Vendor Order',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vendor Orders'), 'url' => ['view-order', 'id' => $model->order_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="sub-order-update">

   <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
		'shipmodel' => $shipmodel
    ]) ?>

</div>
