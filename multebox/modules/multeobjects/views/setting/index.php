<?php
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\builder\Form;
use kartik\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use multebox\models\Country;
use multebox\models\State;
use multebox\models\City;
use multebox\models\search\MulteModel;
use multebox\models\TicketPriority;
use multebox\models\TicketImpact;
use multebox\models\Department;
use multebox\models\Queue;
use multebox\models\TicketCategory;
use kartik\datecontrol\DateControl;
use yii\jui\AutoComplete;

/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\Address $searchModel
 */
$this->title = Yii::t ( 'app', 'System Settings' );
$this->params ['breadcrumbs'] [] = $this->title;
?>

<script type="text/javascript">

function loadState()
{
	$('#state_id').load("<?=Url::to(['/multeobjects/address/ajax-load-states', 'country_id' => $addressModel->country_id, 'state_id' => $addressModel->state_id])?>");
}

function loadCity()
{
	//$('#city_id').load("<?=Url::to(['/multeobjects/address/ajax-load-cities', 'state_id' => $addressModel->state_id, 'city_id' => $addressModel->city_id])?>");	
	$.post("<?=Url::to(['/multeobjects/address/ajax-load-cities-array'])?>", { 'state_id': '<?=$addressModel->state_id?>', '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#city_id').autocomplete({"source": $.parseJSON(result)});
				})
}

function loadQueue()
{
	$('#queue_id').load("<?=Url::to(['/support/ticket/ajax-department-queue', 'department_id' => Yii::$app->params['DEFAULT_TICKET_DEPARTMENT'], 'queue_id' => Yii::$app->params['DEFAULT_TICKET_QUEUE']])?>");	
}

function loadCategory(){
	$('#ticket_category_id_1').load("<?=Url::to(['/support/ticket/ajax-ticket-category', 'department_id' => Yii::$app->params['DEFAULT_TICKET_DEPARTMENT'], 'ticket_category_id_1' => Yii::$app->params['DEFAULT_TICKET_CATEGORY']])?>");
}
   

$(document).ready(function(){
	$('body').tooltip({
			selector: '[data-toggle="tooltip"]'
		});

	$('#country_id').change(function(){
    $.post("<?=Url::to(['/multeobjects/address/ajax-load-states'])?>", { 'country_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#state_id').html(result);
					$('#city_id').html('<option value=""> --Select--</option>');
				})
	})

	$('#state_id').change(function(){
    /*$.post("<?=Url::to(['/multeobjects/address/ajax-load-cities'])?>", { 'state_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#city_id').html(result);
				})*/

	$.post("<?=Url::to(['/multeobjects/address/ajax-load-cities-array'])?>", { 'state_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#city_id').autocomplete({"source": $.parseJSON(result)});
				})
	})

	//Auto Load
	loadState();
	loadCity();

	loadCategory();

	$('#department_id').change(function(){
		$('#queue_id').html('');
		$('#ticket_category_id_1').html('') ;
	 $.post("<?=Url::to(['/support/ticket/ajax-department-queue'])?>", { 'department_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(r){
		$('#queue_id').html(r) ;
	 });
	 
	 $.post("<?=Url::to(['/support/ticket/ajax-ticket-category'])?>", { 'department_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(r){
		$('#ticket_category_id_1').html(r) ;
	 });

	});

	loadQueue();

	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			
			reader.onload = function (e) {
				$('.upload').attr('src', e.target.result);
			}
			
			reader.readAsDataURL(input.files[0]);
		}
	}

	function readURL_F(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			
			reader.onload = function (e) {
				$('.upload-f').attr('src', e.target.result);
			}
			
			reader.readAsDataURL(input.files[0]);
		}
	}
	
	
	$(".inp-f").change(function(){
		readURL_F(this);
		ajaxFileUpload_F(this);
		//$('#w0').submit();
	});

	$(".inp").change(function(){
		readURL(this);
		ajaxFileUpload(this);
		//$('#w0').submit();
	});

	$('.upload').click(function(){
		$('.inp').click();
	})

		$('.upload-f').click(function(){
		$('.inp-f').click();
	})

	function ajaxFileUpload(upload_field)
	{
	document.getElementById('picture_preview').innerHTML = '<div><img src="<?=Url::base()?>/loading.gif" style="height:50px;" /></div>';
	upload_field.form.action = '<?=Url::to(['/multeobjects/setting'])?>';
	upload_field.form.target = 'upload_iframe';
	upload_field.form.submit();
	upload_field.form.action = '';
	upload_field.form.target = '';
	setTimeout(function(){
	document.getElementById('picture_preview').innerHTML = '';
	},2500)
	return true;
	}

	function ajaxFileUpload_F(upload_field)
	{
	document.getElementById('picture_preview_f').innerHTML = '<div><img src="<?=Url::base()?>/loading.gif" style="height:50px;" /></div>';
	upload_field.form.action = '<?=Url::to(['/multeobjects/setting'])?>';
	upload_field.form.target = 'upload_iframe';
	upload_field.form.submit();
	upload_field.form.action = '';
	upload_field.form.target = '';
	setTimeout(function(){
	document.getElementById('picture_preview_f').innerHTML = '';
	},2500)
	return true;
	}
});
</script>

<script type="text/javascript">
$(function () {

	if('<?=isset($sent_email)?$sent_email:''?>' !=''){
		setTimeout(function(){
			document.location.href='<?=Url::to(['/multeobjects/setting/index'])?>';
		},1500);
	}
 
})
</script>


<iframe name="upload_iframe" id="upload_iframe" style="display:none;"></iframe>
<div class="logo-index">
	<!--
	<div class="page-header">
		<h1><?= Html::encode($this->title) ?></h1>
	</div>
	-->
    <div class="box box-default">
	    <div class="box-header with-border">
			<div class="box-title">
				<h5><?php echo Yii::t ( 'app', 'System Settings' ); ?> <small class="m-l-sm"><?php echo Yii::t ( 'app', 'Changes will be at application level' ); ?></small></h5>
			</div>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
			</div>
		</div>
    
        <div class="box-body">

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#general" class="general" role="tab" data-toggle="tab"><?php echo Yii::t ( 'app', 'General Settings' ); ?></a>
					</li>
					<li><a href="#smtp" class="smtp" role="tab" data-toggle="tab"><?php echo Yii::t ( 'app', 'SMTP Settings' ); ?></a></li>
					<li><a href="#logo" class="logo" role="tab" data-toggle="tab"><?php echo Yii::t ( 'app', 'Logo Settings' ); ?></a></li>
					<li><a href="#favicon" class="favicon" role="tab" data-toggle="tab"><?php echo Yii::t ( 'app', 'Favicon Settings' ); ?></a></li>
					<li><a href="#payment" class="payment" role="tab" data-toggle="tab"><?php echo Yii::t ( 'app', 'Payment Settings' ); ?></a></li>
					<li><a href="#company" class="company" role="tab" data-toggle="tab"><?php echo Yii::t ( 'app', 'Company Settings' ); ?></a></li>
                 </ul>
				<div class="tab-content">
					<div class="tab-pane active" id="general"> 
						 <br/>
						 
						 <div class="row">
							 <div class="col-sm-12">
							   <form method="post" class="form-horizontal" action="<?=Url::to(['/multeobjects/setting/update'])?>" enctype="multipart/form-data">
								<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
									<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
									  <div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingOne">
										  <h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
											<?php echo Yii::t ( 'app', 'General' ); ?> 
											</a>
										  </h4>
										</div>
										<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
										  <div class="panel-body">
											<div class="form-group">
												<div class="col-sm-3" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['APPLICATION_NAME'.'_description'] )?>">
												<label><?php echo Yii::t ( 'app', 'Application Name' ); ?></label>
												<input type="text" class="form-control" required name="application_name" value="<?=Yii::$app->params['APPLICATION_NAME'] ?>"></div>

												<div class="col-sm-3" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['APPLICATION_SHORT_NAME'.'_description'] )?>">
												<label><?php echo Yii::t ( 'app', 'Application Short Name' ); ?></label>
												<input type="text" class="form-control" required name="application_short_name" value="<?=Yii::$app->params['APPLICATION_SHORT_NAME'] ?>"></div>

												<div class="col-sm-3" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['LOCALE'.'_description']) ?>">
													<label><?php echo Yii::t ( 'app', 'System Language' ); ?></label>
													<select class="form-control   tooltip_btn" name="LOCALE">
														<?php
															foreach($languages as $lang){
														?>
														<option value="<?php echo $lang['locale']?>" <?=Yii::$app->params['LOCALE'] !=$lang['locale']?'':'selected' ?>><?php echo $lang['language']; ?></option>
														<?php } ?>
													</select>
												</div>

												<div class="col-sm-3" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['APPLICATION_VERSION'.'_description']) ?>">
													<label><?php echo Yii::t ( 'app', 'Application Version' ); ?></label>
													<input type="text" name="APPLICATION_VERSION" class="form-control" readonly value="<?=Yii::$app->params['APPLICATION_VERSION'] ?>">
												</div>

												<div class="col-sm-3" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['TIME_ZONE'.'_description']) ?>">
													 <label><?php echo Yii::t ( 'app', 'Time Zone' ); ?></label>
													 <select class="form-control   tooltip_btn" name="TIME_ZONE">
														<?php
															foreach(MulteModel::getTimezoneList() as $key=>$value){
														?>
														<option value="<?php echo $key?>" <?=Yii::$app->params['TIME_ZONE'] !=$key?'':'selected' ?>><?php echo $value; ?></option>
														<?php } ?>
													</select>
												</div>                                                                                    
																					
											</div>
										  </div>
										</div>
									  </div>
									  <div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingTwo">
										  <h4 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
											 <?php echo Yii::t ( 'app', 'Display' ); ?>  
											</a>
										  </h4>
										</div>
										<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
										  <div class="panel-body">
										   <div class="form-group">
												<div class="col-sm-4" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['RTL_THEME'.'_description'] )?>">
													<label><?php echo Yii::t ( 'app', 'RTL Active' ); ?></label>
													<select class="form-control" name="RTL_THEME">
														<option value="No" <?=Yii::$app->params['RTL_THEME'] =='No'?'selected':'' ?>><?=Yii::t('app', 'No')?></option>
														<option value="Yes" <?=Yii::$app->params['RTL_THEME'] =='Yes'?'selected':'' ?>><?=Yii::t('app', 'Yes')?></option>
													</select>
												</div>

												<div class="col-sm-4" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['FRONTEND_THEME_COLOR'.'_description'] )?>">
													<label><?php echo Yii::t ( 'app', 'Frontend Theme Color' ); ?></label>
													<script src="<?=Url::base()?>/plugins/spectrum.js"></script>
													<link rel="stylesheet" href="<?=Url::base()?>/plugins/spectrum.css" />
													<p><input type="text" class="form-control" id="front_theme" name="FRONTEND_THEME_COLOR"></p>
													<script>
														$("#front_theme").spectrum({
															color: "<?=Yii::$app->params['FRONTEND_THEME_COLOR']?>",
															showInput: true,
															preferredFormat: "hex",
														});
													</script>
												</div>

										   </div>
										  </div>
										</div>
									  </div>
									  <div class="panel panel-default">
										<div class="panel-heading" role="tab" id="Communication1">
										  <h4 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#Communication" aria-expanded="false" aria-controls="Communication">
										   <?php echo Yii::t ( 'app', 'Communication' ); ?>    
											</a>
										  </h4>
										</div>
										<div id="Communication" class="panel-collapse collapse" role="tabpanel" aria-labelledby="Communication1">
										  <div class="panel-body">
										   <div class="form-group">
												<div class="col-sm-6" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['SYSTEM_EMAIL'.'_description'] )?>">
												<label><?php echo Yii::t ( 'app', 'System Email' ); ?></label>
												<input type="text" class="form-control" required name="system_email" value="<?=Yii::$app->params['SYSTEM_EMAIL'] ?>"></div>
										   </div>
										  </div>
										</div>
									  </div>

									  <div class="panel panel-default">
										<div class="panel-heading" role="tab" id="Currency1">
										  <h4 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#Currency" aria-expanded="false" aria-controls="Currency">
										   <?php echo Yii::t ( 'app', 'System Currency' ); ?>    
											</a>
										  </h4>
										</div>
										<div id="Currency" class="panel-collapse collapse" role="tabpanel" aria-labelledby="Currency">
										  <div class="panel-body">
										   <div class="form-group">
												<div class="col-sm-6" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['SYSTEM_CURRENCY'.'_description'] )?>">
													<label><?php echo Yii::t ( 'app', 'System Currency' ); ?></label>
													<select class="form-control   tooltip_btn" name="SYSTEM_CURRENCY">
														<?php
															foreach($currencies as $currency){
														?>
														<option value="<?php echo $currency['currency_code']?>" <?=Yii::$app->params['SYSTEM_CURRENCY'] !=$currency['currency_code']?'':'selected' ?>><?php echo $currency['currency_name']." (".$currency['currency_symbol'].") - ".$currency['currency_code']; ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
										  </div>
									    </div>
									  </div>

									   <div class="panel panel-default">
										<div class="panel-heading" role="tab" id="shipment1">
										  <h4 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#shipment" aria-expanded="false" aria-controls="shipment">
											 <?php echo Yii::t ( 'app', 'Order Shipment' ); ?>  
											</a>
										  </h4>
										</div>
										<div id="shipment" class="panel-collapse collapse" role="tabpanel" aria-labelledby="shipment1">
										  <div class="panel-body">
										   <div class="form-group">
												<div class="col-sm-4" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['VENDOR_SHIP_COD'.'_description'] )?>">
													<label><?php echo Yii::t ( 'app', 'Allow Vendor To Ship COD Orders' ); ?></label>
													<select class="form-control" name="VENDOR_SHIP_COD">
														<option value="No" <?=Yii::$app->params['VENDOR_SHIP_COD'] =='No'?'selected':'' ?>><?=Yii::t('app', 'No')?></option>
														<option value="Yes" <?=Yii::$app->params['VENDOR_SHIP_COD'] =='Yes'?'selected':'' ?>><?=Yii::t('app', 'Yes')?></option>
													</select>
												</div>
										   </div>

										   <div class="form-group">
												<div class="col-sm-4" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['VENDOR_SHIP_ALL'.'_description'] )?>">
													<label><?php echo Yii::t ( 'app', 'Allow Vendor To Ship All Orders (Other Than COD)' ); ?></label>
													<select class="form-control" name="VENDOR_SHIP_ALL">
														<option value="No" <?=Yii::$app->params['VENDOR_SHIP_ALL'] =='No'?'selected':'' ?>><?=Yii::t('app', 'No')?></option>
														<option value="Yes" <?=Yii::$app->params['VENDOR_SHIP_ALL'] =='Yes'?'selected':'' ?>><?=Yii::t('app', 'Yes')?></option>
													</select>
												</div>
										   </div>
										  </div>
										</div>
									  </div>

									  <div class="panel panel-default">
										<div class="panel-heading" role="tab" id="Support_Email_Settings">
										  <h4 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#Support_Email_Settings1" aria-expanded="false" aria-controls="Misc1">
											 <?php echo Yii::t ( 'app', 'Tickets Settings' ); ?> 
											</a>
										  </h4>
										</div>
                                        <div id="Support_Email_Settings1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="Support_Email_Settings">
										  <div class="panel-body">
										   <div class="form-group">
												<div class="col-sm-4" data-container="body" data-toggle="hover" data-placement="top" data-content="<?=Yii::t ( 'app', Yii::$app->params['DEFAULT_TICKET_PRIORITY'.'_description']) ?>">
													<label><?php echo Yii::t ( 'app', 'Default Ticket Priority' ); ?></label>
													<select class="form-control" name="DEFAULT_TICKET_PRIORITY">
														<?php
															foreach(TicketPriority::find()->all() as $row){
														?>
														<option value="<?php echo $row->id?>" <?=Yii::$app->params['DEFAULT_TICKET_PRIORITY'] !=$row->id?'':'selected' ?>><?php echo $row->label; ?></option>
														<?php } ?>
													</select>
												
												</div>

												<div class="col-sm-4" data-container="body" data-toggle="hover" data-placement="top" data-content="<?=Yii::t ( 'app', Yii::$app->params['DEFAULT_TICKET_IMPACT'.'_description']) ?>">
													<label><?php echo Yii::t ( 'app', 'Default Ticket Impact' ); ?></label>
													<select class="form-control" name="DEFAULT_TICKET_IMPACT">
														<?php
															foreach(TicketImpact::find()->all() as $row){
														?>
														<option value="<?php echo $row->id?>" <?=Yii::$app->params['DEFAULT_TICKET_IMPACT'] !=$row->id?'':'selected' ?>><?php echo $row->label; ?></option>
														<?php } ?>
													</select>
												</div>

												<div class="col-sm-4" data-container="body" data-toggle="hover" data-placement="top" data-content="<?=Yii::t ( 'app', Yii::$app->params['DEFAULT_TICKET_DEPARTMENT'.'_description']) ?>">
													<label><?php echo Yii::t ( 'app', 'Default Department' ); ?></label>
													
													<?=Html::dropDownList('DEFAULT_TICKET_DEPARTMENT',Yii::$app->params['DEFAULT_TICKET_DEPARTMENT'], 	 ArrayHelper::map(Department::find()->orderBy('id')->asArray()->all(), 'id', 'label'), ['prompt' => '--Department--','class'=>'form-control','id'=>'department_id','data-validation'=>'required']  )?>
												
												</div>

												<div class="col-sm-4" data-container="body" data-toggle="hover" data-placement="top" data-content="<?=Yii::t ( 'app', Yii::$app->params['DEFAULT_TICKET_QUEUE'.'_description']) ?>">
													<label><?php echo Yii::t ( 'app', 'Default Queue' ); ?></label>
													
													
													<?=Html::dropDownList('DEFAULT_TICKET_QUEUE',Yii::$app->params['DEFAULT_TICKET_QUEUE'], ArrayHelper::map(Queue::find()->where('id=0')->asArray()->all(), 'id', 'queue_title'), ['prompt' => '--Queue--','class'=>'form-control','id'=>'queue_id','data-validation'=>'required']  )?>

												</div>

												<div class="col-sm-4" data-container="body" data-toggle="hover" data-placement="top" data-content="<?=Yii::t ( 'app', Yii::$app->params['DEFAULT_TICKET_CATEGORY'.'_description']) ?>">
													<label><?php echo Yii::t ( 'app', 'Default Ticket Category' ); ?></label>


													<?=Html::dropDownList('DEFAULT_TICKET_CATEGORY',Yii::$app->params['DEFAULT_TICKET_CATEGORY'], ArrayHelper::map(TicketCategory::find()->where('id=0')->asArray()->all(), 'id', 'label'), ['prompt' => '--Ticket Category 1--' , 'class'=>'form-control','id'=>'ticket_category_id_1','data-validation'=>'required']  )?>
												</div>
											</div>
										  </div>
										</div>
									  </div>
									  
									  <div class="panel panel-default">
										<div class="panel-heading" role="tab" id="Misc">
										  <h4 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#Misc1" aria-expanded="false" aria-controls="Misc1">
											 <?php echo Yii::t ( 'app', 'Misc' ); ?> 
											</a>
										  </h4>
										</div>
										<div id="Misc1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="Misc">
										  <div class="panel-body">
										   <div class="form-group">
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['FILE_SIZE'.'_description']) ?>">
												<label><?php echo Yii::t ( 'app', 'Maximum Size (File Upload)' ); ?></label>
												<select class="form-control" name="FILE_SIZE">
													<option <?=Yii::$app->params['FILE_SIZE'] !='5'?'':'selected' ?> value="5">5MB</option>
													<option <?=Yii::$app->params['FILE_SIZE'] !='20'?'':'selected' ?> value="20">20MB</option>
													<option <?=Yii::$app->params['FILE_SIZE'] !='100'?'':'selected' ?> value="100">100MB</option>
													<option <?=Yii::$app->params['FILE_SIZE'] !='0'?'':'selected' ?> value="0"><?php echo Yii::t ( 'app', 'No Limit' ); ?></option>
												</select>
												</div>

												<div class="col-sm-4" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['ALLOW_VENDOR_REGISTRATION'.'_description'] )?>">
													<label><?php echo Yii::t ( 'app', 'Allow Vendor Registration From Frontend' ); ?></label>
													<select class="form-control" name="ALLOW_VENDOR_REGISTRATION">
														<option value="No" <?=Yii::$app->params['ALLOW_VENDOR_REGISTRATION'] =='No'?'selected':'' ?>><?=Yii::t('app', 'No')?></option>
														<option value="Yes" <?=Yii::$app->params['ALLOW_VENDOR_REGISTRATION'] =='Yes'?'selected':'' ?>><?=Yii::t('app', 'Yes')?></option>
													</select>
												</div>

												<div class="col-sm-4" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['HOT_DEAL_END_DATE'.'_description'] )?>">
													<label><?php echo Yii::t ( 'app', 'Hot Deal End Date' ); ?></label>
													<!--<input type="text" class="form-control" required name="hot_deal_end_date" value="<?=Yii::$app->params['HOT_DEAL_END_DATE'] ?>">-->

													<?php
														echo DateControl::widget([
															'name'=>'hot_deal_end_date',
															'id' => 'hot_deal_end_date',
															'value'=> date('Y/m/d H:i:s', Yii::$app->params['HOT_DEAL_END_DATE']),
															'type'=>DateControl::FORMAT_DATETIME,
															'displayFormat' => 'dd/MM/yyyy H:i:s',
														]);
													?>
												</div>

											</div>
										  </div>
										</div>
									  </div>
									</div>
							
								<div class="form-group">
								<div class="col-sm-4"><input type="submit" value="<?php echo Yii::t ( 'app', 'Update' ); ?>" class="btn btn-primary btn-sm"></div></div>
									
							   </form>
							 </div>
						 </div>
					</div>
					<div class="tab-pane" id="smtp"> 
				
						 <br/>
							<?php
							if(!empty($sent_email)){
							?>
								<div class="alert alert-success"><?=$sent_email?>	</div>
							<?php } ?>
						 <div class="row">
							<div class="col-sm-12">
							   <form method="post" class="form-horizontal" action="<?=Url::to(['/multeobjects/setting/update'])?>" enctype="multipart/form-data">
								  <?php Yii::$app->request->enableCsrfValidation = true; ?>
									 <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'SMTP Enable' ); ?></label>
											<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['SMTP_AUTH'.'_description']) ?>">
												<select class="form-control" name="SMTP_AUTH">
													<option value="No" <?=Yii::$app->params['SMTP_AUTH'] =='No'?'selected':'' ?>><?=Yii::t('app', 'No')?></option>
													<option value="Yes" <?=Yii::$app->params['SMTP_AUTH'] =='Yes'?'selected':'' ?>><?=Yii::t('app', 'Yes')?></option>
												 </select>
										 
												<em><?php echo Yii::t ( 'app', 'Notes: if using google SMTP follow these instructions' ); ?> <a href="https://support.google.com/a/answer/176600?hl=en" target="_blank"><?php echo Yii::t ( 'app', 'here' ); ?></a></em>
										
											  </div>
										</div>
										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'SMTP Host' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['SMTP_HOST'.'_description']) ?>">
													<input type="text" class="form-control"  name="SMTP_HOST" value="<?=Yii::$app->params['SMTP_HOST'] ?>" placeholder="smtp.gmail.com">
												</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'SMTP Username' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['SMTP_USERNAME'.'_description']) ?>">
													<input type="text" class="form-control" name="SMTP_USERNAME" value="<?=Yii::$app->params['SMTP_USERNAME'] ?>" placeholder="Your username">
												</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'SMTP Password' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['SMTP_PASSWORD'.'_description']) ?>">
													<input type="password" class="form-control" name="SMTP_PASSWORD" value="**********" placeholder="Your password">
												</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'SMTP Port' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['SMTP_PORT'.'_description']) ?>">
													<input type="text" class="form-control" name="SMTP_PORT" value="<?=Yii::$app->params['SMTP_PORT'] ?>">
												</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'SMTP Encryption' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['SMTP_ENCRYPTION'.'_description']) ?>">
													<select class="form-control" name="SMTP_ENCRYPTION">
														<option value="No"  <?=Yii::$app->params['SMTP_ENCRYPTION'] =='No'?'selected':'' ?>><?=Yii::t('app', 'No Encryption')?></option>
														<option value="ssl"  <?=Yii::$app->params['SMTP_ENCRYPTION'] =='ssl'?'selected':'' ?>>SSL</option>
														<option value="tls"  <?=Yii::$app->params['SMTP_ENCRYPTION'] =='tls'?'selected':'' ?>>TLS</option>
													</select>
									
												</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2"></label>
											<div class="col-sm-2"><input type="submit" value="<?php echo Yii::t ( 'app', 'Update' ); ?>" class="btn btn-primary btn-sm"> </div>
											
											<div class="col-sm-2"><a href="<?=Url::to(['/multeobjects/setting/index', 'email_send' => 'true'])?>" class="btn btn-primary "><?php echo Yii::t ( 'app', 'Test Email Send' ); ?></a> </div>
											
										</div>
								</form>
							</div>
						</div>
					</div>
					
					<div class="tab-pane fade" id="logo"> 
						<br/>	
						 <form method="post" id="frm_logo" enctype="multipart/form-data">
							<?php Yii::$app->request->enableCsrfValidation = true; ?>
							<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
							<div class="row col-sm-8">
								<em class="col-sm-12" style="color:red;">
								<?=Yii::t('app', 'Please hard refresh your browser using keys Ctrl+F5 to refresh browser cache so that changed logos are immediately visible!') ?>
								</em>
							</div><br><br>
							<div class="row">
								<div class="col-sm-8">
									<div class="form-group">
										<label class="col-sm-3"><?php echo Yii::t ( 'app', 'Backend Logo' ); ?></label>
										<input type="file" class="form-control inp" name="logo">
									</div>
								</div>
								<div class="col-sm-4">
									<div id="picture_preview"></div>
									<img src="<?=Yii::$app->params['web_url']?>/logo/back_logo.png" class="img-responsive upload" style="max-height:200px;">
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-sm-8">
									<div class="form-group">
										<label class="col-sm-3"><?php echo Yii::t ( 'app', 'Frontend Logo' ); ?></label>
										<input type="file" class="form-control inp-f" name="logo_f">
									</div>
								</div>
								<div class="col-sm-4">
									<div id="picture_preview_f"></div>
									<img src="<?=Yii::$app->params['web_url']?>/logo/front_logo.png" class="img-responsive upload-f" style="max-height:200px;">
								</div>
							</div>
							<br/><br/>
						<?= Html::submitButton(Yii::t ( 'app', 'Update' ), ['class' => 'btn btn-primary btn-sm']) ?>
						</form>     
					</div>

					<div class="tab-pane fade" id="favicon"> 
						<br/>	
						 <form method="post" id="frm_favicon" enctype="multipart/form-data">
							<?php Yii::$app->request->enableCsrfValidation = true; ?>
							<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
							<div class="row col-sm-8">
								<em class="col-sm-12" style="color:red;">
								<?=Yii::t('app', 'Please hard refresh your browser using keys Ctrl+F5 to refresh browser cache so that changed favicons are immediately visible!') ?>
								</em>
							</div><br><br>
							<div class="row">
								<div class="col-sm-8">
									<div class="form-group">
										<label class="col-sm-3"><?php echo Yii::t ( 'app', 'Backend Favicon' ); ?></label>
										<input type="file" class="form-control inp" name="favicon">
									</div>
								</div>
								<div class="col-sm-4">
									<div id="picture_preview"></div>
									<img src="<?=Yii::$app->params['web_url']?>/logo/back_favicon.ico" class="img-responsive upload" style="max-height:200px;">
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-sm-8">
									<div class="form-group">
										<label class="col-sm-3"><?php echo Yii::t ( 'app', 'Frontend Favicon' ); ?></label>
										<input type="file" class="form-control inp-f" name="favicon_f">
									</div>
								</div>
								<div class="col-sm-4">
									<div id="picture_preview_f"></div>
									<img src="<?=Yii::$app->params['web_url']?>/logo/front_favicon.ico" class="img-responsive upload-f" style="max-height:200px;">
								</div>
							</div>
							<br/><br/>
						<?= Html::submitButton(Yii::t ( 'app', 'Update' ), ['class' => 'btn btn-primary btn-sm']) ?>
						</form>     
					</div>
				   
					<div class="tab-pane" id="payment"> 
						 <br/>
							 <div class="row">
								<div class="col-sm-12">
									 <form method="post" class="form-horizontal" action="<?=Url::to(['/multeobjects/setting/update'])?>" enctype="multipart/form-data">
										<?php Yii::$app->request->enableCsrfValidation = true; ?>
										<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'BitPay Pairing Code (For Bitcoin Payments)' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['BITPAY_PAIRING_CODE'.'_description']) ?>">
													<input type="text" class="form-control"  name="BITPAY_PAIRING_CODE" value="<?=Yii::$app->params['BITPAY_PAIRING_CODE'] ?>" placeholder="<?=Yii::t('app', 'Enter Code')?>">

													<em><?php echo Yii::t ( 'app', 'To accept Bitcoin payments with BitPay Payment Gateway, follow the instructions' ); ?> <a href="javascript:void(0)" onClick="show_bitpay_help()"><?php echo Yii::t ( 'app', 'here' ); ?></a></em>

												</div>
										</div>

										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'BitPay Demo Mode' ); ?></label>
												<div class="col-sm-4" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['BITPAY_DEMO_MODE'.'_description'] )?>">
													<select class="form-control" name="BITPAY_DEMO_MODE">
														<option value="No" <?=Yii::$app->params['BITPAY_DEMO_MODE'] =='No'?'selected':'' ?>><?=Yii::t('app', 'No')?></option>
														<option value="Yes" <?=Yii::$app->params['BITPAY_DEMO_MODE'] =='Yes'?'selected':'' ?>><?=Yii::t('app', 'Yes')?></option>
													</select>
												</div>
										</div>

										<!--<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'BitPay Private Key' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['BITPAY_PRIVATE_KEY'.'_description']) ?>">
													<input type="password" class="form-control"  name="BITPAY_PRIVATE_KEY" value="**********" placeholder="" readonly>
												</div>
										</div>-->

										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'Paypal API ID' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['PAYPAL_API_ID'.'_description']) ?>">
													<input type="text" class="form-control"  name="PAYPAL_API_ID" value="<?=Yii::$app->params['PAYPAL_API_ID'] ?>" placeholder="">
												</div>
										</div>

										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'Paypal Secret ID' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['PAYPAL_SECRET_ID'.'_description']) ?>">
													<input type="password" class="form-control"  name="PAYPAL_SECRET_ID" value="**********" placeholder="">
												</div>
										</div>

										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'PayPal Demo Mode' ); ?></label>
												<div class="col-sm-4" data-container="body" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['IS_DEMO'.'_description'] )?>">
													<select class="form-control" name="IS_DEMO">
														<option value="No" <?=Yii::$app->params['IS_DEMO'] =='No'?'selected':'' ?>><?=Yii::t('app', 'No')?></option>
														<option value="Yes" <?=Yii::$app->params['IS_DEMO'] =='Yes'?'selected':'' ?>><?=Yii::t('app', 'Yes')?></option>
													</select>
												</div>
										</div>

										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'Stripe Publishable Key' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['STRIPE_PUBLISHABLE_KEY'.'_description']) ?>">
													<input type="text" class="form-control"  name="STRIPE_PUBLISHABLE_KEY" value="<?=Yii::$app->params['STRIPE_PUBLISHABLE_KEY'] ?>" placeholder="">
												</div>
										</div>

										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'Stripe Secret Key' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['STRIPE_SECRET_KEY'.'_description']) ?>">
													<input type="password" class="form-control"  name="STRIPE_SECRET_KEY" value="**********" placeholder="">
												</div>
										</div>

										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'RazorPay API Key' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['RAZORPAY_API_KEY'.'_description']) ?>">
													<input type="text" class="form-control"  name="RAZORPAY_API_KEY" value="<?=Yii::$app->params['RAZORPAY_API_KEY'] ?>" placeholder="">
												</div>
										</div>

										<div class="form-group">
											<label class="col-sm-2"><?php echo Yii::t ( 'app', 'RazorPay Secret Key' ); ?></label>
												<div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="<?=Yii::t ( 'app', Yii::$app->params['RAZORPAY_SECRET_KEY'.'_description']) ?>">
													<input type="password" class="form-control"  name="RAZORPAY_SECRET_KEY" value="**********" placeholder="">
												</div>
										</div>
								
										<div class="form-group">
											<label class="col-sm-2"></label>
												<div class="col-sm-2"><input type="submit" value="<?php echo Yii::t ( 'app', 'Update' ); ?>" class="btn btn-primary btn-sm"> </div>
										</div>
									</form>
								</div>
							</div>
					</div>

					<div class="tab-pane" id="company"> 
						<br/>			
						 <div class="company-form">
							<?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_VERTICAL]); 
							
							?>
							<div class="panel panel-info">
								<div class="panel-heading">
									<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Company Detail' ); ?></h3>
								</div>
								<div class="panel-body">
									<?php
									echo Form::widget([
														'model' => $companyModel,
														'form' => $form,
														'columns' => 2,
														'attributes' => [
														'company_name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Company Name...', 'maxlength'=>255]], 
														'company_email'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Company Email...', 'maxlength'=>255]], 
														'phone'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Company Phone...', 'maxlength'=>255]], 
														'mobile'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Company Mobile...', 'maxlength'=>255]], 
														'fax'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Company Fax...', 'maxlength'=>255]], 
														]
													]);?>
								</div>
							</div>
							<div class="panel panel-info">
								<div class="panel-heading">
									<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Address Detail' ); ?></h3>
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label"><?php echo Yii::t ( 'app', 'Address 1' ); ?></label>
												<input type="text" name="address_1" value="<?=$addressModel->address_1?>" data-validation="required" mandatory-field class="form-control">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label"><?php echo Yii::t ( 'app', 'Address 2' ); ?></label>
												<input type="text" name="address_2" value="<?=$addressModel->address_2?>" class="form-control">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label"><?php echo Yii::t ( 'app', 'Zipcode' ); ?></label>
												<input type="text" name="zipcode" data-validation="required" mandatory-field value="<?=$addressModel->zipcode?>" class="form-control">
											</div>
										</div>
									</div>
									<?php
									echo '<div class="row">
											<div class="col-sm-4">
												<div class="form-group required">
													<label class="control-label">'.Yii::t ( 'app', 'Country' ).'</label>
											'.Html::dropDownList('country_id',$addressModel->country_id,
											 ArrayHelper::map(Country::find()->orderBy('country')->asArray()->all(), 'id', 'country'), ['prompt' => '--Select--','class'=>'form-control','id'=>'country_id','data-validation'=>'required', 'mandatory-field' => '' ]  ).'</div></div>
																<div class="col-sm-4">
																<div class="form-group required">
																		<label class="control-label">'.Yii::t ( 'app', 'State' ).'</label>
																'.Html::dropDownList('state_id',$addressModel->state_id,
											 ArrayHelper::map(State::find()->where('id=0')->orderBy('state')->asArray()->all(), 'id', 'state'), ['prompt' => '--Select--','class'=>'form-control','id'=>'state_id','data-validation'=>'required', 'mandatory-field' => '' ]  ).'</div></div>
															<div class="col-sm-4">
																<div class="form-group required">
																		<label class="control-label">'.Yii::t ( 'app', 'City' ).'</label>
																';/*.Html::dropDownList('city_id',$addressModel->city_id,
											 ArrayHelper::map(City::find()->where('id=0')->orderBy('city')->asArray()->all(), 'id', 'city'), ['prompt' => '--Select--','class'=>'form-control','id'=>'city_id' ]  ).'</div></div></div>';*/

											 echo AutoComplete::widget([
												  'name' => 'city_id',
												  'value' => City::findOne($addressModel->city_id)->city,
												  'clientOptions' => [
													  'source' => [],
												  ],
												  'options' => ['placeholder' => Yii::t ( 'app', 'Type few letters and select from matching list' ), 'class' => 'form-control', 'id' => 'city_id', 'data-validation'=>'required' ,'mandatory-field'=>'']
											  ]).'</div></div></div>';
											
											echo Html::submitButton($companyModel->isNewRecord ? Yii::t ( 'app', 'Create' ) : Yii::t ( 'app', 'Update' ), ['class' => $companyModel->isNewRecord ? 'btn btn-success btn-sm company_submit' : 'btn btn-primary company_submit btn-sm']);
											ActiveForm::end(); ?>

								</div> <!-- Panel Body-->    
							</div> <!-- Panel Info -->
						</div>
                    </div>
				</div>
            </div>
		</div>

<script>
function show_bitpay_help()
{
	$('.bitpayhelp').modal('show');
}
</script>

<div class="modal bitpayhelp">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?=Yii::t('app', 'Setup BitPay Account')?></h4>
		  </div>

		  <div class="modal-body">
				  <ol>
					<li><?=Yii::t('app', 'Signup for a new BitPay Business Account by visiting')?> <a href="https://bitpay.com/get-started"><?=Yii::t('app', 'here')?></a> </li>
					<li><?=Yii::t('app', 'Confirm your email')?></li>
					<li><?=Yii::t('app', 'Fill up your business information and submit')?></li>
					<li><?=Yii::t('app', 'Add your bank account or bitcoin wallet adress where you wish to receive funds')?></li>
					<li><?=Yii::t('app', 'After all steps are completed you can start accepting bitcoin payments')?></li>
					<li><?=Yii::t('app', 'When asked - How would you like to accept bitcoin - select Point of Sale Method')?></li>
					<li><?=Yii::t('app', 'Provide email address where you wish to receive payment confirmations and click save changes')?></li>
					<li><?=Yii::t('app', 'Click on Payment Tools option from left side menu')?></li>
					<li><?=Yii::t('app', 'Scroll down to Manage API tokens - or click')?> <a href="https://bitpay.com/dashboard/merchant/api-tokens"><?=Yii::t('app', 'here')?></a> </li>
					<li><?=Yii::t('app', 'Add a new API token by clicking Add New Token button')?></li>
					<li><?=Yii::t('app', 'Provide any label of your choice and click on Add Token')?></li>
					<li><?=Yii::t('app', 'You will get a pairing code that you need to enter into the system')?></li>
					<li><?=Yii::t('app', 'That is it - Now all payments made using Bitcoin checkout method will appear in your BitPay account!')?></li>
				  </ol>
		  </div>

		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
