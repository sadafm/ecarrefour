<?php
use yii\jui\AutoComplete;
use multebox\models\Cart;
use multebox\models\Inventory;
use multebox\models\File;
use multebox\models\Vendor;
use multebox\models\Address;
use multebox\models\Contact;
use multebox\models\search\MulteModel;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use multebox\models\Country;
use multebox\models\State;
use multebox\models\City;
use multebox\models\PaymentMethods;
?>
<script type="text/javascript" src="<?=Url::base()?>/js/jquery-2.1.1.min.js"></script>

<script>
$("#cart").hide();

function Add_Error(obj,msg){
	 $(obj).parents('.form-group').addClass('has-error');
	 $(obj).parents('.form-group').append('<div style="color:#D16E6C; clear:both" class="error"><i class="icon-remove-sign"></i> '+msg+'</div>');
	 return true;
}

function Add_Error2(obj,msg){
	 $(obj).parents('.form-group').addClass('has-error');
	 $(obj).parents('.form-group').append('<div style="color:#D16E6C; clear:both" class="error"><i class="icon-remove-sign"></i> '+msg+'</div>');
	 return true;
}

function Remove_Error(obj){
	$(obj).parents('.form-group').removeClass('has-error');
	$(obj).parents('.form-group').children('.error').remove();
	return false;
}

function Remove_Error2(obj){
	$(obj).parents('.form-group').removeClass('has-error');
	$(obj).parents('.form-group').children('.error').remove();
	return false;
}

$(document).on("change", '.accountradio', function(event)
{
	var accountval = $('input[name="account"]:checked').val();
	//alert(accountval);

	if(accountval == 'returning')
	{
		<?php
			Url::remember();
		?>
		window.location.replace("<?=Url::to(['/site/login'])?>");
	}
});

$(document).on("click", '#button-coupon', function(event)
{
	if ($('#input-coupon').val() != '')
	{
		Remove_Error2($('#input-coupon'));
		$.post("<?=Url::to(['/order/default/ajax-apply-discount'])?>", { 'discount_coupon': $('#input-coupon').val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
				if (result == 'a') // Invalid Coupon
				{
					Add_Error2($('#input-coupon'), "<?=Yii::t('app','Coupon is Invalid!')?>");
					event.preventDefault();
				}
				else if (result == 'b') // Expired Coupon
				{
					Add_Error2($('#input-coupon'), "<?=Yii::t('app','Coupon has Expired!')?>");
					event.preventDefault();
				}
				else if (result == 'c') // Not Applicable on Cart Items
				{
					Add_Error2($('#input-coupon'), "<?=Yii::t('app','Coupon is not applicable on any of cart items!')?>");
					event.preventDefault();
				}
				else if (result == 'd') // Not Applicable on Cart Amount
				{
					Add_Error2($('#input-coupon'), "<?=Yii::t('app','Coupon is not applicable on current cart amount!')?>");
					event.preventDefault();
				}
				else if (result == 'e') // Not issued to current user
				{
					Add_Error2($('#input-coupon'), "<?=Yii::t('app','Coupon is not issued to you!')?>");
					event.preventDefault();
				}
				else if (result == 'f') // Budget Exhausted
				{
					Add_Error2($('#input-coupon'), "<?=Yii::t('app','Coupon already exhausted - Try another!')?>");
					event.preventDefault();
				}
				else
				{
					var coupontext = '<input type="hidden" name="coupon_code" value="'+$('#input-coupon').val()+'"><div class="panel-body">'+ "<?=Yii::t('app', 'Coupon Applied Successfully')?>"+'! <a href="javascript:void(0)" title="Remove" id="removecoupon" onClick=""><i class="fa fa-times-circle"></i></a></div>';
					$('.discountcoupon').html(coupontext)

					$('.cartcontents tbody').html(result);
				}
			})
	}
});

$(document).on("click", '#removecoupon', function(event)
{
	var coupontext = '<label for="input-coupon" class="col-sm-3 control-label">'+"<?=Yii::t('app', 'Enter coupon code')?>"+'</label>'+
                        '<div class="form-group">'+
						  '<div class="table-responsive">'+
						    '<table>'+
						      '<tr>'+
						        '<td class="col-sm-6">'+
                                  '<input type="text" class="form-control" id="input-coupon" placeholder="'+"<?=Yii::t('app', 'Enter your coupon here')?>"+'" name="coupon">'+
						        '</td>'+
						        '<td class="col-sm-3">'+
                                 
                                    '<input type="button" class="btn btn-primary" data-loading-text="Loading..." id="button-coupon" value="'+"<?=Yii::t('app', 'Apply Coupon')?>"+'">'+
                                  
						        '</td>'+
						      '</tr>'+
						    '</table>'+
						  '</div>'+
						'</div>';
	$('.discountcoupon').html(coupontext);

	$.post("<?=Url::to(['/order/default/ajax-refresh-cartpage'])?>", {'_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
		$('.cartcontents tbody').html(result);
		})
});

$(document).on("click", '#button-confirm', function(event)
{
	<?php
		Url::remember();
	?>
	var guest = '<?=Yii::$app->user->isGuest?>';
	if (guest)
	{
		var error='';
		$('[data-validation="required"]').each(function(index, element) 
		{
			Remove_Error($(this));
			
			var e=$(this).val();

			if($(this).val() == '' && !$(this).is("[mandatory-field]"))
			{
				Remove_Error($(this));
			}
			else if($(this).val() == '' && $(this).is("[mandatory-field]"))
			{
				error+=Add_Error($(this),"<?=Yii::t('app','This Field is Required!')?>");
			}
			else if($(this).is("[email-validation]"))
			{
				var atpos=e.indexOf("@");
				var dotpos=e.lastIndexOf(".");

				if (atpos<1 || dotpos<atpos+2 || dotpos+2>=e.length)
				{
					error+=Add_Error($(this),"<?=Yii::t('app','Email Address Not Valid!')?>");
				}
				else
				{
					Remove_Error($(this));
				}	
			}
			else if($(this).is("[num-validation]"))
			{
				if (!e.match(/^\d+$/))
				{
					error+=Add_Error($(this),"<?=Yii::t('app','Please enter a valid number!')?>");
				}
				else
				{
					Remove_Error($(this));
				}	
			}
			else if($(this).is("[num-validation-float]"))
			{
				//if (!e.match(/^\d+$/))
				//if (!e.match(/^[-+]?[0-9]*\.?[0-9]+$/))
				if (!e.match(/^[]?[0-9]*\.?[0-9]+$/))
				{
					error+=Add_Error($(this),"<?=Yii::t('app','Please enter a valid number!')?>");
				}
				else
				{
					Remove_Error($(this));
				}	
			}
			else if($(this).val() == '')
			{
				error+=Add_Error($(this),"<?=Yii::t ('app','This Field is Required!')?>");
			}
			else
			{
				Remove_Error($(this));
			}	

			if(error !='')
			{
				event.preventDefault();
			}
			else
			{
				return true;
			}
		});
	}
	
	return true;
});

$(document).on("change", '#country_id', function(event){
    $.post("<?=Url::to(['/multeobjects/address/ajax-load-states'])?>", { 'country_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#state_id').html(result);
					$('#city_id').html('<option value=""> --Select--</option>');
				})
	})

$(document).on("change", '#state_id', function(event){
    /*$.post("<?=Url::to(['/multeobjects/address/ajax-load-cities'])?>", { 'state_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#city_id').html(result);
				})*/
	$.post("<?=Url::to(['/multeobjects/address/ajax-load-cities-array'])?>", { 'state_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#city_id').autocomplete({"source": $.parseJSON(result)});
				})

	})

</script>

<?php
$digital_ind = 0;
foreach($cart_items as $cart)
{
	$digital_ind = Inventory::findOne($cart->inventory_id)->product->digital;
	if($digital_ind)
	{
		break;
	}
}
?>
  <div id="container">
    
      <!-- Breadcrumb Start-->
      <ul class="breadcrumb">
        <li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
        <li><a href="<?=Url::to(['/order/default/cart'])?>"><?=Yii::t('app', 'Shopping Cart')?></a></li>
        <li><?=Yii::t('app', 'Checkout')?></li>
      </ul>
      <!-- Breadcrumb End-->
      <div class="row">
        <!--Middle Part Start-->
		<form method="post" id="checkoutform" enctype="multipart/form-data">
		<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
		<input type="hidden" name="checkoutsubmit" value="">
        <div id="content" class="col-sm-12">
          <h1 class="title"><?=Yii::t('app', 'Checkout')?></h1>
          <div class="row">
            <div class="col-sm-4">
			<?php
			if(Yii::$app->user->isGuest)
			{
			?>
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4 class="panel-title"><i class="fa fa-sign-in"></i> <?=Yii::t('app', 'Guest Checkout or Login')?></h4>
                </div>
                  <div class="panel-body accountradio"><ul>
                        <div>
                          <label>
                            <input type="radio" checked="checked" value="guest" name="account">
                            <?=Yii::t('app', 'Guest Checkout')?></label>
                        </div>
                        <div>
                          <label>
                            <input type="radio" value="returning" name="account">
                            <?=Yii::t('app', 'Returning Customer')?></label>
                        </div>
                  </ul></div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4 class="panel-title"><i class="fa fa-user"></i> <?=Yii::t('app', 'Your Personal Details')?></h4>
                </div>
                  <div class="panel-body">
                        <fieldset id="account">
                          <div class="form-group required">
                            <label for="input-payment-firstname" class="control-label"><?=Yii::t('app', 'First Name')?></label>
                            <input type="text" class="form-control" id="input-payment-firstname" data-validation="required" mandatory-field placeholder="<?=Yii::t('app', 'First Name')?>" name="first_name">
                          </div>
                          <div class="form-group required">
                            <label for="input-payment-lastname" class="control-label"><?=Yii::t('app', 'Last Name')?></label>
                            <input type="text" class="form-control" id="input-payment-lastname" data-validation="required" mandatory-field placeholder="<?=Yii::t('app', 'Last Name')?>" name="last_name">
                          </div>
                          <div class="form-group required">
                            <label for="input-payment-email" class="control-label"><?=Yii::t('app', 'E-Mail')?></label>
                            <input type="text" class="form-control" id="input-payment-email" data-validation="required" mandatory-field email-validation placeholder="<?=Yii::t('app', 'E-Mail')?>" name="email">
                          </div>
                          <div class="form-group required">
                            <label for="input-payment-telephone" class="control-label"><?=Yii::t('app', 'Telephone')?></label>
                            <input type="text" class="form-control" id="input-payment-telephone" data-validation="required" mandatory-field num-validation placeholder="<?=Yii::t('app', 'Telephone')?>" name="mobile">
                          </div>
                        </fieldset>
                      </div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4 class="panel-title"><i class="fa fa-book"></i> <?=Yii::t('app', 'Your Address')?></h4>
                </div>
                  <div class="panel-body">
                        <fieldset id="address" class="required">
                          <div class="form-group required">
                            <label for="input-payment-address-1" class="control-label"><?=Yii::t('app', 'Address 1')?></label>
                            <input type="text" class="form-control" id="input-payment-address-1" data-validation="required" mandatory-field placeholder="<?=Yii::t('app', 'Address 1')?>" name="address_1">
                          </div>
                          <div class="form-group">
                            <label for="input-payment-address-2" class="control-label"><?=Yii::t('app', 'Address 2')?></label>
                            <input type="text" class="form-control" id="input-payment-address-2" placeholder="<?=Yii::t('app', 'Address 2')?>" name="address_2">
                          </div>
                          <div class="form-group required">
                            <label for="input-payment-postcode" class="control-label"><?=Yii::t('app', 'Post Code')?></label>
                            <input type="text" class="form-control" id="input-payment-postcode" data-validation="required" mandatory-field placeholder="<?=Yii::t('app', 'Post Code')?>" name="zipcode">
                          </div>
                          <?php
					echo '
								<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'Country').'</label>
							'.Html::dropDownList('country_id',  \multebox\models\DefaultValueModule::getDefaultValueId('country'),
		 ArrayHelper::map(Country::find()->orderBy('country')->where('active=1')->asArray()->all(), 'id', 'country'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'country_id','data-validation'=>'required' ,'mandatory-field'=>'' ]  ).'</div>
							
							<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'State').'</label>
							'.Html::dropDownList('state_id', 'state_id',
		 ArrayHelper::map(State::find()->where('id=0')->orderBy('state')->asArray()->all(), 'id', 'state'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'state_id', 'data-validation'=>'required' ,'mandatory-field'=>'' ]  ).'</div>
						
							<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'City').'</label>
							';/*.Html::dropDownList('city_id', 'city_id',
		 ArrayHelper::map(City::find()->where('id=0')->orderBy('city')->asArray()->all(), 'id', 'city'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'city_id', 'data-validation'=>'required' ,'mandatory-field'=>'' ]  ).'</div>';*/
		 echo AutoComplete::widget([
									  'name' => 'city_id',
									  'clientOptions' => [
										  'source' => [],
									  ],
									  'options' => ['placeholder' => Yii::t ( 'app', 'Type few letters and select from matching list' ), 'data-validation'=>'required' ,'mandatory-field'=>'', 'class' => 'form-control', 'id' => 'city_id']
								  ]).'</div>';
		 ?>
                        </fieldset>
                      </div>
              </div>
			<?php
			}
			else
			{
			?>
			  <div class="panel panel-default">
                <div class="panel-heading">
                  <h4 class="panel-title"><i class="fa fa-map-marker"></i> <?=Yii::t('app', 'Select Shipping Address')?></h4>
                </div>
				<input type="hidden" value="returning" name="account">
				<div class="table-responsive">
				  <table class="table table-bordered table-hover">
				    <div class="col-sm-12">
				<?php
				$address_data = Address::find()->where("entity_type='".Yii::$app->user->identity->entity_type."' and entity_id=".Yii::$app->user->identity->entity_id)->orderBy('is_primary desc')->all();

				
				?>
				  <!--<input type="hidden" name="contact_id" value="<?=$contact_data->id?>">-->
				<?php
				foreach ($address_data as $address)
				{
					$contact_data = Contact::find()->where("entity_type='".Yii::$app->user->identity->entity_type."' and entity_id=".Yii::$app->user->identity->entity_id." and address_id='".$address->id."'")->one();

					if(!$contact_data)
						continue;
				?>
					<tr>
					  <div class="col-sm-9">
						  <td class="text-left">
							<strong><?=$contact_data->first_name?> <?=$contact_data->last_name?></strong><br/>
							<?=$address->address_1?><br/>
							<?=$address->address_2?><br/>
							<?=City::findOne($address->city_id)->city?><br/>
							<?=State::findOne($address->state_id)->state?><br/>
							<?=Country::findOne($address->country_id)->country?> - <?=$address->zipcode?><br/>
							Phone: <?=$contact_data->mobile?><br/>
						  </td>
					  </div>
					  <div class="col-sm-3">
					  <td>
						<div class="text-center">
						  <label>
							<input type="radio" <?=$address->is_primary==1?'checked="checked"':''?> name="shippingaddress" value="<?=$address->id?>">
							</label>
						</div>
					  </td>
					  </div>
					</tr>
			   <?php
			   }

				if (!$address_data)
				{
				?>
				<tr>
					  <div class="col-sm-12">
						  <td class="text-left">
						  <?=Yii::t('app', 'You do not have any saved addresses - Please go to My Account section and add new address.')?>
						  
						  <br><br><a href="<?=Url::to(['/customer/default/account'])?>" ><?=Yii::t('app', 'Take me there')?></a>
						  </td>
					  </div>
				<?php
				}

			   ?>	
					  </div>
			   		</table>
				  </div>
               </div>
			<?php
			}
			?>
            </div>

            <div class="col-sm-8">
              <div class="row">
                <div class="col-sm-6">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title"><i class="fa fa-truck"></i> <?=Yii::t('app', 'Delivery Method')?></h4>
                    </div>
                      <div class="panel-body"><ul>
                        <div>
                          <label>
                            <input type="radio" name="shippingtype" value="FLT" checked> <!-- Flat rate Shipping -->
                            <?=Yii::t('app', 'Flat Shipping Rate - Included')?></label>
                        </div>
                      </ul></div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title"><i class="fa fa-credit-card"></i> <?=Yii::t('app', 'Payment Method')?></h4>
                    </div>
                      <div class="panel-body"><ul>
					  <?php
					  
					  if($digital_ind)
					  {
						  $payment_methods = PaymentMethods::find()->where("active=1")->andWhere("method != '".PaymentMethods::_COD."'")->orderBy('sort_order')->all();
					  }
					  else
					  {
						  $payment_methods = PaymentMethods::find()->where("active=1")->orderBy('sort_order')->all();
					  }
						
					  $num = 0;
					  foreach($payment_methods as $method)
					  {
						  if($method->method == PaymentMethods::_RAZORPAY && (Yii::$app->params['SYSTEM_CURRENCY'] !== 'INR' || (isset($_SESSION['CONVERTED_CURRENCY_CODE']) && $_SESSION['CONVERTED_CURRENCY_CODE'] !== 'INR')))
						  {
							  continue;
						  }

					  ?>
                        <div>
                          <label>
                            <input type="radio" <?=$num==0?'checked="checked"':''?> name="paymentmethod" value="<?=$method->method?>">
                            <?=Yii::t('app', $method->label)?></label>
                        </div>
					  <?php
						$num++;
					  }
					  ?>
                      </ul></div>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title"><i class="fa fa-ticket"></i> <?=Yii::t('app', 'Coupon Code')?></h4>
                    </div>
                      <div class="panel-body discountcoupon">
                        <label for="input-coupon" class="col-sm-3 control-label"><?=Yii::t('app', 'Enter coupon code')?></label>
                        <div class="form-group">
						  <div class="table-responsive">
						    <table>
						      <tr>
						        <td class="col-sm-6">
                                  <input type="text" class="form-control" id="input-coupon" placeholder="<?=Yii::t('app', 'Enter coupon..')?>" name="coupon_code">
						        </td>
						        <td class="col-sm-3">
                                  
                                    <input type="button" class="btn btn-primary" data-loading-text="<?=Yii::t('app', 'Loading...')?>" id="button-coupon" value="<?=Yii::t('app', 'Apply Coupon')?>">
                                  
						        </td>
						      </tr>
						    </table>
						  </div>
						</div>
                      </div> <!---->
                  </div>
                </div>

                <div class="col-sm-12">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title"><i class="fa fa-shopping-cart"></i> <?=Yii::t('app', 'Shopping cart')?></h4>
                    </div>
                      <div class="panel-body">
					  <!-- Shopping cart start -->
                        <div class="table-responsive">
						  <table class="table table-bordered cartcontents">
							<thead>
							  <tr>
								<td class="text-center" style="width:15%;"><?=Yii::t('app', 'Image')?></td>
								<td class="text-left"><?=Yii::t('app', 'Product Name')?></td>
								<td class="text-right"><?=Yii::t('app', 'Quantity')?></td>
								<td class="text-right"><?=Yii::t('app', 'Unit Price')?></td>
								<td class="text-right"><?=Yii::t('app', 'Shipping')?></td>
								<td class="text-right"><?=Yii::t('app', 'Special Discount')?></td>
								<td class="text-right"><?=Yii::t('app', 'Coupon Discount')?></td>
								<td class="text-right"><?=Yii::t('app', 'Total')?></td>
							  </tr>
							</thead>
							<tbody>
							<?php
							$total_cart_price = 0;
							//$global_discount = MulteModel::getGlobalDiscount($cart_items, 0); // 0 since this is before order confirmation
							foreach($cart_items as $cart)
							{
								$inventory_item = Inventory::findOne($cart->inventory_id);
								$prod_title = $inventory_item->product_name;
								$fileDetails = File::find()->where("entity_type='product' and entity_id=".$inventory_item->product_id)->one();
							?>
							  <tr>
								<td class="text-center"><a href="<?=Url::to(['/product/default/detail', 'inventory_id' => $cart->inventory_id])?>"><img src="<?=Yii::$app->params['web_url']?>/<?=$fileDetails->new_file_name?>" alt="<?=$prod_title?>" title="<?=$prod_title?>" class="checkout-img" /></a></td>
								<td class="text-left"><a href="<?=Url::to(['/product/default/detail', 'inventory_id' => $cart->inventory_id])?>"><?=$prod_title?></a><br />
								  <small><?=Yii::t('app', 'Sold By')?>: <?=Vendor::findOne($inventory_item)->vendor_name?></small></td>
								<td class="text-left">
								  <div class="text-right">
									<?=$cart->total_items?>
								  </div>
								</td>
								<td class="text-right"><?=MulteModel::formatAmount(MulteModel::getInventoryActualPrice($inventory_item) - MulteModel::getInventoryDiscountAmount($inventory_item, $cart->total_items))?></td>
								<td class="text-right"><?=MulteModel::formatAmount($inventory_item->shipping_cost*$cart->total_items)?></td>
								<td class="text-right"><?=MulteModel::formatAmount($cart->global_discount_temp)?></td>
								<td class="text-right"><?=MulteModel::formatAmount(0)?></td>
								<td class="text-right"><?=MulteModel::formatAmount(MulteModel::getInventoryTotalAmount($inventory_item, $cart->total_items)*$cart->total_items - $cart->global_discount_temp)?></td>
								<?php
								$total_cart_price += MulteModel::getInventoryTotalAmount($inventory_item, $cart->total_items)*$cart->total_items;
								?>
							  </tr>
							<?php
							}
							?>

							<input type="hidden" name="special_discount" value="<?=$global_discount?>">

							<?php
							if ($global_discount > 0)
							{
							?>
							  <tr>
                                <td class="text-right" colspan="7"><strong><?=Yii::t('app', 'Total Special Discount')?>:</strong></td>
                                <td class="text-right"><?=MulteModel::formatAmount($global_discount)?></td>
                              </tr>
							<?php
							}
							?>
							  <tr>
							    <input type="hidden" name="coupon_discount" value="0">
							    <input type="hidden" name="total_cost" value="<?=$total_cart_price - $global_discount?>">
								<td class="text-right" colspan=7><strong><?=Yii::t('app', 'Total Cart Price')?>:</strong></td>
								<td class="text-right"><?=MulteModel::formatAmount($total_cart_price - $global_discount)?></td>
							  </tr>
							</tbody>
						  </table>
						</div>
						<!-- Shopping cart end -->
                      </div>
                  </div>
                </div>
                <div class="col-sm-12">
				  <div class="pull-left">
					<!--<input type="button" type="submit" class="btn btn-primary" id="button-confirm" value="Confirm Order">-->
					<a href="<?=Url::to(['/order/default/cart'])?>" class="btn btn-info"><?=Yii::t('app', 'Edit Order')?></button></a>
				  </div>
				  <div class="pull-right">
					<!--<input type="button" type="submit" class="btn btn-primary" id="button-confirm" value="Confirm Order">-->
					<button type="submit" class="btn btn-primary" id="button-confirm"><?=Yii::t('app', 'Confirm Order')?></button>
				  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
		</form>
        <!--Middle Part End -->
      </div>
    
  </div>
  