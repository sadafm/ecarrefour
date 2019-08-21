<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\Country;
use multebox\models\State;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\StateTax $searchModel
 */

//$this->title = Yii::t('app', 'State Taxes');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="state-tax-index">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'State Tax',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
		'toolbar' => false,
        //'filterModel' => $searchModel,
		'responsiveWrap' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
           // 'tax_id',
			//'country_id',
			[ 
				'attribute' => 'country_id',
				'label' => Yii::t('app', 'Country'),
				'value' => function ($model, $key, $index, $widget)
				{
						return Country::findOne($model->country_id)->country;
				} 
			],
            //'state_id',
			[ 
				'attribute' => Yii::t('app', 'state_id'),
				'label' => 'State',
				'value' => function ($model, $key, $index, $widget)
				{
						return State::findOne($model->state_id)->state;
				} 
			],
            'tax_percentage',
          //  'added_at',
//            'updated_at', 

            [
                'class' => 'yii\grid\ActionColumn',
				'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['/finance/state-tax/update', 'id' => $model->id, 'tax_id' => $_REQUEST['id']]),
                            ['title' => Yii::t('app', 'Edit'),]
                        );
                    },
					'delete' => function ($url, $model) {
						return Html::a('<span class="glyphicon glyphicon-trash"></span>',
						 Yii::$app->urlManager->createUrl(['/finance/state-tax/delete', 'id' => $model->id, 'tax_id' => $_REQUEST['id']]),
							['title' => Yii::t('app', 'Delete'), 'data-confirm' => Yii::t('app', 'Are you sure you want to delete this record?'), 'data-method' => 'post']
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
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode(Yii::t('app', 'State Taxes')).' </h3>',
            'type' => 'info',
            //'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),
            //'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>

</div>
