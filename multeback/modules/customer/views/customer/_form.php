<?php
use yii\jui\AutoComplete;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

use multebox\models\CustomerType;
use yii\helpers\ArrayHelper;
use multebox\models\Country;
use multebox\models\State;
use multebox\models\City;

/**
 * @var yii\web\View $this
 * @var multebox\models\Customer $model
 * @var yii\widgets\ActiveForm $form
 */

if($model->isNewRecord)
{
	$dFlag = false;
}
else
{
	$dFlag = true;
}

?>

<div class="customer-form">
   <?php
		if(empty($_GET['id']))
		{
			$model->customer_type_id = \multebox\models\DefaultValueModule::getDefaultValueId('customer_type');
		}
		$customerType = array();
		foreach(ArrayHelper::map ( CustomerType::find()->where("active=1")->orderBy ( 'sort_order' )->asArray ()->all (), 'id', 'label') as $key => $ct)
		{
			$customerType[$key]=$ct;
		}
		$form = ActiveForm::begin ( [ 
				'type' => ActiveForm::TYPE_VERTICAL ,
				'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']]  //this helps to show icons in validation messages 
		] );?>
		
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Customer Details' ); ?></h3>
			</div>
			
			<div class="panel-body">
			<?php
				echo Form::widget ( [ 
						'model' => $model,
						'form' => $form,
						'columns' => 2,
						'attributes' => [ 
								'customer_name' => [ 
										'type' => Form::INPUT_TEXT,
										'label' => Yii::t('app', 'Login Username'),
										'options' => [ 
												'placeholder' => Yii::t ( 'app', 'Enter User Name' ).'...',
												'maxlength' => 255,
												'disabled' => $dFlag
										],
								],
								'customer_type_id' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'label' => Yii::t ( 'app', 'Customer Type' ),
										'options' => [ 
												'prompt' => '--'.Yii::t ( 'app', 'Customer Type' ).'--',
										],
										'items' => $customerType
								]
						] 
				]);
				
			?>
			</div>
		</div>

		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Contact Details' ); ?></h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label"><?php echo Yii::t ( 'app', 'First name' ); ?></label>
							<input type="text" name="first_name" data-validation="required" mandatory-field class="form-control">
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label"><?php echo Yii::t ( 'app', 'Last Name' ); ?></label>
							<input type="text" name="last_name" data-validation="required" mandatory-field class="form-control">
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label"><?php echo Yii::t ( 'app', 'Email' ); ?></label>
							<input type="text" name="email" data-validation="required" email-validation mandatory-field class="form-control">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label"><?php echo Yii::t ( 'app', 'Phone' ); ?></label>
							<input type="text" name="phone" data-validation="required" num-validation class="form-control">
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label"><?php echo Yii::t ( 'app', 'Mobile' ); ?></label>
							<input type="text" name="mobile" data-validation="required" num-validation mandatory-field class="form-control">
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label"><?php echo Yii::t ( 'app', 'Fax' ); ?></label>
							<input type="text" name="fax" data-validation="required" num-validation class="form-control">
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Address Details' ); ?></h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label"><?php echo Yii::t ( 'app', 'Address 1' ); ?></label>
							<input type="text" name="address_1" data-validation="required" mandatory-field class="form-control">
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label"><?php echo Yii::t ( 'app', 'Address 2' ); ?></label>
							<input type="text" name="address_2" class="form-control">
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label"><?php echo Yii::t ( 'app', 'Zipcode' ); ?></label>
							<input type="text" name="zipcode" data-validation="required" mandatory-field class="form-control">
						</div>
					</div>
				</div>
		
				<?php
					echo '<div class="row">
							<div class="col-sm-4">
								<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'Country').'</label>
							'.Html::dropDownList('country_id',  \multebox\models\DefaultValueModule::getDefaultValueId('country'),
		 ArrayHelper::map(Country::find()->orderBy('country')->where('active=1')->asArray()->all(), 'id', 'country'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'country_id','data-validation'=>'required' ,'mandatory-field'=>'' ]  ).'</div></div>
							<div class="col-sm-4">
							<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'State').'</label>
							'.Html::dropDownList('state_id', 'state_id',
		 ArrayHelper::map(State::find()->where('id=0')->orderBy('state')->asArray()->all(), 'id', 'state'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'state_id', 'data-validation'=>'required' ,'mandatory-field'=>'' ]  ).'</div></div>
						<div class="col-sm-4">
							<div class="form-group required">
									<label class="control-label">'.Yii::t('app', 'City').'</label>
							';/*.Html::dropDownList('city_id', 'city_id',
		 ArrayHelper::map(City::find()->where('id=0')->orderBy('city')->asArray()->all(), 'id', 'city'), ['prompt' => '--'.Yii::t ( 'app', 'Select' ).'--','class'=>'form-control','id'=>'city_id' ]  ).'</div></div></div>';*/

		 echo AutoComplete::widget([
									  'name' => 'city_id',
									  'clientOptions' => [
										  'source' => [],
									  ],
									  'options' => ['placeholder' => Yii::t ( 'app', 'Type few letters and select from matching list' ), 'class' => 'form-control', 'id' => 'city_id']
								  ]).'</div></div></div>';
		 ?>
		 
		 </div>
		</div>
		<?php
		if($model->isNewRecord)
		{
		?>
			<input type="hidden" name="Customer[added_by_id]" class="form-control" value="<?=Yii::$app->user->identity->id?>">
		<?php
		}
		?>
		<input type="hidden" name="Customer[active]" class="form-control" value="0">

		<?php
				echo Html::submitButton ( $model->isNewRecord ? Yii::t ( 'app', 'Create' ) : Yii::t ( 'app', 'Update' ), [ 
							'class' => $model->isNewRecord ? 'btn btn-success btn-sm customer_submit' : 'btn btn-primary btn-sm customer_submit' 
					] );
				ActiveForm::end ();
		?>
		
</div>