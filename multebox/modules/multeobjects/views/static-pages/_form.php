<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
/**
 * @var yii\web\View $this
 * @var multebox\models\StaticPages $model
 * @var yii\widgets\ActiveForm $form
 */
?>
<script src="<?=Url::base()?>/bower_components/ckeditor/ckeditor.js"></script>

<div class="static-pages-form">
    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_VERTICAL]); 
	?>
	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Static Pages' ); ?></h3>
		</div>
		<div class="panel-body">
	<?php
	echo Form::widget([
						'model' => $model,
						'form' => $form,
						'columns' => 1,
						'attributes' => [
										'page_name'=> ['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=> Yii::t('app', 'Enter Page Name').'...', 'disabled' => true, 'maxlength'=>255]], 
									]
						]);

	echo Form::widget ( [ 
						
						'model' => $model,
						'form' => $form,
						'columns' => 1,
						'attributes' => [ 
								
								'content' => [ 
										'type' => Form::INPUT_TEXTAREA,
										'options' => [ 
												'placeholder' => Yii::t('app', 'Content').'...'
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
    CKEDITOR.replace('staticpages-content');
  })
</script>