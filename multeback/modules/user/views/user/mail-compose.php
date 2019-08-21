<?php
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\User $model
 */

$this->title = Yii::t('app','Mail Compose');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if(!empty($msg)){
?>
<div class="alert alert-success"><?=$msg?></div>
<?php } ?>
<div class="user-create">
    <div class="row">
            <form  action="" method="post" enctype="multipart/form-data"  class="form-horizontal" >
               <?php Yii::$app->request->enableCsrfValidation = true; ?>
    <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
            <div class="col-lg-12 animated fadeInRight">
            <div class="mail-box-header">
                <!--<div class="pull-right tooltip-demo">
                     <button class="btn btn-primary btn-sm" type="submit" id="send"><i class="fa fa-reply"></i> <?=Yii::t('app','Send')?></button>
                    <a href="<?=Url::to(['/user/user/view', 'id' => $user->id])?>" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Discard email"><i class="fa fa-times"></i> <?=Yii::t('app','Discard')?></a>
                </div>-->
                <h2> <?=Yii::t('app','Compose mail')?>
                    
                </h2>
            </div>
                <div class="mail-box">


                <div class="mail-body">

                    
                        <div class="form-group"><label class="col-sm-2 control-label"><?=Yii::t('app','To')?>:</label>

                            <div class="col-sm-8"><input type="text" name="to" class="form-control" value="<?=$user->email?>" readonly></div>
                        </div>
                        <div class="form-group"><label class="col-sm-2 control-label"><?=Yii::t('app','Cc')?>:</label>

                            <div class="col-sm-8"><input type="text" name="cc" class="form-control" value=""></div>
                        </div>
                        <div class="form-group"><label class="col-sm-2 control-label"><?=Yii::t('app','Subject')?>:</label>

                            <div class="col-sm-8"><input type="text" id="subject" name="subject" class="form-control" value=""></div>
                        </div>

                </div>

                    <div class="mail-text h-200">
						<div class="form-group"><label class="col-sm-2 control-label"><?=Yii::t('app','Message')?>:</label>
                        <div class="summernote col-sm-8">
                            <textarea class="form-control input-sm ckeditor" name="email_body" id="email_body" rows="8" style="width:100%"></textarea>
                        </div>
<div class="clearfix"></div>
                        </div>
                    <div class="mail-body text-right tooltip-demo col-sm-10">
                       <button class="btn btn-primary btn-sm" type="submit" id="send"><i class="fa fa-reply"></i> <?=Yii::t('app','Send')?></button>
                        <a href="<?=Url::to(['/user/user/view', 'id' => $user->id])?>" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Discard email"><i class="fa fa-times"></i> <?=Yii::t('app','Discard')?></a>
                    </div>
                    <div class="clearfix"></div>



                </div>
                
                        </form>
            </div>
        </div>
</div>

<script src="<?=Url::base()?>/bower_components/ckeditor/ckeditor.js"></script>
<style>
.cke_contents{max-height:250px}
</style>

    <script>
        $(document).ready(function(){
          $('#send').click(function(event){
			  var error='';
			  Remove_Error($('#subject'));
				if($('#subject').val() ==''){
					 error+=Add_Error($('#subject'),'<?=Yii::t('app', 'Subject is Required!')?>');
				}else{
					Remove_Error($('#subject'));
				}
				$('#cke_1_contents').parent().parent().removeAttr('style').next('.error').remove();
				sageLength = CKEDITOR.instances['email_body'].getData().replace(/<[^>]*>/gi, '').length;
				if(sageLength==0){
					error+=Add_ErrorTag($('#cke_1_contents').parent().parent(),'<?=Yii::t('app', 'This Field is Required!')?>');
				}
				if(error !=''){
					event.preventDefault();
				}else{
					return true	
				}
			})

        });

    </script>
