<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var multebox\models\CommissionDetails $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="commission-details-form">

    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'sub_order_id' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Sub Order ID...']],

            'vendor_id' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Vendor ID...']],

            'inventory_id' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Inventory ID...']],

            'commission' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Commission...']],

            'invoiced_ind' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Invoiced Ind...']],

            'vendor_invoice_id' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Vendor Invoice ID...']],

            'added_at' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Added At...']],

            'updated_at' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Updated At...']],

        ]

    ]);

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    );
    ActiveForm::end(); ?>

</div>
