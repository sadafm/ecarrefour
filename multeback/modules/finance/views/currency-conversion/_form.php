<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use multebox\models\Currency;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var multebox\models\CurrencyConversion $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="currency-conversion-form">

    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'from' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'items' => ArrayHelper::map (Currency::find ()->where("currency_code = '".Yii::$app->params['SYSTEM_CURRENCY']."'")->orderBy ( 'currency_code' )->asArray ()->all (), 'currency_code','currency_code'), 
										'options' => [ 
                                                'prompt' => '--'.Yii::t ( 'app', 'From' ).'--'
                                        ] 
								],

            'to' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'items' => ArrayHelper::map (Currency::find ()->orderBy ( 'currency_code' )->asArray ()->all (), 'currency_code','currency_code'), 
										'options' => [ 
                                                'prompt' => '--'.Yii::t ( 'app', 'To' ).'--'
                                        ] 
								],

            'conversion_rate' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Conversion Rate...']],

        ]

    ]);

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    );
    ActiveForm::end(); ?>

</div>
