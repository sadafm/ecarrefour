<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use multebox\models\ProductCategory;
use multebox\models\User;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\Product $searchModel
 */

$this->title = Yii::t('app', 'Products');
$this->params['breadcrumbs'][] = $this->title;

function statusLabel($status)
{
	if ($status !='1')
	{
		$label = "<span class=\"label label-danger\">".Yii::t('app', 'Inactive')."</span>";
	}
	else
	{
		$label = "<span class=\"label label-primary\">".Yii::t('app', 'Active')."</span>";
	}
	return $label;
}
$status = array('0'=>Yii::t('app', 'Inactive'),'1'=>Yii::t('app', 'Active'));

?>
<div class="product-index">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Product',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'responsiveWrap' => false,
		'pjax' => true,
        'columns' => [
			['class' => '\kartik\grid\CheckboxColumn'],
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
            //'category_id',
			'name',
			[ 
				'attribute' => 'active',
			//	'label' => 'Active',
				'format' => 'raw',
				'filterType' => GridView::FILTER_SELECT2,
				'filter' => $status,
				'filterWidgetOptions' => [ 
						'options' => [ 
								'placeholder' => Yii::t('app', 'All...') 
						],
						'pluginOptions' => [ 
								'allowClear' => true 
						] 
				],
				'value' => function ($model, $key, $index, $widget)
				{
						return statusLabel ( $model->active );
				} 
			],

			 [
                'attribute' => 'category_id',
                'filterType' => GridView::FILTER_SELECT2,
				'label' => Yii::t('app', 'Product Category'),
                'format' => 'raw',
                'width' => '300px',
                'filter' => ArrayHelper::map(ProductCategory::find()->orderBy('name')
                    ->where("active=1")
                    ->asArray()
                    ->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'All...')
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ],
                'value' => function ($model, $key, $index, $widget)
                {
					if (isset($model->category_id)) 
					{
						return ProductCategory::findOne($model->category_id)->name;
					}
                }
            ],
           // 'sub_category_id',
			[
                'attribute' => 'sub_category_id',
                'filterType' => GridView::FILTER_SELECT2,
				'label' => Yii::t('app', 'Product Sub Category'),
                'format' => 'raw',
                'width' => '300px',
                'filter' => ArrayHelper::map(ProductSubCategory::find()->orderBy('name')
                    ->where("active=1")
                    ->asArray()
                    ->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'All...')
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ],
                'value' => function ($model, $key, $index, $widget)
                {
					if (isset($model->sub_category_id)) 
					{
						return ProductSubCategory::findOne($model->sub_category_id)->name;
					}
                }
            ],
			[
                'attribute' => 'sub_subcategory_id',
                'filterType' => GridView::FILTER_SELECT2,
				'label' => Yii::t('app', 'Product Sub-SubCategory'),
                'format' => 'raw',
                'width' => '300px',
                'filter' => ArrayHelper::map(ProductSubSubCategory::find()->orderBy('name')
                    ->where("active=1")
                    ->asArray()
                    ->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'All...')
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ],
                'value' => function ($model, $key, $index, $widget)
                {
					if (isset($model->sub_subcategory_id)) 
					{
						return ProductSubSubCategory::findOne($model->sub_subcategory_id)->name;
					}
                }
            ],
           // 'description:ntext',
//            'brand_id', 
//            'active', 
//            'added_by_id', 
//            'sort_order', 
//            'added_at', 
//            'updated_at', 

            [
                'class' => 'yii\grid\ActionColumn',
				'template'=>'{update}  {delete} {action}',
                'buttons' => [
                    'update' => function ($url, $model) {
						if(Yii::$app->params['user_role'] != 'admin' && $model->added_by_id != Yii::$app->user->identity->id)
							return '';
						else
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['/product/product/update', 'id' => $model->id, 'edit' => 't']),
                            ['title' => Yii::t('app', 'Edit'),]
                        );
                    },

					'delete' => function ($url, $model) {
						if(Yii::$app->user->identity->entity_type=='vendor')
							return '';
						else
						return Html::a('<span class="glyphicon glyphicon-trash"></span>',
							 Yii::$app->urlManager->createUrl(['/product/product/delete', 'id' => $model->id]),
								['title' => Yii::t('app', 'Delete'), 'data-method' => 'POST', 'data-confirm' => Yii::t('app', 'Are you sure you want to delete this product?'),]
							);
						},

					'action' => function ($url, $model) {
						if(Yii::$app->user->identity->entity_type=='vendor')
							return '';
						else
						if($model->active == 0)
						{
							return Html::a('<span class="glyphicon glyphicon-ok"></span>',
							 Yii::$app->urlManager->createUrl(['/product/product/activate', 'id' => $model->id, 'activate' => 't']),
								['title' => Yii::t('app', 'Activate'), 'data-confirm' => Yii::t('app', 'Are you sure you want to activate this product?'),]
							);
						}
						else
						{
							return Html::a('<span class="glyphicon glyphicon-remove"></span>',
							 Yii::$app->urlManager->createUrl(['/product/product/deactivate', 'id' => $model->id, 'deactivate' => 't']),
								['title' => Yii::t('app', 'Deactivate'), 'data-confirm' => Yii::t('app', 'Are you sure you want to deactivate this product?'),]
							);
						}
                    }
                ],
            ],
        ],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => false,

        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type' => 'info',
            'before' => '<form action=""  method="post" name="frm"><input type="hidden" name="_csrf" value="'.Yii::$app->request->csrfToken.'"><input type="hidden" name="bulk_download" value="true">'.Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success']).' <a href="javascript:void(0)" onClick="bulk_down()" class="btn btn-danger"><i class="glyphicon glyphicon-download"></i> '.Yii::t ( 'app', 'Download Bulk Inventory Create Template' ).'</a>',
            'after' => '</form>'.Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>

</div>

<script>
function bulk_down(){

		var r = confirm("<?=Yii::t ('app','Are you Sure!')?>");

		if (r == true) {

			document.frm.submit()

		} else {

			

		}	

	}
</script>