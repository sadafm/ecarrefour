<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var multebox\models\TicketImpact $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="ticket-impact-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

'impact'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>Yii::t('app', 'Enter Impact...'), 'maxlength'=>255]], 

'label'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>Yii::t('app', 'Enter Label...'), 'maxlength'=>255]], 

//'active'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Active...']], 

//'sort_order'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>Yii::t('app', 'Enter Sort Order...')]], 

'active' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										//'label' => 'Active',
										'options' => [ 
												'placeholder' => 'Enter State ...' 
										] ,
										'columnOptions'=>['colspan'=>1],
										'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
										'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select').'--'
                                        ]
								],
    ]


    ]);
    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-sm' : 'btn btn-primary btn-sm']);
    ActiveForm::end(); ?>

</div>
