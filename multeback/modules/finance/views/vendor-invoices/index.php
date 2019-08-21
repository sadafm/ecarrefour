<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\Vendor;
use multebox\models\search\MulteModel;
use yii\helpers\ArrayHelper;


/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\VendorInvoices $searchModel
 */

$this->title = Yii::t('app', 'Vendor Invoices');
$this->params['breadcrumbs'][] = $this->title;

function statusLabel($status)
{
	if ($status =='1')
	{
		$label = "<span class=\"label label-primary\">".Yii::t('app', 'Yes')."</span>";
	}
	else
	{
		$label = "<span class=\"label label-danger\">".Yii::t('app', 'No')."</span>";
	}
	return $label;
}
$status = array('0'=>Yii::t('app', 'No'),'1'=>Yii::t('app', 'Yes'));

?>
<div class="vendor-invoices-index">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Vendor Invoices',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'responsiveWrap' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            //'vendor_id',
			[ 
				'attribute' => 'vendor_id',
				'label' => Yii::t('app', 'Vendor Name'),
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'filter' => ArrayHelper::map (Vendor::find ()->orderBy ( 'vendor_name' )->asArray ()->all (), 'id', 'vendor_name'),
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
								$vendormodel = Vendor::findOne($model->vendor_id);
								return $vendormodel->vendor_name;
							} 
			],
            //'total_commission',
			[ 
				'attribute' => 'total_order_amount',
				'label' => Yii::t('app', 'Total Invoiced Order Amount'),
				'value' => function ($model, $key, $index, $widget)
							{
								return MulteModel::formatAmount($model->total_order_amount);
							} 
			],
			[ 
				'attribute' => 'total_commission',
				'value' => function ($model, $key, $index, $widget)
							{
								return MulteModel::formatAmount($model->total_commission);
							} 
			],
            //'paid_ind',
			[ 
				'attribute' => 'paid_ind',
				'label' => Yii::t('app', 'Invoice Settled'),
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
						return statusLabel ( $model->paid_ind );
				} 
			],
            //'added_at',
//            'updated_at', 

            [
                'class' => 'yii\grid\ActionColumn',
				'template' => '{view} {action}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            Yii::$app->urlManager->createUrl(['/finance/vendor-invoices/get-invoice', 'id' => $model->id]),
                            ['title' => Yii::t('app', 'Edit'),]
                        );
                    },

					'action' => function ($url, $model) {
						if($model->paid_ind == 0)
						{
							return Html::a('<span class="glyphicon glyphicon-ok"></span>',
							 Yii::$app->urlManager->createUrl(['/finance/vendor-invoices/mark-paid', 'id' => $model->id]),
								['title' => Yii::t('app', 'Mark Paid'), 'data-confirm' => 'Are you sure you want to mark it as paid?',]
							);
						}
						else
						{
							return Html::a('<span class="glyphicon glyphicon-remove"></span>',
							 Yii::$app->urlManager->createUrl(['/finance/vendor-invoices/mark-unpaid', 'id' => $model->id]),
								['title' => Yii::t('app', 'Mark Unpaid'), 'data-confirm' => 'Are you sure you want to mark it as unpaid?',]
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
            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>

</div>
