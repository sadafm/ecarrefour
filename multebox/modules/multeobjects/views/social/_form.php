<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var multebox\models\Social $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="social-form">

    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'platform' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Platform...', 'maxlength' => 255, 'disabled' => true]],

            'link' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Link...', 'maxlength' => 255]],

            //'active' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Active...']],

			'active' => [ 
							'type' => Form::INPUT_DROPDOWN_LIST,
							'options' => [ 
									'placeholder' => Yii::t('app', 'Is Active').' ...' 
							] ,
							'columnOptions'=>['colspan'=>1],
							'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
							'options' => [ 
									'prompt' => '--'.Yii::t('app', 'Select Status').'--'
							]
					], 

           // 'added_at' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Added At...']],

           // 'updated_at' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Updated At...']],

        ]

    ]);

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    );
    ActiveForm::end(); ?>

</div>
