<?php
use multebox\models\Order;
use multebox\models\OrderStatus;
use multebox\models\City;
use multebox\models\State;
use multebox\models\Country;
use multebox\models\SubOrder;
use multebox\models\PaymentMethods;
use multebox\models\search\MulteModel;
use multebox\models\Inventory;
use multebox\models\File;
use multebox\models\Vendor;
use multebox\models\Cart;
use yii\helpers\Url;
use yii\helpers\Json;

?>
  <div id="container">
    
      <!-- Breadcrumb Start-->
      <ul class="breadcrumb">
        <li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
        <li><a href="<?=Url::to(['/customer/default/account'])?>"><?=Yii::t('app', 'Account')?></a></li>
        <li><?=Yii::t('app', 'Order History')?></li>
      </ul>
      <!-- Breadcrumb End-->
      <div class="row">
        <!--Middle Part Start-->
        <div id="content" class="col-sm-12">
        <h1 class="title"><?=Yii::t('app', 'Order History')?></h1>
          <div class="table-responsive">
			<table class="table table-bordered table-hover">
			  <thead>
				<tr>
				  <td class="text-center"><?=Yii::t('app', 'Order ID')?></td>
				  <td class="text-left"><?=Yii::t('app', 'Order Item(s)')?></td>
				  <td class="text-center"><?=Yii::t('app', 'Order Date')?></td>
				  <td class="text-center"><?=Yii::t('app', 'Status')?></td>
				  <td class="text-right"><?=Yii::t('app', 'Total')?></td>
				  <td></td>
				</tr>
			  </thead>
			  <tbody>
			  <?php
			  foreach($order_list as $order)
			  {
				  $sub_order_list = SubOrder::find()->where("order_id='".$order->id."'")->all();
			  ?>
				<tr>
				  <td class="text-center" >#<?=$order->id?></td>
				  <td>
				  <?php
				  foreach ($sub_order_list as $sub_order)
				  {
				  ?>
				    <?=$sub_order->total_items?> x 
				    <a href="<?=Url::to(['/product/default/detail', 'inventory_id' => $sub_order->inventory_id])?>"><?=Inventory::findOne($sub_order->inventory_id)->product_name?></a>
				    <br>
				  <?php
				  }
				  ?>
				  </td>
				  <td class="text-center"><?=date('M d, Y, H:i', $order->added_at)?></td>
				  <td class="text-center"><?=Yii::t('app', $order->status->label)?></td>
				  <td class="text-right"><?=MulteModel::formatAmount($order->total_cost*$order->conversion_rate, $order->order_currency_symbol)?></td>
				  <td class="text-center">
					<a href="<?=Url::to(['/order/default/information', 'order_id' => $order->id])?>" data-toggle="tooltip" title="<?=Yii::t('app', 'View Order')?>"><i class="fa fa-hand-o-right"></i></a>
					<?php
					if($order->order_status == OrderStatus::_DELIVERED || $order->order_status == OrderStatus::_COMPLETED || $order->order_status == OrderStatus::_RETURNED)
				    {
					?>
					<br><a href="<?=Url::to(['/review/default/product', 'order_id' => $order->id])?>" data-toggle="tooltip" title="<?=Yii::t('app', 'Review Product')?>"><i class="fa fa-star-o"></i></a>
					<br><a href="<?=Url::to(['/review/default/vendor', 'order_id' => $order->id])?>" data-toggle="tooltip" title="<?=Yii::t('app', 'Review Seller')?>"><i class="fa fa-paper-plane-o"></i></a>
					<?php
					}
					?>
				  </td>
				</tr>
			  <?php
			  }
			  ?>
			  </tbody>
			</table>
          </div>
        </div>
        <!--Middle Part End -->
      </div>
	
   </div>
