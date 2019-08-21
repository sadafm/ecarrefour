<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_paypal_refund_details}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int $sub_order_id
 * @property double $amount
 * @property string $result_json
 * @property string $refund_id
 * @property int $added_at
 * @property int $updated_at
 */
class PaypalRefundDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_paypal_refund_details}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'sub_order_id', 'amount', 'result_json', 'refund_id'], 'required'],
            [['order_id', 'sub_order_id', 'added_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['result_json'], 'string'],
            [['refund_id'], 'string', 'max' => 255],
            [['order_id', 'sub_order_id'], 'unique', 'targetAttribute' => ['order_id', 'sub_order_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order_id' => Yii::t('app', 'Order ID'),
            'sub_order_id' => Yii::t('app', 'Sub Order ID'),
            'amount' => Yii::t('app', 'Amount'),
            'result_json' => Yii::t('app', 'Result Json'),
            'refund_id' => Yii::t('app', 'Refund ID'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
