<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use multebox\models\UserRole;
use multebox\models\UserType;
use multebox\models\Vendor;
use multebox\models\Status;
use multebox\models\search\MulteModel;

/**
 *
 * @var yii\web\View $this
 * @var common\models\User $model
 * @var yii\widgets\ActiveForm $form
 */
?>

 <div class="box box-default">
	<div class="box-title">
		<h5> <?php echo $this->title; if(isset($_REQUEST['id']) && $_REQUEST['id']){ ?> <span class="pull-right label <?=$model->active =='1'?'label-primary':'label-danger'?>"> <?=$model->active =='1'?'Active':'Inactive'?> </span><?php } ?></h5>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
		</div>
	</div>
	<div class="box-body">
		<div class="user-form">

    <?php
				
				$form = ActiveForm::begin ( [ 
						'type' => ActiveForm::TYPE_VERTICAL ,
						'options'=>array('enctype' => 'multipart/form-data')
				] );?>
                
               <div class="row">
               		<div class="<?php echo  isset($_REQUEST['id'])?'col-sm-9':'col-sm-12'?>">
                    <?php
				echo Form::widget ( [ 
						
						'model' => $model,
						'form' => $form,
						'columns' => 3,
						'attributes' => [ 
								
								'first_name' => [ 
										'type' => Form::INPUT_TEXT,
										'options' => [ 
												'placeholder' => Yii::t('app', 'Enter First Name').'...',
												'maxlength' => 255 
										] 
								],
								
								'last_name' => [ 
										'type' => Form::INPUT_TEXT,
										'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Last Name').'...',
												'maxlength' => 255 
										] 
								],
								
								'username' => [ 
										'type' => Form::INPUT_TEXT,
										'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Username...'),
												'maxlength' => 255 
										] 
								],
								
								'email' => [ 
										'type' => Form::INPUT_TEXT,
										'options' => [ 
												'placeholder' => Yii::t('app', 'Enter Email').'...',
												'maxlength' => 255 
										] 
								],
								
								'user_type_id' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'options' => [ 
												'placeholder' => Yii::t('app', 'Select User Type...') ,
												'prompt' => '--'.Yii::t('app', 'Select Type').'--'
										],
										'items'=>ArrayHelper::map(UserType::find()->where("active=1 and type != 'Customer'")->orderBy('sort_order')->asArray()->all(), 'id', 'label')
								],
								
								'entity_id' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'options' => [ 
												'placeholder' => Yii::t('app', 'Select Vendor...'),
												'prompt' => '--'.Yii::t('app', 'Select').'--'
										],
										'items'=>ArrayHelper::map(Vendor::find()->asArray()->all(), 'id', 'vendor_name'),
								],
						] 
				] );
				/*if(empty($_REQUEST['id'])){
				echo Form::widget ( [ 
						
						'model' => $model,
						'form' => $form,
						'columns' => 4,
						'attributes' => [ 
								
								'password_hash'=>['type'=> Form::INPUT_PASSWORD, 'options'=>['placeholder'=>'Enter Password...', 'maxlength'=>255,
												'data-validation'=>'required']]
						] 
				] );
				}*/
				echo Form::widget ( [ 
						
						'model' => $model,
						'form' => $form,
						'columns' => 1,
						'attributes' => [ 
								
								'about' => [ 
										'type' => Form::INPUT_TEXTAREA,
										'label' => Yii::t('app', 'About'),
										'options' => [ 
												'placeholder' => Yii::t('app', 'About').'...',
												'maxlength' => 255 
										] 
								]
						] 
				] );
				echo '</div>';
				if(isset($_REQUEST['id'])){?>
                <div class="col-sm-3">
                	<div id="picture_preview"></div>
                            <label><?php echo Yii::t('app', 'Photo'); ?> 
                            
                            </label><br/>
                            <?php
                                if(MulteModel::fileExists(Yii::$app->params['web_url'].'/users/'.$model->id.'.png')){?>
                                    <img src="<?=Yii::$app->params['web_url']?>/users/<?=$model->id?>.png" style="height:185px;" class="upload  img-responsive">								
                                <?php }else{?>
                                    <img src="<?=Url::base()?>/nophoto.jpg" style="height:185px;" class="upload  img-responsive">
                                <?php }
                            ?>
                            <input type="file" name="user_image" class="inp">
                            	<br/><br/>
                 </div>
				<?php }
				echo '</div>';

				if($model->isNewRecord)
				{
				?>
					<input type="hidden" name="User[password_hash]" class="form-control" value="0">
				<?php
				}

				if(empty($_REQUEST['id'])){
			
				echo Html::submitButton ( $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [ 
						'class' => $model->isNewRecord ? 'btn btn-success btn-sm' : 'btn btn-primary btn-sm' 
				] );
				
				} 
				
				ActiveForm::end ();
				?>

</div>

</div>
</div>
