<?php

use yii\helpers\Html;
use kartik\builder\Form;
use multebox\models\search\StateTax;
use kartik\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var multebox\models\Tax $model
 */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Tax',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Taxes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="tax-update">
     <div class="box box-default">
	 <div class="box-header with-border">
	 	<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
		</div>
        <div class="box-title">
            <h5> <?=$this->title ?></h5>
		</div>
	</div>

         <div class="box-body">
    <div class="tax-form">

    <?php 
	
	$form = ActiveForm::begin(['type'=>ActiveForm::TYPE_VERTICAL]);
	
	echo Form::widget([
		'model' => $model,
		'form' => $form,
		'columns' => 3,
		'attributes' => [

							'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>Yii::t('app', 'Enter Name...'), 'maxlength'=>255]], 

							'tax_percentage'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>Yii::t('app', 'Enter Tax Percentage...'), 'maxlength'=>10]], 

							'active' => [ 
											'type' => Form::INPUT_DROPDOWN_LIST,
											//'label' => 'Active',
											'options' => [ 
													'placeholder' => Yii::t('app', 'Enter State ...') 
											] ,
											'columnOptions'=>['colspan'=>1],
											'items'=>array('0'=> Yii::t('app', 'No') ,'1'=> Yii::t('app', 'Yes'))  , 
											'options' => [ 
													'prompt' => '--'.Yii::t('app', 'Select').'--'
											]
										],

						]


	]);

	

	?>
</div></div></div>

<div class="state-tax">
 <?php
                                        
		$searchModel = new StateTax();
		$dataProvider = $searchModel->searchTax( Yii::$app->request->getQueryParams (), $model->id );
		echo Yii::$app->controller->renderPartial("../state-tax/index", [ 
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel 
		] );
                        
		echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
		echo ' <a href="javascript:void(0)" class="btn btn-success" onClick="$(\'.add_taxes\').modal(\'show\');"><i class="glyphicon glyphicon-usd"></i> '.Yii::t('app', 'Add State Tax').'</a>';
		
		ActiveForm::end();
?>
</div>

<?php
	include_once('add_taxes.php');
?>
