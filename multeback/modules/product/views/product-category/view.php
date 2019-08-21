<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use multebox\models\search\MulteModel;
use multebox\models\search\ProductSubCategory as ProductSubCategorySearch;


/**
 * @var yii\web\View $this
 * @var multebox\models\ProductCategory $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$dFlag=false;
if(Yii::$app->user->identity->entity_type == 'vendor')
{
	$dFlag=true;
}
?>

<script src="<?=Url::base()?>/bower_components/ckeditor/ckeditor.js"></script>

<?php $form = ActiveForm::begin ( [ 
						'type' => ActiveForm::TYPE_VERTICAL , 
  						'options'=>array('enctype' => 'multipart/form-data')
				] );?>
<div class="panel panel-info">
	<div class="panel-heading">
    	<h3 class="panel-title"><?php echo Yii::t('app', 'Product Category'); ?> - <?=$model->name?>
        	<div class="pull-right">
                <a class="close" href="<?=Url::to(['/product/product-category/index'])?>" >
                	<span class="glyphicon glyphicon-remove"></span>
                </a>
            </div>
        </h3>
    </div>
    <div class="panel-body">
        	<div class="product-category-update">
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
													'placeholder' => Yii::t('app','Enter Product Category Name...'),
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
                                                    'placeholder' => Yii::t('app','Enter Description...') ,
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
    
                                            'columnOptions'=>['colspan'=>1],
											'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
											'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select').'--',
												'disabled' => $dFlag,
											],
										]
                            ]
                        ]
                   );
                     ActiveForm::end ();?>
                   </div>
                </div>
	        </div>
        	<div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
					<li class="active"><a href="#subcategory" role="tab" data-toggle="tab"><?php echo Yii::t('app', 'Sub Categories'); ?></a></li>
					<?php
						echo Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['product-sub-category/create', 'parent_id'=>$model->id], ['class' => 'btn btn-success']);
					?>
                </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="subcategory"> 
                <br/>	
				 <?php
								
					$dataProviderPrdSubCat = MulteModel::searchProductSubCategory (Yii::$app->request->getQueryParams(), $model->id);
					$searchModelPrdSubCat = new ProductSubCategorySearch;
				
					echo Yii::$app->controller->renderPartial("../product-sub-category/index", [ 
							'dataProvider' => $dataProviderPrdSubCat,
							'searchModel' => $searchModelPrdSubCat
					] );
								
				?>
                </div>
            </div>
          </div>
			<?php
				echo Html::submitButton ( $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [ 
                                                'class' => $model->isNewRecord ? 'btn btn-success product_category_submit' : 'btn btn-primary btn-sm  product_category_submit' 
                                        ] );
			?> 
    </div>
    
</div>  

<script>
  $(function () {
    CKEDITOR.replace('productcategory-description');
  })
</script>