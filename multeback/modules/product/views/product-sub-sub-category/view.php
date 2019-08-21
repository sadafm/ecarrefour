<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use multebox\models\search\MulteModel;
use multebox\models\search\ProductAttributes as ProductAttributesSearch;
use multebox\models\ProductSubCategory;
use yii\helpers\ArrayHelper;
use multebox\models\Tax;

/**
 * @var yii\web\View $this
 * @var multebox\models\ProductSubSubCategory $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', ProductSubCategory::find()->where('id='.$model->parent_id)->one()->name), 'url' => ['product-sub-category/view', 'id' => $model->parent_id]];
$this->params['breadcrumbs'][] = $this->title;

if($_REQUEST['reload'] == 'true')
{
?>
	<script>
		document.location.href="<?=Url::to(['/product/product-sub-sub-category/view', 'id' => $model->id])?>";
	</script>
<?php
}
?>

<script src="<?=Url::base()?>/bower_components/ckeditor/ckeditor.js"></script>
<?php $form = ActiveForm::begin ( [ 
						'type' => ActiveForm::TYPE_VERTICAL , 
  						'options'=>array('enctype' => 'multipart/form-data')
				] );?>
<div class="panel panel-info">
	<div class="panel-heading">
    	<h3 class="panel-title"><?php echo Yii::t('app', 'Product Sub-SubCategory'); ?> - <?=$model->name?>
        	<div class="pull-right">
                <a class="close" href="<?=Url::to(['/product/product-sub-sub-category/index'])?>" >
                	<span class="glyphicon glyphicon-remove"></span>
                </a>
            </div>
        </h3>
    </div>
    <div class="panel-body">
        	<div class="product-sub-sub-category-update">
        		<div class="row">
                	<div class="col-sm-12">
						<?=  Form::widget ( [ 
                             'model' => $model,
                             'form' => $form,
                             'columns' => 4,
                             'attributes' => [ 
                                     'name' => [ 
			                                         'type' => Form::INPUT_TEXT,
		                                            'options' => [ 
													'placeholder' => Yii::t('app','Enter Product Sub-SubCategory Name...'),
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
									'description' => [ 
											'type' => Form::INPUT_TEXTAREA,
											'options' => [ 
													'prompt' => '--'.Yii::t('app','Description Type').'--',
                                                    'placeholder' => Yii::t('app', 'Enter Description...') ,
													//'rows' => 6
											],
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
										],
										'tax_ind' => [ 
														'type' => Form::INPUT_DROPDOWN_LIST,
														'label' => Yii::t('app', 'Apply Tax'),
														'options' => [ 
																'placeholder' => Yii::t('app', 'Enter Active...')
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
																'placeholder' => Yii::t('app', 'Select Tax Type...')
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
                            ]
                        ]
                   );
                     ActiveForm::end ();?>
                   </div>
                </div>
	        </div>
        	<div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
					<li class="active"><a href="#productattributes" role="tab" data-toggle="tab"><?php echo Yii::t('app', 'Product Attributes'); ?></a></li>
					<?php
						echo Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['product-attributes/create', 'parent_id'=>$model->id], ['class' => 'btn btn-success']);
					?>
                </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="productattributes"> 
                <br/>	
				 <?php
								
					$dataProviderPrdAttr = MulteModel::searchProductAttributes (Yii::$app->request->getQueryParams(), $model->id);
					$searchModelPrdAttr = new ProductAttributesSearch;
				
					echo Yii::$app->controller->renderPartial("../product-attributes/index", [ 
							'dataProvider' => $dataProviderPrdAttr,
							'searchModel' => $searchModelPrdAttr
					] );
								
				?>
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

<script>
  $(function () {
    CKEDITOR.replace('productsubsubcategory-description');
  })
</script>