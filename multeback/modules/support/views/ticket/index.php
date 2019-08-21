<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\User;
use multebox\models\TicketType;
use multebox\models\TicketPriority;
use multebox\models\TicketImpact;
use multebox\models\TicketStatus;
use multebox\models\TicketCategory;
use multebox\models\Queue;
use multebox\models\search\MulteModel;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\Ticket $searchModel
 */
 
$this->title = Yii::t('app', 'Tickets');
$this->params['breadcrumbs'][] = $this->title;
function statusLabel($status)
{
	$label = "<span class=\"label label-default\">" . $status . "</span>";

	return $label;
}
?>
    <?php Yii::$app->request->enableCsrfValidation = true; ?>


   
<div class="ticket-index">
    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,'responsive' => true,'responsiveWrap' => false,
		'pjax' => true,
        'columns' => [
		['class' => 'yii\grid\SerialColumn'],
			['class' => '\kartik\grid\CheckboxColumn'],
            
			[ 
					'attribute' => 'ticket_id',
					'label' => Yii::t('app','Ticket Id'),
	
					'width' => '150px' ,
	
					'format' => 'raw',
					
					
	
					'value' => function ($model, $key, $index, $widget)
	
					{
	
						return '<a href="'.Url::to(['/support/ticket/update', 'id' => $model->id]).'">'.$model->ticket_id.'</a>';
	
					}
	
			],
			[ 
					'attribute' => 'ticket_title',
	
					'width' => '550px' ,
	
					'format' => 'raw',
					
					'value' => function ($model, $key, $index, $widget)
	
					{
	
						return '<a href="'.Url::to(['/support/ticket/update', 'id' => $model->id]).'">'.$model->ticket_title.'</a>';
	
					}
	

	
			],

			[ 
				'attribute' => 'user_assigned_id',
				'label' => Yii::t('app','Assigned User'),
				'filterType' => GridView::FILTER_SELECT2,
				'format' => 'raw',
				//'width' => '200px',
				'filter' => ArrayHelper::map ( User::find ()->orderBy ( 'first_name' )->where("active=1")->asArray ()->all (), 'id',
				function ($user, $defaultValue) {
			 $username=$user['username']?$user['username']:$user['email'];
			 return $user['first_name'] . ' ' . $user['last_name'].' ('.$username.')';
}),
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
					// var_dump($model->user);
					if (isset ( $model->user ) && ! empty ( $model->user->first_name )){
						$username=$model->user->username?$model->user->username:$model->user->email;

							$users='<div class="project-people">';
									$path=Yii::$app->params['web_url'].'/users/'.$model->user->id.'.png';
									if(MulteModel::fileExists($path)){
										$image='<img  class="img-circle img-sm"  src="'.$path.'"  data-toggle="hover" data-placement="top" data-content="'.$model->user->first_name.' '.$model->user->last_name.' ('.$model->user->username.')">';								
									 }else{ 
										$image='<img   class="img-circle img-sm" src="'.Url::base().'/nophoto.jpg"  data-toggle="hover" data-placement="top" data-content="'.$model->user->first_name.' '.$model->user->last_name.' ('.$model->user->username.')">';
									 }
									$users.=' <a  href="javascript:void(0)" onClick="showPopup(\''.$model->user->id.'\')">'.$image.'</a>';	
						
								$users.='</div>';
								return $users;
					}
					else
					{
						if(!empty($_GET['Ticket']['queue_id']))
						{
							return '<span class="label label-danger">'.Yii::t('app', 'Not Assigned').'</span>';
						}	
					}					
				} 
		],
		[ 
			'attribute' => 'ticket_category_id_1',
			'label' => Yii::t('app','Category'),
			'filterType' => GridView::FILTER_SELECT2,
			'format' => 'raw',
			//'width' => '150px',
			'filter' => ArrayHelper::map (TicketCategory::find ()->where("active=1")->orderBy ('sort_order' )->asArray ()->all (), 'id', 'label' ),
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
				if (isset ( $model->ticketCategory ) && ! empty ( $model->ticketCategory->label ))
					return statusLabel ( $model->ticketCategory->label );
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
	
	[ 
			'attribute' => 'ticket_priority_id',
			'label' => Yii::t('app','Priority'),
			'filterType' => GridView::FILTER_SELECT2,
			'format' => 'raw',
			//'width' => '150px',
			'filter' => ArrayHelper::map ( TicketPriority::find ()->where("active=1")->orderBy ( 'sort_order' )->asArray ()->all (), 'id', 'label' ),
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
				if (isset ( $model->ticketPriority ) && ! empty ( $model->ticketPriority->label ))
					return statusLabel ( $model->ticketPriority->label );
			} 
	],
	
	
		// queue name column added by deepak
			[ 
				'attribute' => 'queue_id',
				'label' => Yii::t('app','Queue Name'),
				
				'filterType' => GridView::FILTER_SELECT2,
			'format' => 'raw',
				
				'filter' => ArrayHelper::map ( Queue::find ()->where("active=1")->orderBy ( 'id' )->asArray ()->all (), 'id', 'queue_title' ),
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

				if (isset ( $model->queueName ) && ! empty ( $model->queueName->queue_title ))
					return statusLabel ( $model->queueName->queue_title );
			} 
				
				],
				
				[
				'label' => Yii::t('app','SLA Status'),
				'format' => 'raw',
				
				'headerOptions' => ['style' => 'text-align:center;color:#337ab7'],
				'value' => function ($model, $key, $index, $widget)
				{	
					
				if ($model ->ticket_status == TicketStatus::_NEEDSACTION || $model ->ticket_status == TicketStatus::_INPROCESS  || 
				    $model ->ticket_status == TicketStatus::_REOPENED  
					&& $model->due_date < time())
					return "<span class=\"label label-info\">" . Yii::t('app', 'In Process') . "</span>";
					
				 if ($model ->ticket_status == TicketStatus::_COMPLETED  || $model ->ticket_status == TicketStatus::_RESOLVED  ||
     				 $model ->ticket_status == TicketStatus::_CLOSED  || $model ->ticket_status == TicketStatus::_REOPENED 
					&& $model->updated_at <= $model->due_date)
					return "<span class=\"label label-success\">" . Yii::t('app', 'On Time') . "</span>";
					 
				 if ($model ->ticket_status == TicketStatus::_COMPLETED   || $model ->ticket_status == TicketStatus::_RESOLVED   ||
					 $model ->ticket_status == TicketStatus::_CLOSED   || $model ->ticket_status == TicketStatus::_REOPENED 
					 && $model->updated_at >= $model->due_date)
					 return "<span class=\"label label-danger\">" . Yii::t('app', 'Completed Breached') . "</span>";
					 
				 if ($model ->ticket_status == TicketStatus::_NEEDSACTION  || $model ->ticket_status == TicketStatus::_INPROCESS  ||
					 $model ->ticket_status == TicketStatus::_REOPENED  && $model->due_date >= time())
					return "<span class=\"label label-warning\">" . Yii::t('app', 'In Process Breached') . "</span>";
					 
				if ($model ->ticket_status == TicketStatus::_CANCELLED  )
					return "<span class=\"label label-default\">" . Yii::t('app', 'Not Applicable') . "</span>";
				
				}
				
				],
				
 
            [
                'class' => '\kartik\grid\ActionColumn',
				'header'=>Yii::t('app', 'Actions'),
				'width' => '10%' ,
				'template'=>'{update} {delete} {status}',	
                'buttons' => [
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['/support/ticket/update','id' => $model->id]), [
                                                    'title' => Yii::t('app', 'Edit'),
                                                  ]);},
				'view' => function($url,$model){
												return '';
											
											},											
				'status'=>function ($url, $model) {
										if(Yii::$app->user->identity->userType->type!="Customer")
										{
											if(Yii::$app->user->identity->id!=$model->user_assigned_id)
											{
												if(isset($_REQUEST['Ticket']['queue_id']))
													$ticketid = $_REQUEST['Ticket']['queue_id'];
												else
													$ticketid = '';

												return Html::a('<span class="label label-primary">'.Yii::t('app', 'Yank').'</span>', Yii::$app->urlManager->createUrl(['/support/ticket/index','ticket_assigned_id' => $model->id,'Ticket[queue_id]'=>$ticketid,'page'=>'index']), [
																	'title' => Yii::t('app', 'Status'),
											  ]);
											}
										}
								},
				'delete' => function($url,$model){
													if(Yii::$app->user->identity->userType->type!="Customer")
													{
															return Html::a('<span class="glyphicon glyphicon-trash"></span>', Yii::$app->urlManager->createUrl(['/support/ticket/delete','id' => $model->id]), [
														'title' => Yii::t('app', 'Delete'),
														'data' => [                          
																	'method' => 'post',                          
																	'confirm' => Yii::t('app', 'Are you sure?')],
																  ]);
													}
												}
                                    		
                                    		
                ],                
            ],            
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>false,


        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',

			'before' => '<form action="" method="post" name="frm"><input type="hidden" name="_csrf" value="'.$this->renderDynamic('return Yii::$app->request->csrfToken;').'"> <input type="hidden" name="multiple_del" value="true">'.(!Yii::$app->user->can('Ticket.Update')?'':Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Add'), [
                'create'
            ], [
                'class' => 'btn btn-success btn-sm'
            ]) . ' <a href="javascript:void(0)" onClick="all_del()" class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-trash"></i> ' . Yii::t('app', "Delete Selected") . '</a>'),
            'after' => '</form>'.Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('app', 'Reset List'), [
                'index'
            ], [
                'class' => 'btn btn-info btn-sm'
            ]),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>
</div>

<script>
	function all_del(){
		var r = confirm("<?=Yii::t ('app','Are you Sure!')?>");
		if (r == true) {
			document.frm.submit()
		} else {
			
		}	
	}
</script>
<script>
	function showPopup(id){
		$.post("<?=Url::to(['/support/queue/ajax-user-detail'])?>", { 'id': id, '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(r){
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