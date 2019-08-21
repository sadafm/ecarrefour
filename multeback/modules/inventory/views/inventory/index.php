<?php
use yii\helpers\Json;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use multebox\models\Product;
use multebox\models\Inventory;
use multebox\models\Vendor;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\Inventory $searchModel
 */

$this->title = Yii::t('app', 'Inventories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-index">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div> -->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Inventory',
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

        //    'id',
		//'vendor_id',
			[
                'attribute' => 'vendor_id',
                'filterType' => GridView::FILTER_SELECT2,
				'label' => Yii::t('app', 'Vendor Name'),
                'format' => 'raw',
                'width' => '300px',
                'filter' => ArrayHelper::map(Vendor::find()->orderBy('vendor_name')
                    ->where("active=1")
                    ->asArray()
                    ->all(), 'id', 'vendor_name'),
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
					if (isset($model->vendor_id)) 
					{
						return Vendor::findOne($model->vendor_id)->vendor_name;
					}
                }
            ],
            //'product_id',
			[
                'attribute' => 'product_id',
                'filterType' => GridView::FILTER_SELECT2,
				'label' => Yii::t('app', 'Product Name'),
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
            
            //'stock',
			[ 
				'attribute' => 'stock',
				'width' => '30px',
				'value' => function ($model, $key, $index, $widget)
				{
					return $model->stock;
				} 
			],
//            'price_type',
            //'attribute_values:ntext', 
			[ 
				'attribute' => 'attribute_values',
				'format' => 'raw',
				'width' => '120px',
				'value' => function ($model, $key, $index, $widget)
				{
					if($model->attribute_values == '')
					{
						return "<span class=\"label label-info\">".Yii::t('app', 'NA')."</span>";
					}
					else
					{
						$value = Json::decode($model->attribute_values);
						$final='';
						foreach($value as $item)
						{
							if($final == '')
								$final = $item;
							else
								$final = $final."<br/>".$item;
						}
						return $final;
					}
				} 
			],
//            'attribute_price:ntext', 
//            'price', 
//            'discount_type', 
//            'discount', 
//            'shipping_cost', 
//            'added_by_id', 
//            'sort_order', 
//            'added_at', 
//            'updated_at', 
			[ 
				'attribute' => 'featured',
				'format' => 'raw',
				'width' => '100px',
				'filterType' => GridView::FILTER_SELECT2,
				'filter' => array('0'=>Yii::t('app', 'Not Featured'),'1'=>Yii::t('app', 'Featured')),

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
					if($model->featured)
					{
						return "<span class=\"label label-success\">".Yii::t('app', 'Featured')."</span>";
					}
					else
					{
						return "<span class=\"label label-default\">".Yii::t('app', 'Not Featured')."</span>";
					}
				} 
			],

			[ 
				'attribute' => 'special',
				'format' => 'raw',
				'width' => '100px',
				'filterType' => GridView::FILTER_SELECT2,
				'filter' => array('0'=>Yii::t('app', 'Not Special'),'1'=>Yii::t('app', 'Special')),

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
					if($model->special)
					{
						return "<span class=\"label label-success\">".Yii::t('app', 'Special')."</span>";
					}
					else
					{
						return "<span class=\"label label-default\">".Yii::t('app', 'Not Special')."</span>";
					}
				} 
			],

			[ 
				'attribute' => 'hot',
				'format' => 'raw',
				'filterType' => GridView::FILTER_SELECT2,
				'filter' => array('0'=>Yii::t('app', 'Not Hot'),'1'=>Yii::t('app', 'Hot')),

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
					if($model->hot)
					{
						return "<span class=\"label label-success\">".Yii::t('app', 'Hot')."</span>";
					}
					else
					{
						return "<span class=\"label label-default\">".Yii::t('app', 'Not Hot')."</span>";
					}
				} 
			],

            [
                'class' => 'yii\grid\ActionColumn',
				'template'=> Yii::$app->params['user_role'] == 'admin'?'{update}  {delete} {special} {featured} {hot}':'{update}  {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['/inventory/inventory/update', 'id' => $model->id, 'edit' => 't']),
                            ['title' => Yii::t('app', 'Edit'),]
                        );
                    },

					'special' => function ($url, $model) {
						if(!$model->special)
						{
							return Html::a('<span class="glyphicon glyphicon-ok-circle"></span>',
								Yii::$app->urlManager->createUrl(['/inventory/inventory/set-special', 'id' => $model->id, 'return' => 'index']),
								['title' => Yii::t('app', 'Set Special'),]
							);
						}
						else
						{
							return Html::a('<span class="glyphicon glyphicon-remove-circle"></span>',
								Yii::$app->urlManager->createUrl(['/inventory/inventory/unset-special', 'id' => $model->id, 'return' => 'index']),
								['title' => Yii::t('app', 'Unset Special'),]
							);
						}
                    },

					'featured' => function ($url, $model) {
                        if(!$model->featured)
						{
							return Html::a('<span class="glyphicon glyphicon-ok"></span>',
								Yii::$app->urlManager->createUrl(['/inventory/inventory/set-featured', 'id' => $model->id, 'return' => 'index']),
								['title' => Yii::t('app', 'Set Featured'),]
							);
						}
						else
						{
							return Html::a('<span class="glyphicon glyphicon-remove"></span>',
								Yii::$app->urlManager->createUrl(['/inventory/inventory/unset-featured', 'id' => $model->id, 'return' => 'index']),
								['title' => Yii::t('app', 'Unset Featured'),]
							);
						}
                    },

					'hot' => function ($url, $model) {
                        if(!$model->hot)
						{
							$hotcount = Inventory::find()->where(['hot' => 1])->count();
							if($hotcount == 0)
							{
								return Html::a('<span class="glyphicon glyphicon-plus-sign"></span>',
									Yii::$app->urlManager->createUrl(['/inventory/inventory/set-hot', 'id' => $model->id, 'return' => 'index']),
									['title' => Yii::t('app', 'Set Hot'),]
								);
							}
							else
							{
								return '';
							}
						}
						else
						{
							return Html::a('<span class="glyphicon glyphicon-minus-sign"></span>',
								Yii::$app->urlManager->createUrl(['/inventory/inventory/unset-hot', 'id' => $model->id, 'return' => 'index']),
								['title' => Yii::t('app', 'Unset Hot'),]
							);
						}
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
            'before' => Yii::$app->params['user_role'] == 'admin'?'':'<form action=""  method="post" name="frm"><input type="hidden" name="_csrf" value="'.Yii::$app->request->csrfToken.'"><input type="hidden" name="bulk_download" value="true">'.Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success']).' <a href="javascript:void(0)" onClick="bulk_down()" class="btn btn-danger"><i class="glyphicon glyphicon-download"></i> '.Yii::t ( 'app', 'Download Selected Records For Bulk Update' ).'</a>',
            'after' => Yii::$app->params['user_role'] == 'admin'?'':'</form>'.Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
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