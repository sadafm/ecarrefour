<?php
use yii\helpers\Html;
use yii\helpers\Url;
use multebox\models\search\Queue;
use kartik\widgets\ActiveForm;
/**
 * @var yii\web\View $this
 * @var multebox\models\Queue $model
 */
$this->title = Yii::t('app', 'Queue') . ' - ' . $model->queue_title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Queues'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="queue-update">
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

<div class="users">
 <?php
                                        
                        $searchModelUser = new Queue();
                        $dataProviderUser = $searchModelUser->searchQueueUser( Yii::$app->request->getQueryParams (), $model->id );
                        echo Yii::$app->controller->renderPartial("user_tab", [ 
                                'dataProviderUser' => $dataProviderUser,
                                'searchModelUser' => $searchModelUser 
                        ] );
                        
echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
		echo ' <a href="javascript:void(0)" class="btn btn-success" onClick="$(\'.exist_users\').modal(\'show\');"><i class="glyphicon glyphicon-user"></i> '.Yii::t('app', 'Add User to Queue').'</a>';
		ActiveForm::end(); 
                        ?>
</div>
<?php
include_once('join_users.php');
?>

