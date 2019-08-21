<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use yii\helpers\ArrayHelper;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use multebox\models\ProductCategory;
use yii\web\NotFoundHttpException;
use multebox\models\ProductAttributeValues;


/**
 * @var yii\web\View $this
 * @var multebox\models\ProductAttributes $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<?php
	if(isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] > 0)
	{
		$dFlag = true;
	}
	else
	{
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
	}

	$c_id = ProductSubSubCategory::find()->where('id = '.$_REQUEST['parent_id'])->one()->parent_id;
	$b_name = ProductSubCategory::find()->where('id = '.$c_id)->one()->name;
	$b_id = ProductSubCategory::find()->where('id = '.$c_id)->one()->parent_id;
	$a_name = ProductCategory::find()->where('id = '.$b_id)->one()->name;
?>
<div class="product-attributes-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_VERTICAL]); 
	
	?>
	<div class="form-group">
		<label class="control-label"><?php echo Yii::t ( 'app', 'Main Category' ); ?></label>
		<input type="text" name="" class="form-control" value="<?=$a_name?>" disabled=true>
	</div>

	<div class="form-group">
		<label class="control-label"><?php echo Yii::t ( 'app', 'Sub Category' ); ?></label>
		<input type="text" name="" class="form-control" value="<?=$b_name?>" disabled=true>
	</div>
	<?php

	echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

	'parent_id' => [
					'type' => Form::INPUT_DROPDOWN_LIST,
					'label' => Yii::t('app', 'Sub Sub-Category'),
					'options' => [ 
							'placeholder' => Yii::t('app', 'Select Product Sub-SubCategory ...') 
					] ,
					'columnOptions'=>['colspan'=>1],
					'items'=> ArrayHelper::map (ProductSubSubCategory::find ()->where("active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'),  
					'options' => [ 
							'prompt' => '--'.Yii::t('app', 'Select').'--',
							'value' => $_REQUEST['parent_id'],
							'disabled' => $dFlag,
					],
				],

	'fixed' => [ 
					'type' => Form::INPUT_DROPDOWN_LIST,
					'label' => Yii::t('app', 'Fixed Values'),
					'options' => [ 
							'placeholder' => Yii::t('app', 'Select ...') 
					] ,
					'columnOptions'=>['colspan'=>1],
					'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
					'options' => [ 
							'prompt' => '--'.Yii::t('app', 'Select').'--'
					]
				],

	'fixed_id' => [
					'type' => Form::INPUT_DROPDOWN_LIST,
					'label' => Yii::t('app', 'Attribute Values List'),
					'options' => [ 
							'placeholder' => Yii::t('app', 'Select Attribute Values List...') 
					] ,
					'columnOptions'=>['colspan'=>1],
					'items'=> ArrayHelper::map (ProductAttributeValues::find ()->orderBy ( 'name' )->asArray ()->all (), 'id','name'),  
					'options' => [ 
							'prompt' => '--'.Yii::t('app', 'Select').'--',
							//'value' => $_REQUEST['parent_id'],
							//'disabled' => $dFlag,
							//'data-validation' => 'required', 
							//'mandatory-field-2' => ''
					],
				],

	'name'=>	[
					'name'=> Form::INPUT_TEXT, 
					'options'=>[
								'placeholder'=>Yii::t('app', 'Enter Name...'), 
								'maxlength'=>255, 
								//'data-validation' => 'required', 
								//'mandatory-field-2' => ''
							],
					'label' => Yii::t('app', 'Product Attribute Name'),
				], 

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

	if($model->isNewRecord)
	{
	?>
		<input type="hidden" name="ProductAttributes[added_by_id]" class="form-control" value="<?=Yii::$app->user->identity->id?>">
	<?php
	}

	if($dFlag)
	{
	?>
		<input type="hidden" name="ProductAttributes[parent_id]" class="form-control" value="<?=$_REQUEST['parent_id']?>">
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