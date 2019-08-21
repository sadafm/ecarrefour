<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

use multebox\models\VendorType;
use multebox\models\User;
use multebox\models\search\UserType as UserTypeSearch;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\VendorSearch $searchModel
 */

$this->title = Yii::t('app', 'Vendors');
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
<div class="vendor-index">
   <!-- <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div> -->
   
    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'responsiveWrap' => false,
		'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'vendor_name',
            //'vendor_type_id',
			[ 
				'attribute' => 'vendor_type_id',
				'label' => Yii::t('app', 'Vendor Type'),
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'width' => '100px',
				'filter' => ArrayHelper::map ( VendorType::find ()->where("active=1")->orderBy ( 'sort_order' )->asArray ()->all (), 'id', 'label' ),
				'filterWidgetOptions' => [ 
				'options' => [ 
									'placeholder' => Yii::t('app', 'All...')  
								],
				'pluginOptions' => [ 
								'	allowClear' => true 
								] 
							],
				'value' => function ($model, $key, $index, $widget)
							{
								if (isset ( $model->vendorType ) && ! empty ( $model->vendorType->label ))
									return $model->vendorType->label;
							} 
			],
            //'added_by_id',
			[ 
				'attribute' => 'added_by_id',
				'label' => Yii::t('app', 'Added By'),
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'width' => '100px',
				'filter' => ArrayHelper::map ( User::find ()->where("user_type_id='".UserTypeSearch::getCompanyUserType('Employee')->id."'")->orderBy ( 'id' )->asArray ()->all (), 'id', 'username' ),
				'filterWidgetOptions' => [ 
				'options' => [ 
									'placeholder' => Yii::t('app', 'All...')  
								],
				'pluginOptions' => [ 
								'	allowClear' => true 
								] 
							],
				'value' => function ($model, $key, $index, $widget)
							{
								if ($model->added_by_id == 0)
									return Yii::t('app', 'System');
								else
								{
									$usermodel = User::findOne($model->added_by_id);
									return $usermodel->first_name." ".$usermodel->last_name." (".$usermodel->username.")";
								}
							} 
			],
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
//            'added_at', 
//            'updated_at', 

            [
                'class' => 'yii\grid\ActionColumn',
				'template'=>'{update}  {delete} {action}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['/vendor/vendor/view', 'id' => $model->id, 'edit' => 't']),
                            ['title' => Yii::t('app', 'Edit'),]
                        );
                    },

					'action' => function ($url, $model) {
						if($model->active == 0)
						{
							return Html::a('<span class="glyphicon glyphicon-ok"></span>',
							 Yii::$app->urlManager->createUrl(['/vendor/vendor/activate', 'id' => $model->id, 'activate' => 't']),
								['title' => Yii::t('app', 'Activate'), 'data-confirm' => Yii::t('app', 'Are you sure you want to activate this vendor?'),]
							);
						}
						else
						{
							return Html::a('<span class="glyphicon glyphicon-remove"></span>',
							 Yii::$app->urlManager->createUrl(['/vendor/vendor/deactivate', 'id' => $model->id, 'deactivate' => 't']),
								['title' => Yii::t('app', 'Deactivate'), 'data-confirm' => Yii::t('app', 'Are you sure you want to deactivate this vendor?'),]
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
