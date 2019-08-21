<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use yii\helpers\ArrayHelper;
use multebox\models\ProductSubCategory;
use multebox\models\Tax;
use yii\web\NotFoundHttpException;


/**
 * @var yii\web\View $this
 * @var multebox\models\ProductSubSubCategory $model
 * @var yii\widgets\ActiveForm $form
 */
$xFlag=false;
if(Yii::$app->user->identity->entity_type == 'vendor')
{
	$model->active=0;
	$xFlag=true;
}
?>

<script src="<?=Url::base()?>/bower_components/ckeditor/ckeditor.js"></script>

<?php
	if(isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] > 0)
	{
		$dFlag = true;
	}
	else
	{
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
	}
?>
<div class="product-sub-sub-category-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_VERTICAL]); 
	echo Form::widget([
    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [
	'parent_id' => [
					'type' => Form::INPUT_DROPDOWN_LIST,
					'label' => Yii::t('app', 'Sub Category'),
					'options' => [ 
							'placeholder' => Yii::t('app', 'Select Product Sub Category...') 
					] ,
					'columnOptions'=>['colspan'=>1],
					'items'=> ArrayHelper::map (ProductSubCategory::find ()->where("active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'),  
					'options' => [ 
							'prompt' => '--'.Yii::t('app', 'Select').'--',
							'value' => $_REQUEST['parent_id'],
							'disabled' => $dFlag,
					],
				],
	'name'=>	[
					'name'=> Form::INPUT_TEXT, 
					'options'=>['placeholder'=>Yii::t('app', 'Enter Name...'), 'maxlength'=>255],
					'label' => Yii::t('app', 'Sub-SubCategory Name'),
				], 

	'description' => ['type' => Form::INPUT_TEXTAREA, 'options' => ['placeholder' => Yii::t('app', 'Enter Description...'),'rows' => 6]],
	]
]);

echo Form::widget([
    'model' => $model,
    'form' => $form,
    'columns' => 4,
    'attributes' => [
	'active' => [ 
					'type' => Form::INPUT_DROPDOWN_LIST,
				//	'label' => 'Status',

					'columnOptions'=>['colspan'=>1],
					'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
					'options' => [ 
							'prompt' => '--'.Yii::t('app', 'Select').'--',
							'disabled' => $xFlag,	
					]
				],
	'tax_ind' => [ 
					'type' => Form::INPUT_DROPDOWN_LIST,
					'label' => Yii::t('app', 'Apply Tax'),
					'options' => [ 
							'placeholder' => 'Enter Active ...' 
					] ,
					'columnOptions'=>['colspan'=>1],
					'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
					'options' => [ 
							'prompt' => '--'.Yii::t('app', 'Select').'--'
					]
				],
	'tax_id' => [
					'type' => Form::INPUT_DROPDOWN_LIST,
					'label' => Yii::t('app', 'Tax Type'),
					'options' => [ 
							'placeholder' => 'Select Tax Type...' 
					] ,
					'columnOptions'=>['colspan'=>1],
					'items'=> ArrayHelper::map (Tax::find ()->where("active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'),  
					'options' => [ 
							'prompt' => '--'.Yii::t('app', 'Select').'--',
					],
				],
	'return_window' => [
					'type' => Form::INPUT_TEXT,
					'options' => [ 
							'placeholder' => Yii::t('app', 'Input number of days...')
					] ,
				],
//'created_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Created At...']], 

//'updated_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Updated At...']], 

    ]


    ]);

	if($model->isNewRecord)
	{
	?>
		<input type="hidden" name="ProductSubSubCategory[added_by_id]" class="form-control" value="<?=Yii::$app->user->identity->id?>">
	<?php
	}

	if($dFlag)
	{
	?>
		<input type="hidden" name="ProductSubSubCategory[parent_id]" class="form-control" value="<?=$_REQUEST['parent_id']?>">
	<?php
	}

	if(Yii::$app->user->identity->entity_type == 'vendor')
	{
	?>
		<input type="hidden" name="ProductSubSubCategory[active]" class="form-control" value="0">
	<?php
	}

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-sm' : 'btn btn-primary btn-sm']);
    ActiveForm::end(); ?>

</div>

<script>
  $(function () {
    CKEDITOR.replace('productsubsubcategory-description');
  })
</script>