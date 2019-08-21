<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\ProductAttributeValues;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\ProductAttributes $searchModel
 */

//$this->title = Yii::t('app', 'Product Attributes');
//$this->params['breadcrumbs'][] = $this->title;

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

function styleLabel($fixed)
{
	if ($fixed =='1')
	{
		$label = "<span class=\"label label-success\">".Yii::t('app', 'Fixed')."</span>";
	}
	else
	{
		$label = "<span class=\"label label-warning\">".Yii::t('app', 'Configurable')."</span>";
	}
	return $label;
}

$status = array('0'=>Yii::t('app', 'Inactive'),'1'=>Yii::t('app', 'Active'));

?>
<div class="product-attributes-index">
   
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Product Category',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
		//'pjax' => true,
        //'filterModel' => $searchModel,
		'responsiveWrap' => false,
		'toolbar' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
		   //'fixed',
		   [ 
				'attribute' => 'fixed',
				'format' => 'raw',
				'label' => Yii::t('app', 'Attribute Style'),

				'value' => function ($model, $key, $index, $widget)
				{
					return styleLabel ( $model->fixed );
				} 
			],
		   //'fixed_id',
		   [ 
				'attribute' => 'fixed_id',
				'format' => 'raw',
				'label' => Yii::t('app', 'Attribute Values List'),

				'value' => function ($model, $key, $index, $widget)
				{
					if($model->fixed == 1)
					{
						return "<a href='".Url::to(['/product/product-attribute-values/update', 'id' => $model->fixed_id])."'>".ProductAttributeValues::findOne($model->fixed_id)->name."</a>";
					}
					else
					{
						return "<span class=\"label label-info\">".Yii::t('app', 'NA')."</span>";
					}
				} 
			],
            //'name',
			 [ 
				'attribute' => 'name',
				'format' => 'raw',
				'label' => Yii::t('app', 'Attribute Name'),

				'value' => function ($model, $key, $index, $widget)
				{
					if($model->fixed == 0)
					{
						return $model->name;
					}
					else
					{
						return "<span class=\"label label-info\">".Yii::t('app', 'NA')."</span>";
					}
				} 
			],
            //'active',
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
          //  'sort_order',
//            'added_at', 
//            'updated_at', 

            [
                'class' => 'yii\grid\ActionColumn',
				'template'=>'{update}  {delete} {action}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['/product/product-attributes/update', 'id' => $model->id, 'parent_id' => $_REQUEST['id'], 'edit' => 't']),
                            ['title' => Yii::t('app', 'Edit'),]
                        );
                    },

					'delete' => function ($url, $model) {
						return Html::a('<span class="glyphicon glyphicon-trash"></span>',
						 Yii::$app->urlManager->createUrl(['/product/product-attributes/delete', 'id' => $model->id, 'sub_sub_category_id' => $_REQUEST['id']]),
							['title' => Yii::t('app', 'Delete'), 'data-confirm' => Yii::t('app', 'Are you sure you want to delete this record?'),]
						);
					},

					'action' => function ($url, $model) {
						if($model->active == 0)
						{
							return Html::a('<span class="glyphicon glyphicon-ok"></span>',
							 Yii::$app->urlManager->createUrl(['/product/product-attributes/activate', 'id' => $model->id, 'sub_sub_category_id' => $_REQUEST['id'], 'activate' => 't']),
								['title' => Yii::t('app', 'Activate'), 'data-confirm' => Yii::t('app', 'Are you sure you want to activate this attribute?'),]
							);
						}
						else
						{
							return Html::a('<span class="glyphicon glyphicon-remove"></span>',
							 Yii::$app->urlManager->createUrl(['/product/product-attributes/deactivate', 'id' => $model->id, 'sub_sub_category_id' => $_REQUEST['id'], 'deactivate' => 't']),
								['title' => Yii::t('app', 'Deactivate'), 'data-confirm' => Yii::t('app', 'Are you sure you want to deactivate this attribute?'),]
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
            //'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success']),
            //'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>

</div>
