<?php
use yii\jui\AutoComplete;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use multebox\models\Country;
use multebox\models\State;
use multebox\models\City;
use kartik\widgets\DepDrop;
?>

<script>
$(document).ready(function()
{
	$('.add_address').click(function(event)
	{
		var error='';
		$('[address-data-validation="required"]').each(function(index, element) 
		{
			Remove_Error($(this));
			
			var e=$(this).val();

			if($(this).val() == '' && !$(this).is("[mandatory-field-2]"))
			{
				Remove_Error($(this));
			}
			else if($(this).val() == '' && $(this).is("[mandatory-field-2]"))
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
		})
	})
})
</script>
<style>
.ui-front {
    z-index: 2000 !important;
}
</style>
<div class="modal fade bs-example-modal-lg addressae">
<form method="post" id="addressform" action=""  enctype="multipart/form-data">
<?php Yii::$app->request->enableCsrfValidation = true; ?>
    <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
   	<input type="hidden" name="addressae" value="true">
    <input type="hidden" name="address_id" value="<?=$sub_address_model->id?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=Yii::t('app', 'Address')?></h4>
      </div>
      <div class="modal-body">
      		 <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label"><?=Yii::t('app', 'Address 1')?></label>
                                    <input type="text" name="sub_address_1" address-data-validation="required" mandatory-field-2 class="form-control" value="<?=$sub_address_model->address_1?>">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label"><?=Yii::t('app', 'Address 2')?></label>
                                    <input type="text" name="sub_address_2" class="form-control" value="<?=$sub_address_model->address_2?>">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label"><?=Yii::t('app', 'ZipCode')?>:</label>
                                    <input type="text" name="sub_zipcode" address-data-validation="required" mandatory-field-2 class="form-control"  value="<?=$sub_address_model->zipcode?>">
                                </div>
                            </div>
                        </div>
                		<?php
				echo '<div class="row">
						<div class="col-sm-4">
							<div class="form-group required">
								<label class="control-label">'.Yii::t('app', 'Country').'</label>
						'.Html::dropDownList('sub_country_id',$sub_address_model->country_id,
     ArrayHelper::map(Country::find()->orderBy('country')->asArray()->all(), 'id', 'country'), ['prompt' => '--Select--','class'=>'form-control','id'=>'sub_country_id','address-data-validation'=>'required', 'mandatory-field-2' => '' ]  ).'</div></div>
	 					<div class="col-sm-4">
						<div class="form-group required">
								<label class="control-label">'.Yii::t('app', 'State').'</label>
						'.Html::dropDownList('sub_state_id',$sub_address_model->state_id,
     ArrayHelper::map(State::find()->where('id=0')->orderBy('state')->asArray()->all(), 'id', 'state'), ['prompt' => '--Select--','class'=>'form-control','id'=>'sub_state_id','address-data-validation'=>'required', 'mandatory-field-2' => '']  ).'</div></div>
	 				<div class="col-sm-4">
						<div class="form-group required">
								<label class="control-label">'.Yii::t('app', 'City').'</label>
						';/*.Html::dropDownList('sub_city_id',$sub_address_model->city_id,
     ArrayHelper::map(City::find()->where('id=0')->orderBy('city')->asArray()->all(), 'id', 'city'), ['prompt' => '--Select--','class'=>'form-control','id'=>'sub_city_id']  ).'</div></div></div>';*/

	 echo AutoComplete::widget([
									  'name' => 'sub_city_id',
									  'value' => City::findOne($sub_address_model->city_id)->city,
									  'clientOptions' => [
										  'source' => [],
									  ],
									  'options' => ['placeholder' => Yii::t ( 'app', 'Type few letters and select from matching list' ), 'class' => 'form-control', 'id' => 'sub_city_id']
								  ]).'</div></div></div>';

						?>
      </div>
      <div class="modal-footer">
      	<button type="submit" class="btn btn-primary add_address btn-sm">
        	<i class="fa fa-road"></i><?=Yii::t('app', 'Save')?> </button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-remove"></i><?=Yii::t('app', 'Close')?> </button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</form>
</div><!-- /.modal -->
