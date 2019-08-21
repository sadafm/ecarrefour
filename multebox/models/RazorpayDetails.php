<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "tbl_razorpay_details".
 *
 * @property int $id
 * @property int $order_id
 * @property double $amount
 * @property string $json_data
 * @property string $razorpay_order_id
 * @property string $razorpay_payment_id
 * @property string $razorpay_signature
 * @property int $payment_confirmed
 * @property int $added_at
 * @property int $updated_at
 */
class RazorpayDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_razorpay_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'amount', 'json_data', 'razorpay_order_id', 'payment_confirmed'], 'required'],
            [['order_id', 'payment_confirmed', 'added_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['json_data'], 'string'],
            [['razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature'], 'string', 'max' => 255],
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
            'json_data' => Yii::t('app', 'Json Data'),
            'razorpay_order_id' => Yii::t('app', 'Razorpay Order ID'),
            'razorpay_payment_id' => Yii::t('app', 'Razorpay Payment ID'),
            'razorpay_signature' => Yii::t('app', 'Razorpay Signature'),
            'payment_confirmed' => Yii::t('app', 'Payment Confirmed'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
