<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\User;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\ProductCategory $searchModel
 */

$this->title = Yii::t('app', 'Product Categories');
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
<div class="product-category-index">
   
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Product Category',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'responsiveWrap' => false,
		'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
            'name',
            //'active',
            //'description:ntext',
			/*[
				'attribute' => 'description',
			//	'label' => 'Active',
				'format' => 'raw',
			],*/
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
				'attribute' => 'added_by_id',
				'label' => Yii::t('app', 'Added By'),
				'format' => 'raw',
				'value' => function ($model, $key, $index, $widget)
							{
								$added_by = User::findOne($model->added_by_id);
								if($added_by->entity_type == 'employee')
									return "<span class=\"label label-success\">".Yii::t('app', 'Employee')."</span>";
								else
									return "<span class=\"label label-warning\">".Yii::t('app', 'Vendor')."</span>";
							} 
			],
          //  'sort_order',
//            'added_at', 
//            'updated_at', 

            [
                'class' => 'yii\grid\ActionColumn',
				'template'=>'{update}  {action} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['/product/product-category/view', 'id' => $model->id, 'edit' => 't']),
                            ['title' => Yii::t('app', 'Edit'),]
                        );
                    },

					'action' => function ($url, $model) {
						if(Yii::$app->user->identity->entity_type=='vendor')
							return '';
						else
						if($model->active == 0)
						{
							return Html::a('<span class="glyphicon glyphicon-ok"></span>',
							 Yii::$app->urlManager->createUrl(['/product/product-category/activate', 'id' => $model->id, 'activate' => 't']),
								['title' => Yii::t('app', 'Activate'), 'data-confirm' => Yii::t('app', 'Are you sure you want to activate this category?'),]
							);
						}
						else
						{
							return Html::a('<span class="glyphicon glyphicon-remove"></span>',
							 Yii::$app->urlManager->createUrl(['/product/product-category/deactivate', 'id' => $model->id, 'deactivate' => 't']),
								['title' => Yii::t('app', 'Deactivate'), 'data-confirm' => Yii::t('app', 'Are you sure you want to deactivate this category?'),]
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
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success']),
            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>

</div>
