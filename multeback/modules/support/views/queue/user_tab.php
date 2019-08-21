<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\User;
use multebox\models\search\MulteModel;
use yii\helpers\ArrayHelper;
?>
    <?php 
	
		$btn='<a href="javascript:void(0)" class="btn btn-success btn-sm" onClick="$(\'.exist_users\').modal(\'show\');"><i class="glyphicon glyphicon-user"></i> '.Yii::t('app', 'Add User to Queue').'</a>';
	
	Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProviderUser,
		'toolbar' => false,
        //'filterModel' => $searchModelUser,
		'responsiveWrap' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
  //          'id',
            //'task_id',
            //'task_name',,
								[ 
										'attribute' => 'user_id',
										'label' => Yii::t('app', 'Image'),
										'format' => 'raw',
										'width' => '50px',
										'value' => function ($model, $key, $index, $widget)
										{
												$users='<div class="project-people">';
														$path=Yii::$app->params['web_url'].'/users/'.$model->user_id.'.png';
														if(MulteModel::fileExists($path)){
															$image='<img class="img-sm img-circle" src="'.Yii::$app->params['web_url'].'/users/'.$model->user_id.'.png">';								
														 }else{ 
															$image='<img class="img-sm img-circle" src="'.Url::base().'/nophoto.jpg">';
														 }
														$users.=' <a href="javascript:void(0)" onClick="showPopup(\''.$model->user_id.'\')">'.$image.'</a>';	
												$users.='</div>';
												return $users;
										} 
								],
			
			
			 
			[ 
				'attribute' => 'user_id',
				'label' =>Yii::t('app', 'First Name') ,
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'width' => '25%',
				'filter' => ArrayHelper::map (User::find()->orderBy ( 'id' )->asArray ()->all (), 'id', 'first_name' ),
				'filterWidgetOptions' => [ 
						'options' => [ 
								'placeholder' => Yii::t('app', 'All...'), 
						],
						'pluginOptions' => [ 
								'allowClear' => true 
						] 
				],
				'value' => function ($model, $key, $index, $widget) {
					//var_dump($model->user);
				if(isset($model->user) && !empty($model->user->first_name)) 
					return $model->user->first_name;
				} 
			],
			[ 
				'attribute' => 'user_id',
				'label' => Yii::t('app', 'Last Name'),
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'width' => '25%',
				'filter' => ArrayHelper::map (User::find()->orderBy ( 'id' )->asArray ()->all (), 'id', 'first_name' ),
				'filterWidgetOptions' => [ 
						'options' => [ 
								'placeholder' => Yii::t('app', 'All...'),
						],
						'pluginOptions' => [ 
								'allowClear' => true 
						] 
				],
				'value' => function ($model, $key, $index, $widget) {
					//var_dump($model->user);
				if(isset($model->user) && !empty($model->user->first_name)) 
					return $model->user->last_name;
				} 
			],
			[ 
				'attribute' => 'user_id',
				'label' => Yii::t('app', 'Username'),
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'width' => '25%',
				'filter' => ArrayHelper::map (User::find()->orderBy ( 'id' )->asArray ()->all (), 'id', 'first_name' ),
				'filterWidgetOptions' => [ 
						'options' => [ 
								'placeholder' => Yii::t('app', 'All...'), 
						],
						'pluginOptions' => [ 
								'allowClear' => true 
						] 
				],
				'value' => function ($model, $key, $index, $widget) {
					//var_dump($model->user);
				if(isset($model->user) && !empty($model->user->first_name)) 
					
					//return '<a href="javascript:void(0)" onClick="showPopup(\''.$model->user_id.'\')">'.$model->user->username.'</a>';
					return $model->user->username;
				} 
			],
			
			[
				'attribute' => 'user_id',
				'label' => Yii::t('app', 'User Type'),
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'width' => '25%',
				'value' => function ($model, $key, $index, $widget) {
					//var_dump($model->user);
				if(isset($model->user) && !empty($model->user->user_type_id)) 
					return $model->user->userType->label;
				}
				
				
			],
			
			[ 
				'attribute' => 'user_id',
				'label' => Yii::t('app', 'Email'),
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'width' => '25%',
				'value' => function ($model, $key, $index, $widget) {
					//var_dump($model->user);
				if(isset($model->user) && !empty($model->user->email)) 
					return $model->user->email;
				} 
			],

			[ 
										'class' => '\kartik\grid\ActionColumn',
										'template' => '{update} {view} {delete}',
										'buttons' => [ 
												'update' => function ($url, $model)
													{
													return '';
												},
												'view' => function ($url, $model)
													{
													return '';
												},
												'delete' => function ($url, $model)
												{
													return Html::a('<span class="glyphicon glyphicon-trash"></span>', Yii::$app->urlManager->createUrl(['/support/queue/update','id' => $_REQUEST['id'], 'udel' => $model->id]), [
															'title' => Yii::t('app', 'Delete'),
															'data' => [                          
																		'method' => 'post',                          
																		'confirm' => Yii::t('app', 'Are you sure?')],
																	  ]);

												}
										]
										 
								] 
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        //'floatHeader'=>true,


        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Yii::t('app', 'Users in this Queue').'</h3>',
            'type'=>'info',
            'before'=>$btn,                                                                                                                                                 /*         'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),*/
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>