<?php

use yii\helpers\Html;
use multebox\models\TicketStatus;

/**
 * @var yii\web\View $this
 * @var multebox\models\Vendor $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="vendor-form">
		<form id="w0" class="form-vertical" method="post">
		<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Report Issue' ); ?></h3>
			</div>
			
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label class="control-label"><?php echo Yii::t ( 'app', 'Problem' ); ?></label>
							<input type="text" name="Ticket[ticket_title]" data-validation="required" mandatory-field class="form-control" placeholder="<?=Yii::t('app', 'Enter Problem')?>...">
						</div>

						<div class="form-group">
							<label class="control-label"><?php echo Yii::t ( 'app', 'Description' ); ?></label>
							<textarea id="issue_description" class="form-control" data-validation="required" mandatory-field name="Ticket[ticket_description]" rows="6" placeholder="<?=Yii::t('app', 'Enter Description')?>..."></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>

		<input type="hidden" name="Ticket[ticket_priority_id]" class="form-control" value="<?=Yii::$app->params['DEFAULT_TICKET_PRIORITY']?>">
		<input type="hidden" name="Ticket[ticket_impact_id]" class="form-control" value="<?=Yii::$app->params['DEFAULT_TICKET_IMPACT']?>">
		<input type="hidden" name="Ticket[ticket_category_id_1]" class="form-control" value="<?=Yii::$app->params['DEFAULT_TICKET_CATEGORY']?>">
		<input type="hidden" name="Ticket[queue_id]" class="form-control" value="<?=Yii::$app->params['DEFAULT_TICKET_QUEUE']?>">
		<input type="hidden" name="Ticket[ticket_status]" class="form-control" value="<?=TicketStatus::_NEEDSACTION?>">
		<input type="hidden" name="Ticket[ticket_customer_id]" class="form-control" value="<?=Yii::$app->user->identity->entity_id?>">
		<input type="hidden" name="Ticket[department_id]" class="form-control" value="<?=Yii::$app->params['DEFAULT_TICKET_DEPARTMENT']?>">
		<input type="hidden" name="Ticket[added_by_user_id]" class="form-control" value="<?=Yii::$app->user->identity->id?>">

		<?php
				echo Html::submitButton ( Yii::t ( 'app', 'Submit' ), [ 
							'class' => 'btn btn-primary btn-sm issue_submit' 
					] );
		?>
	</form>	
</div>