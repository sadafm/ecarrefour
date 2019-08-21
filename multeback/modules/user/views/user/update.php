<?php
use yii\helpers\Url;
use yii\helpers\Html;
use multebox\models\search\User;
use multebox\models\search\UserType as UserTypeSearch;
/**
 * @var yii\web\View $this
 * @var common\models\User $model
 */
$this->title = Yii::t('app', 'Update User').' : ' . ' ' . $model->first_name." ".$model->last_name;
if(Yii::$app->params['user_role'] == 'admin')
{	
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
}
$this->params['breadcrumbs'][] = ['label' => $model->first_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
function getUserRoles($id){
	$connection = \Yii::$app->db;
		$sql="select auth_item.* from auth_item,auth_assignment where auth_item.type=2 and auth_assignment.user_id=".$id." and auth_assignment.item_name=auth_item.name";
		$command=$connection->createCommand($sql);
		$dataReader=$command->queryAll();
		$roles ='<ul class="list-group">';
		if($dataReader){
			foreach($dataReader as $role){
				$roles.='<li class="list-group-item">'.$role['name']."</li>";
			}
		}else{
			return '<div class="alert alert-danger">No Roles</div>';
		}
		
		return $roles."</ul>";	
}
?>
<script type="text/javascript"> 
$(document).ready(function(){
    if($('#user-user_type_id').val() ==<?=UserTypeSearch::getCompanyUserType('Vendor')->id?>){
		 $('.field-user-entity_id').show();
	}else{
		 $('.field-user-entity_id').hide();
	}
	$('.tabbable').appendTo('#w0');
	$('#user-user_type_id').change(function(){
        if($(this).val() ==<?=UserTypeSearch::getCompanyUserType('Vendor')->id?>){
			 $('.field-user-entity_id').show();
		}else{
			 $('.field-user-entity_id').hide();
		}
	})
	$('#user-username').attr('disabled',true);
	$('#user-user_type_id').attr('disabled',true);
	$('#user-entity_id').attr('disabled',true);
	//$('.ddddd').modal('show');
/*if('<?php echo $model->username ?>' =='admin' || '<?php echo $model->username ?>' =='Admin'){
		$('.field-user-user_type_id').hide();
		$('#user-user_type_id').hide();
	}*/
	if('<?=!empty($new_password)?$new_password:''?>' !=''){
	
	//	$('.msg').modal('show');
	alert("New password is "+'<?=!empty($new_password)?$new_password:''?>');
			window.location.href="<?=Url::to(['/user/user/update', 'id' => $model->id, 'edit' => 't'])?>";
	}
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
		//$('#w0').submit();
	});
	$('.upload').click(function(){
		$('.inp').click();
	})
	function ajaxFileUpload(upload_field)
	{
	// Checking file type
		/*var re_text = /\.jpg|\.gif|\.jpeg/i;
		var filename = upload_field.value;
			if (filename.search(re_text) == -1) {
				alert("File should be either jpg or gif or jpeg");
				upload_field.form.reset();
				return false;
			}*/
	document.getElementById('picture_preview').innerHTML = '<div><img src="<?=Url::base()?>/loading.gif?>" style="height:50px;" /></div>';
	upload_field.form.action = "<?=Url::to(['/user/user/update', 'id' => $_REQUEST['id']])?>";
	upload_field.form.target = 'upload_iframe';
	upload_field.form.submit();
	upload_field.form.action = '';
	upload_field.form.target = '';
	setTimeout(function(){
	document.getElementById('picture_preview').innerHTML = '';
	},2500)
	return true;
	}
	
});
</script>
<style>
.project-index .kv-panel-before,.project-index .kv-panel-after,.queue-index .kv-panel-before,.queue-index .kv-panel-after{
	padding:0px !important
}
</style>
<iframe name="upload_iframe" id="upload_iframe" style="display:none;"></iframe>
<div class="user-update">
	
	<!--
    <h1><?= Html::encode($this->title) ?></h1>
	-->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
<div class="tabbable">
<?php
if(Yii::$app->user->identity->userType->type!="Vendor")
{
?>
	      <div class="tab-content">
                <div class="tab-pane active" id="roles"> 
                <br/>	
                	<div class="panel panel-info">
                    	<div class="panel-heading">
                        	<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> <?php echo Yii::t('app', 'Roles'); ?>
                            	<div class="pull-right">
                                	
                                </div>
                            </h3>
                        </div>
                        <div class="panel-body">
                        <a href="<?=Url::to(['/multeobjects/setting/rights', 'assign_user_id' => $model->id])?>" class="btn btn-primary btn-sm"><?=Yii::t('app', 'Roles & Operations')?></a>
                        	<?=getUserRoles($model->id)?>
                            
                        </div>
                    </div>
                </div>
				
            </div>
            <?php
}
				echo Html::submitButton ( $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [ 
						'class' => $model->isNewRecord ? 'btn btn-success btn-sm' : 'btn btn-primary btn-sm' 
				] );
				if(Yii::$app->user->identity->userType->type!="Vendor")
				{
				if(!empty($_GET['id']) and  strtolower($model->username)  !='admin' ){?>
                <a href="<?=Url::to(['/user/user/update', 'id' => $model->id, 'edit' => 't', 'active' => $model->active !='1'?'yes':'no'])?>" onClick="return confirm('<?=Yii::t('app', 'Are you Sure')?>')" class="btn <?=$model->active !='1'?'btn-primary btn-sm':'btn-danger btn-sm'?>"><?=$model->active !='1'?'Activate User':'Deactivate User'?></a>
                
                <?php } 
				if(!empty($_GET['id']) and  strtolower($model->username)  !='admin' ){?>
                <a href="<?=Url::to(['/user/user/update', 'id' => $model->id, 'edit' => 't', 'reset_password' => 'true'])?>" onClick="return confirm('<?=Yii::t('app', 'Are you Sure')?>')" class="btn btn-success btn-sm"><?php echo Yii::t('app', 'Reset Password'); ?></a>
                
                <?php }
				}
				echo "</form>";
			?>
          </div>
</div>
<div class="modal fade ddddd">
  <div class="modal-dialog  modal-lg">
    <div class="modal-content">
    	 <div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       		 <h4 class="modal-title"><?php echo Yii::t('app', 'Password has been Reset'); ?></h4>
      </div>
		<div class="modal-body">
			<form>  
			<br/>
            <div class="alert alert-success"><?php echo Yii::t('app', 'Password is'); ?> : <?=!empty($new_password)?$new_password:''?></div>
            <div class="form-actions">
                    <button type="button" class="close" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
            </div>
	</form>
		</div>
   </div>
 </div>
</div>
