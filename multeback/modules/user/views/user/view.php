<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use multebox\models\search\History;
use multebox\models\search\MulteModel;
/**
 * @var yii\web\View $this
 * @var common\models\User $model
 */

$this->title = "Profile: ".$model->first_name." ".$model->last_name ;
if(Yii::$app->params['user_role'] == 'admin')
{
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;
function getUserRoles($id){
	$connection = \Yii::$app->db;
		$sql="select auth_item.* from auth_item,auth_assignment where auth_item.type=2 and auth_assignment.user_id=".$id." and auth_assignment.item_name=auth_item.name";
		$command=$connection->createCommand($sql);
		$dataReader=$command->queryAll();
		if($dataReader){
			$roles = '';
			foreach($dataReader as $role){
				$roles.=$role['name']." ";
			}
		}else{
			return '<span class="label label-danger">'.Yii::t('app', 'No Roles').'</span>';
		}
		
		return $roles;	
}
?>

<!-- Begin Profile Image Section -->
<div class="row">
        <div class="col-md-3">
				<div><h5><?php echo Yii::t('app', 'Profile'); ?> <span class="pull-right label <?=$model->active =='1'?'label-primary':'label-danger'?>"> <?=$model->active =='1'?Yii::t('app', 'Active'):Yii::t('app', 'Inactive')?> </span></h5>
				</div>
          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
			 <?php 
			 if(MulteModel::fileExists(Yii::$app->params['web_url'].'/users/'.$model->id.'.png'))
			 {
			 ?>
              <img class="profile-user-img img-responsive img-circle" src="<?=Yii::$app->params['web_url']?>/users/<?=$model->id?>.png" alt="User profile picture">								
             <?php
			 }
			 else
			 {
			 ?>
			  <img class="profile-user-img img-responsive img-circle" src="<?=Url::base()?>/nophoto.jpg" class="img-circle">
             <?php
			 }
			 ?>
              <h3 class="profile-username text-center"><?php echo $model->first_name." ".$model->last_name; ?></h3>

              <p class="text-muted text-center">
				<i class="fa fa-bookmark"></i> <?php echo getUserRoles($model->id); ?>&nbsp;&nbsp;&nbsp;&nbsp;
				<i class="fa fa-smile-o"></i><?php echo $model->userType->label; ?>
			  </p>
			  <p class="text-muted text-center"><i class="fa fa-envelope"></i> <?php echo $model->email; ?></p>
              <p class="text-muted text-center"><i class="fa fa-user"></i> <?php echo $model->username; ?></p>

              <a href="<?=Url::to(['/user/user/update', 'id' => $model->id])?>" class="btn btn-success btn-block"><b><?=Yii::t('app', 'Update Profile')?></b></a> 
			  <a href="<?=Url::to(['/user/user/mail-compose', 'id' => $model->id])?>" class="btn btn-primary btn-block"><b><?=Yii::t('app', 'Send Message')?></b></a>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary">
            <div class="box-header with-border text-center">
              <h3 class="box-title"><?=Yii::t('app', 'About Me')?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
             <?php echo Html::decode($model->about); ?>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
<!-- End Profile Image Section -->

<!-- Begin Activities Section -->
<div class="col-md-9">
<div><h5><?php echo Yii::t('app', 'Activities'); ?></h5>
				</div>
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#activity" data-toggle="tab"><?=Yii::t('app', 'Activity')?></a></li>
        </ul>
		<div class="tab-content">
			<div class="active tab-pane" id="activity">
				<?php
				foreach(History::getUserActivities($model->id) as $row)
				{
				?>	
				<div class="post">
					<a href="#" class="pull-left">
	                    <?php if(file_exists('users/'.$model->id.'.png')){?>
	                            <img src="<?=Url::base()?>/users/<?=$model->id?>.png" class="img-circle img-sm">								
                        <?php }else{?>
                                <img src="<?=Url::base()?>/users/nophoto.jpg" class="img-circle img-sm">
                        <?php }?>
                    </a>
					<div class="user-block">
						<strong><?=ucwords($row['entity_type'])?></strong>. <br>
                        <small class="text-muted"><?=date('F d,Y',$row['added_at'])?></small>
                        <div class="well"><?=$row['notes']?></div>
					</div>
				</div>
				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
<!-- End Activities Section -->

</div>