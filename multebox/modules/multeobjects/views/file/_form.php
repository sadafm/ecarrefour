<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var multebox\models\File $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="file-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

'file_name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter File Name...', 'maxlength'=>255]], 

'file_title'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter File Title...', 'maxlength'=>255]], 

'file_type'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter File Type...', 'maxlength'=>255]], 

'file_path'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter File Path...', 'maxlength'=>255]], 

'entity_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Entity ID...']], 

'entity_type'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Entity Type...', 'maxlength'=>255]], 

'added_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Added At...']], 

'updated_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Updated At...']], 

    ]


    ]);
    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
