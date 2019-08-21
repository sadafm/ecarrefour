<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use yii\helpers\ArrayHelper;
use multebox\models\ProductSubSubCategory;
use multebox\models\ProductSubCategory;
use multebox\models\ProductCategory;

/**
 * @var yii\web\View $this
 * @var multebox\models\Commission $model
 * @var yii\widgets\ActiveForm $form
 */
if(!$model->isNewRecord)
{
	$dFlag = true;
}
else
{
	$dFlag = false;
}
?>

<div class="commission-form">

    <?php $form = ActiveForm::begin ( [ 

						'type' => ActiveForm::TYPE_VERTICAL ,
						'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']],  //this helps to show icons in validation messages 

				] );?>
				

				<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Commission Details' ); ?></h3>
		</div>
		<div class="panel-body">
		<div class="col-sm-12">
		<?php

				 echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 1,
						'attributes' => [ 
								'category_id' => [ 
										'label' => Yii::t ( 'app', 'Product Category' ),
										'type' => Form::INPUT_DROPDOWN_LIST,
										'options' => [ 
												'prompt' => '--'.Yii::t ( 'app', 'Select' ).'--',
												'disabled'=>$dFlag,
										],
										'items' => ArrayHelper::map (ProductCategory::find ()->where("active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'),
								],

								'sub_category_id' => [ 
										'label' => Yii::t ( 'app', 'Product Sub-Category' ),
										'type' => Form::INPUT_DROPDOWN_LIST,
										'options' => [ 
												'prompt' => '--'.Yii::t ( 'app', 'Select' ).'--',
												'disabled'=>$dFlag,
										],
										'items' => ArrayHelper::map (ProductSubCategory::find ()->where("id=0 and active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'),
									],

								'sub_subcategory_id' => [ 
										'label' => Yii::t ( 'app', 'Product Sub-SubCategory' ),
										'type' => Form::INPUT_DROPDOWN_LIST,
										'options' => [ 
												'prompt' => '--'.Yii::t ( 'app', 'Select' ).'--',
												'disabled'=>$dFlag,
										],
										'items' => ArrayHelper::map (ProductSubSubCategory::find ()->where("id=0 and active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'),
									],

								'commission_type' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
									//	'label' => 'Status',
										'options' => [ 
												'placeholder' => 'Select Commission Type...' 
										] ,
										'columnOptions'=>['colspan'=>1],
										'items'=>array('F'=> Yii::t('app', 'Fixed') ,'P'=> Yii::t('app', 'Percent'))  , 
										'options' => [ 
												'prompt' => '--'.Yii::t('app', 'Select').'--'
										]
									],

								'commission' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Commission...']],
							]
					]
				 );

	?>
	</div>
	</div>
	</div>
	<?php

	if($model->isNewRecord)
	{
	?>
		<input type="hidden" name="Commission[added_by_id]" class="form-control" value="<?=Yii::$app->user->identity->id?>">
	<?php
	}
    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    );
    ActiveForm::end(); ?>

</div>
