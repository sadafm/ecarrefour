<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var multebox\models\TicketImpact $model
 */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Impacts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl.'../../vendor/bower/bootstrap/dist/js/bootstrap.min.js', ['depends' => [yii\web\YiiAsset::className()]]);?>
<script src="../../vendor/bower/jquery/dist/jquery.js"></script>
<script>
	$(document).ready(function(e) {
		if(<?=$_GET['id']?>){
        	$('#ticketimpact-impact').attr('readonly',true);
		} 
    });
</script>
<div class="ticket-impact-view">


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
            ///'id',
            'impact',
            'label',
           // 'active',
          //  'sort_order',
            //'added_at',
           // 'modified_at',
            //'active',
			['attribute'=>'active','value' => $model->active?Yii::t('app', 'Yes'): Yii::t('app', 'No'), 'type'=>DetailView::INPUT_DROPDOWN_LIST,'items'=>array(''=>'--'.Yii::t('app', 'Select').'--','0'=>Yii::t('app', 'No'),'1'=>  Yii::t('app', 'Yes'))]
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
