<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use multebox\models\Inventory;
use multebox\models\Address;
use multebox\models\DiscountCoupons;
use multebox\models\Contact;
use multebox\models\Vendor;
use multebox\models\City;
use multebox\models\State;
use multebox\models\Country;
use multebox\models\Tax;
use multebox\models\StateTax;
use multebox\models\PaymentMethods;
use multebox\models\search\MulteModel;

$vendor = Vendor::findOne($sub_order->vendor_id);
$vaddress = Address::find()->where("is_primary=1 and entity_type='vendor' and entity_id=".$vendor->id)->one();
$vcontact = Contact::find()->where("is_primary=1 and entity_type='vendor' and entity_id=".$vendor->id)->one();
$inventory = MulteModel::mapJsonToModel(Json::decode($sub_order->inventory_snapshot), new Inventory);
//$tax = MulteModel::mapJsonToModel(Json::decode($sub_order->tax_snapshot), new Tax);
if($sub_order->state_tax_snapshot)
{
	$statetaxmodel = MulteModel::mapJsonToModel(Json::decode($sub_order->state_tax_snapshot), new StateTax);

	if($statetaxmodel->tax_percentage > 0)
		$tax_percentage = $statetaxmodel->tax_percentage;
	else
		$tax_percentage = 0;
}
else
{
	$taxmodel = MulteModel::mapJsonToModel(Json::decode($sub_order->tax_snapshot), new Tax);

	if($taxmodel->tax_percentage > 0)
		$tax_percentage = $taxmodel->tax_percentage;
	else
		$tax_percentage = 0;
}
$discountcoupon = MulteModel::mapJsonToModel(Json::decode($sub_order->discount_coupon_snapshot), new DiscountCoupons);
$caddress = MulteModel::mapJsonToModel(Json::decode($order->address_snapshot), new Address);
$ccontact = MulteModel::mapJsonToModel(Json::decode($order->contact_snapshot), new Contact);
?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=Yii::t('app', 'Invoice')?>
        <small>#<?=$invoice->id?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> <?=Yii::t('app', 'Home')?></a></li>
        <li><a href="#"><?=Yii::t('app', 'Examples')?></a></li>
        <li class="active"><?=Yii::t('app', 'Invoice')?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <?=Yii::$app->params['COMPANY_NAME']?>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="row invoice-info">
	  <div class="col-sm-12">
        <div class=" invoice-col">
          <?=Yii::t('app', 'From')?>
          <address>
            <strong><?=$vendor->vendor_name?></strong><br>
            <?=$vaddress->address_1?><br>
            <?=$vaddress->address_2?><br>
			<?=City::findOne($vaddress->city_id)->city?>, <?=State::findOne($vaddress->state_id)->state?><br>
			<?=Country::findOne($vaddress->country_id)->country?> - <?=$vaddress->zipcode?><br>
            <?=Yii::t('app', 'Phone')?>: <?=$vcontact->mobile?><br>
            <?=Yii::t('app', 'Email')?>: <?=$vcontact->email?>
          </address>
        </div>
        <!-- /.col -->
        <div class=" invoice-col">
          <?=Yii::t('app', 'To')?>
          <address>
             <strong><?=$ccontact->first_name?> <?=$ccontact->last_name?></strong><br>
            <?=$caddress->address_1?><br>
            <?=$caddress->address_2?><br>
			<?=City::findOne($caddress->city_id)->city?>, <?=State::findOne($caddress->state_id)->state?><br>
			<?=Country::findOne($caddress->country_id)->country?> - <?=$caddress->zipcode?><br>
            <?=Yii::t('app', 'Phone')?>: <?=$ccontact->mobile?><br>
            <?=Yii::t('app', 'Email')?>: <?=$ccontact->email?>
          </address>
        </div>
        <!-- /.col -->
        <div class=" invoice-col">
          <b><?=Yii::t('app', 'Invoice')?> #<?=$invoice->id?></b><br>
		  <b><?=Yii::t('app', 'Invoice Date	')?>:</b> <?=$invoice->added_at?date('d-M-Y H:i', $invoice->added_at):date('d-M-Y H:i')?><br>
          <b><?=Yii::t('app', 'Order ID')?>:</b> <?=$sub_order->id?><br>
          <b><?=Yii::t('app', 'Payment Method')?>:</b> <?=Yii::t('app', PaymentMethods::getLabelByMethod($sub_order->payment_method))?><br>
          <b><?=Yii::t('app', 'Shipment Method')?>:</b> <?=Yii::t('app', 'Flat Rate Shipping');?>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
	  </div>

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped">
            <thead>
            <tr>
              <th><?=Yii::t('app', 'Quantity')?></th>
              <th><?=Yii::t('app', 'Product')?></th>
              <th><?=Yii::t('app', 'Description')?> #</th>
              <th class="text-right"><?=Yii::t('app', 'Unit Price')?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td><?=$sub_order->total_items?></td>
              <td><?=$inventory->product_name?></td>
              <td><?=$inventory->attribute_values?></td>
			  <?php
			  $before_tax = MulteModel::getPriceBeforeTax($sub_order->total_cost/$sub_order->total_items, $tax_percentage) + ($sub_order->total_site_discount + $sub_order->total_coupon_discount - $sub_order->total_shipping)/$sub_order->total_items;
			  ?>
              <td class="text-right"><?=MulteModel::formatAmount($before_tax*$sub_order->conversion_rate, $sub_order->order_currency_symbol)?> x <?=$sub_order->total_items?></td>
            </tr>
            </tbody>
          </table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-6">

		</div>
        <!-- /.col -->
        <div class="col-xs-6 pull-right">
          

          <div class="table-responsive">
            <table class="table text-right">
			  <tr>
                <th><?=Yii::t('app', 'Special Discount')?>:</th>
                <td>-<?=MulteModel::formatAmount($sub_order->total_site_discount*$sub_order->conversion_rate, $sub_order->order_currency_symbol)?></td>
              </tr>
			  <?php
			  if($sub_order->total_coupon_discount > 0)
			  {
			  ?>
			  <tr>
                <th><?=Yii::t('app', 'Coupon Discount')?> (<?=$discountcoupon->coupon_code?>):</th>
                <td>-<?=MulteModel::formatAmount($sub_order->total_coupon_discount*$sub_order->conversion_rate, $sub_order->order_currency_symbol)?></td>
              </tr>
			  <?php
			  }
			  ?>
              <tr>
                <th><?=Yii::t('app', 'Shipping')?>:</th>
                <td><?=MulteModel::formatAmount($sub_order->total_shipping*$sub_order->conversion_rate, $sub_order->order_currency_symbol)?></td>
              </tr>
			  <tr>
                <th style="width:50%"><?=Yii::t('app', 'Subtotal')?>:</th>
                <td><i><?=MulteModel::formatAmount(($sub_order->total_cost - $sub_order->total_tax)*$sub_order->conversion_rate, $sub_order->order_currency_symbol)?></i></td>
              </tr>
			  <tr>
                <th><?=Yii::t('app', 'Tax')?> (<?=$tax_percentage?>%):</th>
                <td><?=MulteModel::formatAmount($sub_order->total_tax*$sub_order->conversion_rate, $sub_order->order_currency_symbol)?></td>
              </tr>
			  <tr>
                <th><?=Yii::t('app', 'Final Total')?>:</th>
                <td><strong><?=MulteModel::formatAmount($sub_order->total_cost*$sub_order->conversion_rate, $sub_order->order_currency_symbol)?></strong></td>
              </tr>
            </table>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- this row will not appear when printing -->
      <div class="row no-print">
        <div class="col-xs-12">
         
			<?php
			echo Html::a('<i class="fa fa-download"></i> '.Yii::t('app','Download Invoice'), 
						Url::to(['/order/sub-order/get-invoice-pdf']), 
									[
										'class' => 'btn btn-primary btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $sub_order->id,
													'method' => 'post',
													],
									]);
								?>
          </button>
        </div>
      </div>
    </section>
    <!-- /.content -->
    <div class="clearfix"></div>
