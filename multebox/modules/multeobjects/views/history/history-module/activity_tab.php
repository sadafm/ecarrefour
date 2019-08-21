<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\User;
use yii\helpers\ArrayHelper;
?>
    <?php 
	Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProviderActivity,
        //'filterModel' => $searchModelNotes,
		'responsiveWrap' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

  //          'id',
            //'task_id',
            //'task_name',
			[ 
					'attribute' => 'notes',
					'format' => 'raw',
					'width' => '60%' 
			],
			[ 
					'attribute' => 'added_at',
					'width' => '20%',
					'value' => function ($model, $key, $index, $widget) {
					if(isset($model->added_at)) 
						return date('F d,Y',$model->added_at);
					}  
					
					
			],
			
			 
			[ 
				'attribute' => 'user_id',
				'label' => Yii::t('app', 'User'),
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'width' => '20%',
				'filter' => ArrayHelper::map (User::find()->orderBy ( 'id' )->asArray ()->all (), 'id', 'first_name' ),
				'filterWidgetOptions' => [ 
						'options' => [ 
								'placeholder' => Yii::t('app', 'All...') 
						],
						'pluginOptions' => [ 
								'allowClear' => true 
						] 
				],
				'value' => function ($model, $key, $index, $widget) {
					//var_dump($model->user);
				if(isset($model->user) && !empty($model->user->first_name)) 
					return $model->user->username;
				} 
			],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        //'floatHeader'=>true,




        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Activities </h3>',
            'type'=>'info',
            /*'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),                                                                                                                                                          'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),*/
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>