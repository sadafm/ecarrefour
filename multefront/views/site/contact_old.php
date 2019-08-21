<?php
use yii\helpers\Url;
?>
<script type="text/javascript" src="<?=Url::base()?>/js/jquery-2.1.1.min.js"></script>

<script>
$(document).on("click", '.enquiry_submit', function(event){
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
		else if($(this).is("[num-validation]"))
		{
			if (!e.match(/^\d+$/))
			{
				error+=Add_Error($(this),'<?=Yii::t('app','Please enter a valid number!')?>');
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
		});
	});

</script>
<!-- Breadcrumb Start-->
  <ul class="breadcrumb">
	<li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
	<li><?=Yii::t('app', 'Contact Us')?></li>
  </ul>
  <!-- Breadcrumb End-->

  <!-- Main Container -->
  <section class="main-container col1-layout">
    <div class="main container">
      <div class="row">
        <section class="col-main col-sm-12">
          
          <div id="contact" class="page-content page-contact">
          <div class="page-title">
            <h2><?=Yii::t('app', 'Contact Us')?></h2>
          </div>
            <div id="message-box-conact"><?=Yii::t('app', 'Feel free to reach out to us in case of any queries.')?></div>
            <div class="row">
              <div class="col-xs-12 col-sm-6" id="contact_form_map">
                <h3 class="page-subheading">Let's get in touch</h3>
                <p><?=Yii::t('app', 'Fill up the contact form and we will revert as soon as possible.')?></p>
				<p><?=Yii::t('app', 'Contact us if you need any help related to')?>:</p>
                <ul>
                  <li><?=Yii::t('app', 'Bulk Orders')?></li>
                  <li><?=Yii::t('app', 'Drop Shipment')?></li>
                  <li><?=Yii::t('app', 'Partnering With Us')?></li>
                </ul>
                <br/>
                <ul class="store_info">
                  <li><i class="fa fa-phone"></i><span><?=Yii::$app->params['company']['mobile']?></span></li>
                  <li><i class="fa fa-envelope"></i><?=Yii::t('app', 'Email')?>: <span><a href="mailto:<?=Yii::$app->params['company']['company_email']?>"><?=Yii::$app->params['company']['company_email']?></a></span></li>
                </ul>
              </div>
              <div class="col-sm-6">
                <h3 class="page-subheading"><?=Yii::t('app', 'Make an enquiry')?></h3>
				<form method="post" action="<?=Url::to(['/site/contact'])?>">
				<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
				<input type="hidden" name="sendenquiry" value="1">
                <div class="contact-form-box">
                  <div class="form-group">
                    <label><?=Yii::t('app', 'Name')?></label>
                    <input type="text" class="form-control input-sm" id="name" name="name" data-validation="required" mandatory-field/>
                  </div>
                  <div class="form-group">
                    <label><?=Yii::t('app', 'Email')?></label>
                    <input type="text" class="form-control input-sm" id="email" name="email" data-validation="required" mandatory-field email-validation/>
                  </div>
                  <div class="form-group">
                    <label><?=Yii::t('app', 'Phone')?></label>
                    <input type="text" class="form-control input-sm" id="phone" name="phone" data-validation="required" mandatory-field num-validation/>
                  </div>
                  <div class="form-group">
                    <label><?=Yii::t('app', 'Message')?></label>
                    <textarea class="form-control input-sm" rows="10" id="message" name="message" data-validation="required" mandatory-field></textarea>
                  </div>
                  <div class="form-group">
                    <button class="button enquiry_submit" type="submit"><i class="fa fa-send"></i>&nbsp; <span><?=Yii::t('app', 'Send Message')?></span></button>
                  </div>
                </div>
				</form>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>
  <!-- Main Container End -->
