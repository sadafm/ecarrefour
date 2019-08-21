<?php
use multebox\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use multebox\models\Country;
use multebox\models\State;
?>
<script>
$(document).ready(function()
{
	$('#country_id').change(function(){
    $.post("<?=Url::to(['/multeobjects/address/ajax-load-states'])?>", { 'country_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#state_id').html(result);
				})
	})


	$('.add_tax').click(function(event)
	{
		var error='';
		$('[tax-data-validation="required"]').each(function(index, element) 
		{
			Remove_Error($(this));
			
			var e=$(this).val();

			if($(this).val() == '' && !$(this).is("[mandatory-field-2]"))
			{
				Remove_Error($(this));
			}
			else if($(this).val() == '' && $(this).is("[mandatory-field-2]"))
			{
				error+=Add_Error($(this),'<?=Yii::t('app','This Field is Required!')?>');
			}
			else if($(this).is("[email-validation]"))
			{
				var atpos=e.indexOf("@");
				var dotpos=e.lastIndexOf(".");

				if (atpos<1 || dotpos<atpos+2 || dotpos+2>=e.length)
				{
					error+=Add_Error($(this),'<?=Yii::t('app','Email Address Not Valid!')?>');
				}
				else
				{
					Remove_Error($(this));
				}	
			}
			else if($(this).is("[num-validation-float]"))
			{
				if (!e.match(/^[]?[0-9]*\.?[0-9]+$/))
				{
					error+=Add_Error($(this),'<?=Yii::t('app','Please enter a valid number!')?>');
				}
				else
				{
					Remove_Error($(this));
				}	
			}
			else if($(this).val() == '')
			{
				error+=Add_Error($(this),'<?=Yii::t ('app','This Field is Required!')?>');
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
<div class="modal fade add_taxes">
  <div class="modal-dialog">
    <div class="modal-content">
   	  <div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=Yii::t('app', 'Add State Tax')?></h4>
      </div>
      <div class="modal-body" style="overflow:auto">
      	<form action="" method="post">
        <?php Yii::$app->request->enableCsrfValidation = true; ?>
    <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
      	<div class="row">
		<?php
		echo			'<div class="col-sm-4">
								<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'Country').'</label>
							'.Html::dropDownList('country_id',  \multebox\models\DefaultValueModule::getDefaultValueId('country'),
		 ArrayHelper::map(Country::find()->orderBy('country')->where('active=1')->asArray()->all(), 'id', 'country'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'country_id','tax-data-validation'=>'required' ,'mandatory-field-2'=>'' ]  ).'</div></div>
							<div class="col-sm-4">
							<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'State').'</label>
							'.Html::dropDownList('state_id', 'state_id',
		 ArrayHelper::map(State::find()->where('id=0')->orderBy('state')->asArray()->all(), 'id', 'state'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'state_id', 'tax-data-validation'=>'required' ,'mandatory-field-2'=>'' ]  ).'</div></div>';
		 ?>
			 <div class="col-sm-4">
				<div class="form-group required">
					<label class="control-label"><?=Yii::t('app', 'Tax')?></label>
					<input type="text" name="tax_percentage" class="form-control" tax-data-validation="required" mandatory-field-2 num-validation-float>
				</div>
			 </div>
		 </div>
        <div class="form-group">
			<input type="hidden" name="state_tax_add">
        	<input type="submit" value="<?=Yii::t('app', 'Save')?>" class="btn btn-success btn-sm add_tax">
        </div>
        </form>
      </div>
   </div>
</div>
</div>