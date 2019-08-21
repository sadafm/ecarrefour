<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\User;
use yii\helpers\Json;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\ProductAttributeValues $searchModel
 */

$this->title = Yii::t('app', 'Product Attribute Values');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-attribute-values-index">
   <!-- <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div> -->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Product Attribute Values',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'responsiveWrap' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            //'values:ntext',
			[ 
				'attribute' => 'values',
				'format' => 'raw',
				'value' => function ($model, $key, $index, $widget)
				{
					$value = Json::decode($model->values);
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
			],
           // 'added_at',
           // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model) {
						if(Yii::$app->user->identity->entity_type=='vendor')
							return '';
						else
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['/product/product-attribute-values/update', 'id' => $model->id, 'edit' => 't']),
                            ['title' => Yii::t('app', 'Edit'),]
                        );
                    },

					'delete' => function ($url, $model) {
						if(Yii::$app->user->identity->entity_type=='vendor')
							return '';
						else
						return Html::a('<span class="glyphicon glyphicon-trash"></span>',
							 Yii::$app->urlManager->createUrl(['/product/product-attribute-values/delete', 'id' => $model->id]),
								['title' => Yii::t('app', 'Delete'), 'data-confirm' => Yii::t('app', 'Are you sure you want to delete this attribute value list?'),]
							);
						},

					'view' => function ($url, $model) {
                        return '';
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
