<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use multebox\models\TicketPriority;
use multebox\models\TicketImpact;
use yii\helpers\ArrayHelper;


/**
 * @var yii\web\View $this
 * @var multebox\models\TicketSla $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="ticket-sla">

    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            //'ticket_priority_id' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Ticket Priority ID...']],
			'ticket_priority_id' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'items' => ArrayHelper::map ( TicketPriority::find ()->where("active=1")->orderBy ( 'sort_order' )->asArray ()->all (), 'id', 'label' )  , 
										'options' => [ 
                                                'prompt' => '--Select '.Yii::t ( 'app', 'Status' ).'--'
                                        ] 
								],

           // 'ticket_impact_id' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Ticket Impact ID...']],
			'ticket_impact_id' => [ 
										'type' => Form::INPUT_DROPDOWN_LIST,
										'items' => ArrayHelper::map ( TicketImpact::find ()->where("active=1")->orderBy ( 'sort_order' )->asArray ()->all (), 'id', 'label' )  , 
										'options' => [ 
                                                'prompt' => '--Select '.Yii::t ( 'app', 'Status' ).'--'
                                        ] 
								],

            'sla_duration' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Num Of Hours...']],

        ]

    ]);

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    );
    ActiveForm::end(); ?>

</div>
