<?php
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\builder\Form;
use kartik\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\UserType;
use multebox\models\User;
use yii\helpers\ArrayHelper;
/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\Address $searchModel
 */

$this->title = Yii::t ( 'app', 'System Settings' );
$this->params ['breadcrumbs'] [] = $this->title;
function getUserRoles($user_id){
	$connection = \Yii::$app->db;
	$sql="select auth_item.* from auth_item,auth_assignment where auth_item.type=2 and auth_assignment.user_id=$user_id and auth_assignment.item_name=auth_item.name";
	$command=$connection->createCommand($sql);
	$dataReader=$command->queryAll();
	$roles ='';
	if($dataReader){
		foreach($dataReader as $role){
			$roles.=$role['name']."</br>";
		}
	}
	return $roles;
}
function getUserOperations($user_id){
	$connection = \Yii::$app->db;
	$sql="select auth_item.* from auth_item,auth_assignment where auth_item.type=0 and auth_assignment.user_id=$user_id and auth_assignment.item_name=auth_item.name";
	$command=$connection->createCommand($sql);
	$dataReader=$command->queryAll();
	$roles ='';
	if($dataReader){
		foreach($dataReader as $role){
			$roles.=$role['name']."</br>";
		}
	}
	return $roles;
}
function getUserAssignments(){
	if(!empty($_REQUEST['assign_user_id'])){
		$connection = \Yii::$app->db;
		$sql="select auth_assignment.*,auth_item.type from auth_item, auth_assignment where  auth_assignment.user_id=$_REQUEST[assign_user_id] and auth_assignment.item_name=auth_item.name";
		$command=$connection->createCommand($sql);
		$dataReader=$command->queryAll();
		if($dataReader){
			return $dataReader;
		}
	}else{
		return '';	
	}
}
function checkParentExists($parent,$child){
		$connection = \Yii::$app->db;
		$sql="select * from auth_item_child where  parent='$parent' and child='$child'";
		$command=$connection->createCommand($sql);
		$dataReader=$command->queryAll();
		if($dataReader){
			return 'yes';
		}else{
			return 'no';
		}
}
function countChild($parent){
		$connection = \Yii::$app->db;
		$sql="select * from auth_item_child where  parent='$parent'";
		$command=$connection->createCommand($sql);
		$dataReader=$command->queryAll();
		if($dataReader){
			return "[".count($dataReader)."]";
		}else{
			return '';
		}
}
function roleParent(){
		$connection = \Yii::$app->db;
		$sql="select auth_item_child.*,auth_item.type from auth_item, auth_item_child where auth_item_child.child=auth_item.name and auth_item_child.child='$_REQUEST[role_id]'";
		$command=$connection->createCommand($sql);
		$dataReader=$command->queryAll();
		return $dataReader;
}
function roleChild(){
		$connection = \Yii::$app->db;
		$sql="select auth_item_child.*,auth_item.type from auth_item, auth_item_child where auth_item_child.parent=auth_item.name and auth_item_child.parent='$_REQUEST[role_id]'";
		$command=$connection->createCommand($sql);
		$dataReader=$command->queryAll();
		return $dataReader;
}
function operationParent(){
		$connection = \Yii::$app->db;
		$sql="select auth_item_child.*,auth_item.type from auth_item, auth_item_child where auth_item_child.child=auth_item.name and auth_item_child.child='$_REQUEST[operation_id]'";
		$command=$connection->createCommand($sql);
		$dataReader=$command->queryAll();
		return $dataReader;
}
function operationChild(){
		$connection = \Yii::$app->db;
		$sql="select auth_item_child.*,auth_item.type from auth_item, auth_item_child where auth_item_child.parent=auth_item.name and auth_item_child.parent='$_REQUEST[operation_id]'";
		$command=$connection->createCommand($sql);
		$dataReader=$command->queryAll();
		return $dataReader;
}
function getRoleType($type){
	$connection = \Yii::$app->db;
	$sql="select auth_item.type from auth_item where  name ='$type'";
	$command=$connection->createCommand($sql);
	$dataReader=$command->queryOne();
	return $dataReader['type'];
}
function getDescription($id){
	$connection = \Yii::$app->db;
	$sql="select auth_item.description from auth_item where  name ='$id'";
	$command=$connection->createCommand($sql);
	$dataReader=$command->queryOne();
	return $dataReader['description'];
}
function getUserName($id){
	$connection = \Yii::$app->db;
	$sql="select first_name,last_name from tbl_user where  id ='$id'";
	$command=$connection->createCommand($sql);
	$dataReader=$command->queryOne();
	return $dataReader['first_name']." ".$dataReader['last_name'];
}
$active = array('0'=>Yii::t('app', 'Inactive'),'1'=>Yii::t('app', 'Active'));
?>

<style>	
.cke_contents{max-height:250px}
.slider .tooltip.top {
    margin-top: -36px;
    z-index: 100;
}
.close {
    color: #000000;
    float: right;
    font-size: 18px;
    font-weight: bold;
    line-height: 1;
    opacity: 0.2;
    text-shadow: 0 1px 0 #ffffff;
}
</style>
<script type="text/javascript">
function Add_Error(obj,msg){
	 $(obj).parents('.form-group').addClass('has-error');
	 $(obj).parents('.form-group').append('<div style="color:#D16E6C; clear:both" class="error"><i class="icon-remove-sign"></i> '+msg+'</div>');
	 return true;
}
function Remove_Error(obj){
	$(obj).parents('.form-group').removeClass('has-error');
	$(obj).parents('.form-group').children('.error').remove();
	return false;
}
function Add_ErrorTag(obj,msg){
	obj.css({'border':'1px solid #D16E6C'});
	
	obj.after('<div style="color:#D16E6C; clear:both" class="error"><i class="icon-remove-sign"></i> '+msg+'</div>');
	 return true;
}
function Remove_ErrorTag(obj){
	obj.removeAttr('style').next('.error').remove();
	return false;
}
$(document).ready(function(e) {
  $('#role_frm').submit(function(event){
		var error='';
		$('#role_frm [data-validation="required"]').each(function(index, element) {
			Remove_Error($(this));
			if($(this).val() == ''){
				error+=Add_Error($(this),"<?=Yii::t('app', 'This Field is Required!')?>");
			}else{
					Remove_Error($(this));							
			}
			if(error !=''){
				event.preventDefault();
			}else{
				return true;
			}
		});
	});
	$('#operation_frm').submit(function(event){
		var error='';
		$('#operation_frm [data-validation="required"]').each(function(index, element) {
			//alert($(this).attr('id'));
			Remove_Error($(this));
			if($(this).val() == ''){
				error+=Add_Error($(this),"<?=Yii::t('app', 'This Field is Required!')?>");
			}else{
					Remove_Error($(this));							
			}
			if(error !=''){
				event.preventDefault();
			}else{
				return true;
			}
		});
	});  
});
//})
</script>
<style>	
.cke_contents{max-height:250px}
.slider .tooltip.top {
    margin-top: -36px;
    z-index: 100;
}
.close {
    color: #000000;
    float: right;
    font-size: 18px;
    font-weight: bold;
    line-height: 1;
    opacity: 0.2;
    text-shadow: 0 1px 0 #ffffff;
}
</style>
<div class="logo-index">

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><?php echo Yii::t ( 'app', 'RBAC Settings' ); ?> <small class="m-l-sm"><?php echo Yii::t ( 'app', 'Changes will be at application level' ); ?></small></h3>
            <div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
			</div>
        </div>
        
        <div class="box-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
	                <li class="active">
	                	<a href="#assignments" role="tab" data-toggle="tab"><?php echo Yii::t ( 'app', 'Assignments' ); ?></a>
	                </li>
					<li><a href="#role" role="tab" data-toggle="tab"><?php echo Yii::t ( 'app', 'Roles' ); ?></a></li>
	                <li><a href="#permission" role="tab" data-toggle="tab"><?php echo Yii::t ( 'app', 'Permissions' ); ?></a></li>
                </ul>
                    
                <div class="tab-content">
                    <div class="tab-pane active" id="assignments"> 
                    	<br/>
                    	<?php if(empty($_REQUEST['assign_user_id'])){ ?>
                    	<?php
							if($users){?>
                        
                    	<?php
				
				Pjax::begin ();
				echo GridView::widget ( [ 
						'dataProvider' => $dataProvider,
						'filterModel' => $searchModel,
						'responsiveWrap' => false,
						'pjax' => true,
						'columns' => [ 
								[ 
										'class' => 'yii\grid\SerialColumn' 
								],
								[ 
										'attribute' => 'id',
										'label' => Yii::t('app', 'Image'),
										'format' => 'raw',
										'width' => '20px',
										'value' => function ($model, $key, $index, $widget)
										{
												$users='<div class="project-people">';
														$path='users/'.$model->id.'.png';
														if(file_exists($path)){
															$image='<img  class="img-circle img-sm" src="'.Url::base().'/users/'.$model->id.'.png">';								
														 }else{ 
															$image='<img class="img-circle img-sm" src="'.Url::base().'/users/nophoto.jpg">';
														 }
														$users.=' <a href="'. Url::to(['/multeobjects/setting/rights', 'assign_user_id' => $model->id]) .'">'.$image.'</a>';	
												$users.='</div>';
												return $users;
										} 
								],
								[ 
										'attribute' => 'first_name',
										'label' => Yii::t('app', 'First Name'),
										'format' => 'raw',
										'width' => '10%',
										'value' => function ($model, $key, $index, $widget)
										{
														$users=' <a href="'. Url::to(['/multeobjects/setting/rights', 'assign_user_id' => $model->id]) .'">'.$model->first_name.'</a>';
												return $users;
										} 
								],
								[ 
										'attribute' => 'last_name',
										'label' => Yii::t('app', 'Last Name'),
										'format' => 'raw',
										'width' => '10%',
										'value' => function ($model, $key, $index, $widget)
										{
														$users=' <a href="'. Url::to(['/multeobjects/setting/rights', 'assign_user_id' => $model->id]) .'">'.$model->last_name.'</a>';
												return $users;
										} 
								],

								'username',

								'email:email',
								
								[ 
										'attribute' => 'id',
										'label'=>Yii::t('app', 'Roles'),
										'format' => 'raw',
										'width' => '10%',
										'value' => function ($model, $key, $index, $widget)
										{
											return getUserRoles($model->id);
										} 
								],
								[ 
										'attribute' => 'id',
										'label'=>Yii::t('app', 'Operations'),
										'format' => 'raw',
										'width' => '150px',
										'value' => function ($model, $key, $index, $widget)
										{
											return getUserOperations($model->id);
										} 
								],
								 [
               'class' => '\kartik\grid\ActionColumn',
				
    			'template'=>'{update} {view} {delete}',
                'buttons' => [
				'width' => '150px',
                'update' => function ($url, $model) {
                                   return  '<a href="'. Url::to(['/multeobjects/setting/rights', 'assign_user_id' => $model->id]).'" class="btn btn-primary btn-xs">'.Yii::t('app', 'Roles & Operations').'</a>';},
				'view' => function ($url, $model) {
                                    return "";},
				'delete' => function ($url, $model) {
					return '';}
				

                ],
								],
								
	 
						],
						'responsive' => true,
						'hover' => true,
						'condensed' => true,
						'floatHeader' => false,
						
						'panel' => [ 
								'heading' => '<i class="glyphicon glyphicon-th-list"></i> '.Yii::t('app', 'Assignments'),
								'type' => 'info',
								'after' => Html::a ( '<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset List'), [ 
										'rights' 
								], [ 
										'class' => 'btn btn-info btn-sm' 
								] ),
								'showFooter' => false 
						] 
				] );
				Pjax::end ();
				?>								
							<?php	}
							}else{
							?>
							<div class="row">
                            
						
                        	<form method="post" class="form-horizontal" action="" enctype="multipart/form-data">
                                <?php Yii::$app->request->enableCsrfValidation = true; ?>
                                <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
                                <div class="form-group">
                                	<div class="col-sm-6">
									<h4><?=Yii::t('app', 'Assignment for User')?> : <?= getUserName($_REQUEST['assign_user_id'])?></h4>
                                        <?php
											if(getUserAssignments() !=''){?>
                                            
                                    	<table class="table table-bordered table-striped">
										<?php 		
											foreach(getUserAssignments() as $assign){
										?>
                                        	<tr>
                                            	<td><?=$assign['item_name']?></td>
                                                <td><?=Yii::t('app',$assign['type']=='2'?'Role':'Operation')?></td>
												<?php
												/* Dont let anyone remove admin role from user: admin 
													Also dont let anyone remove role Customer */
												if((User::findOne($_REQUEST['assign_user_id'])->username == 'admin' && $assign['type'] == 2 && $assign['item_name'] == 'Admin'))
												{
													?>
													<td width="100"></td>
													<?php
												}
												else
												{
												?>
                                                <td width="100"><a href="<?=Url::to(['/multeobjects/setting/rights', 'assign_user_id' => $_REQUEST['assign_user_id'], 'assign_user_remove' => urlencode($assign['item_name'])])?>" onClick="return confirm('<?=Yii::t('app','Are you Sure!')?>')" class="btn btn-danger btn-xs" ><?=Yii::t('app',"Remove")?></a></td>
												<?php
												}
												?>
                                            </tr>
                                        <?php } ?>
                                        </table>
										<?php } else{
											echo Yii::t('app',"no assignment");
										}
											?>
                                            <a href="<?=Url::to(['/multeobjects/setting/rights'])?>" class="btn btn-primary  btn-sm"><?=Yii::t('app',"Back")?></a>
                                    </div>
                                	
									<div class="col-sm-6">
										<h4><?=Yii::t('app', 'Assign Roles and Operations from the below drop down and click Assign')?></h4>
                                    	<?php 
											if($assigment_error){?>
											<div class="alert alert-danger">
                                            	<?php
													foreach($assigment_error as $errors){
														foreach($errors as $error){	?>
													<li><?=$error?></li>		
												<?php	}
													}
												?>
                                            </div>
											
										<?php }	 ?>
										
                                    	<select name="auth_item" class="form-control">
                                        	<optgroup label="Roles">
                                            	<?php
													if($roles){
														foreach($roles as $role){
															?>
														<option><?=$role['name']?></option>	
													<?php	}
													}
												?>
                                            </optgroup>
                                            <optgroup label="Operations">
                                            	<?php
													if($operations){
														foreach($operations as $operation){?>
														<option><?=$operation['name']?></option>	
													<?php	}
													}
												?>
                                            </optgroup>
                                        </select>
										<br>
										<?= Html::submitButton(Yii::t ( 'app', 'Assign' ), ['class' => 'btn btn-primary pull-right']) ?>
                                    </div>
                                   
                                    
                                </div>
                            
                     </form>
					 </div>
                     <?php } ?>
                        </div>
                        <div class="tab-pane table-responsive" id="permission"> 
                            
							 <h4 class="pull-right"><?=Yii::t('app',"Click on the buttons to Assign/Revoke permissions")?></h4>
                             <table class="table table-bordered table-striped">
                             	<thead>
                                	<tr>
                                    	<th rowspan=2> <?=Yii::t('app',"Permission")?></th>
                                        <th colspan=<?=$roles?count($roles):0?> class="text-center"><?=Yii::t('app',"Roles")?></th>
                                    </tr>
									<tr>
                                    	<?php
											foreach($roles as $roleCol){
										?>
                                        <th><?=Yii::t('app',$roleCol['name'])?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                	<?php foreach($operations as $opRow){ ?>
                                       
                                       
                                	<tr>
                                    	 <td><?=$opRow['name']?></td>
                                    	<?php
											foreach($roles as $roleCol){
										?>
                                        <td>
											<?php 
												if(checkParentExists($roleCol['name'],$opRow['name']) =='yes'){
											if($roleCol['name'] == 'Admin' || $roleCol['name'] == 'Vendor')
											{
												?>
												<a href="#" class="btn btn-default btn-xs" onClick="return alert('<?=Yii::t('app','Can not revoke permissons from system role!')?>')"><?=Yii::t('app',"Revoke")?></a>	
												<?php
												continue;
											}
											?>
												<a href="<?=Url::to(['/multeobjects/setting/rights', 'child' => urlencode($opRow['name']), 'parent' => urlencode($roleCol['name']), 'remove_child' => 'true'])?>" class="btn btn-danger btn-xs"  onClick="return confirm('<?=Yii::t('app','Are you Sure!')?>')"><?=Yii::t('app',"Revoke")?></a>	
											<?php }else{ 
											if($roleCol['name'] == 'Admin' || $roleCol['name'] == 'Vendor')
											{
												?>
												<a href="#" class="btn btn-default btn-xs"  onClick="return alert('<?=Yii::t('app','Can not assign permissions to system role!')?>')"><?=Yii::t('app',"Assign")?></a>	
												<?php
												continue;
											}
											?>
													<a href="<?=Url::to(['/multeobjects/setting/rights', 'child' => urlencode($opRow['name']), 'parent' => urlencode($roleCol['name'])])?>" class="btn btn-primary btn-xs" onClick="return confirm('<?=Yii::t('app','Are you Sure!')?>')"><?=Yii::t('app','Assign')?></a>		
											<?php	} ?>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                     <?php } ?>
                                </tbody>
                             </table>
                        </div>
                        <div class="tab-pane" id="role"> 
                            <br/>
                            
                            <?php
							if(empty($_REQUEST['add_role']) && empty($_REQUEST['role_id'])){?>
                            	<a href="<?=Url::to(['/multeobjects/setting/rights', 'add_role' => 'true']) ?>" class="btn btn-primary btn-sm"><?=Yii::t('app',"Add New Role")?></a
								><?php if($roles){?>
                               <table class="table table-bordered table-striped">
                               		<thead>
                                    	<tr>
                                        	<th><?=Yii::t('app',"Role")?></th>
                                            <th><?=Yii::t('app',"Role Description")?></th>
                                            <th><?=Yii::t('app',"Action")?></th>
                                            
                                        </tr>
                                    </thead>
								<?php	foreach($roles as $role){?>
									<tr>
									<?php
									if($role['name'] == 'Admin' || $role['name'] == 'Customer' || $role['name'] == 'Vendor' || $role['name'] == 'Employee')
									{
									?>
										<td><?=$role['name']." ".countChild($role['name'])?></td>
									<?php
									}
									else
									{
									?>
										<td><a href="<?=Url::to(['/multeobjects/setting/rights', 'role_id' => $role['name']])?>"><?=$role['name']." ".countChild($role['name'])?></a></td>
									<?php
									}
									?>
                                        <td><?=$role['description']?></td>
                                        <td>
                                        <?php
											if(!in_array($role['name'],array('Admin','Vendor', 'Employee','Customer'))){
										?>
                                        <a href="<?=Url::to(['/multeobjects/setting/rights', 'role_del' => $role['name']]) ?>" onClick="return confirm('<?=Yii::t('app','Are you Sure!')?>')" class="btn btn-danger btn-xs"><?=Yii::t('app',"Remove")?></a>
                                        <?php } ?>
                                        </td>
									</tr>	
								<?php	}?>
                                </table>
							<?php	}else echo Yii::t('app',"No Data");
							}
							if(!empty($_REQUEST['add_role']) && empty($_REQUEST['role_id'])){
							?>
                            	<h3><?=Yii::t('app',"Add Role")?></h3>
                            	<form method="post" class="form-horizontal" action="" enctype="multipart/form-data" id="role_frm">
                                <?php Yii::$app->request->enableCsrfValidation = true; ?>
                                <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
                                	<?php 
										if($role_add_error){?>
										<div class="alert alert-danger">
											<?php
												foreach($role_add_error as $errors){
													foreach($errors as $error){	?>
												<li><?=$error?></li>		
											<?php	}
												}
											?>
										</div>	
									<?php }	 ?>
                                	<div class="form-group">
                                    	<div class="col-sm-4">
                                        	<label><?=Yii::t('app',"Name")?> <font color="#FF0000">*</font></label>
                                        	<input type="text" class="form-control" name="role_name" id="role_name" data-validation="required" value="<?= isset($_POST['role_name'])?$_POST['role_name']:''?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                    	<div class="col-sm-4">
                                        	<label><?=Yii::t('app',"Description")?> <font color="#FF0000">*</font></label>
                                        	<input type="text" class="form-control" name="role_description" id="role_description" value="<?= isset($_POST['role_description'])?$_POST['role_description']:''?>" data-validation="required">
											</div>
                                    </div>
                                    <div class="form-group">
                                    	 <div class="col-sm-2">
                                        	<a href="<?=Url::to(['/multeobjects/setting/rights']) ?>" class="btn btn-default  btn-sm"><?=Yii::t('app',"Back")?></a>
                                        </div>
										<div class="col-sm-2"  align="right">
                                    	<?= Html::submitButton(Yii::t ( 'app', 'Save' ), ['class' => 'btn btn-primary  btn-sm role_add']) ?>
                                    </div>
                                    </div>
                                </form>
                            <?php }
							if(!empty($_REQUEST['role_id']) && $_REQUEST['role_id'] != 'Admin' && $_REQUEST['role_id'] != 'Customer') {?>
                            	<h3><?=Yii::t('app',"Role")?> : <?=$_REQUEST['role_id']?></h3>
                                <div class="form-group">
                                	<div class="row">
                                	<div class="col-sm-6">
                                    	<h3><?=Yii::t('app',"Relations")?></h3>
                                        <h4><?=Yii::t('app',"Parent")?></h4>
                                        <?php
											if(roleParent()){?>
                                            
                                    	<table class="table table-bordered table-striped">
										<?php 		
											foreach(roleParent() as $roleParent){
										?>
                                        	<tr>
                                            	<td><?=$roleParent['parent']?></td>
                                                <td><?=Yii::t('app',getRoleType($roleParent['parent'])=='2'?'Role':'Operation')?></td>
                                            </tr>
                                        <?php } ?>
                                        </table>
										<?php } else{
											echo Yii::t('app',"This item has no parents.");
										}
											?>
                                            <h4><?=Yii::t('app',"Children")?></h4>
                                            <?php
											if(roleChild()){?>
                                            
                                    	<table class="table table-bordered table-striped">
										<?php 		
											foreach(roleChild() as $roleChild){
										?>
                                        	<tr>
                                            	<td><?=$roleChild['child']?></td>
                                                <td><?=Yii::t('app',getRoleType($roleChild['child'])=='2'?'Role':'Operation')?></td>
                                                <td>
                                                	<a href="<?=Url::to(['/multeobjects/setting/rights', 'child' => urlencode($roleChild['child']), 'parent' => urlencode($_REQUEST['role_id']), 'role_child_del' => 'true', 'role_id' => urlencode($_REQUEST['role_id'])])?>" class="btn btn-danger btn-xs"  onClick="return confirm('<?=Yii::t('app','Are you Sure!')?>')"><?=Yii::t('app','Remove')?></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </table>
										<?php } else{
											echo Yii::t('app',"This item has no children.");
										}
											?>
                                            <br/><a href="<?=Url::to(['/multeobjects/setting/rights'])?>" class="btn btn-primary btn-sm"><?=Yii::t('app',"Back")?></a>
                                    </div>
                                	<div class="col-sm-4 col-sm-offset-2">
									<div class="ibox-content">
                                    	<h3><?=Yii::t('app',"Update Role")?></h3>
                                        <form method="post" action="" enctype="multipart/form-data">
                                <?php Yii::$app->request->enableCsrfValidation = true; ?>
                                <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
                                	<label><?=Yii::t('app',"Role")?></label>
                                	<input type="text" readonly class="form-control" value="<?=$_REQUEST['role_id']?>">
                                    <label><?=Yii::t('app',"Description")?></label>
                                    <input type="text" name="edit_role_description" value="<?=getDescription($_REQUEST['role_id'])?>" class="form-control"><br/>
                                    <?= Html::submitButton(Yii::t ( 'app', 'Update' ), ['class' => 'btn btn-primary  btn-sm']) ?>
                                </form>
								</div>
										<div class="ibox-content">
                                    	<h3><?=Yii::t('app',"Add Child")?></h3>
                                    	<?php 
											if($roleChild_assigment_error){?>
											<div class="alert alert-danger">
                                            	<?php
													foreach($roleChild_assigment_error as $errors){
														foreach($errors as $error){	?>
													<li><?=$error?></li>		
												<?php	}
													}
												?>
                                            </div>	
										<?php }	 ?>
                                        
								<form method="post"  action="" enctype="multipart/form-data">
                                <?php Yii::$app->request->enableCsrfValidation = true; ?>
                                <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
                                    	<select name="role_child_auth_item" class="form-control">
                                        <optgroup label="Roles">
                                            	<?php
													if($roles){
														foreach($roles as $role){?>
														<option><?=$role['name']?></option>	
													<?php	}
													}
												?>
                                            </optgroup>
                                            <optgroup label="Operations">
                                            	<?php
													if($operations){
														foreach($operations as $operation){?>
														<option><?=$operation['name']?></option>	
													<?php	}
													}
												?>
                                            </optgroup>
                                        </select>
                                        <br/>
										<?= Html::submitButton(Yii::t ( 'app', 'Save' ), ['class' => 'btn btn-primary  btn-sm']) ?>
                            
                     </form>
					 </div>
                                    </div>
                                    </div>
                                </div>
							<?php }
							else
							{
								?>
								<?php
							}?>
                        </div>
                        <div class="tab-pane fade" id="operations"> 
                            <br/>	
                            <?php
							if(empty($_REQUEST['add_operation']) && empty($_REQUEST['operation_id'])){?>
                            <h3><?=Yii::t('app',"Operations")?></h3>
                             <a href="<?=Url::to(['/multeobjects/setting/rights', 'add_operation' => 'true']) ?>" class="btn btn-primary btn-sm"><?=Yii::t('app',"Add New")?></a>
							<?php	if($operations){?>
                               <table class="table table-bordered table-striped">
                               		<thead>
                                    	<tr>
                                        	<th><?=Yii::t('app',"Name")?></th>
                                            <th><?=Yii::t('app',"Description")?></th>
                                            <th><?=Yii::t('app',"Data")?></th>
                                        </tr>
                                    </thead>
								<?php	foreach($operations as $operation){?>
									<tr>
										<td><a href="<?=Url::to(['/multeobjects/setting/rights', 'operation_id' => $operation['name']]) ?>"><?=$operation['name']." ".countChild($operation['name'])?></a></td>
                                        <td><?=$operation['description']?></td>
                                        <td><?=$operation['data']?></td>
									</tr>	
								<?php	}?>
                                </table>
							<?php	}else echo Yii::t('app',"No Data");
								
							}
							if(!empty($_REQUEST['add_operation']) && empty($_REQUEST['operation_id'])){
							?>
                            	<h3><?=Yii::t('app',"Add Operation")?></h3>
                            	<form method="post" class="form-horizontal" action="" enctype="multipart/form-data" id="operation_frm">
                                <?php Yii::$app->request->enableCsrfValidation = true; ?>
                                <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
                                	<?php 
										if($operation_add_error){?>
										<div class="alert alert-danger">
											<?php
												foreach($operation_add_error as $errors){
													foreach($errors as $error){	?>
												<li><?=$error?></li>		
											<?php	}
												}
											?>
										</div>	
									<?php }	 ?>
                                	<div class="form-group">
                                    	<div class="col-sm-4">
                                        	<label><?=Yii::t('app',"Name")?> <font color="#FF0000">*</font></label>
                                        	<input type="text" class="form-control" name="operation_name" id="operation_name" data-validation="required" value="<?= isset($_POST['operation_name'])?$_POST['operation_name']:''?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                    	<div class="col-sm-4">
                                        	<label><?=Yii::t('app',"Description")?> <font color="#FF0000">*</font></label>
                                        	<input type="text" class="form-control" name="operation_description" id="operation_description" value="<?= isset($_POST['operation_description'])?$_POST['operation_description']:''?>" data-validation="required">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                    	<div class="col-sm-4">
                                        	<label><?=Yii::t('app',"Data")?></label>
                                        	<input type="text" class="form-control" name="operation_data" value="<?= isset($_POST['operation_data'])?$_POST['operation_data']:''?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                    	<div class="col-sm-2">
                                    	<?= Html::submitButton(Yii::t ( 'app', 'Save' ), ['class' => 'btn btn-primary  btn-sm operation_add']) ?>
                                    </div>
                                        <div class="col-sm-2" align="right">
                                        	<a href="<?=Url::to(['/multeobjects/setting/rights'])?>" class="btn btn-primary  btn-sm"><?=Yii::t('app',"Back")?></a>
                                        </div>
                                    </div>
                                </form>
                                
                            <?php }
							if(!empty($_REQUEST['operation_id'])){?>
                            	<h3><?=Yii::t('app',"Operation")?> : <?=$_REQUEST['operation_id']?></h3>
								
                                <div class="form-group">
                                	<div class="row">
                                        <div class="col-sm-6">
                                            <h3><?=Yii::t('app',"Relations")?></h3>
                                            <h4><?=Yii::t('app',"Parent")?></h4>
                                            <?php
                                                if(operationParent()){?>
                                                
                                            <table class="table table-bordered table-striped">
                                            <?php 		
                                                foreach(operationParent() as $operationParent){
                                            ?>
                                                <tr>
                                                    <td><?=$operationParent['parent']?></td>
                                                    <td><?=Yii::t('app',getRoleType($operationParent['parent'])=='2'?'Role':'Operation')?></td>
                                                </tr>
                                            <?php } ?>
                                            </table>
                                            <?php } else{
                                                echo Yii::t('app',"This item has no parents.");
                                            }
                                                ?>
                                                <h4><?=Yii::t('app',"Children")?></h4>
                                                <?php
                                                if(operationChild()){?>
                                                
                                            <table class="table table-bordered table-striped">
                                            <?php 		
                                                foreach(operationChild() as $operationChild){
                                            ?>
                                                <tr>
                                                    <td><?=$operationChild['child']?></td>
                                                    <td><?=Yii::t('app',getRoleType($operationChild['child'])=='2'?'Role':'Operation')?></td>
                                                    <td>
                                                        <a href="<?=Url::to(['/multeobjects/setting/rights', 'child' => urlencode($operationChild['child']), 'parent' => urlencode($_REQUEST['operation_id']), 'operation_child_del' => 'true', 'operation_id' => urlencode($_REQUEST['operation_id'])]) ?>" class="btn btn-danger btn-xs"  onClick="return confirm('<?=Yii::t('app','Are you Sure!')?>')"><?=Yii::t('app',"Remove")?></a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </table>
                                            <?php } else{
                                                echo Yii::t('app',"This item has no children.");
                                            }
                                                ?><br/>
                                <a href="<?=Url::to(['/multeobjects/setting/rights']) ?>" class="btn btn-primary  btn-sm"><?=Yii::t('app',"Back")?></a>    
                                        </div>
                                        <div class="col-sm-4 col-sm-offset-2">
                                            <h3><?=Yii::t('app',"Update Operation")?></h3>
                                            <form method="post" action="" enctype="multipart/form-data">
                                    <?php Yii::$app->request->enableCsrfValidation = true; ?>
                                    <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
                                        <label><?=Yii::t('app',"Operation")?></label>
                                        <input type="text" readonly class="form-control" value="<?=$_REQUEST['operation_id']?>">
                                        <label><?=Yii::t('app',"Description")?></label>
                                        <input type="text" name="edit_operation_description" value="<?=getDescription($_REQUEST['operation_id'])?>" class="form-control"><br/>
                                        <?= Html::submitButton(Yii::t ( 'app', 'Update' ), ['class' => 'btn btn-primary  btn-sm']) ?>
                                    </form>
                                            <h3><?=Yii::t('app',"Add Child")?></h3>
                                            <?php 
                                                if($operationChild_assigment_error){?>
                                                <div class="alert alert-danger">
                                                    <?php
                                                        foreach($operationChild_assigment_error as $errors){
                                                            foreach($errors as $error){	?>
                                                        <li><?=$error?></li>		
                                                    <?php	}
                                                        }
                                                    ?>
                                                </div>	
                                            <?php }	 ?>
                                            <form method="post" class="form-horizontal" action="" enctype="multipart/form-data">
                                    <?php Yii::$app->request->enableCsrfValidation = true; ?>
                                    <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
                                            <select name="operation_child_auth_item" class="form-control">
                                                <optgroup label="Operations">
                                                    <?php
                                                        if($operations){
                                                            foreach($operations as $operation){?>
                                                            <option><?=$operation['name']?></option>	
                                                        <?php	}
                                                        }
                                                    ?>
                                                </optgroup>
                                            </select>
                                            <br/>
                                            <?= Html::submitButton(Yii::t ( 'app', 'Save' ), ['class' => 'btn btn-primary  btn-sm']) ?>
                                
                         </form>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>  
                        </div>
				</div>
            </div>
    
</div>
</div>
</div>
