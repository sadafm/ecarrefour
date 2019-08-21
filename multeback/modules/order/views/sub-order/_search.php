<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var multebox\models\search\SubOrder $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="sub-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'vendor_id') ?>

    <?= $form->field($model, 'inventory_id') ?>

    <?= $form->field($model, 'total_items') ?>

    <?php // echo $form->field($model, 'discount_coupon_id') ?>

    <?php // echo $form->field($model, 'global_discount_id') ?>

    <?php // echo $form->field($model, 'tax_id') ?>

    <?php // echo $form->field($model, 'inventory_snapshot') ?>

    <?php // echo $form->field($model, 'discount_coupon_snapshot') ?>

    <?php // echo $form->field($model, 'global_discount_snapshot') ?>

    <?php // echo $form->field($model, 'tax_snapshot') ?>

    <?php // echo $form->field($model, 'total_cost') ?>

    <?php // echo $form->field($model, 'total_shipping') ?>

    <?php // echo $form->field($model, 'total_site_discount') ?>

    <?php // echo $form->field($model, 'total_coupon_discount') ?>

    <?php // echo $form->field($model, 'discount_coupon_type') ?>

    <?php // echo $form->field($model, 'total_tax') ?>

    <?php // echo $form->field($model, 'delivery_method') ?>

    <?php // echo $form->field($model, 'payment_method') ?>

    <?php // echo $form->field($model, 'sub_order_status') ?>

    <?php // echo $form->field($model, 'added_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
