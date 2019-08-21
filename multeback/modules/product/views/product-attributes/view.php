<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use multebox\models\search\MulteModel;
use multebox\models\ProductSubSubCategory;
use multebox\models\ProductSubCategory;
use multebox\models\ProductCategory;

/**
 * @var yii\web\View $this
 * @var multebox\models\ProductSubCategory $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Attributes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', ProductSubSubCategory::find()->where('id='.$model->parent_id)->one()->name), 'url' => ['product-sub-sub-category/view', 'id' => $model->parent_id]];
$this->params['breadcrumbs'][] = $this->title;

$c_id = ProductSubSubCategory::find()->where('id = '.$_REQUEST['parent_id'])->one()->parent_id;
$b_name = ProductSubCategory::find()->where('id = '.$c_id)->one()->name;
$b_id = ProductSubCategory::find()->where('id = '.$c_id)->one()->parent_id;
$a_name = ProductCategory::find()->where('id = '.$b_id)->one()->name;

if($_REQUEST['reload'] == 'true')
{
?>
	<script>
		document.location.href="<?=Url::to(['/product/product-attributes/view', 'id' => $model->id])?>";
	</script>
<?php
}
?>

<?php $form = ActiveForm::begin ( [ 
						'type' => ActiveForm::TYPE_VERTICAL , 
  						'options'=>array('enctype' => 'multipart/form-data')
				] );?>
<div class="panel panel-info">
	<div class="panel-heading">
    	<h3 class="panel-title"><?php echo Yii::t('app', 'Product Attributes'); ?> - <?=$model->name?>
        	<div class="pull-right">
                <a class="close" href="<?=Url::to(['/product/product-sub-sub-category/view', 'id' => $model->parent_id])?>" >
                	<span class="glyphicon glyphicon-remove"></span>
                </a>
            </div>
        </h3>
    </div>
    <div class="panel-body">
        	<div class="product-attributes-update">
        		<div class="row">
                	<div class="col-sm-12">
<div class="form-group">
		<label class="control-label"><?php echo Yii::t ( 'app', 'Main Category' ); ?></label>
		<input type="text" name="" class="form-control" value="<?=$a_name?>" disabled=true>
	</div>

	<div class="form-group">
		<label class="control-label"><?php echo Yii::t ( 'app', 'Sub Category' ); ?></label>
		<input type="text" name="" class="form-control" value="<?=$b_name?>" disabled=true>
	</div>
				   <?=  Form::widget ( [ 
                             'model' => $model,
                             'form' => $form,
                             'columns' => 4,
                             'attributes' => [ 
                                     'parent_id' => [ 
			                                         'type' => Form::INPUT_TEXT,
		                                            'options' => [ 
													'placeholder' => Yii::t('app','Enter Product Attribute Name...'),
													'maxlength' => 255,
													'label' => Yii::t('app', 'Sub Category'),
													'value' => $b_name,
													'disabled' => true
											],
    
                                            'columnOptions' => [ 
													'colspan' => 3 
											] 
										]
                            ]
                        ]
                   );?>
						<?=  Form::widget ( [ 
                             'model' => $model,
                             'form' => $form,
                             'columns' => 4,
                             'attributes' => [ 
                                     'name' => [ 
			                                         'type' => Form::INPUT_TEXT,
		                                            'options' => [ 
													'placeholder' => Yii::t('app','Enter Product Attribute Name...'),
													'maxlength' => 255 
											],
    
                                            'columnOptions' => [ 
													'colspan' => 3 
											] 
										]
                            ]
                        ]
                   );?>
                    
					 <?=  Form::widget ( [ 
                             'model' => $model,
                             'form' => $form,
                             'columns' => 4,
                             'attributes' => [ 
                                     'active' => [ 
			                                         'type' => Form::INPUT_DROPDOWN_LIST,
		                                            'options' => [ 
													'placeholder' => Yii::t('app','Is Active?...'),
											],
    
                                            'columnOptions'=>['colspan'=>1],
											'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
											'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select').'--'
											],
										]
                            ]
                        ]
                   );
                     ActiveForm::end ();?>
                   </div>
                </div>
	        </div>

			<?php
				echo Html::submitButton ( $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [ 
                                                'class' => $model->isNewRecord ? 'btn btn-success product_attributes_submit' : 'btn btn-primary btn-sm  product_attributes_submit' 
                                        ] );
			?> 
    </div>
    
</div>  
