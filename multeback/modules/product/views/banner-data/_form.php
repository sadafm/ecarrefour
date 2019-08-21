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
use multebox\models\Product;
use multebox\models\Inventory;
use multebox\models\BannerType;


/**
 * @var yii\web\View $this
 * @var multebox\models\BannerData $model
 * @var yii\widgets\ActiveForm $form
 */

 if($model->isNewRecord)
 {
	 $dFlag = false;
 }
 else
 {
	 $dFlag = false;
 }

?>

<div class="banner-data-form">

	<?php $form = ActiveForm::begin ( [ 
						'type' => ActiveForm::TYPE_VERTICAL ,
						'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']],  //this helps to show icons in validation messages 
				] );?>

	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Banner Data' ); ?><em> (<?=Yii::t('app', 'Select desired category/product to link banner image accordingly on frontend')?>) </em></h3>
		</div>
		<div class="panel-body">
			<div class="col-sm-12">
				
				<?php
					$productitems = ArrayHelper::map (Product::find ()->where("id=0")->orderBy ( 'name' )->asArray ()->all (), 'id','name');
					$inventoryitems = ArrayHelper::map (Inventory::find ()->where("id=0")->orderBy ( 'product_name' )->asArray ()->all (), 'id','product_name');
				echo '<div class="row">
							<div class="col-sm-4">
								<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'Product Category').'</label>
							'.Html::dropDownList('BannerData[category_id]',  $model->category_id,
		 ArrayHelper::map(ProductCategory::find ()->where("active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'category_id']  ).'</div></div>
							<div class="col-sm-4">
							<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'Product Sub Category').'</label>
							'.Html::dropDownList('BannerData[sub_category_id]', 'sub_category_id',
		 ArrayHelper::map(ProductSubCategory::find ()->where("id=0 and active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'sub_category_id']  ).'</div></div>
						<div class="col-sm-4">
							<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'Product Sub-SubCategory').'</label>
							'.Html::dropDownList('BannerData[sub_subcategory_id]', 'sub_subcategory_id',
		 ArrayHelper::map(ProductSubSubCategory::find ()->where("id=0 and active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'sub_subcategory_id']  ).'</div></div></div>';
				


				echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 1,
						'attributes' => [ 
								'product_id' => [ 
										'label' => Yii::t ( 'app', 'Product' ),
										'type' => Form::INPUT_DROPDOWN_LIST,
										'options' => [ 
												'prompt' => '--'.Yii::t ( 'app', 'Select' ).'--',
												'disabled'=>$dFlag,
										],
										'items' => $productitems,
								],
							]
					]);

				echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 1,
						'attributes' => [ 
								'inventory_id' => [ 
										'label' => Yii::t ( 'app', 'Inventory Item' ),
										'type' => Form::INPUT_DROPDOWN_LIST,
										'options' => [ 
												'prompt' => '--'.Yii::t ( 'app', 'Select' ).'--',
												'disabled'=>$dFlag,
										],
										'items' => $inventoryitems,
								],
							]
					]);

			echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 3,
						'attributes' => [ 
								'text_1' => [ 
										'label' => Yii::t ( 'app', 'First Line' ),
										'type' => Form::INPUT_TEXT
								],

								'text_2' => [ 
										'label' => Yii::t ( 'app', 'Second Line' ),
										'type' => Form::INPUT_TEXT
								],

								'text_3' => [ 
										'label' => Yii::t ( 'app', 'Button Text' ),
										'type' => Form::INPUT_TEXT
								],
							]
					]);
			
			$bannerType = ArrayHelper::map (BannerType::find ()->orderBy ( 'id' )->asArray ()->all (), 'id','type');

			echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 1,
						'attributes' => [ 
								'banner_type' => [ 
										'label' => Yii::t ( 'app', 'Banner Type' ),
										'type' => Form::INPUT_DROPDOWN_LIST,
										'options' => [ 
												'prompt' => '--'.Yii::t ( 'app', 'Select' ).'--',
												'disabled'=>$dFlag,
										],
										'items' => $bannerType,
								],
							]
					]);
					?>

	<?php
	if(!$model->isNewRecord)
	{
	?>
		<div class="row">
			<div class="col-sm-12">
				<img src="<?=Yii::$app->params['web_url'].'/banner/'.$model->banner_new_name?>" class="img-responsive" style="width:50%;border:1px dotted black"></img>
			</div>
		</div>
		<br>
	<?php
		 echo $form->field($model, 'banner_file')->fileInput()->label(Yii::t('app', 'Change Banner'));
	}
	else
	{
		echo $form->field($model, 'banner_file')->fileInput()->label(Yii::t('app', 'Add Banner'));
	}
	?>

	<?php

		echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
			['class' => $model->isNewRecord ? 'btn btn-success inventory_submit' : 'btn btn-primary inventory_submit']
		);
    ActiveForm::end(); ?>

</div>
