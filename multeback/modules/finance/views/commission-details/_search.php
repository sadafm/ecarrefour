<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var multebox\models\search\CommissionDetails $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="commission-details-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sub_order_id') ?>

    <?= $form->field($model, 'vendor_id') ?>

    <?= $form->field($model, 'inventory_id') ?>

    <?= $form->field($model, 'commission') ?>

    <?php // echo $form->field($model, 'invoiced_ind') ?>

    <?php // echo $form->field($model, 'vendor_invoice_id') ?>

    <?php // echo $form->field($model, 'added_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
