<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\PaymentMethods;
use multebox\models\OrderStatus;
use multebox\models\Vendor;
use multebox\models\Inventory;
use multebox\models\search\MulteModel;
use multebox\models\File;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\SubOrder $searchModel
 */

$this->title = Yii::t('app', 'Vendor Orders');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orders'), 'url' => ['/order/order/index']];
$this->params['breadcrumbs'][] = $this->title;

$paymentitems = PaymentMethods::find ()->orderBy ( 'id' )->asArray ()->all ();

if($paymentitems)
{
	for($i=0; $i < count($paymentitems); $i++)
	{
		$paymentitems[$i]['label'] = Yii::t('app', $paymentitems[$i]['label']);
	}
}

$orderitems = OrderStatus::find ()->orderBy ( 'id' )->asArray ()->all ();

if($orderitems)
{
	for($i=0; $i < count($orderitems); $i++)
	{
		$orderitems[$i]['label'] = Yii::t('app', $orderitems[$i]['label']);
	}
}

?>
<div class="sub-order-index">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Sub Order',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'responsiveWrap' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
				'attribute' => 'inventory_id',
				'label' => Yii::t('app', 'Item'),
				'format' => 'raw',
				'filter' => false,
				'value' => function ($model, $key, $index, $widget)
							{
								$inventory_item = Inventory::findOne($model->inventory_id);
								$prod_title = $inventory_item->product_name;
								$fileDetails = File::find()->where("entity_type='product' and entity_id=".$inventory_item->product_id)->one();
								
								$abc = '<a target="_blank" href="'.str_replace(Yii::$app->params['backend_url'], Yii::$app->params['frontend_url'], Url::to(['/product/default/detail', 'inventory_id' => $inventory_item->id], true)).'"><img src="'.Yii::$app->params['web_url'].'/'.$fileDetails->new_file_name.'" alt="'.$prod_title.'" title="'.$prod_title.'"  class="img-responsive img-sm"/></a>';

								return $abc;
								
							} ,
			],
            //'id',
			[
				'attribute' => 'id',
				'label' => Yii::t('app', 'Sub Order#'),
				'width' => '100px',
			],
            'order_id',
            //'vendor_id',
			[ 
				'attribute' => 'vendor_id',
				'label' => Yii::t('app', 'Vendor Name'),
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				'filter' => Yii::$app->user->identity->entity_type=='vendor'?'false':ArrayHelper::map ( Vendor::find ()->orderBy ( 'vendor_name' )->asArray ()->all (), 'id', 'vendor_name' ),
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

            //'total_items',
			[
				'attribute' => 'total_items',
				'width' => '80px',
			],
//            'discount_coupon_id', 
//            'global_discount_id', 
//            'tax_id', 
//            'inventory_snapshot:ntext', 
//            'discount_coupon_snapshot:ntext', 
//            'global_discount_snapshot:ntext', 
//            'tax_snapshot:ntext', 
            //'total_shipping',
			[
				'attribute' => 'total_shipping',
				'value' => function ($model, $key, $index, $widget)
							{
								return MulteModel::formatAmount($model->total_shipping);
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
            //'total_tax', 
			[
				'attribute' => 'total_tax',
				'value' => function ($model, $key, $index, $widget)
							{
								return MulteModel::formatAmount($model->total_tax);
							} 
			],
			//'total_cost',
			[
				'attribute' => 'total_cost',
				'value' => function ($model, $key, $index, $widget)
							{
								return MulteModel::formatAmount($model->total_cost);
							} 
			],
			[
				'attribute' => 'total_converted_cost',
				'value' => function ($model, $key, $index, $widget)
							{
								return MulteModel::formatAmount($model->total_converted_cost, $model->order_currency_symbol);
							} 
			],
//            'delivery_method', 
           // 'payment_method', 
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
           // 'sub_order_status', 
			[ 
				'attribute' => 'sub_order_status',
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
								$orderstatusmodel = OrderStatus::find()->where("status='".$model->sub_order_status."'")->one();
								return Yii::t('app', $orderstatusmodel->label);
							} 
			],
//            'added_at', 
//            'updated_at', 

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return '';
                    },
					'delete' => function ($url, $model) {
                        return '';
                    },
					'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            Yii::$app->urlManager->createUrl(['/order/sub-order/sub-order-view', 'id' => $model->id]),
                            ['title' => Yii::t('app', 'View'),]
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
            //'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),
            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset List'), ['view-order', 'id' => $_REQUEST['id']], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>

</div>
