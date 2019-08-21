<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
/**
 * @var yii\web\View $this
 * @var multebox\models\TicketCategory $model
 */
$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Ticket Category',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Category'), 'url' => ['index']];
///$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="ticket-category-update">
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

	<div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
			<li class="active"><a href="#desc" role="tab" data-toggle="tab"><?= Yii::t('app', 'Description')?></a></li>
			<?php
			if($model->parent_id == 0)
			{
			?>
			<li>
				<a href="#sub_category" role="tab" data-toggle="tab"><?= Yii::t('app', 'Sub Category')?></a>
			</li>
			<?php
			}
			?>
        </ul>
    
		<div class="tab-content">
			<div class="tab-pane  active" id="desc"> 
				<br/>
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label" for="lname"><?=Yii::t('app', 'Description')?></label>
						<div class="controls">
							<textarea id="ticketcategory-description" class="form-control" name="TicketCategory[description]" rows="6" placeholder="Enter Description..."><?=$model->description?></textarea>
						</div>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="sub_category"> 
			<br/>
			<?php
				$searchModel = new \multebox\models\search\TicketCategory();
				$dataProvider= $searchModel->searchSubCategory( Yii::$app->request->getQueryParams (), $model->id);
				
				echo Yii::$app->controller->renderPartial("sub-category-tab", [ 
						'dataProvider' => $dataProvider,
				] );
				
			?>
			</div>
		</div>

		<?php
		echo Html::submitButton ( $model->isNewRecord ? Yii::t('app', 'Create' ): Yii::t('app', 'Update'), [ 
		
								'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary btn-sm' 
		
						] );?>
					   
		<?php
			ActiveForm::end();
		?>
    </div>
</div>