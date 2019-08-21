<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\Customer;
use multebox\models\PaymentMethods;
use multebox\models\OrderStatus;
use multebox\models\search\MulteModel;
use yii\helpers\ArrayHelper;


/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\Order $searchModel
 */

$this->title = Yii::t('app', 'Orders');
$this->params['breadcrumbs'][] = $this->title;

$paymentitems = PaymentMethods::find ()->orderBy ( 'id' )->asArray ()->all ();
if($paymentitems)
	$pcnt = count($paymentitems);
else
	$pcnt = 0;
for($i=0; $i < $pcnt; $i++)
{
	$paymentitems[$i]['label'] = Yii::t('app', $paymentitems[$i]['label']);
}

$orderitems = OrderStatus::find ()->orderBy ( 'id' )->asArray ()->all ();
if($orderitems)
	$ocnt = count($orderitems);
else
	$ocnt = 0;
for($i=0; $i < $ocnt; $i++)
{
	$orderitems[$i]['label'] = Yii::t('app', $orderitems[$i]['label']);
}
?>
<div class="order-index">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Order',
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
			[
				'attribute' => 'id',
				'label' => Yii::t('app', 'Order ID'),
				//'width' => '40px',
			],
            //'customer_id',
			[ 
				'attribute' => 'customer_id',
				'label' => Yii::t('app', 'Customer'),
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'filter' => ArrayHelper::map ( Customer::find ()->orderBy ( 'id' )->asArray ()->all (), 'id', 'customer_name' ),
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
								$customermodel = Customer::findOne($model->customer_id);
								return $customermodel->customer_name;
							} 
			],
//            'cart_snapshot:ntext',
//            'discount_coupon_snapshot:ntext',
//            'global_discount_snapshot:ntext',
			[
				'attribute' => 'total_cost',
				'value' => function ($model, $key, $index, $widget)
							{
								return MulteModel::formatAmount($model->total_cost);
							} 
			],
            //'total_site_discount', 
			[
				'attribute' => 'total_site_discount',
				'value' => function ($model, $key, $index, $widget)
							{
								return MulteModel::formatAmount($model->total_site_discount);
							} 
			],
            //'total_coupon_discount', 
			[
				'attribute' => 'total_coupon_discount',
				'value' => function ($model, $key, $index, $widget)
							{
								return MulteModel::formatAmount($model->total_coupon_discount);
							} 
			],
//            'discount_coupon_type', 
//            'address_snapshot:ntext', 
//            'contact_snapshot:ntext', 
//            'delivery_method', 
            //'payment_method', 
			[ 
				'attribute' => 'payment_method',
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'filter' => ArrayHelper::map ( $paymentitems, 'method', 'label' ),
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
								$paymentmethodmodel = PaymentMethods::find()->where("method='".$model->payment_method."'")->one();
								return Yii::t('app', $paymentmethodmodel->label);
							} 
			],
            //'order_status',
			[ 
				'attribute' => 'order_status',
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'filter' => ArrayHelper::map ( $orderitems, 'status', 'label' ),
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
								$orderstatusmodel = OrderStatus::find()->where("status='".$model->order_status."'")->one();
								return Yii::t('app', $orderstatusmodel->label);
							} 
			],
//            'added_at', 
//            'updated_at', 

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            Yii::$app->urlManager->createUrl(['/order/sub-order/view-order', 'id' => $model->id]),
                            ['title' => Yii::t('app', 'View'),]
                        );
                    },
					'delete' => function ($url, $model) {
						return '';
					},
					'update' => function ($url, $model) {
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
            //'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success']),
            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>

</div>
