<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel multebox\models\search\Glocalization */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Glocalizations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="glocalization-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?php
	Pjax::begin();
	 echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'responsiveWrap' => false,
'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
	
        //    'id',
			[ 
				'attribute' => 'language',
				'format' => 'raw',
				'value' => function ($model, $key, $index, $widget)
				{
						return '<a href="'. Url::to(['/multeobjects/glocalization/update', 'id' => $model->id]) .'">'.$model->language.'</a>';
				} 
		],
         //   'language',
            //'locale',
			[ 
				'attribute' => 'locale',
				'format' => 'raw'
		],

           // ['class' => '\kartik\grid\ActionColumn'],
		   [
                'class' => '\kartik\grid\ActionColumn',
                'buttons' => [
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['/multeobjects/glocalization/update','id' => $model->id,'edit'=>'t']), [
                                                    'title' => Yii::t('app', 'Edit'),
                                                  ]);},
				'view' => function ($url, $model) {

						return '';
				}
                ],
            ],
        ],
	'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>false,




        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success btn-sm']),                                                                                                                                                          'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info btn-sm']),
            'showFooter'=>false
        ],
    ]); Pjax::end();  ?>

</div>
