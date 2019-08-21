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
$(document).ready(function(){
	if('<?=!empty($address_modal)?'1':''?>' !='')
	{
		$('.addressmodal').modal('show');
		$('#modal-state').load("<?=Url::to(['/multeobjects/address/ajax-load-states', 'country_id' => $address_modal->country_id, 'state_id' => $address_modal->state_id])?>");

		//$('#modal-city').load("<?=Url::to(['/multeobjects/address/ajax-load-cities', 'state_id' => $address_modal->state_id, 'city_id' => $address_modal->city_id])?>");
		$.post("<?=Url::to(['/multeobjects/address/ajax-load-cities-array'])?>", { 'state_id': '<?=$address_modal->state_id?>', '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#modal-city').autocomplete({"source": $.parseJSON(result)});
				})
	}
});

$(document).on("click", '#addressedit', function(event)
{
	$.post("<?=Url::to(['/customer/default/account'])?>", { 'edit_address_id': $(this).attr('value'), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
		$( "body" ).html(result);
	});
});

$(document).on("click", '#closemodal', function(event)
{
	window.location.replace("<?=Url::to(['/customer/default/account'])?>");
});

$(document).on("click", '#addressadd', function(event)
{
	$('.addressmodal').modal('show');
});

$(document).on("click", '#editdetails', function(event)
{
	$(".firstname").removeAttr('disabled');
	$(".lastname").removeAttr('disabled');
	$(".useremail").removeAttr('disabled');
	$(".updatebutton").removeAttr('disabled');
	$(".cancelbutton").removeAttr('disabled');
});

$(document).on("click", '.cancelbutton', function(event)
{
	$(".firstname").attr('disabled', 0);
	$(".lastname").attr('disabled', 0);
	$(".useremail").attr('disabled', 0);
	$(".updatebutton").attr('disabled', 0);
	$(".cancelbutton").attr('disabled', 0);
});

$(document).on("click", '#button-password', function(event)
{
	<?php
		Url::remember();
	?>

	Remove_Error($('.oldpassword'));
	Remove_Error($('.newpassword'));
	Remove_Error($('.confirmpassword'));

	if($('.oldpassword').val() == '')
	{
		Add_Error($('.oldpassword'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}

	if($('.newpassword').val() == '')
	{
		Add_Error($('.newpassword'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}

	if($('.confirmpassword').val() == '')
	{
		Add_Error($('.confirmpassword'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}
	
	if($('.newpassword').val() != $('.confirmpassword').val())
	{
		Add_Error($('.confirmpassword'),"<?=Yii::t('app','Passwords do not match!')?>");
		event.preventDefault();
	}

});

$(document).on("click", '#button-confirm', function(event)
{
	<?php
		Url::remember();
	?>
	
	Remove_Error($('.firstname'));
	Remove_Error($('.lastname'));
	Remove_Error($('.useremail'));

	if($('.firstname').val() == '')
	{
		Add_Error($('.firstname'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}

	if($('.lastname').val() == '')
	{
		Add_Error($('.lastname'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}

	if($('.useremail').val() == '')
	{
		Add_Error($('.useremail'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}
	else
	{
		var atpos=$('.useremail').val().indexOf("@");
		var dotpos=$('.useremail').val().lastIndexOf(".");

		if (atpos<1 || dotpos<atpos+2 || dotpos+2>=$('.useremail').val().length)
		{
			Add_Error($('.useremail'),"<?=Yii::t('app','Email Address Not Valid!')?>");
			event.preventDefault();
		}
	}
});

$(document).on("change", '#modal-country', function(event){
    $.post("<?=Url::to(['/multeobjects/address/ajax-load-states'])?>", { 'country_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#modal-state').html(result);
					//$('#modal-city').html('<option value=""> --Select--</option>');
					$('#modal-city').val('');
				})
	})

$(document).on("change", '#modal-state', function(event){
    /*$.post("<?=Url::to(['/multeobjects/address/ajax-load-cities'])?>", { 'state_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#modal-city').html(result);
				})*/
	$.post("<?=Url::to(['/multeobjects/address/ajax-load-cities-array'])?>", { 'state_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#modal-city').autocomplete({"source": $.parseJSON(result)});
				})
	})


$(document).on("click", '.update_details', function(event)
{
	Remove_Error($('#modal-firstname'));
	Remove_Error($('#modal-lastname'));
	Remove_Error($('#modal-telephone'));
	Remove_Error($('#modal-address-1'));
	Remove_Error($('#modal-zipcode'));
	Remove_Error($('#modal-country'));
	Remove_Error($('#modal-state'));
	Remove_Error($('#modal-city'));

	if($('#modal-firstname').val() == '')
	{
		Add_Error($('#modal-firstname'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}

	if($('#modal-lastname').val() == '')
	{
		Add_Error($('#modal-lastname'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}

	if($('#modal-telephone').val() == '')
	{
		Add_Error($('#modal-telephone'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}
	else
	{
		if (!$('#modal-telephone').val().match(/^\d+$/))
		{
			Add_Error($('#modal-telephone'),"<?=Yii::t('app','Please enter a valid number!')?>");
			event.preventDefault();
		}
	}

	if($('#modal-address-1').val() == '')
	{
		Add_Error($('#modal-address-1'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}

	if($('#modal-zipcode').val() == '')
	{
		Add_Error($('#modal-zipcode'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}

	if($('#modal-country').val() == '')
	{
		Add_Error($('#modal-country'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}

	if($('#modal-state').val() == '')
	{
		Add_Error($('#modal-state'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}

	if($('#modal-city').val() == '')
	{
		Add_Error($('#modal-city'),"<?=Yii::t('app','This field is required!')?>");
		event.preventDefault();
	}
	
})

</script>
<style>
.ui-front {
    z-index: 2000 !important;
}
</style>
  <div id="container">
    
      <!-- Breadcrumb Start-->
      <ul class="breadcrumb">
        <li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
        <li><a href="<?=Url::to(['/order/default/history'])?>"><?=Yii::t('app', 'Order History')?></a></li>
        <li><?=Yii::t('app', 'Account')?></li>
      </ul>
      <!-- Breadcrumb End-->
      <div class="row">
        <!--Middle Part Start-->
        <div id="content" class="col-sm-12">
          <h1 class="title"><?=Yii::t('app', 'Account Details')?></h1>
          <div class="row">
            <div class="col-sm-3">
			  <div class="panel panel-default">
                <div class="panel-heading">
                  <h4 class="panel-title"><i class="fa fa-map-marker"></i>&nbsp&nbsp <?=Yii::t('app', 'Saved Address')?> &nbsp&nbsp<sup><a href="javascript:void(0)" data-toggle="tooltip" id="addressadd" title="<?=Yii::t('app', 'Add New Address')?>"><i class="fa fa-plus-square-o"></i></a></sup></h4>
                </div>
				<div class="table-responsive">
				  <table class="table table-bordered table-hover">
				    <div class="col-sm-12">
				<?php
				$address_data = Address::find()->where("entity_type='".Yii::$app->user->identity->entity_type."' and entity_id=".Yii::$app->user->identity->entity_id)->orderBy('is_primary desc')->all();
				?>
				<?php
				foreach ($address_data as $address)
				{
					$contact_data = Contact::find()->where("entity_type='".Yii::$app->user->identity->entity_type."' and entity_id=".Yii::$app->user->identity->entity_id." and address_id='".$address->id."'")->one();
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
							<?=Yii::t('app', 'Phone')?>: <?=$contact_data->mobile?><br/>
						  </td>
					  </div>
					  <div class="col-sm-3">
					  <td>
						<div class=" text-center">
						  <a href="javascript:void(0)" data-toggle="tooltip" value="<?=$address->id?>" id="addressedit" title="<?=Yii::t('app', 'Edit Address')?>"><i class="fa  fa-edit"></i></a>
						  <?php
						  if(!$address->is_primary)
						  {
						  ?>
						  <a href="<?=Url::to(['/customer/default/account', 'del_address_id' => $address->id])?>" data-toggle="tooltip" title="<?=Yii::t('app', 'Delete Address')?>" onclick="return confirm('<?=Yii::t ('app','Are you Sure!')?>')"><i class="fa fa-trash-o"></i></a>

						  <a href="<?=Url::to(['/customer/default/account', 'def_address_id' => $address->id])?>" data-toggle="tooltip" title="<?=Yii::t('app', 'Make Default')?>" onclick="return confirm('<?=Yii::t ('app','Are you Sure!')?>')"><i class="fa fa-star-o"></i></a>
						  <?php
						  }
						  ?>
						</div>
					  </td>
					  </div>
					</tr>
			   <?php
			   }
			   ?>	
					  </div>
			   		</table>
				  </div>
               </div>
            </div>

            <div class="col-sm-6">
               <div class="panel panel-default">
                  <div class="panel-heading">
                      <h4 class="panel-title"><i class="fa fa-user"></i> <?=Yii::t('app', 'User Details')?> &nbsp<sup><a href="javascript:void(0)" data-toggle="tooltip" id="editdetails" title="<?=Yii::t('app', 'Edit Details')?>"><i class="fa fa-edit"></i></a></sup></h4>
                  </div>
                  <div class="panel-body">
				  <form method="post" id="userdetailsform" enctype="multipart/form-data">
				  <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
				    <div class="form-group">
						<label class="control-label"><?=Yii::t('app', 'First Name')?></label>
                        <input type="text" class="form-control firstname" name="firstname" disabled value="<?=Yii::$app->user->identity->first_name?>">
				    </div>
					<div class="form-group">
						<label class="control-label"><?=Yii::t('app', 'Last Name')?></label>
                        <input type="text" class="form-control lastname" name="lastname" disabled value="<?=Yii::$app->user->identity->last_name?>">
				    </div>
					<div class="form-group">
						<label class="control-label"><?=Yii::t('app', 'Email')?> <small style="color:red">(<?=Yii::t('app', 'Changing email will change your login user')?>)</small></label>
                        <input type="text" class="form-control useremail" name="useremail" disabled value="<?=Yii::$app->user->identity->email?>">
				    </div>
					<button type="submit" class="btn btn-primary updatebutton" disabled id="button-confirm"><?=Yii::t('app', 'Update')?></button>
					<input type="button" class="btn btn-info cancelbutton" disabled id="button-cancel" value="Cancel">
				  </form>
                  </div>
               </div>
            </div>

			<div class="col-sm-3">
               <div class="panel panel-default">
                  <div class="panel-heading">
                      <h4 class="panel-title"><i class="fa fa-exchange"></i> <?=Yii::t('app', 'Change Password')?></h4>
                  </div>
                  <div class="panel-body">
				  <form method="post" id="passwordform" enctype="multipart/form-data">
				  <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
				    <div class="form-group">
						<label class="control-label"><?=Yii::t('app', 'Old Password')?></label>
                        <input type="password" class="form-control oldpassword" name="oldpassword">
				    </div>
					<div class="form-group">
						<label class="control-label"><?=Yii::t('app', 'New Password')?></label>
                        <input type="password" class="form-control newpassword" name="newpassword">
				    </div>
					<div class="form-group">
						<label class="control-label"><?=Yii::t('app', 'Confirm New Password')?></label>
                        <input type="password" class="form-control confirmpassword" name="confirmpassword">
				    </div>
					<button type="submit" class="btn btn-primary passwordbutton" id="button-password"><?=Yii::t('app', 'Change')?></button>
					<br><small><?=Yii::t('app', 'You need to re-login after password change')?></small>
				  </form>
                  </div>
               </div>
            </div>

		 </div>
      </div>
    </div>
   
 </div>

 <div class="modal addressmodal" data-backdrop="static" data-keyboard="false">
 <form method="post" id="addressform" action=""  enctype="multipart/form-data">
 <input type="hidden" name="modal_address_id" value="<?=$address_modal->id?>">
 <input type="hidden" name="modal_contact_id" value="<?=$contact_modal->id?>">
 <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" id="closemodal" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-book"></i> <?=Yii::t('app', 'Your Details')?></h4>
      </div>
      <div class="modal-body">
   <div class="panel panel-default">

	  <div class="panel-body">
			<fieldset id="address" class="required">
			  <div class="form-group required">
				<label for="modal-firstname" class="control-label"><?=Yii::t('app', 'First Name')?></label>
				<input type="text" class="form-control" value="<?=$contact_modal->first_name?>" id="modal-firstname" placeholder="<?=Yii::t('app', 'First Name')?>" name="modal_firstname">
			  </div>
			  <div class="form-group required">
				<label for="modal-lastname" class="control-label"><?=Yii::t('app', 'Last Name')?></label>
				<input type="text" class="form-control" value="<?=$contact_modal->last_name?>" id="modal-lastname" placeholder="<?=Yii::t('app', 'Last Name')?>" name="modal_lastname">
			  </div>
			  <div class="form-group required">
				<label for="modal-telephone" class="control-label"><?=Yii::t('app', 'Telephone')?></label>
				<input type="text" class="form-control" value="<?=$contact_modal->mobile?>" id="modal-telephone" placeholder="<?=Yii::t('app', 'Telephone')?>" name="mobile">
			  </div>
			  <div class="form-group required">
				<label for="modal-address-1" class="control-label"><?=Yii::t('app', 'Address 1')?></label>
				<input type="text" class="form-control" value="<?=$address_modal->address_1?>" id="modal-address-1" placeholder="<?=Yii::t('app', 'Address 1')?>" name="address_1">
			  </div>
			  <div class="form-group">
				<label for="modal-address-2" class="control-label"><?=Yii::t('app', 'Address 2')?></label>
				<input type="text" class="form-control" value="<?=$address_modal->address_2?>" id="modal-address-2" placeholder="<?=Yii::t('app', 'Address 2')?>" name="address_2">
			  </div>
			  <div class="form-group required">
				<label for="modal-zipcode" class="control-label"><?=Yii::t('app', 'Post Code')?></label>
				<input type="text" class="form-control" value="<?=$address_modal->zipcode?>" id="modal-zipcode" placeholder="<?=Yii::t('app', 'Post Code')?>" name="zipcode">
			  </div>
			  <?php
		echo '
					<div class="form-group required">
						<label class="control-label">'.Yii::t('app', 'Country').'</label>
				'.Html::dropDownList('country_id',  $address_modal->country_id,
ArrayHelper::map(Country::find()->orderBy('country')->where('active=1')->asArray()->all(), 'id', 'country'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'modal-country', 'name' => 'country_id']  ).'</div>
				
				<div class="form-group required">
						<label class="control-label">'.Yii::t('app', 'State').'</label>
				'.Html::dropDownList('state_id', 'state_id',
ArrayHelper::map(State::find()->where('id=0')->orderBy('state')->asArray()->all(), 'id', 'state'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'modal-state', 'name' => 'state_id']  ).'</div>
			
				<div class="form-group required">
						<label class="control-label">'.Yii::t('app', 'City').'</label>
				';/*.Html::dropDownList('city_id', 'city_id',
ArrayHelper::map(City::find()->where('id=0')->orderBy('city')->asArray()->all(), 'id', 'city'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'modal-city', 'name' => 'city_id']  ).'</div>';*/

echo AutoComplete::widget([
									  'name' => 'city_id',
									  'value' => City::findOne($address_modal->city_id)->city,
									  'clientOptions' => [
										  'source' => [],
									  ],
									  'options' => ['placeholder' => Yii::t ( 'app', 'Type few letters and select from matching list' ), 'class' => 'form-control', 'id' => 'modal-city']
								  ]).'</div>';
?>
			</fieldset>
		  </div>
		  </div>	
		    </div>
			<div class="modal-footer">
      	<button type="submit" class="btn btn-primary update_details">
        	<i class="fa fa-save"></i> <?=Yii::t('app', 'Save')?> </button>
      </div>
 </div>
  </div>
  </form>
 </div>