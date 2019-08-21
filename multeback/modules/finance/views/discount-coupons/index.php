<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use multebox\models\User;
use multebox\models\ProductCategory;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\DiscountCoupons $searchModel
 */

$this->title = Yii::t('app', 'Discount Coupons');
$this->params['breadcrumbs'][] = $this->title;

function statusLabel($discount_type)
{
	if ($discount_type =='F')
	{
		$label = "<span class=\"label label-info\">".Yii::t('app', 'Fixed')."</span>";
	}
	else
	{
		$label = "<span class=\"label label-success\">".Yii::t('app', 'Percent')."</span>";
	}
	return $label;
}

function expiryLabel($max_uses, $used_count, $expiry_datetime)
{
	if (intval($expiry_datetime) <= time())
	{
		$label = date('M d, Y:H:i:s', $expiry_datetime)." <span class=\"label label-danger\">".Yii::t('app', 'Expired')."</span>";
	}
	else
	{
		if(intval($used_count) >= intval($max_uses))
		{
			$label = date('M d, Y:H:i:s', $expiry_datetime)." <span class=\"label label-danger\">".Yii::t('app', 'Expired')."</span>";
		}
		else
		{
			$label = date('M d, Y:H:i:s', $expiry_datetime)." <span class=\"label label-success\">".Yii::t('app', 'Active')."</span>";
		}
	}
	return $label;
}

$discount_type = array('F'=>Yii::t('app', 'Fixed'),'P'=>Yii::t('app', 'Percent'));

?>
<div class="discount-coupons-index">
  <!--  <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div> -->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Discount Coupons',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'responsiveWrap' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

          //  'id',
			'coupon_code',
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
					if ($model->category_id != '') 
					{
						return ProductCategory::findOne($model->category_id)->name;
					}
					else
					{
						return "<span class=\"label label-info\">".'NA'."</span>";
					}
                }
            ],
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
					if ($model->sub_category_id != '') 
					{
						return ProductSubCategory::findOne($model->sub_category_id)->name;
					}
					else
					{
						return "<span class=\"label label-info\">".'NA'."</span>";
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
					if ($model->sub_subcategory_id != '') 
					{
						return ProductSubSubCategory::findOne($model->sub_subcategory_id)->name;
					}
					else
					{
						return "<span class=\"label label-info\">".'NA'."</span>";
					}
                }
            ],

			[ 
				'attribute' => 'discount_type',
			//	'label' => 'Active',
				'format' => 'raw',
				'filterType' => GridView::FILTER_SELECT2,
				'filter' => $discount_type,
				'filterWidgetOptions' => [ 
						'options' => [ 
								'placeholder' => Yii::t('app', 'Discount Type...') 
						],
						'pluginOptions' => [ 
								'allowClear' => true 
						] 
				],
				'value' => function ($model, $key, $index, $widget)
				{
						return statusLabel ( $model->discount_type );
				} 
			],
           // 'inventory_id',
            
//            'discount_type', 
            'discount', 
			[ 
				'attribute' => 'max_discount',
				'format' => 'raw',
				'label' => Yii::t('app', 'Maximum Discount'),

				'value' => function ($model, $key, $index, $widget)
				{
					if($model->discount_type == 'P')
					{
						return $model->max_discount;
					}
					else
					{
						return "<span class=\"label label-info\">".Yii::t('app', 'NA')."</span>";
					}
				} 
			],
			'min_cart_amount', 
			'max_budget',
			'used_budget',
            'max_uses', 
			'used_count',
            //'expiry_datetime:datetime', 
			[ 
				'attribute' => 'expiry_datetime',
			//	'label' => 'Active',
				'format' => 'raw',
				'value' => function ($model, $key, $index, $widget)
				{
						return expiryLabel ( $model->max_uses, $model->used_count, $model->expiry_datetime );
				} 
			],
//            'customer_id', 
//            'added_by_id', 
//            'added_at', 
//            'updated_at', 
			
			[
                'class' => '\kartik\grid\DataColumn',
				'mergeHeader' => true,
				'label' => Yii::t('app', 'Coupon Type'),
				'format' => 'raw',
				'value' => function ($model, $key, $index, $widget)
				{
					if(User::findOne($model->added_by_id)->entity_type == 'vendor')
					{
						return "<span class=\"label label-success\">".Yii::t('app', 'Vendor')."</span>";
					}
					else
					{
						return "<span class=\"label label-warning\">".Yii::t('app', 'Site')."</span>";
					}
				} 
            ],
			[
                //'class' => 'yii\grid\DataColumn',
				'class' => '\kartik\grid\DataColumn',
				'mergeHeader' => true,
				'label' => Yii::t('app', 'Coupon Level'),
                'format' => 'raw',
				'value' => function ($model, $key, $index, $widget)
				{
					if($model->category_id == '')
					{
						if($model->customer_id == '')
							return "<span class=\"label label-info\">".Yii::t('app', 'Global')."</span>";
						else
							return "<span class=\"label label-info\">".Yii::t('app', 'Customer - Global')."</span>";
					}
					else
					{
						if($model->sub_category_id == '')
						{
							if($model->customer_id == '')
								return "<span class=\"label label-info\">".Yii::t('app', 'Category')."</span>";
							else
								return "<span class=\"label label-info\">".Yii::t('app', 'Customer - Category')."</span>";
						}
						else
						{
							if($model->sub_subcategory_id == '')
							{
								if($model->customer_id == '')
									return "<span class=\"label label-info\">".Yii::t('app', 'Sub Category')."</span>";
								else
									return "<span class=\"label label-info\">".Yii::t('app', 'Customer - Sub Category')."</span>";
							}
							else
							{
								if($model->inventory_id == '')
								{
									if($model->customer_id == '')
										return "<span class=\"label label-info\">".Yii::t('app', 'Sub-SubCategory')."</span>";
									else
										return "<span class=\"label label-info\">".Yii::t('app', 'Customer - Sub-SubCategory')."</span>";
								}
								else
								{
									if($model->customer_id == '')
										return "<span class=\"label label-info\">".Yii::t('app', 'Inventory')."</span>";
									else
										return "<span class=\"label label-info\">".Yii::t('app', 'Customer - Inventory')."</span>";
								}
							}
						}
					}
					return '';
				} 
            ],

            [
                'class' => 'kartik\grid\ActionColumn',
				'template'=>'{update}  {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
						if(Yii::$app->params['user_role'] != 'admin' && $model->added_by_id != Yii::$app->user->identity->id)
							return '';
						else
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['/finance/discount-coupons/update', 'id' => $model->id, 'edit' => 't']),
                            ['title' => Yii::t('app', 'Edit'),]
                        );
                    },

					'delete' => function ($url, $model) {
						if(Yii::$app->params['user_role'] != 'admin' && $model->added_by_id != Yii::$app->user->identity->id)
							return '';
						else
						return Html::a('<span class="glyphicon glyphicon-trash"></span>',
							 Yii::$app->urlManager->createUrl(['/finance/discount-coupons/delete', 'id' => $model->id]),
								['title' => Yii::t('app', 'Delete'), 'data-confirm' => 'Are you sure you want to delete this coupon?', 'data-method' => 'post',]
							);
						},
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
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success']),
            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>

</div>
