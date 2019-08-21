<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_paypal_details}}".
 *
 * @property int $id
 * @property int $order_id
 * @property double $amount
 * @property string $result_json
 * @property string $payment_id
 * @property string $payer_id
 * @property string $sale_id
 * @property int $added_at
 * @property int $updated_at
 */
class PaypalDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_paypal_details}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'amount', 'result_json', 'payment_id', 'payer_id', 'sale_id'], 'required'],
            [['order_id', 'added_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['result_json'], 'string'],
            [['payment_id', 'payer_id', 'sale_id'], 'string', 'max' => 255],
            [['order_id'], 'unique'],
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
            'amount' => Yii::t('app', 'Amount'),
            'result_json' => Yii::t('app', 'Result Json'),
            'payment_id' => Yii::t('app', 'Payment ID'),
            'payer_id' => Yii::t('app', 'Payer ID'),
            'sale_id' => Yii::t('app', 'Sale ID'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
