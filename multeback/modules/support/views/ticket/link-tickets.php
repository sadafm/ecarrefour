<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\User;
use multebox\models\TicketType;
use multebox\models\TicketPriority;
use multebox\models\TicketImpact;
use multebox\models\TicketStatus;
use multebox\models\TicketCategory;
use multebox\models\Queue;
use yii\helpers\ArrayHelper;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\Ticket $searchModel
 */
//$this->title = Yii::t('app', 'Tickets');
//$this->params['breadcrumbs'][] = $this->title;
?>
<!-- <form action="" method="post" name="frm"> -->
    <?php Yii::$app->request->enableCsrfValidation = true; ?>
   
<div class="ticket-index">
    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
		'responsiveWrap' => false,
		'pjax' => true,
        'columns' => [
			['class' => '\kartik\grid\CheckboxColumn'],
            ['class' => 'yii\grid\SerialColumn'],
           'ticket_id',
            //'ticket_title',
			[ 
					'attribute' => 'ticket_title',
					'width' => '350px' ,
					'format' => 'raw',
					'value' => function ($model, $key, $index, $widget)
					{
						return '<a href="'.Url::to(['/support/ticket/update', 'id' => $model->id]).'">'.$model->ticket_title.'</a>';
					}
	
			],
			
	[ 
			'attribute' => 'ticket_status',
			'label' => Yii::t('app','Status'),
			'filterType' => GridView::FILTER_SELECT2,
			'format' => 'raw',
			//'width' => '150px',
			'filter' => ArrayHelper::map (TicketStatus::find ()->where("active=1")->orderBy ('sort_order' )->asArray ()->all (), 'status', 'label' ),
			'filterWidgetOptions' => [ 
					'options' => [ 
							'placeholder' => Yii::t('app', 'All...') 
					],
					'pluginOptions' => [ 
							'allowClear' => true 
					] 
			],
			'value' => function ($model, $key, $index, $widget)
			{
				// var_dump($model->ticketPriority);
				if (isset ( $model->ticketStatus ) && ! empty ( $model->ticketStatus->label ))
					return statusLabel ( $model->ticketStatus->label );
			} 
	],
	
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>false,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode(Yii::t('app', 'Link Tickets')).' </h3>',
            'type'=>'info',
            'before' => '<form action="" method="post" name="frmx"><input type="hidden" name="_csrf" value="'.$this->renderDynamic('return Yii::$app->request->csrfToken;').'"> <input type="hidden" name="multiple_link" value="true"> <a href="javascript:void(0)" onClick="all_link()" class="btn btn-info btn-sm"><i class="glyphicon glyphicon-link"></i> ' . Yii::t('app', 'Link Selected') . '</a>',
            'after' => '</form>',
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>
</div>
<!-- </form> -->
<script>
	function all_link(){
		var r = confirm("<?=Yii::t ('app','Are you Sure!')?>");
		if (r == true) {
			document.frmx.submit()
		} else {
			
		}	
	}
</script>
<script>
	function showPopup(id){
		$.post("<?=Url::to(['/liveobjects/queue/ajax-user-detail'])?>", { 'id': id, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(r){
			$('.modal-body').html(r);
		}).done(function(){
			$('.bs-example-modal-lg').modal('show');
		})
	}
</script>
</div>
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="gridSystemModalLabel"><?=Yii::t('app', 'User Detail')?></h4>
    </div>
      <div class="modal-body">
      
      </div>
    </div>
  </div>
