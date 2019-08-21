<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
/**
 * @var yii\web\View $this
 * @var multebox\models\EmailTemplate $model
 * @var yii\widgets\ActiveForm $form
 */
?>
<script src="<?=Url::base()?>/bower_components/ckeditor/ckeditor.js"></script>

<div class="email-template-form">
    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_VERTICAL]); 
	?>
	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Email Template' ); ?></h3>
		</div>
		<div class="panel-body">
	<?php
	echo Form::widget([
						'model' => $model,
						'form' => $form,
						'columns' => 1,
						'attributes' => [
										'template_name'=> ['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=> Yii::t('app', 'Enter Template Name').'...', 'disabled' => true, 'maxlength'=>255]], 
										'template_subject'=> ['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=> Yii::t('app', 'Enter Template Subject').'...','rows'=> 6]], 
									]
						]);

	echo Form::widget ( [ 
						
						'model' => $model,
						'form' => $form,
						'columns' => 1,
						'attributes' => [ 
								
								'template_body' => [ 
										'type' => Form::INPUT_TEXTAREA,
										'options' => [ 
												'placeholder' => Yii::t('app', 'Body').'...'
										] 
								] 
						] 
				] );
?>
</div>
</div>
<?php
    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-sm' : 'btn btn-primary btn-sm']);
    ActiveForm::end(); ?>
</div>

<script>
  $(function () {
    CKEDITOR.replace('emailtemplate-template_body');
  })
</script>