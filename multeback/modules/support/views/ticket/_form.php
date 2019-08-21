<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use yii\helpers\ArrayHelper;
use multebox\models\TicketPriority;
use multebox\models\TicketImpact;
use multebox\models\TicketStatus;
use multebox\models\TicketCategory;
use multebox\models\User;
use multebox\models\Queue;
use multebox\models\Customer;
use multebox\models\Department;
/**
 * @var yii\web\View $this
 * @var multebox\models\Ticket $model
 * @var yii\widgets\ActiveForm $form
 */
?>
<script src="<?=Url::base()?>/bower_components/ckeditor/ckeditor.js"></script>

<div class="ticket-form">

    <?php

	if ($model->due_date != '')
	{
		$model->due_date=date('Y/m/d H:i:s', $model->due_date);	// H for 24 hrs format. Use h for 12 hrs format
	}

	$dFlag = false;
	if(!empty($_GET['customer_id']) || !$model->isNewRecord)
	{
		if (!empty($_GET['customer_id']))
			$model->ticket_customer_id=$_GET['customer_id'];
		$dFlag =true;
	}

	$queues = ArrayHelper::map ( Queue::find ()->where("id=0")->orderBy ( 'queue_title' )->asArray ()->all (), 'id', 'queue_title' ) ;	
	$users=ArrayHelper::map ( User::find ()->where('id=0')->orderBy ( 'first_name' )->asArray ()->all (), 'id', 
								function ($user, $defaultValue) 
								{
									$username=$user['username']?$user['username']:$user['email'];
									return $user['first_name'] . ' ' . $user['last_name'].' ('.$username.')';
								}
							);	
	$category1 = ArrayHelper::map ( TicketCategory::find ()->where("id=0")->orderBy ( 'sort_order' )->asArray ()->all (), 'id', 'label' ); 
	$category2 = ArrayHelper::map ( TicketCategory::find ()->where("id=0")->orderBy ( 'sort_order' )->asArray ()->all (), 'id', 'label' );

	if($model->isNewRecord)
		$form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL ]);

	echo Form::widget ( [ 
	'model' => $model,
    'form' => $form,
    'columns' => 4,
    'attributes' => [
		'ticket_title'=>[
					'type'=> Form::INPUT_TEXT, 
					'options'=>[
							'placeholder'=>Yii::t ( 'app', 'Enter Subject...'), 
							'maxlength'=>255] ,
										'columnOptions' => [ 
												'colspan' => 3
										]], 
		'ticket_customer_id' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'options' => [ 
												'placeholder' => Yii::t('app', 'Enter ').Yii::t('app','Customer').' ID...' 
										],
										'columnOptions'=>['colspan'=>1],
										'items'=>ArrayHelper::map(Customer::find()->orderBy('customer_name')->asArray()->all(), 'id', 'customer_name')  , 
										'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select ').Yii::t('app','Customer').'--',
												'disabled' => $dFlag,
                                        ] 
								]
					]
					]);

	echo Form::widget ( [ 
	'model' => $model,
    'form' => $form,
    'columns' => 4,
    'attributes' => [
							

								'ticket_status' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'items' => ArrayHelper::map ( TicketStatus::find ()->where("active=1")->orderBy ( 'sort_order' )->asArray ()->all (), 'status', 'label' )  , 
										'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select ').Yii::t ( 'app', 'Status' ).'--'
                                        ] 
								],

								'ticket_priority_id' => [ 
									'type' => Form::INPUT_DROPDOWN_LIST,
									'items' => ArrayHelper::map ( TicketPriority::find ()->where("active=1")->orderBy ( 'sort_order' )->asArray ()->all (), 'id', 'label' )  , 

										'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select ').Yii::t ( 'app', 'Priority' ).'--'
                                        ] 
								],
						
								'ticket_impact_id' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'items' => ArrayHelper::map ( TicketImpact::find ()->where("active=1")->orderBy ( 'sort_order' )->asArray ()->all (), 'id', 'label' )  , 
										'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select ').Yii::t ( 'app', 'Impact' ).'--'
                                        ] 
								],
									
								'department_id' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'options' => [ 
												'placeholder' => Yii::t('app', 'Enter ').Yii::t('app','Department').' ID...' 
										],

										//'columnOptions'=>['colspan'=>2],
										'items'=>ArrayHelper::map(Department::find()->orderBy('name')->asArray()->all(), 'id', 'name')  , 
										'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select ').Yii::t('app','Department').'--'
                                        ] 
								],

								
						]
					]);

	echo Form::widget([
    'model' => $model,
    'form' => $form,
    'columns' => 4,
    'attributes' => [

	
						'queue_id' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'items' =>$queues ,  
										'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select ').Yii::t ( 'app', 'Queue' ).'--'
                                        ] 
								],


								'user_assigned_id' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'items' =>$users, 
										'options' => [ 
                                                'prompt' => '--'.Yii::t ( 'app', 'Select User' ).'--'
                                        ] 
								], 


						'ticket_category_id_1' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'items' => $category1, 
										'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select ').Yii::t ( 'app', 'Category 1' ).'--'
                                        ] 
								],
    					'ticket_category_id_2' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'options' => [ 
												'placeholder' => Yii::t('app', 'Enter ').Yii::t('app','Category 2').' '.Yii::t('app', 'ID...'), 
										],
										
										'items' => $category2, 
										'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select ').Yii::t('app','Category 2').'--'
                                        ] 
								],
					
						'due_date' => [ 
										'type' => Form::INPUT_WIDGET,
										'widgetClass' => DateControl::classname (),
										'options' => [ 
												'language' => 'eg',
												'type' => DateControl::FORMAT_DATETIME,
												'disabled' => true,
										] 
								],

			]
    ]);
	if($model->isNewRecord){
		echo Form::widget ( [ 
		'model' => $model,
		'form' => $form,
		'columns' => 1,
		'attributes' => [
			'ticket_description'=>['type'=> Form::INPUT_TEXTAREA, 'options'=>['placeholder'=>Yii::t('app', 'Enter Description...'),'rows'=> 6]], 
			]
			]);
	
			echo Html::submitButton ( $model->isNewRecord ?Yii::t('app','Create')  :Yii::t('app','Update') , [ 
						'class' => $model->isNewRecord ? 'btn btn-success update_ticket' : 'btn btn-primary update_ticket' 
				] );

			ActiveForm::end ();
				}
				
	?>
</div>

<script>
  $(function () {
    CKEDITOR.replace('ticket-ticket_description');
  })
</script>