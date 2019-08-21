<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use yii\helpers\ArrayHelper;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use multebox\models\ProductCategory;
use multebox\models\Inventory;
use multebox\models\User;
use kartik\widgets\DateTimePicker;

/**
 * @var yii\web\View $this
 * @var multebox\models\DiscountCoupons $model
 * @var yii\widgets\ActiveForm $form
 */

if ($model->expiry_datetime != '')
{
	$model->expiry_datetime=date('Y-m-d H:i:s', $model->expiry_datetime);
}

if(!$model->isNewRecord)
{
	$dFlag = true;
}
else
{
	$dFlag = false;
}
?>

<div class="discount-coupons-form">

    <?php $form = ActiveForm::begin ( [ 

						'type' => ActiveForm::TYPE_VERTICAL ,
						'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']],  //this helps to show icons in validation messages 

				] );?>
				

				<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Discount Coupon Details' ); ?></h3>
		</div>
		<div class="panel-body">
		<div class="col-sm-12">
		<?php

				 echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 1,
						'attributes' => [ 
								'coupon_code' => [
										'type' => Form::INPUT_TEXT, 
										'options' => [
												'placeholder' => 'Enter coupon code...',
												'disabled'=>$dFlag,
										]
								],

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

								'discount_type' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
									//	'label' => 'Status',
										'options' => [ 
												'placeholder' => 'Select Discount Type...' 
										] ,
										'columnOptions'=>['colspan'=>1],
										'items'=>array('F'=> Yii::t('app', 'Fixed') ,'P'=> Yii::t('app', 'Percent'))  , 
										'options' => [ 
												'prompt' => '--'.Yii::t('app', 'Select').'--'
										]
									],

								'discount' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => Yii::t('app', 'Enter discount...')]],

								'max_discount' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => Yii::t('app', 'Enter maximum discount...')]],

								'max_budget' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => Yii::t('app', 'Enter maximum budget...')]],

								'min_cart_amount' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => Yii::t('app', 'Enter minimum cart amount...')]],

								'max_uses' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => Yii::t('app', 'Enter max uses...')]],

								'expiry_datetime' => [ 
										'type'=> Form::INPUT_WIDGET, 'widgetClass'=>DateTimePicker::classname(),
										'options'=>[
											'type'=>DateTimePicker::TYPE_COMPONENT_PREPEND,
											'pluginOptions' => [
																  'autoclose'=>true,
																  'format' => 'yyyy-mm-dd hh:ii:s',
																  'todayHighlight' => true,
															],
										]
								],
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
		<input type="hidden" name="DiscountCoupons[added_by_id]" class="form-control" value="<?=Yii::$app->user->identity->id?>">
	<?php
	}
    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    );
    ActiveForm::end(); ?>

</div>