<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var multebox\models\Testimonial $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="testimonial-form">

    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL]); 
	
	echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 2,
						'attributes' => [ 
								'writer_name' => [ 
										'type' => Form::INPUT_TEXT,
										'options' => ['placeholder' => 'Enter Testimonial...', 'maxlength' => 255]
								],

								'writer_designation' => [ 
										'type' => Form::INPUT_TEXT,
										'options' => ['placeholder' => 'Enter Testimonial...', 'maxlength' => 255]
								]
							]
					]);

	echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 1,
						'attributes' => [ 
								'testimonial' => [ 
										'type' => Form::INPUT_TEXTAREA,
										'options' => ['placeholder' => 'Enter Testimonial...', 'maxlength' => 255, 'rows'=> 5, 'style' => 'resize:none']
								],
							]
					]);


	if(!$model->isNewRecord && $model->writer_image)
	{
	?>
		<div class="row">
			<div class="col-sm-12">
				<img src="<?=Yii::$app->params['web_url'].'/testimonial/'.$model->writer_new_image?>" class="img-responsive" style="max-width:50%;max-height:200px;border:1px dotted black"></img>
			</div>
		</div>
		<br>
	<?php
		 echo $form->field($model, 'writer_image')->fileInput()->label(Yii::t('app', 'Change Image'));
	}
	else
	{
		echo $form->field($model, 'writer_image')->fileInput()->label(Yii::t('app', 'Add Image'));
	}

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    );
    ActiveForm::end(); ?>

</div>
