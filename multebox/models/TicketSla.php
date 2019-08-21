<?php

namespace multebox\models;
use multebox\models\TicketPriority;
use multebox\models\TicketImpact;

use Yii;

/**
 * This is the model class for table "tbl_sla".
 *
 * @property integer $id
 * @property integer $ticket_priority_id
 * @property integer $ticket_impact_id
 * @property integer $sla
 */
class TicketSla extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_ticket_sla';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_priority_id', 'ticket_impact_id', 'sla_duration'], 'required'],
            [['ticket_priority_id', 'ticket_impact_id', 'sla_duration'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ticket_priority_id' => Yii::t('app', 'Ticket Priority'),
            'ticket_impact_id' => Yii::t('app', 'Ticket Impact'),
            'sla_duration' => Yii::t('app', 'SLA Duration (Hours)'),
        ];
    }

	public function getTicketPriority()
    {
    	return $this->hasOne(TicketPriority::className(), ['id' => 'ticket_priority_id']);
    }

	public function getTicketImpact()
    {
    	return $this->hasOne(TicketImpact::className(), ['id' => 'ticket_impact_id']);
    }
}
