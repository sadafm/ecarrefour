<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use multebox\models\ProductCategory;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;


/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\Commission $searchModel
 */

$this->title = Yii::t('app', 'Commissions');
$this->params['breadcrumbs'][] = $this->title;

function statusLabel($commission_type)
{
	if ($commission_type =='F')
	{
		$label = "<span class=\"label label-info\">".Yii::t('app', 'Fixed')."</span>";
	}
	else
	{
		$label = "<span class=\"label label-success\">".Yii::t('app', 'Percent')."</span>";
	}
	return $label;
}
$commission_type = array('F'=>Yii::t('app', 'Fixed'),'P'=>Yii::t('app', 'Percent'));

?>
<div class="commission-index">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Commission',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'responsiveWrap' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
           // 'category_id',
           // 'sub_category_id',
           // 'commission_type',
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
						$row = ProductCategory::findOne($model->category_id);

						if($row->name != '')
							return $row->name;
						else
							return "<span class=\"label label-default\">".Yii::t('app', 'Match All')."</span>";
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
					if (isset($model->sub_category_id)) 
					{
						$row = ProductSubCategory::findOne($model->sub_category_id);

						if($row->name != '')
							return $row->name;
						else
							return "<span class=\"label label-default\">".Yii::t('app', 'Match All')."</span>";
					}
                }
            ],
			[
                'attribute' => 'sub_subcategory_id',
                'filterType' => GridView::FILTER_SELECT2,
				'label' => Yii::t('app', 'Product Sub SubCategory'),
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
						$row = ProductSubSubCategory::findOne($model->sub_subcategory_id);
						if($row->name != '')
							return $row->name;
						else
							return "<span class=\"label label-default\">".Yii::t('app', 'Match All')."</span>";
					}
                }
            ],
		   [ 
				'attribute' => 'commission_type',
			//	'label' => 'Active',
				'format' => 'raw',
				'filterType' => GridView::FILTER_SELECT2,
				'filter' => $commission_type,
				'filterWidgetOptions' => [ 
						'options' => [ 
								'placeholder' => Yii::t('app', 'Commission Type...') 
						],
						'pluginOptions' => [ 
								'allowClear' => true 
						] 
				],
				'value' => function ($model, $key, $index, $widget)
				{
						return statusLabel ( $model->commission_type );
				} 
			],
            'commission',
//            'added_by_id', 
//            'added_at', 
//            'updated_at', 

            [
                'class' => 'yii\grid\ActionColumn',
				'template'=>'{update}  {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['/finance/commission/update', 'id' => $model->id, 'edit' => 't']),
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
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success']),
            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>

</div>
