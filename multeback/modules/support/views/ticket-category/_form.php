<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use multebox\models\Department;
use multebox\models\TicketCategory;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var multebox\models\TicketCategory $model
 * @var yii\widgets\ActiveForm $form
 */

$dFlag=false;
$aFlag=false;
if (!empty($_REQUEST['parent_id']))
{
	$dFlag=true;
}
if (!$model->isNewRecord)
{
	$aFlag=true;
}

?>

<script src="<?=Url::base()?>/bower_components/ckeditor/ckeditor.js"></script>

<div class="ticket-category-form">
    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_VERTICAL]); 
	
	echo Form::widget([
    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [
'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>Yii::t('app', 'Enter Name...'), 'maxlength'=>255, 'disabled' => $aFlag]], 
'label'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>Yii::t('app', 'Enter Label...'), 'maxlength'=>255]], 
'active' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										//'label' => 'Active',
										'options' => [ 
												'placeholder' => Yii::t('app', 'Enter State').' ...' 
										] ,
										'columnOptions'=>['colspan'=>1],
										'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
										'options' => [ 
                                                'prompt' => '--'.Yii::t('app', 'Select Status').'--'
                                        ]
								], 
								
'department_id'=>['type'=> Form::INPUT_DROPDOWN_LIST, 
	'options'=>['placeholder'=>Yii::t('app', 'Enter Department ID...'),'prompt'=>Yii::t('app','--Department--'), 'disabled'=>$dFlag],
	'items'=>ArrayHelper::map(Department::find()->where("active=1")->orderBy('sort_order')->all(),'id','name')], 
//'sort_order'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Sort Order...']],  
///'description'=>['type'=> Form::INPUT_TEXTAREA, 'options'=>['placeholder'=>'Enter Description...','rows'=> 6]],
//'added_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Added At...']], 
//'updated_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Updated At...']], 
    ]

    ]);
	if(!empty($_REQUEST['parent_id'])){
		echo '<input type="hidden" value="'.$_REQUEST['parent_id'].'"  name="TicketCategory[parent_id]" >';
		echo '<input type="hidden" value="'.$model->department_id.'"  name="TicketCategory[department_id]" >';
	}
	if($model->isNewRecord){
		
		
	echo Form::widget([
    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [
					'description'=>['type'=> Form::INPUT_TEXTAREA, 'options'=>['placeholder'=>Yii::t('app', 'Enter Description...'),'rows'=> 6]],
			]

    ]);
	
	if(empty($_REQUEST['parent_id']))
	{
	?>
		<input type="hidden" name="TicketCategory[parent_id]" class="form-control" value="0">
	<?php
	}
    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-sm' : 'btn btn-primary btn-sm']);
     
	
	
	ActiveForm::end();
	}

	?>
</div>

<script>
  $(function () {
    CKEDITOR.replace('ticketcategory-description');
  })
</script>