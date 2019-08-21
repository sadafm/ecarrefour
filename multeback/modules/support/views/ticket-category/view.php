<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var multebox\models\TicketCategory $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Category'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', ucfirst($model->sub->name)), 'url' => ['/support/ticket-category/update','id'=>$model->sub->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="../../vendor/bower/jquery/dist/jquery.js"></script>
<script>
	$(document).ready(function(e) {
		if(<?=$_GET['id']?>){
        	$('#ticketcategory-name').attr('readonly',true);
		} 
    });
</script>
<div class="ticket-category-view">
   


    <?= DetailView::widget([
            'model' => $model,
            'condensed'=>false,
            'hover'=>true,
            'mode'=>Yii::$app->request->get('edit')=='t' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'panel'=>[
            'heading'=>$this->title,
            'type'=>DetailView::TYPE_INFO,
        ],
        'attributes' => [
            //'id',
            'name',
            'label',
            ['attribute'=>'active','value' => $model->active?Yii::t('app', 'Yes'): Yii::t('app', 'No'), 'type'=>DetailView::INPUT_DROPDOWN_LIST,'items'=>array(''=>'--'.Yii::t('app', 'Select').'--','0'=>Yii::t('app', 'No'),'1'=>  Yii::t('app', 'Yes'))],
			//'description:ntext',
            //'parent_id',
            //'department_id',
            //'sort_order',
            //'added_at',
            //'updated_at',
        ],
        'deleteOptions'=>[
        'url'=>['delete', 'id' => $model->id],
        'data'=>[
        'confirm'=>Yii::t('app', 'Are you sure you want to delete this item?'),
        'method'=>'post',
        ],
        ],
        'enableEditMode'=>true,
    ]) ?>

</div>
