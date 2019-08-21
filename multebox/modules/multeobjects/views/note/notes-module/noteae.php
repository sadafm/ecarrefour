<?php
use yii\helpers\Url;
?>
<style>
	.modal-dialog{width:80% !important;}
</style>
<script src="<?=Url::base()?>/bower_components/ckeditor/ckeditor.js"></script>
<script>
$(document).ready(function(e) {
	$('.notesAdd').click(function(event){
			$('#notes_ck').parent().removeAttr('style').next('.error').remove();
			sageLength = CKEDITOR.instances['notes_ck'].getData().replace(/<[^>]*>/gi, '').length;
			if(sageLength==0){
				Add_ErrorTag($('#notes_ck').parent(),"<?=Yii::t ('app','This Field is Required!')?>");
			event.preventDefault();
			}
		})

	$('.notesEdit').click(function(event){
			$('#notes_edit_ck').parent().removeAttr('style').next('.error').remove();
			sageLength = CKEDITOR.instances['notes_edit_ck'].getData().replace(/<[^>]*>/gi, '').length;
			if(sageLength==0){
				Add_ErrorTag($('#notes_edit_ck').parent(),"<?=Yii::t ('app','This Field is Required!')?>");
			event.preventDefault();
			}
		})
});
</script>
<div class="modal fade add-notes-modal">
			  <div class="modal-dialog">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=Yii::t('app', 'Close')?></span></button>
					<h4 class="modal-title"><?=Yii::t('app', 'Notes')?></h4>
				  </div>
					<div class="modal-body">
<form  class="form-horizontal" role="form" name="noteae" id="noteae" action="" method="post" >
            <?php Yii::$app->request->enableCsrfValidation = true; ?>
    <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
                 <input type="hidden" name="entity_id" value="<?=$_GET['id']?>">
                 <input type="hidden" name="entity_type" value="task">
                 <input type="hidden" name="add_note_model" value="true">
				  <!--<legend>Notes</legend> -->
						
						<?php if ($error != '') { ?>
									<div class="alert alert-danger alert-dismissable">
										<button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button>
										<?php echo $error; ?>
									</div>
							<?php } ?>
						
						<div class="form-group">
							<label class="col-sm-2 control-label"><?=Yii::t('app', 'Notes')?>:<font color="#FF0000">*</font></label>
							<div class="col-sm-8">
							<textarea class="form-control"  name="notes_ck" id="notes_ck" rows=6 ><?php echo isset($notes) ? $notes : ''; ?></textarea> <span class="help-block"></span>
							</div>
						</div>
						
			
						 <div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button class="btn btn-primary btn-sm notesAdd" type="submit" name="Submit" value="Save Notes"><i class="fa fa-comment"></i> <?=Yii::t('app', 'Save')?></button>
								<button type="button" class="btn btn-default  btn-sm" data-dismiss="modal"><i class="fa fa-remove"></i> <?=Yii::t('app', 'Close')?></button>
							</div>
						</div>
                </form>
					</div>
				 
				</div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

<div class="modal fade edit-notes-modal">
			  <div class="modal-dialog">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=Yii::t('app', 'Close')?></span></button>
					<h4 class="modal-title"><?=Yii::t('app', 'Notes')?></h4>
				  </div>
					<div class="modal-body">
<form  class="form-horizontal" role="form" name="noteae" id="noteae" action="" method="post" >
            <?php Yii::$app->request->enableCsrfValidation = true; ?>
    <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
                 <input type="hidden" name="entity_id" value="<?=$_GET['id']?>">
                 <input type="hidden" name="entity_type" value="task">
                 <input type="hidden" name="edit_note_model" value="true">
				 <input type="hidden" name="note_id" value="<?=$_GET['note_id']?>">
				  <!--<legend>Notes</legend> -->
						
						<?php if ($error != '') { ?>
									<div class="alert alert-danger alert-dismissable">
										<button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button>
										<?php echo $error; ?>
									</div>
							<?php } ?>
						
						<div class="form-group">
							<label class="col-sm-2 control-label"><?=Yii::t('app', 'Notes')?>:<font color="#FF0000">*</font></label>
							<div class="col-sm-8">
							<textarea class="form-control"  name="notes_edit_ck" id="notes_edit_ck" rows=6 ><?php echo isset($notes) ? $notes : ''; ?><?php echo $noteModel->notes; ?></textarea> <span class="help-block"></span>
							</div>
						</div>
						
			
						 <div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" class="btn btn-primary btn-sm notesEdit" name="Submit" value="Save Notes"><i class="fa fa-comment"></i> <?=Yii::t('app', 'Save')?></button>
								<button type="button" class="btn btn-default  btn-sm" data-dismiss="modal"><i class="fa fa-remove"></i> <?=Yii::t('app', 'Close')?></button>
							</div>
						</div>
                </form>
					</div>
				 
				</div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
                 

<script>
  $(function () {
    CKEDITOR.replace('notes_ck');
	CKEDITOR.replace('notes_edit_ck');
  })
</script>