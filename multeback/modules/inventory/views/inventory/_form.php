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
use multebox\models\Product;
use multebox\models\Vendor;
use multebox\models\Inventory;
use multebox\models\ProductAttributes;
use multebox\models\ProductAttributeValues;
use multebox\models\LicenseKeyCode;

/**
 * @var yii\web\View $this
 * @var multebox\models\Inventory $model
 * @var yii\widgets\ActiveForm $form
 */

 if($model->isNewRecord)
 {
	 $dFlag = false;
 }
 else
 {
	 $dFlag = true;
 }

 if($model->product->license_key_code)
 {
	 $stockFlag = true;
 }
 else
 {
	 $stockFlag = false;
 }
?>

<div class="inventory-form">
	<?php $form = ActiveForm::begin ( [ 
						'type' => ActiveForm::TYPE_VERTICAL ,
						'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']],  //this helps to show icons in validation messages 
				] );?>

	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Inventory Item' ); ?>
			
			</h3>
		</div>
		
		<div class="pull-right">
			<?php
				if(Yii::$app->params['user_role'] == 'admin')
				{
					echo "<br>";
					if(!$model->featured)
						echo Html::a('<i class="fa fa-check"></i> '.Yii::t('app', 'Set Featured'), ['/inventory/inventory/set-featured','id'=>$model->id], ['class'=>'btn btn-primary btn-xs']);
					else
						echo Html::a('<i class="fa fa-close"></i> '.Yii::t('app', 'Unset Featured'), ['/inventory/inventory/unset-featured','id'=>$model->id], ['class'=>'btn btn-danger btn-xs']);
					echo "&nbsp;";
					if(!$model->special)
						echo Html::a('<i class="fa fa-check"></i> '.Yii::t('app', 'Set Special'), ['/inventory/inventory/set-special','id'=>$model->id], ['class'=>'btn btn-primary btn-xs']);
					else
						echo Html::a('<i class="fa fa-close"></i> '.Yii::t('app', 'Unset Special'), ['/inventory/inventory/unset-special','id'=>$model->id], ['class'=>'btn btn-danger btn-xs']);
					echo "&nbsp;";
					if($model->hot)
						echo Html::a('<i class="fa fa-close"></i> '.Yii::t('app', 'Unset Hot'), ['/inventory/inventory/unset-hot','id'=>$model->id], ['class'=>'btn btn-primary btn-xs']);
					
					$hotcount = Inventory::find()->where(['hot' => 1])->count();

					if($hotcount == 0)
					{
						echo Html::a('<i class="fa fa-check"></i> '.Yii::t('app', 'Set Hot'), ['/inventory/inventory/set-hot','id'=>$model->id], ['class'=>'btn btn-primary btn-xs']);
					}
					echo "&nbsp;";

					echo "<br>";
				}
			?>
		</div>

		<div class="panel-body">
			<div class="col-sm-12">
				<?php
				if($model->isNewRecord)
				{
					$productitems = ArrayHelper::map (Product::find ()->where("id=0")->orderBy ( 'name' )->asArray ()->all (), 'id','name');
				echo '<div class="row">
							<div class="col-sm-4">
								<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'Product Category').'</label>
							'.Html::dropDownList('category_id',  'category_id',
		 ArrayHelper::map(ProductCategory::find ()->where("active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'category_id','data-validation'=>'required' ,'mandatory-field'=>'' ]  ).'</div></div>
							<div class="col-sm-4">
							<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'Product Sub Category').'</label>
							'.Html::dropDownList('sub_category_id', 'sub_category_id',
		 ArrayHelper::map(ProductSubCategory::find ()->where("id=0 and active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'sub_category_id', 'data-validation'=>'required' ,'mandatory-field'=>'' ]  ).'</div></div>
						<div class="col-sm-4">
							<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'Product Sub-SubCategory').'</label>
							'.Html::dropDownList('sub_subcategory_id', 'sub_subcategory_id',
		 ArrayHelper::map(ProductSubSubCategory::find ()->where("id=0 and active=1")->orderBy ( 'name' )->asArray ()->all (), 'id','name'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'sub_subcategory_id', 'data-validation'=>'required' ,'mandatory-field'=>'' ]  ).'</div></div></div>';
				}
				else
				{
					$productitems = ArrayHelper::map (Product::find ()->where("id=$model->product_id")->orderBy ( 'name' )->asArray ()->all (), 'id','name');

					echo '<div class="row">
							<div class="col-sm-4">
								<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'Product Category').'</label>
							'.Html::textInput('c_id', ProductCategory::findOne($model->product->category_id)->name, ['class'=>'form-control', 'disabled'=>'true']).'</div></div>
							<div class="col-sm-4">
							<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'Product Sub Category').'</label>
							'.Html::textInput('sc_id', ProductSubCategory::findOne($model->product->sub_category_id)->name, ['class'=>'form-control', 'disabled'=>'true']).'</div></div>
						<div class="col-sm-4">
							<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'Product Sub-SubCategory').'</label>
							'.Html::textInput('ssc_id', ProductSubSubCategory::findOne($model->product->sub_subcategory_id)->name, ['class'=>'form-control', 'disabled'=>'true']).'</div></div></div>';
				}
				
				if($model->isNewRecord)
				{
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
				}
				else
				{
					echo Form::widget ( [ 
							'model' => $model,
							'form' => $form,
							'columns' => 3,
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

									'vendor_id' => [ 
											'label' => Yii::t ( 'app', 'Vendor Name' ),
											'type' => Form::INPUT_TEXT,
											'options' => [ 
													'placeholder' => Yii::t ( 'app', 'Vendor Name' ).'...',
													'value' => Vendor::findOne($model->vendor_id)->vendor_name,
													'disabled'=>$dFlag,
											],
									],

									'total_sale' => [ 
											'label' => Yii::t ( 'app', 'Total Sale' ),
											'type' => Form::INPUT_TEXT,
											'options' => [ 
													'placeholder' => Yii::t ( 'app', 'Total Sale' ).'...',
													'disabled'=>$dFlag,
													'value' => $model->total_sale?$model->total_sale:0,
											],
									],
								]
						]);
				}
					?>

	<div class="digital-template" hidden><a href="<?=Url::base()?>/digital_license_key_code_template.xlsx" class="btn btn-primary btn-sm"><?=Yii::t('app', 'Download Template')?></a></div>
	<?= $form->field($model, 'digital_file')->fileInput()->hiddenInput()->label(false);?>
	
	<?php

	if($model->isNewRecord || (!$model->isNewRecord && Inventory::findOne($model->id)->product->license_key_code))
	{
		echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 2,
						'attributes' => [ 
								'send_as_attachment' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
									//	'label' => 'Status',
										'options' => [ 
												'placeholder' => Yii::t('app', 'Select'),
										] ,
										'columnOptions'=>['colspan'=>1],
										'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
										'options' => [ 
												//'prompt' => '--'.Yii::t('app', 'Select').'--',
										]
									],

									'attachment_file_name' => [ 
										'label' => Yii::t ( 'app', 'Attachment File Name' ),
										'type' => Form::INPUT_TEXT,
										'options' => [ 
														'placeholder' => Yii::t('app', 'Enter Attachment File Name...') 
												],
								],
							]
					]);
	}
	
	?>

	<?php
	if(!$model->isNewRecord)
	{
	?>
		<?php
		 echo Form::widget ( [ 
				'model' => $model,
				'form' => $form,
				'columns' => 3,
				'attributes' => [
						'discount_type' => [ 
								'type' => Form::INPUT_DROPDOWN_LIST,
							//	'label' => 'Status',
								'options' => [ 
										'placeholder' => Yii::t('app', 'Select Discount Type...') 
								] ,
								'columnOptions'=>['colspan'=>1],
								'items'=>array('F'=> Yii::t('app', 'Flat') ,'P'=> Yii::t('app', 'Percent'))  , 
								'options' => [ 
										'prompt' => '--'.Yii::t('app', 'Select').'--'
								]
							],
						
						'discount' => [ 
								'label' => Yii::t ( 'app', 'Discount' ),
								'type' => Form::INPUT_TEXT,
								'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Discount...')
										],
							],
						'price_type' => [ 
								'type' => Form::INPUT_DROPDOWN_LIST,
							//	'label' => 'Status',
								'options' => [ 
										'placeholder' => Yii::t('app', 'Select Price Type...')
								] ,
								'columnOptions'=>['colspan'=>1],
								'items'=>array('F'=> Yii::t('app', 'Flat') ,'B'=> Yii::t('app', 'Base'))  , 
								'options' => [ 
										'prompt' => '--'.Yii::t('app', 'Select').'--'
								]
							],
						
						'price' => [ 
								'label' => Yii::t ( 'app', 'Price' ),
								'type' => Form::INPUT_TEXT,
								'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Price...')
										],
							],

						'shipping_cost' => [ 
								'label' => Yii::t ( 'app', 'Shipping Cost' ),
								'type' => Form::INPUT_TEXT,
								'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Price...')
										],
							],
						
						'stock' => [ 
								'label' => Yii::t ( 'app', 'Stock' ),
								'type' => Form::INPUT_TEXT,
								'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Stock...'),
												'disabled' => $stockFlag,
										],
							],
						]
					]);

			echo Form::widget ( [ 
				'model' => $model,
				'form' => $form,
				'columns' => 3,
				'attributes' => [
					
						'length' => [ 
								'type' => Form::INPUT_TEXT,
								'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Length...')
										],
							],

						'width' => [ 
								'type' => Form::INPUT_TEXT,
								'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Width...')
										],
							],

						'height' => [ 
								'type' => Form::INPUT_TEXT,
								'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Height...')
										],
							],

						'weight' => [ 
								'type' => Form::INPUT_TEXT,
								'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Weight...')
										],
							],

						'warranty' => [ 
								'type' => Form::INPUT_TEXT,
								'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Warranty...')
										],
							],

						'active' => [ 
											'type' => Form::INPUT_DROPDOWN_LIST,
											'label' => Yii::t('app', 'Active'),
											'options' => [ 
													'placeholder' => Yii::t('app', 'Select Active...') 
											] ,
											'columnOptions'=>['colspan'=>1],
											'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
											'options' => [ 
													'prompt' => '--'.Yii::t('app', 'Select').'--',
											]
										],

						]
					]);
			?>
			<label class="control-label"><?=Yii::t('app', 'Search Tags (Enter Comma Separated Values)')?></label>
					  <textarea class="form-control" rows="2" name="inventory_tags" maxlength="512" placeholder="Enter search tags..." style="resize:none"><?=$tags?></textarea>
					  <label class="control-label"><?=Yii::t('app', 'Max Length: 512 Characters')?></label>

			<?php
			$prdrec = Product::findOne($model->product_id);
			if($prdrec->digital && !$prdrec->license_key_code)
			{
				?><br><br>
				<label class="control-label"><?php echo Yii::t ( 'app', 'Change File' ); ?></label>: <?=$model->digital_file?>
				<input type="file" name="Inventory[digital_file]" class="form-control" value="">
				<?php
			}
			else if($prdrec->digital && $prdrec->license_key_code)
			{
				?><br><br>
				<label class="control-label"><?php echo Yii::t ( 'app', 'Add More Records' ); ?></label>
				<input type="file" name="Inventory[digital_file]" class="form-control" value="">
				<br>
				<a class="btn btn-warning btn-sm" href="javascript:void(0)" onClick="show_records()">
					<i class="glyphicon glyphicon-link"></i> <?=Yii::t('app','Show Existing Records')?>
				</a>
				<?php
			}

			?>
			</div>
		</div>
	</div>
	<?php
	}
	?>
			
	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Attribute Details' ); ?></h3>
		</div>
		<div class="panel-body">

			<?php
			if(!$model->isNewRecord)
			{
				foreach ($inventoryDetails as $row)
				{
				?>
				<div class="col-sm-2">
					<div class="form-group">
						<label class="control-label">
							<?php
							$pat = ProductAttributes::find()->where("id=".$row->attribute_id)->one();
							if($pat->fixed == 0)
							{
								echo $pat->name;
							}
							else
							{
								echo ProductAttributeValues::find()->where("id=".$pat->fixed_id)->one()->name;
							}
							?>
						</label>
						<input type="hidden" name="Inventory[attribute_values][]" value='<?=htmlspecialchars($row->attribute_value)?>'>
						<input type="hidden" name="inventory_detail_ids[]" value='<?=$row->id?>'>
						<input type="text" name="" data-validation="required" mandatory-field class="form-control" disabled value='<?=htmlspecialchars($row->attribute_value)?>'>
					</div>
				</div>

				<div class="col-sm-2">
					<div class="form-group">
						<label class="control-label"><?php echo Yii::t ( 'app', 'Price' ); ?></label>
						<input type="text" name="Inventory[attribute_price][]" data-validation="required" mandatory-field num-validation-float class="form-control" value="<?=$row->attribute_price?>">
					</div>
				</div>
				<?php
				}

				if(!$inventoryDetails)
					echo Yii::t('app', 'No Attributes');
			}
			?>
			<div class="col-sm-12">
				<div class="table-responsive m-t" id="mystable">
					<!-- Product attributes will be loaded here dynamically -->
				</div>
			</div>

			<div class="col-sm-12">
				<?php
				?>
			</div>
		</div> <!-- Panel Body -->
	</div> <!-- Panel Info -->

	<?php
	if(!$model->isNewRecord)
	{
	?>
			<div class="panel panel-info slab_discounts">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Slab Discounts' ); ?></h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-12">
					<?php
					echo Form::widget ( [ 
							'model' => $model,
							'form' => $form,
							'columns' => 2,
							'attributes' => [
									'slab_discount_ind' => [ 
											'type' => Form::INPUT_DROPDOWN_LIST,
											'label' => Yii::t('app', 'Enable Slab Discount'),
											'options' => [ 
													'placeholder' => Yii::t('app', 'Select Slab Indicator...') 
											] ,
											'columnOptions'=>['colspan'=>1],
											'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
											'options' => [ 
													'prompt' => '--'.Yii::t('app', 'Select').'--',
											]
										],
									
									'slab_discount_type' => [ 
											'type' => Form::INPUT_DROPDOWN_LIST,
										//	'label' => 'Status',
											'options' => [ 
													'placeholder' => Yii::t('app', 'Select Slab Discount Type...') 
											] ,
											'columnOptions'=>['colspan'=>1],
											'items'=>array('F'=> Yii::t('app', 'Flat') ,'P'=> Yii::t('app', 'Percent'))  , 
											'options' => [ 
													'prompt' => '--'.Yii::t('app', 'Select').'--',
											]
										],
									]
								]);

						echo Form::widget ( [ 
							'model' => $model,
							'form' => $form,
							'columns' => 4,
							'attributes' => [
									'slab_1_range' => [ 
											//'label' => Yii::t ( 'app', 'Shipping Cost' ),
											'type' => Form::INPUT_TEXT,
											'options' => [ 
															'placeholder' => Yii::t('app', 'Enter Range...')
													],
										],
									
									'slab_1_discount' => [ 
											//'label' => Yii::t ( 'app', 'Shipping Cost' ),
											'type' => Form::INPUT_TEXT,
											'options' => [ 
															'placeholder' => Yii::t('app', 'Enter Discount...')
													],
										],

									'slab_2_range' => [ 
											//'label' => Yii::t ( 'app', 'Shipping Cost' ),
											'type' => Form::INPUT_TEXT,
											'options' => [ 
															'placeholder' => Yii::t('app', 'Enter Range...')
													],
										],
									
									'slab_2_discount' => [ 
											//'label' => Yii::t ( 'app', 'Shipping Cost' ),
											'type' => Form::INPUT_TEXT,
											'options' => [ 
															'placeholder' => Yii::t('app', 'Enter Discount...')
													],
										],

									'slab_3_range' => [ 
											//'label' => Yii::t ( 'app', 'Shipping Cost' ),
											'type' => Form::INPUT_TEXT,
											'options' => [ 
															'placeholder' => Yii::t('app', 'Enter Range...')
													],
										],
									
									'slab_3_discount' => [ 
											//'label' => Yii::t ( 'app', 'Shipping Cost' ),
											'type' => Form::INPUT_TEXT,
											'options' => [ 
															'placeholder' => Yii::t('app', 'Enter Discount...')
													],
										],

									'slab_4_range' => [ 
											//'label' => Yii::t ( 'app', 'Shipping Cost' ),
											'type' => Form::INPUT_TEXT,
											'options' => [ 
															'placeholder' => Yii::t('app', 'Enter Range...')
													],
										],
									
									'slab_4_discount' => [ 
											//'label' => Yii::t ( 'app', 'Shipping Cost' ),
											'type' => Form::INPUT_TEXT,
											'options' => [ 
															'placeholder' => Yii::t('app', 'Enter Discount...')
													],
										],
								]
						]);
				?>
				</div>
			</div>
		</div>
	<?php
	}
	?>
	
	<?php
	if($model->isNewRecord)
	{
	?>
		<input type="hidden" name="Inventory[added_by_id]" class="form-control" value="<?=Yii::$app->user->identity->id?>">
		<input type="hidden" name="Inventory[vendor_id]" class="form-control" value="<?=Yii::$app->user->identity->entity_id?>">
		<input type="hidden" name="Inventory[slab_discount_ind]" class="form-control" value="0">
		<input type="hidden" name="Inventory[active]" class="form-control" value="1">
	<?php
	}

	if(Yii::$app->params['user_role'] != 'admin')
	{
		echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
			['class' => $model->isNewRecord ? 'btn btn-success inventory_submit' : 'btn btn-primary inventory_submit']
		);
	}
    ActiveForm::end(); ?>

</div>

<script>
function show_records()
{
	$('.showrecords').modal('show');
}
</script>

<?php
if(!$model->isNewRecord)
{
?>
<div class="modal showrecords">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?=Yii::t('app', 'Existing Records')?></h4>
		  </div>

		  <div class="modal-body">
				  <?= $this->render('show-records', [
												'dataProvider' => (new LicenseKeyCode)->getCodesForInventory(Yii::$app->request->getQueryParams(), $model->id),
											]) 
				?>
		  </div>

		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
}
?>