<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use yii\helpers\ArrayHelper;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use multebox\models\ProductCategory;
use multebox\models\ProductBrand;
use multebox\models\ProductAttributes;
use multebox\models\ProductAttributeValues;
use multebox\models\search\MulteModel;
use multebox\models\FileModel;

/**
 * @var yii\web\View $this
 * @var multebox\models\Product $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<?php
if(!$model->isNewRecord)
	$dFlag = true;
else
	$dFlag = false;
?>

<script src="<?=Url::base()?>/bower_components/ckeditor/ckeditor.js"></script>

<div class="product-form">

    <?php $form = ActiveForm::begin ( [ 

						'type' => ActiveForm::TYPE_VERTICAL ,
						'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']],  //this helps to show icons in validation messages 

				] );?>

	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Product Details' ); ?></h3>
		</div>
		<div class="panel-body">
		<div class="col-sm-12">
		<?php
			echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 1,
						'attributes' => [ 
								'name' => [ 
										'label' => Yii::t ( 'app', 'Product Name' ),
										'type' => Form::INPUT_TEXT,
										'options' => [ 
														'placeholder' => Yii::t('app', 'Enter Name...') 
												],
								],
							]
					]
				 );

				 echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 3,
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
							]
					]
				 );

				 echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 3,
						'attributes' => [
								'brand_id' => [ 
										'label' => Yii::t ( 'app', 'Brand Name' ),
										'type' => Form::INPUT_DROPDOWN_LIST,
										'options' => [ 
												'prompt' => '--'.Yii::t ( 'app', 'Select' ).'--',
										],
										'items' => ArrayHelper::map (ProductBrand::find ()->where("active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'),
								],

								'digital' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
									//	'label' => 'Status',
										'options' => [ 
												'placeholder' => Yii::t('app', 'Select Digital ...'), 
										] ,
										'columnOptions'=>['colspan'=>1],
										'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
										'options' => [ 
												//'prompt' => '--'.Yii::t('app', 'Select').'--',
												'disabled' => $dFlag,
										]
									],

								'license_key_code' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
									//	'label' => 'Status',
										'options' => [ 
												'placeholder' => Yii::t('app', 'Select License-Key-Code ...'), 
										] ,
										'columnOptions'=>['colspan'=>1],
										'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
										'options' => [ 
												//'prompt' => '--'.Yii::t('app', 'Select').'--',
												'disabled' => true,
										]
									],

								'upc_code' => [ 
										'type' => Form::INPUT_TEXT,
										'options' => [ 
														'placeholder' => Yii::t('app', 'Enter UPC Code...') 
												],
								],


								'active' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
									//	'label' => 'Status',
										'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Active ...'),
										] ,
										'columnOptions'=>['colspan'=>1],
										'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
										'options' => [ 
												'prompt' => '--'.Yii::t('app', 'Select').'--'
										]
									],
							]
					]
				 );
				?>
				</div>
				<div class="col-sm-12">
				<?php
				  echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 2,
						'attributes' => [ 
								'description' => [ 
										'label' => Yii::t ( 'app', 'Product Description' ),
										'type' => Form::INPUT_TEXTAREA,
										'options' => [ 
														'placeholder' => Yii::t('app', 'Enter Description...') 
												],
								],
							]
					]
				 );
				 ?>
				 </div>
				 <?php
		?>
		</div>

    <?php

	if($model->isNewRecord)
	{
	?>
		<input type="hidden" name="Product[added_by_id]" class="form-control" value="<?=Yii::$app->user->identity->id?>">
	<?php
	}
	else
	{
	?>
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#attachment" role="tab" data-toggle="tab"><?php echo Yii::t('app', 'Product Images'); ?>
						<span class="badge"> <?= FileModel::getAttachmentCount('product',$model->id)?></span>
					</a>
				</li>
			</ul>
		   
			<div class="tab-content">
				<div class="tab-pane active" id="attachment"> 
					<br/>			
				  <?php
									
						$searchModelAttch = new MulteModel();
						$dataProviderAttach = $searchModelAttch->searchAttachments( Yii::$app->request->getQueryParams (), $model->id,'product');
						
						echo Yii::$app->controller->renderPartial("../../../../../multebox/modules/multeobjects/views/file/attachment-module/attachments", [ 
								'dataProviderAttach' => $dataProviderAttach,
								'searchModelAttch' => $searchModelAttch,
								'product_id'=>$model->id,
								'entity_type'=>'product',
						] );
									
				?>
				</div>
			</div>
		</div>

		<?php
		$image_type="image/JPG, image/PNG, image/JPEG";
		?>
		<a href="javascript:void(0)" class="btn btn-success btn-sm" onClick="$('.savepopup').modal('show');"><i class="glyphicon glyphicon-save"></i> <?=Yii::t('app', 'New Attachment')?></a>
	<?php
	}

	echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary btn-sm']
    );
    ActiveForm::end(); 
	
	if(!$model->isNewRecord)
	{
		include_once(__DIR__ .'/../../../../../multebox/modules/multeobjects/views/file/attachment-module/attachmentae.php');
	}

	?>

</div>

<script>
  $(function () {
    CKEDITOR.replace('product-description');
  })
</script>