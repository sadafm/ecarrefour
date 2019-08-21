<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_order_status}}".
 *
 * @property int $id
 * @property string $status
 * @property string $label
 * @property int $added_at
 * @property int $updated_at
 */
class OrderStatus extends \yii\db\ActiveRecord
{
	const _NEW = 'NEW';
	const _IN_PROCESS = 'INP';
	const _AWAITING_SHIPMENT = 'AWS';
	const _PARTIALLY_SHIPPED = 'PRS';
	const _SHIPPED = 'SHP';
	const _PARTIAL_RETURN_REQUESTED = 'PRR';
	const _PARTIAL_RETURN_APPROVED = 'PRA';
	const _PARTIAL_RETURN_REJECTED = 'PRJ';
	const _PARTIAL_RETURN_CANCELED = 'PRC';
	const _PARTIALLY_RETURNED = 'PRT';
	const _RETURN_REQUESTED = 'RRD';
	const _RETURN_APPROVED = 'RAD';
	const _OUT_FOR_PICKUP = 'OFP';
	const _OUT_FOR_DELIVERY = 'OFD';
	const _PICKED_UP = 'PUP';
	const _RETURNED = 'RTD';
	const _RETURN_CANCELED = 'RCD';
	const _RETURN_REJECTED = 'RRJ';
	const _CANCELLATION_REQUESTED = 'CRD';
	const _CANCELLATION_APPROVED = 'CAD';
	const _CANCELED = 'CLD';
	const _ACCEPTED = 'ACC';
	const _CONFIRMED = 'CNF';
	const _COMPLETED = 'COM';
	const _DELIVERED = 'DVD';
	const _REFUNDED = 'RFD';
	const _READY_TO_SHIP = 'RTS';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_order_status}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'label'], 'required'],
            [['added_at', 'updated_at'], 'integer'],
            [['status'], 'string', 'max' => 10],
            [['label'], 'string', 'max' => 255],
            [['status'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'status' => Yii::t('app', 'Status'),
            'label' => Yii::t('app', 'Label'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

	public static function getStatusValue($id)
    {
    	return OrderStatus::findOne($id)->status;
    }

	public static function getLabelByStatus($status)
    {
    	return OrderStatus::find()->where("status='".$status."'")->one()->label;
    }

	public static function getIdByValue($val)
    {
    	return OrderStatus::find()->where("status='".$val."'")->one()->id;
    }
}
