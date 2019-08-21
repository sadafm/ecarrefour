<?php

use yii\helpers\Html;
use yii\helpers\Url;
use multebox\modules\support\controllers\TicketController;
use yii\helpers\ArrayHelper;
use multebox\models\FileModel;
use multebox\models\TicketStatus;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use multebox\models\search\MulteModel;
use multebox\models\search\Ticket as TicketSearch;
use multebox\models\search\TicketResolution as TicketResolutionSearch;

/**
 * @var yii\web\View $this
 * @var multebox\models\Ticket $model
 */

if(isset($_REQUEST['err_msg']))
{
	?>
	<script>
	alert("<?=$_REQUEST['err_msg']?>");
	</script>
	<?php
}

$this->title = Yii::t('app', $model->ticket_id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tickets'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

function getUserRoleCounts(){
	$connection = \Yii::$app->db;
	$id = Yii::$app->user->identity->id;
	$sql="select auth_item.* from auth_item,auth_assignment where auth_item.type=2 and auth_assignment.user_id=$id and auth_assignment.item_name=auth_item.name and auth_item.name='Customer'";
		$command=$connection->createCommand($sql);
		$dataReader=$command->queryAll();
		return $dataReader?count($dataReader):0;	
}
?>


<script src="<?=Url::base()?>/bower_components/ckeditor/ckeditor.js"></script>
<script>

$(document).ready(function(){
	if('<?=!empty($_REQUEST['attach_update'])?$_REQUEST['attach_update']:''?>' !=''){
		$('.popup').modal('show');
	}

	if('<?=!empty($_GET['note_id'])?$_GET['note_id']:''?>' !=''){
		$('.edit-notes-modal').modal('show');
	}

	$('#ticket-department_id').change(function(){
	$.post("<?=Url::to(['/support/ticket/ajax-department-queue'])?>", { 'department_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(r){
		$('#ticket-queue_id').html(r) ;
	 });

	$.post("<?=Url::to(['/support/ticket/ajax-ticket-category'])?>", { 'department_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(r){
		$('#ticket-ticket_category_id_1').html(r) ;
	 })
	})
		
	 $('#ticket-queue_id').load("<?=Url::to(['/support/ticket/ajax-department-queue', 'department_id' => $model->department_id, 'queue_id' => $model->queue_id])?>");

	 $('#ticket-ticket_category_id_1').load("<?=Url::to(['/support/ticket/ajax-ticket-category', 'department_id' => $model->department_id, 'ticket_category_id_1' => $model->ticket_category_id_1])?>");

	$('#ticket-queue_id').change(function(){
	 $.post("<?=Url::to(['/support/ticket/ajax-queue-users'])?>", { 'queue_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(r){
		$('#ticket-user_assigned_id').html(r) ;
	 })
	})
	 $('#ticket-user_assigned_id').load("<?=Url::to(['/support/ticket/ajax-queue-users', 'queue_id' => $model->queue_id, 'user_id' => $model->user_assigned_id])?>");

	$('#ticket-ticket_category_id_1').change(function(){
	 $.post("<?=Url::to(['/support/ticket/ajax-category-change'])?>", { 'ticket_category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(r){
		$('#ticket-ticket_category_id_2').html(r) ;
	 })
   })
	$('#ticket-ticket_category_id_2').load("<?=Url::to(['/support/ticket/ajax-category-change', 'ticket_category_id' => $model->ticket_category_id_1, 'ticket_category_id_2' => $model->ticket_category_id_2])?>");

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

});
</script>
<?php
$form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL ]); 
?>

<div class="ticket-update">
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
				'form' => $form,
			]) ?>
		</div>

					
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#desc" role="tab" data-toggle="tab"><?= Yii::t('app', 'Ticket Description')?></a></li>
				<?php
				if(Yii::$app->user->can('Ticket.Update'))
				{
				?>
					<li><a href="#attachments" role="tab" data-toggle="tab"><?= Yii::t('app', 'Attachments')?>	
						  <span class="badge"> <?= FileModel::getAttachmentCount('ticket',$model->id)?></span>
					</a></li>

					<li><a href="#notes" role="tab" data-toggle="tab"><?= Yii::t('app', 'Notes')?></a></li>
				<?php
				}
				?>

			</ul>
			
			<div class="tab-content">
				<div class="tab-pane  active" id="desc"> 
				<br/>

			<?php
			echo '<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
								<label class="control-label" for="lname">'.Yii::t('app', 'Description').':
								</label>

								<div class="controls">
								<textarea id="ticket-ticket_description" class="form-control" name="Ticket[ticket_description]" rows="6" placeholder="'.Yii::t('app', 'Enter Description').'...">'.$model->ticket_description.'</textarea>
								</div>
							</div>
							</div>
						</div>';
			?>

			</div>
			<div class="tab-pane" id="attachments"> 
			<br/>
            <?php
 
								$searchModelAttch = new MulteModel();
								$dataProviderAttach = $searchModelAttch->searchAttachments( Yii::$app->request->getQueryParams (), $model->id,'ticket');
    
                                echo Yii::$app->controller->renderPartial("../../../../../multebox/modules/multeobjects/views/file/attachment-module/attachments", [ 
                                        'dataProviderAttach' => $dataProviderAttach,
                                        'searchModelAttch' => $searchModelAttch,
                                        'ticket_id'=>$model->id,
										'entity_type'=>'ticket',
                                ] );
                                ?>
    </div>
	
    <div class="tab-pane fade" id="notes"> 
    <br/>	

                 <?php

                                $searchModelNotes = new MulteModel();
								$dataProviderNotes = $searchModelNotes->searchNotes( Yii::$app->request->getQueryParams (), $model->id, 'ticket');

                                echo Yii::$app->controller->renderPartial("../../../../../multebox/modules/multeobjects/views/note/notes-module/notes", [ 
                                        'dataProviderNotes' => $dataProviderNotes,
                                        'searchModelNotes' => $searchModelNotes
                                ] );
                                ?>
    </div>

    <input type="hidden" name="old_owner" value="<?=$model->user_assigned_id?>">
    <input type="hidden" name="old_ticket_priority_id" value="<?=$model->ticket_priority_id?>">
    <input type="hidden" name="old_ticket_status" value="<?=$model->ticket_status?>">
    </div>

    <?php
	if(Yii::$app->user->can('Ticket.Update'))
	{
    echo Html::submitButton ( $model->isNewRecord ? Yii::t('app', 'Create' ): Yii::t('app', 'Update'), [ 
                            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary btn-sm  update_ticket' 
                    ] );?> <a href="javascript:void(0)" class="btn btn-success btn-sm" onClick="$('.add-notes-modal').modal('show');"><i class="glyphicon glyphicon-comment"></i> <?= Yii::t('app', 'New Note')?></a>

                    <a href="javascript:void(0)" class="btn btn-success btn-sm" onClick="$('.savepopup').modal('show');"><i class="glyphicon glyphicon-save"></i> <?= Yii::t('app', 'New Attachment')?></a>
					
                    <?php                     
                    if($model->user_assigned_id!=Yii::$app->user->identity->id){
                    ?>
                    <a href="<?=Url::to(['/support/ticket/index', 'ticket_assigned_id' => $_REQUEST['id'], 'page' => 'update'])?>" class="btn btn-primary btn-sm"><?= Yii::t('app', 'Yank')?></a>
                    <?php
                    }
                    ?>

                    <?php
	}
                    ActiveForm::end ();
    ?>
        </div>
    </div>
</div>
<?php

	include_once(__DIR__ .'/../../../../../multebox/modules/multeobjects/views/file/attachment-module/attachmentae.php');
	include_once(__DIR__ .'/../../../../../multebox/modules/multeobjects/views/note/notes-module/noteae.php');
	$entity_type='ticket';//// This Variable is Important 
?>
