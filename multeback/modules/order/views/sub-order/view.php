<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var multebox\models\SubOrder $model
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sub Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-order-view">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>


    <?= DetailView::widget([
        'model' => $model,
        'condensed' => false,
        'hover' => true,
        'mode' => Yii::$app->request->get('edit') == 't' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
        'panel' => [
            'heading' => $this->title,
            'type' => DetailView::TYPE_INFO,
        ],
        'attributes' => [
            'id',
            'order_id',
            'vendor_id',
            'inventory_id',
            'total_items',
            'discount_coupon_id',
            'global_discount_id',
            'tax_id',
            'inventory_snapshot:ntext',
            'discount_coupon_snapshot:ntext',
            'global_discount_snapshot:ntext',
            'tax_snapshot:ntext',
            'total_cost',
            'total_shipping',
            'total_site_discount',
            'total_coupon_discount',
            'discount_coupon_type',
            'total_tax',
            'delivery_method',
            'payment_method',
            'sub_order_status',
            'added_at',
            'updated_at',
        ],
        'deleteOptions' => [
            'url' => ['delete', 'id' => $model->id],
        ],
        'enableEditMode' => true,
    ]) ?>

</div>
