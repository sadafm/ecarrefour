<?php
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use multebox\models\Order;
use multebox\models\OrderStatus;
use multebox\models\Tax;
use multebox\models\StateTax;
use multebox\models\User;
use multebox\models\search\MulteModel;
use multebox\models\Vendor;
use multebox\models\Inventory;
use multebox\models\PaymentMethods;
use multebox\models\DiscountCoupons;
use yii\helpers\ArrayHelper;
use multebox\models\City;
use multebox\models\State;
use multebox\models\Country;
use multebox\models\File;
use multebox\models\ShippingDetail;

/**
 * @var yii\web\View $this
 * @var multebox\models\SubOrder $model
 * @var yii\widgets\ActiveForm $form
 */

 $dFlag = true;

 function getDiscountCouponName($coupon_id)
 {
	 $coupon = DiscountCoupons::findOne($coupon_id);

	 if($coupon)
		 return $coupon->coupon_code;
	 else
		 return Yii::t('app', 'No Coupon Applied');
 }

 /*function getSubOrderVendorCost($model)
 {
	 $coupon = DiscountCoupons::findOne($model->discount_coupon_id);

	 if(count($coupon) > 0)
	 {
		 $added_by = User::findOne($coupon->added_by_id);
		 if($added_by->entity_type == 'vendor')
		 {
			 $cost = $model->total_cost + $model->total_site_discount;
		 }
		 else
		 {
			$cost = $model->total_cost + $model->total_site_discount + $model->total_coupon_discount;
		 }
	 }
	 else
	 {
		 $cost = $model->total_cost + $model->total_site_discount;
	 }
	 
	 return round($cost, 2);
 }*/

 function getTaxPercent($tax_snapshot, $state_tax_snapshot)
 {	 
	 if($state_tax_snapshot)
	 {
		$statetaxmodel = MulteModel::mapJsonToModel(Json::decode($state_tax_snapshot), new StateTax);

		if($statetaxmodel->tax_percentage > 0)
			$tax_percentage = $statetaxmodel->tax_percentage.'%';
		else
			$tax_percentage = Yii::t('app', 'No Tax Applied');
	 }
	 else if($tax_snapshot)
	 {
		$taxmodel = MulteModel::mapJsonToModel(Json::decode($tax_snapshot), new Tax);

		if($taxmodel->tax_percentage > 0)
			$tax_percentage = $taxmodel->tax_percentage.'%';
		else
			$tax_percentage = Yii::t('app', 'No Tax Applied');
	 }
	 else
	 {
		$tax_percentage = Yii::t('app', 'No Tax Applied');
	 }

	 return $tax_percentage;
 }

?>

<div class="sub-order-form">

    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL]); 
	?>
	
	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Vendor Order Details' ); ?></h3>
		</div>
		<div class="panel-body">
		<div class="col-sm-12">

	<div class="row">
    <div class="col-sm-9">
	<?php

	echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 4,
        'attributes' => [
			'sub_order_status' => [
									//'type' => Form::INPUT_DROPDOWN_LIST,
									'type' => Form::INPUT_TEXT,
									'label' => Yii::t('app', 'Order Status'),
									//'items' => ArrayHelper::map ( OrderStatus::find ()->orderBy ( 'label' )->asArray ()->all (), 'status', 'label' ),
									'options' => [
													'placeholder' => Yii::t('app', 'Enter Sub Order Status...'), 
													'maxlength' => 3, 
													'disabled' => $dFlag,
													'value' => Yii::t('app', OrderStatus::getLabelByStatus($model->sub_order_status))
												]
								],

			//'order_id' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Order ID...', 'disabled' => $dFlag]],
            
            'total_items' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Total Items...', 'disabled' => $dFlag]],

			'total_cost' => [
								'type' => Form::INPUT_TEXT, 
								'options' => [
												'placeholder' => Yii::t('app', 'Enter Total Cost...'), 
												'disabled' => $dFlag,
												'value' => MulteModel::formatAmount($model->total_cost)
											]
							],
			'vendor_entitlement' => [
								'type' => Form::INPUT_TEXT, 
								'label' => Yii::t('app', 'Vendor Entitlement'),
								'options' => [
												'placeholder' => '...', 
												'disabled' => $dFlag,
												'value' => MulteModel::formatAmount(MulteModel::getSubOrderVendorCost($model))
											]
							],

			]
		]);
	
	echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 4,
        'attributes' => [
			'total_converted_cost' => [
								'type' => Form::INPUT_TEXT, 
								'options' => [
												'placeholder' => Yii::t('app', 'Enter Converted Cost...'), 
												'disabled' => $dFlag,
												'value' => MulteModel::formatAmount($model->total_converted_cost, $model->order_currency_symbol)
											]
							],


            'delivery_method' => [
									'type' => Form::INPUT_TEXT, 
									'options' => [
										'placeholder' => Yii::t('app', 'Enter Delivery Method...'), 
										'maxlength' => 3, 
										'disabled' => $dFlag,
										'value' => Yii::t('app', 'Flat Rate Shipping')
									]
								],

			'total_shipping' => [
									'type' => Form::INPUT_TEXT, 
									'options' => [
													'placeholder' => Yii::t('app', 'Enter Total Shipping...'), 
													'disabled' => $dFlag,
													'value' => MulteModel::formatAmount($model->total_shipping)
												]
								],

            'payment_method' => [
									'type' => Form::INPUT_TEXT, 
									'options' => [
													'placeholder' => Yii::t('app', 'Enter Payment Method...'), 
													'maxlength' => 3, 
													'disabled' => $dFlag,
													'value' => Yii::t('app', PaymentMethods::getLabelByMethod($model->payment_method))
												]
								],
			]
		]);

	echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

			'inventory_id' => [
								'type' => Form::INPUT_TEXT, 
								'label' => Yii::t('app', 'Product Name'),
								'options' => [
									'placeholder' => Yii::t('app', 'Enter Inventory ID...'), 
									'disabled' => $dFlag,
									'value' => Inventory::findOne($model->inventory_id)->product_name
								]
							],
			]
		]);

	echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [
            'vendor_id' => [
							'type' => Form::INPUT_TEXT, 
							'label' => Yii::t('app', 'Vendor Name'),
							'options' => [
								'placeholder' => Yii::t('app', 'Enter Vendor ID...'), 
								'disabled' => $dFlag,
								'value' => Vendor::findOne($model->vendor_id)->vendor_name
							]
						],
			]
		]);


	

	echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 3,
        'attributes' => [

            'discount_coupon_id' => [
										'type' => Form::INPUT_TEXT, 
										'label' => Yii::t('app', 'Discount Coupon'),
										'options' => [
														'placeholder' => Yii::t('app', 'Enter Discount Coupon ID...'), 
														'disabled' => $dFlag,
														'value' => getDiscountCouponName($model->discount_coupon_id)
													]
									],

			'total_coupon_discount' => [
										'type' => Form::INPUT_TEXT, 
										'options' => [
														'placeholder' => Yii::t('app', 'Enter Total Coupon Discount...'), 
														'disabled' => $dFlag,
														'value' => MulteModel::formatAmount($model->total_coupon_discount)
													]
										],

			'total_site_discount' => [
										'type' => Form::INPUT_TEXT, 
										'options' => [
														'placeholder' => Yii::t('app', 'Enter Total Site Discount...'), 
														'disabled' => $dFlag,
														'value' => MulteModel::formatAmount($model->total_site_discount)
													]
									],

           // 'global_discount_id' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Global Discount ID...']],
           
			]
		]);

	echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 3,
        'attributes' => [
			'tax_id' => [
							'type' => Form::INPUT_TEXT, 
							'label' => Yii::t('app', 'Tax Percentage'),
							'options' => [
											'placeholder' => Yii::t('app', 'Enter Tax ID...'), 
											'disabled' => $dFlag,
											'value' => getTaxPercent($model->tax_snapshot, $model->state_tax_snapshot)
										]
						],

			'total_tax' => [
							'type' => Form::INPUT_TEXT, 
							'options' => [
											'placeholder' => Yii::t('app', 'Enter Total Tax...'),
											'disabled' => $dFlag,
											'value' => MulteModel::formatAmount($model->total_tax)
										]
							],

            'added_at' => [
							'type' => Form::INPUT_TEXT,
							'label' => Yii::t('app', 'Order Date'),
							'options' => [
											'placeholder' => Yii::t('app', 'Enter Added At...'), 
											'disabled' => $dFlag,
											'value' => date('d-M-Y H:i', $model->added_at)
										]
						],

            //'updated_at' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Updated At...']],

            //'discount_coupon_snapshot' => ['type' => Form::INPUT_TEXTAREA, 'options' => ['placeholder' => 'Enter Discount Coupon Snapshot...','rows' => 6]],

            //'global_discount_snapshot' => ['type' => Form::INPUT_TEXTAREA, 'options' => ['placeholder' => 'Enter Global Discount Snapshot...','rows' => 6]],

            //'tax_snapshot' => ['type' => Form::INPUT_TEXTAREA, 'options' => ['placeholder' => 'Enter Tax Snapshot...','rows' => 6]],

           
			]
		]);
		?>
		</div>
		<div class="col-sm-3">
		  <div class="panel panel-info">
		    <div class="panel-heading">
			  <h3 class="panel-title"><?php echo Yii::t ( 'app', 'Item Details' ); ?></h3>
		    </div>
		  <div class="panel-body">

		  <div class="row">
		  <?php
		    $inventory_json = $model->inventory_snapshot;
			$inventory_item = MulteModel::mapJsonToModel(Json::decode($inventory_json), new Inventory);
			$prod_title = $inventory_item->product_name;
			$fileDetails = File::find()->where("entity_type='product' and entity_id=".$inventory_item->product_id)->one();
		  ?>
		    <div class="col-sm-12 text-center">
		      <a target="_blank" href="<?=str_replace(Yii::$app->params['backend_url'], Yii::$app->params['frontend_url'], Url::to(['/product/default/detail', 'inventory_id' => $inventory_item->id], true))?>"><img src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$prod_title?>" title="<?=$prod_title?>"  style="height: 110px;"/></a>
		    </div>
		  </div>
		  
		  </div> <!-- panel body -->
	      </div> <!-- panel info -->
		  
		  <div class="panel panel-info">
		    <div class="panel-heading">
			  <h3 class="panel-title"><?php echo Yii::t ( 'app', 'Address Details' ); ?></h3>
		    </div>
		  <div class="panel-body">

		  <div class="row">
		    <div class="col-sm-12">
			<?php
			$order = Order::findOne($model->order_id);
			$address = Json::decode($order->address_snapshot);
			$contact = Json::decode($order->contact_snapshot);
			?>
			<b>Shipping Address</b><br>
			<?=$contact['first_name']?> <?=$contact['last_name']?><br>
			<?=$address['address_1']?><br>
			<?=$address['address_2']?><br>
			<?=City::findOne($address['city_id'])->city?><br/>
			<?=State::findOne($address['state_id'])->state?><br/>
			<?=Country::findOne($address['country_id'])->country?> - <?=$address['zipcode']?><br/>
			Phone: <?=$contact['mobile']?><br/>
			<?php
			?>
		    </div>
		  </div>

		  </div> <!-- panel body -->
	      </div> <!-- panel info -->
		  
		</div>
		</div>

</div>
	</div>
	</div>
	<?php
	if($model->sub_order_status == OrderStatus::_READY_TO_SHIP || $model->sub_order_status == OrderStatus::_SHIPPED || $model->sub_order_status == OrderStatus::_DELIVERED)
	{
	?>
		<input type="hidden" name="ShippingDetail[sub_order_id]" value="<?=$model->id?>">
	  <div class="panel panel-info">
		<div class="panel-heading">
		  <h3 class="panel-title"><?php echo Yii::t ( 'app', 'Item Shipping Details' ); ?></h3>
		</div>
	    <div class="panel-body">
		<div class="col-sm-12">
	      <div class="row">
			<?php
			echo Form::widget([

			'model' => $shipmodel,
			'form' => $form,
			'columns' => 3,
			'attributes' => [
				'tracking_number' => [
								'type' => Form::INPUT_TEXT, 
								'options' => [
												'placeholder' => Yii::t('app', 'Enter Tracking Number...'), 
											]
							],

				'carrier' => [
								'type' => Form::INPUT_TEXT, 
								'options' => [
												'placeholder' => Yii::t('app', 'Enter Carrier...'),
											]
								],

				'tracking_url' => [
								'type' => Form::INPUT_TEXT,
								'options' => [
												'placeholder' => Yii::t('app', 'Enter Tracking URL...'), 
											]
							],
			   
				]
			]);
			?>
	      </div>
	    </div>
	  </div>
    </div>
	<?php
	}
?>
	<?php
    /*echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    );*/

	

	if($model->sub_order_status == OrderStatus::_NEW)
	{
		echo Html::a(Yii::t('app','Accept Order'), 
					Url::to(['/order/sub-order/update-status']), 
								[
									'class' => 'btn btn-success btn-sm',
									'data-method' => 'POST',
									'data-params' => [
												'id' => $model->id,
												'rqst' => 'accept',
												'method' => 'post',
												],
								]);
?> <?php
		echo Html::a(Yii::t('app','Reject Order'), 
					Url::to(['/order/sub-order/update-status']), 
								[
									'class' => 'btn btn-danger btn-sm',
									'data-method' => 'POST',
									'data-params' => [
												'id' => $model->id,
												'rqst' => 'reject',
												'method' => 'post',
												],
								]);
?> <?php

	}

	if($model->sub_order_status == OrderStatus::_CANCELED)
	{
		if(Yii::$app->user->identity->entity_type != 'vendor' && $model->payment_method != PaymentMethods::_COD)
		{
			echo Html::a(Yii::t('app','Issue Refund'), // If not auto refunded in previous step, system employee can retry a payment refund using this page.
						Url::to(['/order/sub-order/update-status']), 
									[
										'class' => 'btn btn-info btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $model->id,
													'rqst' => 'refund',
													'method' => 'post',
													],
									]);
			?> <?php
		}
	}

	if($model->sub_order_status == OrderStatus::_CONFIRMED)
	{
		echo Html::a(Yii::t('app','Process Order'), 
					Url::to(['/order/sub-order/update-status']), 
								[
									'class' => 'btn btn-success btn-sm',
									'data-method' => 'POST',
									'data-params' => [
												'id' => $model->id,
												'rqst' => 'inprocess',
												'method' => 'post',
												],
								]);
?> <?php
		echo Html::a(Yii::t('app','Cancel Order'), 
					Url::to(['/order/sub-order/update-status']), 
								[
									'class' => 'btn btn-danger btn-sm',
									'data-method' => 'POST',
									'data-params' => [
												'id' => $model->id,
												'rqst' => 'cancel',
												'method' => 'post',
												],
								]);
?> <?php

	}

	if($model->sub_order_status == OrderStatus::_IN_PROCESS)
	{
		echo Html::a(Yii::t('app','Ready To Ship'), 
					Url::to(['/order/sub-order/update-status']), 
								[
									'class' => 'btn btn-success btn-sm',
									'data-method' => 'POST',
									'data-params' => [
												'id' => $model->id,
												'rqst' => 'readytoship',
												'method' => 'post',
												],
								]);
?> <?php
		echo Html::a(Yii::t('app','Cancel Order'), 
					Url::to(['/order/sub-order/update-status']), 
								[
									'class' => 'btn btn-danger btn-sm',
									'data-method' => 'POST',
									'data-params' => [
												'id' => $model->id,
												'rqst' => 'cancel',
												'method' => 'post',
												],
								]);
?> <?php

	}

	if($model->sub_order_status == OrderStatus::_READY_TO_SHIP)
	{
		echo Html::a(Yii::t('app','Cancel Order'), 
					Url::to(['/order/sub-order/update-status']), 
								[
									'class' => 'btn btn-danger btn-sm',
									'data-method' => 'POST',
									'data-params' => [
												'id' => $model->id,
												'rqst' => 'cancel',
												'method' => 'post',
												],
								]);
?> <?php

		if(Yii::$app->user->identity->entity_type == 'vendor')
		{
			if(($model->payment_method != PaymentMethods::_COD && Yii::$app->params['VENDOR_SHIP_ALL'] == 'Yes') || ($model->payment_method == PaymentMethods::_COD && Yii::$app->params['VENDOR_SHIP_COD'] == 'Yes'))
			{
				echo Html::a(Yii::t('app','Mark As Shipped'), 
							Url::to(['/order/sub-order/update-status']), 
										[
											'class' => 'btn btn-info btn-sm',
											'data-method' => 'POST',
											'data-params' => [
														'id' => $model->id,
														'rqst' => 'shipped',
														'method' => 'post',
														],
										]);
				?> <?php				
			}
		}
		else
		{
			echo Html::a(Yii::t('app','Mark As Shipped'), 
						Url::to(['/order/sub-order/update-status']), 
									[
										'class' => 'btn btn-info btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $model->id,
													'rqst' => 'shipped',
													'method' => 'post',
													],
									]);
			?> <?php
		}
	}

	if($model->sub_order_status == OrderStatus::_SHIPPED || $model->sub_order_status == OrderStatus::_RETURN_REJECTED || $model->sub_order_status == OrderStatus::_RETURN_CANCELED)
	{
		if(Yii::$app->user->identity->entity_type == 'vendor')
		{
			if(($model->payment_method != PaymentMethods::_COD && Yii::$app->params['VENDOR_SHIP_ALL'] == 'Yes') || ($model->payment_method == PaymentMethods::_COD && Yii::$app->params['VENDOR_SHIP_COD'] == 'Yes'))
			{
				echo Html::a(Yii::t('app','Cancel Order'), 
							Url::to(['/order/sub-order/update-status']), 
										[
											'class' => 'btn btn-danger btn-sm',
											'data-method' => 'POST',
											'data-params' => [
														'id' => $model->id,
														'rqst' => 'cancel',
														'method' => 'post',
														],
										]);
				?> <?php

				echo Html::a(Yii::t('app','Mark As Delivered'), 
							Url::to(['/order/sub-order/update-status']), 
										[
											'class' => 'btn btn-success btn-sm',
											'data-method' => 'POST',
											'data-params' => [
														'id' => $model->id,
														'rqst' => 'delivered',
														'method' => 'post',
														],
										]);
				?> <?php				
			}
		}
		else
		{
			echo Html::a(Yii::t('app','Cancel Order'), 
						Url::to(['/order/sub-order/update-status']), 
									[
										'class' => 'btn btn-danger btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $model->id,
													'rqst' => 'cancel',
													'method' => 'post',
													],
									]);
			?> <?php

			echo Html::a(Yii::t('app','Mark As Delivered'), 
						Url::to(['/order/sub-order/update-status']), 
									[
										'class' => 'btn btn-success btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $model->id,
													'rqst' => 'delivered',
													'method' => 'post',
													],
									]);
			?> <?php
		}
	}

	if($model->sub_order_status == OrderStatus::_RETURN_REQUESTED)
	{
		if(Yii::$app->user->identity->entity_type == 'vendor')
		{
			if(($model->payment_method != PaymentMethods::_COD && Yii::$app->params['VENDOR_SHIP_ALL'] == 'Yes') || ($model->payment_method == PaymentMethods::_COD && Yii::$app->params['VENDOR_SHIP_COD'] == 'Yes'))
			{
				echo Html::a(Yii::t('app','Approve Return'), 
							Url::to(['/order/sub-order/update-status']), 
										[
											'class' => 'btn btn-info btn-sm',
											'data-method' => 'POST',
											'data-params' => [
														'id' => $model->id,
														'rqst' => 'approvereturn',
														'method' => 'post',
														],
										]);
				?> <?php

				echo Html::a(Yii::t('app','Reject Return'), 
							Url::to(['/order/sub-order/update-status']), 
										[
											'class' => 'btn btn-info btn-sm',
											'data-method' => 'POST',
											'data-params' => [
														'id' => $model->id,
														'rqst' => 'rejectreturn',
														'method' => 'post',
														],
										]);
				?> <?php				
			}
		}
		else
		{
			echo Html::a(Yii::t('app','Approve Return'), 
						Url::to(['/order/sub-order/update-status']), 
									[
										'class' => 'btn btn-info btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $model->id,
													'rqst' => 'approvereturn',
													'method' => 'post',
													],
									]);
			?> <?php

			echo Html::a(Yii::t('app','Reject Return'), 
						Url::to(['/order/sub-order/update-status']), 
									[
										'class' => 'btn btn-info btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $model->id,
													'rqst' => 'rejectreturn',
													'method' => 'post',
													],
									]);
			?> <?php
		}
	}

	if($model->sub_order_status == OrderStatus::_RETURN_APPROVED)
	{
		if(Yii::$app->user->identity->entity_type == 'vendor')
		{
			if(($model->payment_method != PaymentMethods::_COD && Yii::$app->params['VENDOR_SHIP_ALL'] == 'Yes') || ($model->payment_method == PaymentMethods::_COD && Yii::$app->params['VENDOR_SHIP_COD'] == 'Yes'))
			{
				echo Html::a(Yii::t('app','Mark As Returned'), 
							Url::to(['/order/sub-order/update-status']), 
										[
											'class' => 'btn btn-info btn-sm',
											'data-method' => 'POST',
											'data-params' => [
														'id' => $model->id,
														'rqst' => 'returned',
														'method' => 'post',
														],
										]);
				?> <?php

				echo Html::a(Yii::t('app','Reject Return'), 
							Url::to(['/order/sub-order/update-status']), 
										[
											'class' => 'btn btn-info btn-sm',
											'data-method' => 'POST',
											'data-params' => [
														'id' => $model->id,
														'rqst' => 'rejectreturn',
														'method' => 'post',
														],
										]);
				?> <?php				
			}
		}
		else
		{
			echo Html::a(Yii::t('app','Mark As Returned'), 
						Url::to(['/order/sub-order/update-status']), 
									[
										'class' => 'btn btn-info btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $model->id,
													'rqst' => 'returned',
													'method' => 'post',
													],
									]);
			?> <?php

			echo Html::a(Yii::t('app','Reject Return'), 
						Url::to(['/order/sub-order/update-status']), 
									[
										'class' => 'btn btn-info btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $model->id,
													'rqst' => 'rejectreturn',
													'method' => 'post',
													],
									]);
			?> <?php
		}
	}

	if($model->sub_order_status == OrderStatus::_RETURNED) 
	{
		if(Yii::$app->user->identity->entity_type != 'vendor')
		{
			echo Html::a(Yii::t('app','Issue Refund'), // If not auto refunded in previous step, system employee can retry a payment refund using this page.
						Url::to(['/order/sub-order/update-status']), 
									[
										'class' => 'btn btn-info btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $model->id,
													'rqst' => 'refund',
													'method' => 'post',
													],
									]);
			?> <?php
		}
	}
	ActiveForm::end();
	?> 

</div>


<div class="row">
<div class="col-sm-12">
<div class="pull-right">
	<?php

	echo Html::a(Yii::t('app','View Invoice'), 
						Url::to(['/order/sub-order/get-invoice']), 
									[
										'class' => 'btn btn-warning btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $model->id,
													'method' => 'post',
													],
									]);
			?> <?php

	echo Html::a(Yii::t('app','Download Invoice'), 
						Url::to(['/order/sub-order/get-invoice-pdf']), 
									[
										'class' => 'btn btn-primary btn-sm',
										'data-method' => 'POST',
										'data-params' => [
													'id' => $model->id,
													'method' => 'post',
													],
									]);
			?>
	</div>
	</div>
	</div>