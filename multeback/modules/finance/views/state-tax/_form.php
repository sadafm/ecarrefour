<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use multebox\models\Country;
use multebox\models\State;

/**
 * @var yii\web\View $this
 * @var multebox\models\StateTax $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="state-tax-form">

    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            //'tax_id' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Tax ID...']],

			'country_id' => [
								'type' => Form::INPUT_TEXT, 
								'options' => [
												'placeholder' => 'Enter Country ID...',
												'value' => Country::findOne($model->country_id)->country,
												'disabled' => true,
											]
							],

            'state_id' => [
								'type' => Form::INPUT_TEXT, 
								'options' => [
												'placeholder' => 'Enter State ID...',
												'value' => State::findOne($model->state_id)->state,
												'disabled' => true,
											]
						],

            'tax_percentage' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Tax Percentage...', 'maxlength' => 10]],

            //'added_at' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Added At...']],

            //'updated_at' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Updated At...']],

        ]

    ]);
	
    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    );
	?>
	<a href="<?=Url::to(['/finance/tax/update', 'id' => $_REQUEST['tax_id']])?>" class="btn btn-info"><?=Yii::t('app', 'Back')?></a>
	<?php
    ActiveForm::end(); ?>

</div>
