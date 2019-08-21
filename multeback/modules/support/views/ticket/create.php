<?php
use yii\helpers\Html;
use yii\helpers\Url;
/**
 * @var yii\web\View $this
 * @var multebox\models\Ticket $model
 */
$this->title = Yii::t('app', 'Create Ticket');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tickets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
function getUserRoleCounts(){
	$connection = \Yii::$app->db;
	$id = Yii::$app->user->identity->id;
	$sql="select auth_item.* from auth_item,auth_assignment where auth_item.type=2 and auth_assignment.user_id=$id and auth_assignment.item_name=auth_item.name and auth_item.name='Customer'";
		$command=$connection->createCommand($sql);
		$dataReader=$command->queryAll();
		return $dataReader?count($dataReader):0;	
}
?>

<script>

$(document).ready(function(e) {

	$('#ticket-ticket_impact_id').change(function(){
		
		var priority = $('#ticket-ticket_priority_id :selected').val();
		var impact = $('#ticket-ticket_impact_id :selected').val();
	 if($('#ticket-ticket_priority_id').val()=='' || $('#ticket-ticket_impact_id').val()==''){
		 
	 }else{
		 $.post("<?=Url::to(['/support/ticket/ajax-ticket-sla'])?>", { 'ticket_priority_id': priority, 'ticket_impact_id' : impact, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(data){
				if(data)
					alert(data);
			});
	 }
	})
	
	$('#ticket-ticket_priority_id').change(function(){
		
		var priority = $('#ticket-ticket_priority_id :selected').val();
		var impact = $('#ticket-ticket_impact_id :selected').val();
	if($('#ticket-ticket_priority_id').val()=='' || $('#ticket-ticket_impact_id').val()==''){
		
	}else{
		$.post("<?=Url::to(['/support/ticket/ajax-ticket-sla'])?>", { 'ticket_priority_id': priority, 'ticket_impact_id' : impact, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(data){
					if(data)
						alert(data);
				});
	 }
	})
	
	$('#ticket-department_id').change(function(){
	 $.post("<?=Url::to(['/support/ticket/ajax-department-queue'])?>", { 'department_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(r){
		$('#ticket-queue_id').html(r) ;
	 });
			
	 $.post("<?=Url::to(['/support/ticket/ajax-ticket-category'])?>", { 'department_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(r){
		$('#ticket-ticket_category_id_1').html(r) ;
	 })
   })

	 $('#ticket-queue_id').change(function(){
	 $.post("<?=Url::to(['/support/ticket/ajax-queue-users'])?>", { 'queue_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(r){
		$('#ticket-user_assigned_id').html(r) ;
	 })
   })
	
	$('#ticket-ticket_category_id_1').change(function(){
	 $.post("<?=Url::to(['/support/ticket/ajax-category-change'])?>", { 'ticket_category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(r){
		$('#ticket-ticket_category_id_2').html(r) ;
	 })
   })
   $('#w0').submit(function(event){
		error ='';
		$('#cke_1_contents').parent().parent().removeAttr('style').next('.error').remove();
		sageLength = CKEDITOR.instances['ticket-ticket_description'].getData().replace(/<[^>]*>/gi, '').length;
		if(sageLength==0){
			Add_ErrorTag($('#cke_1_contents').parent().parent(),'<?=Yii::t ('app','This Field is Required!')?>');
		event.preventDefault();
		}
	})
});
</script>
<div class="ticket-create">
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
			<?= $this->render('_form', [
				'model' => $model,
			]) ?>
		</div>

	</div>
</div>
