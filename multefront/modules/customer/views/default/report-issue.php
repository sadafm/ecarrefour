<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('app', 'Report Issue');
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="text/javascript" src="<?=Url::base()?>/js/jquery-2.1.1.min.js"></script>
<script>

$(document).on("click", '.issue_submit', function(event){
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
	});

</script>

<div class="issue-create">
   <!-- <div class="page-header">
       <h1><?= Html::encode($this->title) ?></h1> 
    </div> -->
    <?= $this->render('_issueform', [
        'model' => $model,
    ]) ?>

</div>
