<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use multebox\models\Address;
use multebox\models\CommissionDetails;
use multebox\models\VendorInvoices;
use multebox\models\Contact;
use multebox\models\Inventory;
use multebox\models\Vendor;
use multebox\models\SubOrder;
use multebox\models\City;
use multebox\models\State;
use multebox\models\Country;
use multebox\models\search\MulteModel;

$invoice = VendorInvoices::findOne($invoice_id);
$commissiondetails = CommissionDetails::find()->where("vendor_invoice_id=".$invoice->id)->all();
$vendor = Vendor::findOne($invoice->vendor_id);
$vaddress = Address::find()->where("entity_type='vendor' and entity_id=".$invoice->vendor_id." and is_primary=1")->one();
$vcontact = Contact::find()->where("entity_type='vendor' and entity_id=".$invoice->vendor_id." and is_primary=1")->one();

?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=Yii::t('app', 'Invoice')?>
        <small>#<?=$invoice->id?></small>
      </h1>

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
        <div class="invoice-col">
          <?=Yii::t('app', 'From')?>
          <address>
            <strong><?=Yii::$app->params['COMPANY_NAME']?>
          </address>
        </div>
        <!-- /.col -->
        <div class="invoice-col">
          <?=Yii::t('app', 'To')?>
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
        <div class="invoice-col text-left">
          <b><?=Yii::t('app', 'Invoice')?> #<?=$invoice->id?></b><br>
		  <b><?=Yii::t('app', 'Invoice Date	')?>:</b> <?=$invoice->added_at?date('d-M-Y H:i', $invoice->added_at):date('d-M-Y H:i')?><br>
        </div>
        <!-- /.col -->
      </div>
	  </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped">
            <thead>
            <tr>
              <th><?=Yii::t('app', 'Order ID')?></th>
			  <th class="text-right"><?=Yii::t('app', 'Order Amount')?> #</th>
              <th class="text-right"><?=Yii::t('app', 'Commission')?> #</th>
            </tr>
            </thead>
            <tbody>
			<?php
			foreach ($commissiondetails as $row)
			{
			?>
            <tr>
              <td><?=$row->sub_order_id?></td>
			  <td class="text-right"><?=MulteModel::formatAmount($row->sub_order_total)?></td>
              <td class="text-right"><?=MulteModel::formatAmount($row->commission)?></td>
            </tr>
			<?php
			}
			?>
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
          <p class="lead"></p>

          <div class="table-responsive">
            <table class="table text-right">
			  <tr>
                <th class="text-right"><?=Yii::t('app', 'Total Commission')?>:</th>
                <td class="text-right"><strong><?=MulteModel::formatAmount($invoice->total_commission)?></strong></td>
			  </tr>
			  <tr>
				<th class="text-right"><?=Yii::t('app', 'Total Order Amount')?>:</th>
                <td class="text-right"><strong><?=MulteModel::formatAmount($invoice->total_order_amount)?></strong></td>
			  </tr>
			  <tr>
				<th class="text-right"><?=Yii::t('app', 'Final Payout')?>:</th>
                <td class="text-right"><strong><?=MulteModel::formatAmount($invoice->total_order_amount - $invoice->total_commission)?></strong></td>
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
						Url::to(['/finance/vendor-invoices/get-invoice-pdf']), 
									[
										'class' => 'btn btn-primary btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $invoice->id,
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
