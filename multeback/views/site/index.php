<?php
use yii\helpers\Url;
use multebox\models\search\MulteModel;
use multebox\models\SalesReport;
use multebox\models\Order;
use multebox\models\File;
use multebox\models\OrderStatus;
use multebox\models\PaymentMethods;

$this->title = Yii::$app->params['APPLICATION_NAME'].' '.Yii::t('app', 'Dashboard');
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- ChartJS -->
<script src="<?=Url::base()?>/bower_components/chart.js/Chart.js"></script>

<?php
if(Yii::$app->user->identity->entity_type=="employee")
{
?>
    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-cart-arrow-down"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?=Yii::t('app', 'Orders')?></span>
              <span class="info-box-number"><?=MulteModel::getCurrentMonthOrderCount()?></span>
			  <br><span class="label label-info pull-right"><?=Yii::t('app', 'This Month')?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-money"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?=Yii::t('app', 'Sale Amount')?></span>
              <span class="info-box-number"><?=MulteModel::getCurrentMonthSaleAmount()?></span>
			  <br><span class="label label-info pull-right"><?=Yii::t('app', 'This Month')?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-dollar"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?=Yii::t('app', 'Commission')?></span>
              <span class="info-box-number"><?=MulteModel::getCurrentMonthCommission()?></span>
			  <br><span class="label label-info pull-right"><?=Yii::t('app', 'This Month')?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?=Yii::t('app', 'New Vendors')?></span></span>
              <span class="info-box-number"><?=MulteModel::getCurrentMonthVendors()?></span>
			  <br><span class="label label-info pull-right"><?=Yii::t('app', 'This Month')?>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
			<?php
			$no_of_months = 7;
			$calculated = $no_of_months-1;
			?>
              <h3 class="box-title"><?=Yii::t('app', 'Last')?> <?=$no_of_months?> <?=Yii::t('app', 'Months Recap')?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  <p class="text-center">
				  <?php
					 $oldmonth = date("M, Y", strtotime("-$calculated month"));
					 $currentmonth = date("M, Y", strtotime("-0 month"));
				  ?>
                    <strong><?=Yii::t('app', 'Sales')?>: <?=$oldmonth?> - <?=$currentmonth?></strong>
                  </p>

                  <div class="chart">
                    <!-- Sales Chart Canvas -->
                    <canvas id="salesChart" style="height: 180px;"></canvas>
                  </div>

				  <?php
				   $obj = new SalesReport;
				   $obj->salesReportChart('salesChart', $no_of_months);
				  ?>
                  <!-- /.chart-responsive -->
                </div>
              </div>
              <!-- /.row -->
            </div>
            <!-- ./box-body -->
            <div class="box-footer">
              <div class="row">
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    
                    <h5 class="description-header"><?=MulteModel::getTotalOrderCount($no_of_months)?></h5>
                    <span class="description-text"><?=Yii::t('app', 'TOTAL ORDERS')?></span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    
                    <h5 class="description-header"><?=MulteModel::getTotalSaleAmount($no_of_months)?></h5>
                    <span class="description-text"><?=Yii::t('app', 'TOTAL SALE')?></span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    
                    <h5 class="description-header"><?=MulteModel::getTotalCommission($no_of_months)?></h5>
                    <span class="description-text"><?=Yii::t('app', 'TOTAL COMMISSION')?></span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block">
                    
                    <h5 class="description-header"><?=MulteModel::getTotalVendors($no_of_months)?></h5>
                    <span class="description-text"><?=Yii::t('app', 'NEW VENDORS')?></span>
                  </div>
                  <!-- /.description-block -->
                </div>
              </div>
              <!-- /.row -->
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <div class="col-md-8">

          <!-- TABLE: LATEST ORDERS -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><?=Yii::t('app', 'Latest Orders')?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th><?=Yii::t('app', 'Order ID')?></th>
                    <th><?=Yii::t('app', 'Order Total')?></th>
					<th><?=Yii::t('app', 'Payment Method')?></th>
                    <th><?=Yii::t('app', 'Status')?></th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php
				  $orders = MulteModel::getOrdersForDashboard();
				  foreach($orders as $order)
				  {
					  switch($order->order_status)
					  {
						  case OrderStatus::_NEW:
						  case OrderStatus::_CONFIRMED:
							  $label="label-primary";
							  break;

						  case OrderStatus::_IN_PROCESS:
						  case OrderStatus::_READY_TO_SHIP:
							  $label="label-warning";
							  break;

						  case OrderStatus::_SHIPPED:
						  case OrderStatus::_DELIVERED:
							  $label="label-success";
							  break;

					      default:
							  $label="label-info";
					  }
				  ?>
                  <tr>
                    <td><a href="<?=Url::to(['/order/sub-order/view-order', 'id' => $order->id])?>"><?=$order->id?></a></td>
                    <td><?=MulteModel::formatAmount($order->total_cost)?></td>
					<td><?=Yii::t('app', PaymentMethods::getLabelByMethod($order->payment_method))?></td>
                    <td><span class="label <?=$label?>"><?=Yii::t('app', OrderStatus::getLabelByStatus($order->order_status))?></span></td>
                  </tr>
				  <?php
				  }
				  ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="<?=Url::to(['/order/order/index'])?>" class="btn btn-sm btn-info btn-flat pull-left"><?=Yii::t('app', 'View All Orders')?></a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4">
          
          <!-- PRODUCT LIST -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><?=Yii::t('app', 'Recently Added Inventory Items')?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <ul class="products-list product-list-in-box">
			  <?php
			  $inventories = MulteModel::getInventoriesForDashboard();

			  foreach($inventories as $inventory)
			  {
				  $prod_title = $inventory->product_name;
				  $fileDetails = File::find()->where("entity_type='product' and entity_id=".$inventory->product_id)->one();
			  ?>
                <li class="item">
                  <div class="product-img">
                    <img src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$prod_title?>" title="<?=$prod_title?>" />
                  </div>
                  <div class="product-info">
                    <a href="<?=Url::to(['/inventory/inventory/update', 'id' => $inventory->id])?>" class="product-title"><?=$inventory->product_name?>
                      <span class="label label-success pull-right"><?=MulteModel::formatAmount($inventory->price)?></span></a>
                    <span class="product-description">
                          <?=Yii::t('app', 'Stock')?>: <?=$inventory->stock?>
                        </span>
                  </div>
                </li>
			  <?php
			  }
			  ?>
              </ul>
            </div>
            <!-- /.box-body -->
            <div class="box-footer text-center">
              <a href="<?=Url::to(['/inventory/inventory/index'])?>" class="uppercase"><?=Yii::t('app', 'View All Inventory Items')?></a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
<?php
}
else if(Yii::$app->user->identity->entity_type=="vendor") // Vendor Dashboard
{
?>
   <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-cart-arrow-down"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?=Yii::t('app', 'Orders')?></span>
              <span class="info-box-number"><?=MulteModel::getCurrentMonthVendorOrderCount()?></span>
			  <br><span class="label label-info pull-right"><?=Yii::t('app', 'This Month')?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-money"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?=Yii::t('app', 'Sale Amount')?></span>
              <span class="info-box-number"><?=MulteModel::getCurrentMonthVendorSaleAmount()?></span>
			  <br><span class="label label-info pull-right"><?=Yii::t('app', 'This Month')?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-dollar"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?=Yii::t('app', 'Income')?></span>
              <span class="info-box-number"><?=MulteModel::getCurrentMonthVendorIncome()?></span>
			  <br><span class="label label-info pull-right"><?=Yii::t('app', 'This Month')?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-star"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?=Yii::t('app', 'Average Rating')?></span>
              <span class="info-box-number">
			  <?php 
				$rating = MulteModel::getCurrentMonthVendorRating();
				if ($rating > 0)
					echo round($rating,2);
				else
					echo Yii::t('app', 'No Data');
			  ?>
			  </span>
			  <br><span class="label label-info pull-right"><?=Yii::t('app', 'This Month')?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
			<?php
			$no_of_months = 7;
			$calculated = $no_of_months-1;
			?>
              <h3 class="box-title"><?=Yii::t('app', 'Last')?> <?=$no_of_months?> <?=Yii::t('app', 'Months Recap')?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  <p class="text-center">
				  <?php
					 $oldmonth = date("M, Y", strtotime("-$calculated month"));
					 $currentmonth = date("M, Y", strtotime("-0 month"));
				  ?>
                    <strong><?=Yii::t('app', 'Sales')?>: <?=$oldmonth?> - <?=$currentmonth?></strong>
                  </p>

                  <div class="chart">
                    <!-- Sales Chart Canvas -->
                    <canvas id="salesChart" style="height: 180px;"></canvas>
                  </div>

				  <?php
				   $obj = new SalesReport;
				   $obj->salesReportChartVendor('salesChart', $no_of_months);
				  ?>
                  <!-- /.chart-responsive -->
                </div>
              </div>
              <!-- /.row -->
            </div>
            <!-- ./box-body -->
            <div class="box-footer">
              <div class="row">
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    
                    <h5 class="description-header"><?=MulteModel::getTotalVendorOrderCount($no_of_months)?></h5>
                    <span class="description-text"><?=Yii::t('app', 'TOTAL ORDERS')?></span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    
                    <h5 class="description-header"><?=MulteModel::getTotalVendorSaleAmount($no_of_months)?></h5>
                    <span class="description-text"><?=Yii::t('app', 'TOTAL SALE')?></span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    
                    <h5 class="description-header"><?=MulteModel::getTotalVendorIncome($no_of_months)?></h5>
                    <span class="description-text"><?=Yii::t('app', 'TOTAL INCOME')?></span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block">
                    
                    <h5 class="description-header"><?=round(MulteModel::getAverageVendorRating($no_of_months),2)?></h5>
                    <span class="description-text"><?=Yii::t('app', 'AVERAGE RATING')?></span>
                  </div>
                  <!-- /.description-block -->
                </div>
              </div>
              <!-- /.row -->
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <div class="col-md-8">

          <!-- TABLE: LATEST ORDERS -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><?=Yii::t('app', 'Latest Orders')?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th><?=Yii::t('app', 'Order ID')?></th>
                    <th><?=Yii::t('app', 'Order Total')?></th>
					<th><?=Yii::t('app', 'Payment Method')?></th>
                    <th><?=Yii::t('app', 'Status')?></th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php
				  $suborders = MulteModel::getVendorOrdersForDashboard();
				  foreach($suborders as $suborder)
				  {
					  switch($suborder->sub_order_status)
					  {
						  case OrderStatus::_NEW:
						  case OrderStatus::_CONFIRMED:
							  $label="label-primary";
							  break;

						  case OrderStatus::_IN_PROCESS:
						  case OrderStatus::_READY_TO_SHIP:
							  $label="label-warning";
							  break;

						  case OrderStatus::_SHIPPED:
						  case OrderStatus::_DELIVERED:
							  $label="label-success";
							  break;

					      default:
							  $label="label-info";
					  }
				  ?>
                  <tr>
                    <td><a href="<?=Url::to(['/order/sub-order/sub-order-view', 'id' => $suborder->id])?>"><?=$suborder->id?></a></td>
                    <td><?=MulteModel::formatAmount($suborder->total_cost)?></td>
					<td><?=Yii::t('app', PaymentMethods::getLabelByMethod($suborder->payment_method))?></td>
                    <td><span class="label <?=$label?>"><?=Yii::t('app', OrderStatus::getLabelByStatus($suborder->sub_order_status))?></span></td>
                  </tr>
				  <?php
				  }
				  ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="<?=Url::to(['/order/sub-order/vendor-index'])?>" class="btn btn-sm btn-info btn-flat pull-left"><?=Yii::t('app', 'View All Orders')?></a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4">
          
          <!-- PRODUCT LIST -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><?=Yii::t('app', 'Recently Added Inventory Items')?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <ul class="products-list product-list-in-box">
			  <?php
			  $inventories = MulteModel::getVendorInventoriesForDashboard();

			  foreach($inventories as $inventory)
			  {
				  $prod_title = $inventory->product_name;
				  $fileDetails = File::find()->where("entity_type='product' and entity_id=".$inventory->product_id)->one();
			  ?>
                <li class="item">
                  <div class="product-img">
                    <img src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$prod_title?>" title="<?=$prod_title?>" />
                  </div>
                  <div class="product-info">
                    <a href="<?=Url::to(['/inventory/inventory/update', 'id' => $inventory->id])?>" class="product-title"><?=$inventory->product_name?>
                      <span class="label label-success pull-right"><?=MulteModel::formatAmount($inventory->price)?></span></a>
                    <span class="product-description">
                          <?=Yii::t('app', 'Stock')?>: <?=$inventory->stock?>
                        </span>
                  </div>
                </li>
			  <?php
			  }
			  ?>
              </ul>
            </div>
            <!-- /.box-body -->
            <div class="box-footer text-center">
              <a href="<?=Url::to(['/inventory/inventory/index'])?>" class="uppercase"><?=Yii::t('app', 'View All Inventory Items')?></a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
<?php
}
?>