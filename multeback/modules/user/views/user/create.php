<?php

use yii\helpers\Html;
use multebox\models\search\UserType as UserTypeSearch;

/**
 * @var yii\web\View $this
 * @var common\models\User $model
 */

$this->title = Yii::t('app', 'Create User', [
    'modelClass' => 'User',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<script>
function addError(obj,error){
	$(obj).parent().addClass('has-error');
	$(obj).next('.help-block').text(error);
}
function removeError(obj){
	$(obj).parent().removeClass('has-error');
	$(obj).next('.help-block').text('');
}
	$(document).ready(function(e) {
        if($('#user-user_type_id').val() ==<?=UserTypeSearch::getCompanyUserType('Vendor')->id?>){
				 $('.field-user-entity_id').show();
			}else{
				 $('.field-user-entity_id').hide();
			}
		$('#user-user_type_id').change(function(){
            if($(this).val() ==<?=UserTypeSearch::getCompanyUserType('Vendor')->id?>){
				 $('.field-user-entity_id').show();
			}else{
				 $('.field-user-entity_id').hide();
			}
		})
		$('#w0').submit(function(){
			if($('#user-user_type_id').val() ==<?=UserTypeSearch::getCompanyUserType('Vendor')->id?>){
				if($('#user-entity_id').val() == '')
				{
					addError($('#user-entity_id'),'<?=Yii::t ('app','This Field is Required!')?>');
					return false;
				}
			}else{
				 removeError($('#user-entity_id'));
			}
            if($('#user-user_type_id').val() ==<?=UserTypeSearch::getCompanyUserType('Vendor')->id?>){
				 $('.field-user-entity_id').show();
			}else{
				 $('.field-user-entity_id').hide();
			}
		})
    });
</script>
<div class="user-create">
	<!--
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
	-->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
