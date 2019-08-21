<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var multebox\models\VendorType $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="payment-methods-form">
<?

?>
    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

'method'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>Yii::t('app', 'Enter Method...'), 'maxlength'=>255]], 

'label'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>Yii::t('app', 'Enter Label...'), 'maxlength'=>32]], 

//'sort_order'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>Yii::t('app', 'Enter Sort Order...'), 'maxlength'=>32]], 

//'status'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Status...']], 
'active' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
									//	'label' => 'Status',
										'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Active ...') 
										] ,
										'columnOptions'=>['colspan'=>1],
										'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
										'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select').'--'
                                        ]
								],
//'created_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Created At...']], 

//'updated_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Updated At...']], 

    ]


    ]);
	if( Html::getInputId($model, 'id')){
		$disabled='<input class="form-control" type="text" maxlength="255" value="'.$model->method.'" name="PaymentMethods[method]">';	
}
    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-sm' : 'btn btn-primary btn-sm']);
    ActiveForm::end(); ?>

</div>
