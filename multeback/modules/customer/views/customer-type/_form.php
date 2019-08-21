<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var multebox\models\CustomerType $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="customer-type-form">
<?

?>
    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

'type'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>Yii::t('app', 'Enter Type...'), 'maxlength'=>255]], 

'label'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>Yii::t('app', 'Enter Label...'), 'maxlength'=>32]], 


'active' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
									//	'label' => 'Status',
										'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Active...') 
										] ,
										'columnOptions'=>['colspan'=>1],
										'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
										'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select').'--'
                                        ]
								],

    ]


    ]);
	if( Html::getInputId($model, 'id')){
		$disabled='<input class="form-control" type="text" placeholder="'.Yii::t('app', 'Enter Type...').'" maxlength="255" value="'.$model->type.'" name="CustomerType[type]">';	
}
    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-sm' : 'btn btn-primary btn-sm']);
    ActiveForm::end(); ?>

</div>
