<?php
use multebox\models\search\MulteModel;
use multebox\models\search\Customer;
use yii\helpers\Html;
use multebox\models\FileModel;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use multebox\models\Country;
use multebox\models\State;
use multebox\models\City;
use multebox\models\User;
use kartik\widgets\DepDrop;
use kartik\datecontrol\DateControl;
use multebox\models\CustomerType;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var common\models\Project $model
 */
if(isset($_REQUEST['err_msg']))
{
	?>
	<script>
	alert("<?=$_REQUEST['err_msg']?>");
	</script>
	<?php
}
$this->title = Yii::t('app', 'Update Customer').' : ' . $model->customer_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
if(isset($_GET['msg']))
$msgBox = $_GET['msg'];

if($model->isNewRecord)
{
	$dFlag = false;
}
else
{
	$dFlag = true;
}
?>

<script>

function loadState()
{
	$('#state_id').load("<?=Url::to(['/multeobjects/address/ajax-load-states', 'country_id' => $addressModel->country_id, 'state_id' => $addressModel->state_id])?>");
}

function loadCity()
{
	$('#city_id').load("<?=Url::to(['/multeobjects/address/ajax-load-cities', 'state_id' => $addressModel->state_id, 'city_id' => $addressModel->city_id])?>");	
}
   
$(document).ready(function(){
	if('<?=!empty($_REQUEST['attach_update'])?$_REQUEST['attach_update']:''?>' !=''){
		$('.popup').modal('show');
	}
	if('<?=!empty($_GET['note_id'])?$_GET['note_id']:''?>' !=''){
		$('.edit-notes-modal').modal('show');
	}
	if('<?=!empty($_GET['contact_edit'])?$_GET['contact_edit']:''?>' !=''){
		$('.contactae').modal('show');
	}
	if('<?=!empty($_GET['address_edit'])?$_GET['address_edit']:''?>' !=''){
		$('.addressae').modal('show');
		
		$('#sub_state_id').load("<?=Url::to(['/multeobjects/address/ajax-load-states', 'country_id' => $sub_address_model->country_id, 'state_id' => $sub_address_model->state_id])?>");
		//$('#sub_city_id').load("<?=Url::to(['/multeobjects/address/ajax-load-cities', 'state_id' => $sub_address_model->state_id, 'city_id' => $sub_address_model->city_id])?>");	
		$.post("<?=Url::to(['/multeobjects/address/ajax-load-cities-array'])?>", { 'state_id': '<?=$sub_address_model->state_id?>', '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#sub_city_id').autocomplete({"source": $.parseJSON(result)});
				})
	}

	$('#sub_country_id').change(function(){
    $.post("<?=Url::to(['/multeobjects/address/ajax-load-states'])?>", { 'country_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#sub_state_id').html(result);
					$('#sub_city_id').html('<option value=""> --Select--</option>');
				})
	})

	$('#sub_state_id').change(function(){
    $.post("<?=Url::to(['/multeobjects/address/ajax-load-cities'])?>", { 'state_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#sub_city_id').html(result);
				})
	})

	//Auto Load
	loadState();
	loadCity();

function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		
		reader.onload = function (e) {
			$('.upload').attr('src', e.target.result);
		}
		
		reader.readAsDataURL(input.files[0]);
	}
}
$(".inp").change(function(){
	readURL(this);
	ajaxFileUpload(this);
});
$('.upload').click(function(){
	$('.inp').click();
});

function ajaxFileUpload(upload_field)
{
	document.getElementById('picture_preview').innerHTML = '<div><img src="<?=Url::base()?>/loading.gif" style="height:50px;" /></div>';
	upload_field.form.action = "<?=Url::to(['/customer/customer/view', 'id' => $_GET['id']])?>";
	upload_field.form.target = 'upload_iframe';
	upload_field.form.submit();
	upload_field.form.action = '';
	upload_field.form.target = '';
	setTimeout(function(){
	document.getElementById('picture_preview').innerHTML = '';
	},2500)
	return true;
}

if('<?= isset($msgBox)?$msgBox:''?>' != ''){
		setTimeout(function(){
			document.location.href="<?=Url::to(['/customer/customer/view', 'id' => $_GET['id']])?>";
		},2000);
	}

});
</script>
<?php
if(!empty($msgBox)){?>
	<div class="alert alert-success"><?=$msgBox?></div>
<?php }
?>

<iframe name="upload_iframe" id="upload_iframe" style="display:none;"></iframe>
 <?php $form = ActiveForm::begin ( [ 
						'type' => ActiveForm::TYPE_VERTICAL , 
  						'options'=>array('enctype' => 'multipart/form-data')
				] );?>
<div class="panel panel-info">
	<div class="panel-heading">
    	<h3 class="panel-title"><?php echo Yii::t('app', 'Customer'); ?> - <?=$model->customer_name?>
        	<div class="pull-right">
                <a class="close" href="<?=Url::to(['/customer/customer/index'])?>" >
                	<span class="glyphicon glyphicon-remove"></span>
                </a>
            </div>
        </h3>
    </div>
    <div class="panel-body">
        	<div class="customer-update">
        		<div class="row">
                	<div class="col-sm-9">
						<?=  Form::widget ( [ 
                             'model' => $model,
                             'form' => $form,
                             'columns' => 4,
                             'attributes' => [ 
                                     'customer_name' => [ 
			                                         'type' => Form::INPUT_TEXT,
													 'label' => 'Login Username',
		                                            'options' => [ 
													'placeholder' => Yii::t('app','Enter User Name...'),
													'maxlength' => 255,
													'disabled' => $dFlag
											],
    
                                            'columnOptions' => [ 
													'colspan' => 3 
											] 
										]
                            ]
                        ]
                   );?>
                     <?=  Form::widget ( [ 
                            'model' => $model,
							'form' => $form,
							'columns' => 4,
							'attributes' => [ 
									'customer_type_id' => [ 
											'type' => Form::INPUT_DROPDOWN_LIST,
											'options' => [ 
													'prompt' => '--'.Yii::t('app','Customer Type').'--',
                                                    'placeholder' => 'Enter Customer Type...' 
											],
    
                                            'items' => ArrayHelper::map ( CustomerType::find ()->orderBy ( 'sort_order' )->asArray ()->all (), 'id', 'label' )
                                    ]
                                ] 
                        ]
                     );?>
					 <?=  Form::widget ( [ 
                             'model' => $model,
                             'form' => $form,
                             'columns' => 4,
                             'attributes' => [ 
                                     'active' => [ 
			                                         'type' => Form::INPUT_DROPDOWN_LIST,
		                                            'options' => [ 
													'placeholder' => Yii::t('app','Is Active?...'),
											],
    
                                            'columnOptions'=>['colspan'=>1],
											'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
											'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select').'--'
											],
										]
                            ]
                        ]
                   );
                     ActiveForm::end ();?>
                   	</div>
                    <div class="col-sm-3" style="overflow:hidden" align="center">
                    	<div id="picture_preview"></div>
                    	<label><?=Yii::t('app','Image / Logo')?></label><br/>
                    	<?php
							if(MulteModel::fileExists(Yii::$app->params['web_url'].'/customers/'.$model->id.'.png')){?>
                            	<img src="<?=Yii::$app->params['web_url']?>/customers/<?=$model->id?>.png" style="height:185px;" class="upload img-responsive">								
							<?php }else{?>
								<img src="<?=Url::base()?>/nophoto.jpg" style="height:185px;" class="upload img-responsive">
							<?php }
						?>
                        <input type="file" name="customer_image" class="form-control inp">
                    </div>
                </div>
        
        </div>
        	<div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
					<li class="active"><a href="#contacts" role="tab" data-toggle="tab"><?php echo Yii::t('app', 'Contacts'); ?></a></li>
					<li><a href="#addresses" role="tab" data-toggle="tab"><?php echo Yii::t('app', 'Addresses'); ?></a></li>
					<li>
						<a href="#attachment" role="tab" data-toggle="tab"><?php echo Yii::t('app', 'Attachments'); ?>
							<span class="badge"> <?= FileModel::getAttachmentCount('customer',$model->id)?></span>
						</a>
					</li>
					<li><a href="#notes" role="tab" data-toggle="tab"><?php echo Yii::t('app', 'Notes'); ?></a></li>
					<li><a href="#activity" role="tab" data-toggle="tab"><?php echo Yii::t('app', 'Activities'); ?></a></li>
                </ul>
            
            <div class="tab-content">
                <div class="tab-pane fade" id="contact_detail"> 
                <br/>
                </div>
                <div class="tab-pane" id="address"> 
                <br/>

                </div>
                <div class="tab-pane fade" id="notes"> 
                <br/>	
				 <?php
								
					$searchModelNotes = new MulteModel();
					$dataProviderNotes = $searchModelNotes->searchNotes( Yii::$app->request->getQueryParams (), $model->id,'customer' );
					
					echo Yii::$app->controller->renderPartial("../../../../../multebox/modules/multeobjects/views/note/notes-module/notes", [ 
							'dataProviderNotes' => $dataProviderNotes,
							'searchModelNotes' => $searchModelNotes
					] );
								
				?>
                </div>
                <div class="tab-pane fade" id="attachment"> 
                <br/>			
			  <?php
								
					$searchModelAttch = new MulteModel();
					$dataProviderAttach = $searchModelAttch->searchAttachments( Yii::$app->request->getQueryParams (), $model->id,'customer');
					
					echo Yii::$app->controller->renderPartial("../../../../../multebox/modules/multeobjects/views/file/attachment-module/attachments", [ 
							'dataProviderAttach' => $dataProviderAttach,
							'searchModelAttch' => $searchModelAttch,
							'task_id'=>$model->id,
							'entity_type'=>'customer',
					] );
								
				?>
                </div>
                <div class="tab-pane fade" id="activity"> 
                <br/>			
				<?php
									
				   $searchModelHistory = new MulteModel();
						$dataProviderHistory = $searchModelHistory->searchHistory( Yii::$app->request->getQueryParams (), $model->id,'customer' );
						echo Yii::$app->controller->renderPartial("../../../../../multebox/modules/multeobjects/views/history/history-module/histories", [ 
								'dataProviderHistory' => $dataProviderHistory,
								'searchModelHistory' => $searchModelHistory 
						] );
					
					?>      
                </div>
                <div class="tab-pane fade" id="addresses"> 
                <br/>			
				<?php
									
					$searchAddresses = new MulteModel();
					$dataProviderAddresses = $searchAddresses->searchAddresses( Yii::$app->request->getQueryParams (), $model->id,'customer');
					
					echo Yii::$app->controller->renderPartial("../../../../../multebox/modules/multeobjects/views/address/address-model/addresses", [ 
							'dataProviderAddresses' => $dataProviderAddresses
					] );
					
					?>      
                </div>
                <div class="tab-pane   active" id="contacts"> 
                <br/>			
				<?php
									
					$searchContacts = new MulteModel();
					$dataProviderContacts = $searchContacts->searchContacts( Yii::$app->request->getQueryParams (), $model->id,'customer');
					
					echo Yii::$app->controller->renderPartial("../../../../../multebox/modules/multeobjects/views/contact/contact-model/contacts", [ 
							'dataProviderContacts' => $dataProviderContacts
					] );
					
					?>      
                </div>
            </div>
          </div>
           <?php
            echo Html::submitButton ( $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [ 
            
                                    'class' => $model->isNewRecord ? 'btn btn-success customer_submit' : 'btn btn-primary btn-sm  customer_submit' 
            
                            ] );?> <a href="javascript:void(0)" class="btn btn-success btn-sm" onClick="$('.add-notes-modal').modal('show');"><i class="glyphicon glyphicon-comment"></i> <?=Yii::t('app', 'New Note')?></a>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm" onClick="$('.savepopup').modal('show');"><i class="glyphicon glyphicon-save"></i> <?=Yii::t('app', 'New Attachment')?></a>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm" onClick="$('.addressae').modal('show');"><i class="glyphicon glyphicon-road"></i> <?=Yii::t('app', 'New Address')?></a>
                             <a href="javascript:void(0)" class="btn btn-success btn-sm" onClick="$('.contactae').modal('show');"><i class="glyphicon glyphicon-phone"></i> <?=Yii::t('app', 'New Contact')?></a>
                            <!-- <a href="javascript:void(0)" class="btn btn-success btn-sm" onClick="$('.sendEmail').modal('show');"><i class="fa fa-envelope"></i> <?=Yii::t('app', 'Send Email')?></a>-->
				
    </div>
    
</div>  
<?php
	include_once(__DIR__ .'/../../../../../multebox/modules/multeobjects/views/file/attachment-module/attachmentae.php');
	include_once(__DIR__ .'/../../../../../multebox/modules/multeobjects/views/note/notes-module/noteae.php');
	include_once(__DIR__ .'/../../../../../multebox/modules/multeobjects/views/address/address-model/addressae.php');
	include_once(__DIR__ .'/../../../../../multebox/modules/multeobjects/views/contact/contact-model/contactae.php');
	//include_once(__DIR__ .'/../../../../../multebox/modules/multeobjects/views/email-template/email-model/send-email.php');
?>