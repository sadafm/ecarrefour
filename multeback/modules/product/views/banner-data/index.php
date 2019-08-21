<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use multebox\models\Product;
use multebox\models\BannerType;
use multebox\models\Inventory;
use multebox\models\ProductCategory;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\BannerData $searchModel
 */

$this->title = Yii::t('app', 'Banner Data');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banner-data-index">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Banner Data',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'responsiveWrap' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'banner_file',
//            'text_1',
//            'text_2',
//            'text_3',
			[
                'attribute' => 'banner_type',
                'filterType' => GridView::FILTER_SELECT2,
				'label' => Yii::t('app', 'Banner Type'),
                'format' => 'raw',
                'width' => '300px',
                'filter' => ArrayHelper::map(BannerType::find()->orderBy('type')
                    ->asArray()
                    ->all(), 'id', 'type'),
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
					if (isset($model->banner_type)) 
					{
						return BannerType::findOne($model->banner_type)->type;
					}
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
			[
                'attribute' => 'product_id',
                'filterType' => GridView::FILTER_SELECT2,
				'label' => Yii::t('app', 'Product'),
                'format' => 'raw',
                'width' => '300px',
                'filter' => ArrayHelper::map(Product::find()->orderBy('name')
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
					if (isset($model->product_id)) 
					{
						return Product::findOne($model->product_id)->name;
					}
                }
            ],
			[
                'attribute' => 'inventory_id',
                'filterType' => GridView::FILTER_SELECT2,
				'label' => Yii::t('app', 'Inventory Item'),
                'format' => 'raw',
                'width' => '300px',
                'filter' => ArrayHelper::map(Inventory::find()->orderBy('product_name')
                    ->asArray()
                    ->all(), 'id', 'product_name'),
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
					if (isset($model->inventory_id)) 
					{
						$inventory = Inventory::findOne($model->inventory_id);
						return $inventory->product_name.' ('.($inventory->attribute_values != ''?$inventory->attribute_values:'No Attributes').')';
					}
                }
            ],
//            'added_at', 
//            'updated_at', 

            [
                'class' => 'yii\grid\ActionColumn',
				'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['product/banner-data/update', 'id' => $model->id]),
                            ['title' => Yii::t('app', 'Edit'),]
                        );
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
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),
            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>

</div>
