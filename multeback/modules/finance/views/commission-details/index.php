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
 * @var multebox\models\search\CommissionDetails $searchModel
 */

$this->title = Yii::t('app', 'Commission Details');
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
<div class="commission-details-index">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Commission Details',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'responsiveWrap' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

 //           'id',
            //'sub_order_id',
			[
				'attribute' => 'sub_order_id',
				'label' => Yii::t('app', 'Sub Order ID'),
			],
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
            //'inventory_id',
            //'commission',
			[ 
				'attribute' => 'commission',
				'value' => function ($model, $key, $index, $widget)
							{
								return MulteModel::formatAmount($model->commission);
							} 
			],
            //'invoiced_ind',
			[ 
				'attribute' => 'invoiced_ind',
				'label' => Yii::t('app', 'Invoice Generated'),
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
						return statusLabel ( $model->invoiced_ind );
				} 
			],
//            'vendor_invoice_id', 
//            'added_at', 
//            'updated_at', 

            [
                'class' => 'yii\grid\ActionColumn',
				'visible' => false,
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['/finance/commission-details/view', 'id' => $model->id, 'edit' => 't']),
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
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i> '.Yii::t('app','Process Commisions'), ['process'], [
													'class' => 'btn btn-success', 
													'data' => [
																'confirm' => Yii::t('app', 'Are you sure?'),
																'method' => 'post',
																],
															]).' '.
															Html::a('<i class="glyphicon glyphicon-usd"></i> '.Yii::t('app', 'Generate Invoices'), ['generate-invoices'], [
													'class' => 'btn btn-warning', 
													'data' => [
																'confirm' => Yii::t('app', 'Are you sure?'),
																'method' => 'post',
																],
															]),
			'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>

</div>
