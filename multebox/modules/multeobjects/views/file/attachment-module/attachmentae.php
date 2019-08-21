<?php
//use Yii;
use yii\helpers\Url;
use multebox\models\User as UserDetail;
$loggedUser=UserDetail::find()->where('id='.Yii::$app->user->identity->id)->asArray()->one();
?>
<div class="modal fade bs-example-modal-lg savepopup">
<script>
$(function(){
		$('#newatt').click(function(){
			if($('#f_title').val()==''){
				addError($('#f_title'),"<?=Yii::t ('app','This Field is Required!')?>");
				return false;
			}else{
				removeError($('#f_title'));
				return true;
			}
		})
})
function validate()
{
	var invalidExt = <?php echo json_encode(Yii::$app->params['invalid_ext']);?>;
	var filename = document.getElementById("f_title").value;
	var extension = filename.substring(filename.lastIndexOf('.')+1);
	var uExt = extension.toUpperCase();
	/*if (invalidExt.indexOf(uExt) != -1)
	{
		alert("Selected file is not allowed to be uploaded!");
		return false;
	}*/
	for(i=0; i < invalidExt.length; i++)
	{
		if(uExt.indexOf(invalidExt[i]) != -1)
		{
			alert("<?=Yii::t('app', 'Selected file is not allowed to be uploaded!')?>");
			return false;
		}
	}

	var imagetest = <?=''.$image_type != ''?1:0?>;

	if(imagetest == '1')
	{
		if(uExt != 'JPG' && uExt != 'JPEG' && uExt != 'PNG')
		{
			alert("<?=Yii::t('app', 'Only JPG and PNG files are allowed!')?>");
			return false;
		}
	}
	return true;
}
</script>
<form method="post" name="newatt" action=""  enctype="multipart/form-data">
<input type="hidden" name="entity_id" value="<?=$_REQUEST['id']?>">
<input type="hidden" name="add_attach" value="yes">
<?php Yii::$app->request->enableCsrfValidation = true; ?>
    <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
  <div class="modal-dialog">
    <div class="modal-content">
    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=Yii::t('app', 'New Attachment')?></h4>
      </div>
      <div class="modal-body">
      		<div class="form-group">
            	<label><?=Yii::t('app', 'Title')?></label>
        		<input type="text" name="filetitle" class="form-control">
                <span class="help-block"></span>
            </div>
            <div class="form-group">
				<?php
					if(Yii::$app->params['FILE_SIZE']=="0")
						$limit = "No Limit";
					else
						$limit = Yii::$app->params['FILE_SIZE']."MB";
				?>
            	<label><?=Yii::t('app', 'Attachment (Max allowed size:').' '.$limit.')'?></label>
            	<input type="file" name="attach" id="f_title"  class="form-control" accept="<?=isset($image_type)?$image_type:'*'?>">
            </div>
      </div>
      <div class="modal-footer">
      	<button type="submit" class="btn btn-primary  btn-sm" id="newatt" onClick="return validate();"><i class="fa fa-upload"></i> <?=Yii::t('app', 'Upload')?> </button>
        <button type="button" class="btn btn-default  btn-sm" data-dismiss="modal"><i class="fa fa-remove"></i> <?=Yii::t('app', 'Close')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</form>
</div><!-- /.New Attachment modal -->
<!--///////////////////////////////////////////-->
<div class="modal fade bs-example-modal-lg popup">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=Yii::t('app', 'Change Attachment')?></h4>
      </div>
      		<form method="post" action=""  enctype="multipart/form-data">
            <?php Yii::$app->request->enableCsrfValidation = true; ?>
    <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
    		  <input type="hidden" name="entity_id" value="<?=$_REQUEST['id']?>">
        	  <input type="hidden" value="<?=$attachModel->id?>" id="attId" name="att_id">
              <input type="hidden" value="true"  name="edit_attach">
              <div class="modal-body">
                    <div class="form-group">
                        <label><?=Yii::t('app', 'Title')?></label>
                        <input type="text" value="<?=$attachModel->file_title?>" name="filetitle" id="attTitle" class="form-control">
                    </div>
                    <div class="form-group">
                        <label><?=Yii::t('app', 'Attachment')?></label>
                        <input type="file" name="attach" class="form-control">
                    </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> <?=Yii::t('app', 'Upload')?> </button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-remove"></i>  <?=Yii::t('app', 'Close')?></button>
              </div>
        </form>
        
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.Change Attachment modal -->
<!--//////////////////////////////////////////////////////-->
<div class="modal fade bs-example-modal-lg email">
<script>
function addError(obj,error){
	$(obj).parent().addClass('has-error');
	$(obj).next('.help-block').text(error);
}
function removeError(obj){
	$(obj).parent().removeClass('has-error');
	$(obj).next('.help-block').text('');
}
$(function(){
		$('#send_email').click(function(){
			var error='';
			if($('#uemail').val()==''){
				addError($('#uemail'),"<?=Yii::t ('app','This Field is Required!')?>");
				error='error';
			}else{
				removeError($('#uemail'));
			}
			////////// Subject///////////
			if($('#esubject').val()==''){
				addError($('#esubject'),"<?=Yii::t ('app','This Field is Required!')?>");
				error='error';
			}else{
				removeError($('#esubject'));
			}
			//////////////Body///////////
			if($('#ebody').val()==''){
				addError($('#ebody'),"<?=Yii::t ('app','This Field is Required!')?>");
				error='error';
			}else{
				removeError($('#ebody'));
			}
			if(error ==''){
				return true;
			}else{
				return false;
			}
		})
})
</script>
<form method="post" action=""  enctype="multipart/form-data">
<?php Yii::$app->request->enableCsrfValidation = true; ?>
    <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
    <input type="hidden" name="send_attachment_file" value="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=Yii::t('app', 'Send Mail')?></h4> (<i><?=Yii::t('app', 'Multi Email bind with \',\' Separator')?></i>)
      </div>
      <div class="modal-body">
      		<input type="hidden" name="filename" id="filename">
            <input type="hidden" name="attname" id="attname">
            
            <div class="form-group">
            	<label><?=Yii::t('app', 'To')?></label>
            	<input type="text" name="uemail" id="uemail" value="<?=$email?$email:UserDetail::findOne($entity_user)->email?>" class="form-control" >
                <span class="help-block"></span>
            </div>
            <div class="form-group">
            	<label><?=Yii::t('app', 'CC')?></label>
            	<input type="text" name="cc" class="form-control" >
            </div>
            <div class="form-group">
            <label><?=Yii::t('app', 'Subject')?></label>
        	<input type="text" name="subject" class="form-control" value="<?php echo $loggedUser['first_name']." ".$loggedUser['last_name'].Yii::t('app', ' sent you a file'); ?>" id="esubject" >
            <span class="help-block"></span>
            </div>
            <div class="form-group">
            	<label><?=Yii::t('app', 'Body')?></label>
            	<textarea class="form-control" name="email_body" rows="8" id="ebody"></textarea>
                <span class="help-block"></span>
            </div>
      </div>
      <div class="modal-footer">
      	<button type="submit" class="btn btn-primary btn-sm" id="send_email">
        	<i class="fa fa-envelope"></i> <?=Yii::t('app', 'Send Email')?></button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-remove"></i> <?=Yii::t('app', 'Close')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</form>
</div><!-- /.modal -->
<script>
function sendAttachment(attname,name){
			$('#attname').val(attname);
			$('#filename').val(name);
			//$('#uemail').val(uemail);
			var mailBody="<?=Yii::t('app', 'Hello')?>,\n\n"
			
				+ "<?php echo $loggedUser['first_name']." ".$loggedUser['last_name'] ?> <?=Yii::t('app', 'sent you this file, click on the below link to view the file')?>\n"
				
				+ "<a href='<?=Yii::$app->params['web_url']?>/"+name+"'>"+attname+"</a> ";
			$("#ebody").html(mailBody);
			$('.email').modal('show');
}
function changeAttach(id,name){
			$('#attId').val(id);
			$('#attTitle').val(name);
			$('.popup').modal('show');
}
</script>
