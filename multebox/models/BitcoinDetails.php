<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "tbl_bitcoin_details".
 *
 * @property int $id
 * @property int $order_id
 * @property double $amount
 * @property string $invoice_id
 * @property string $json_response
 * @property string $status
 * @property int $added_at
 * @property int $updated_at
 */
class BitcoinDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_bitcoin_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'amount', 'invoice_id', 'status'], 'required'],
            [['order_id', 'added_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['json_response'], 'string'],
            [['invoice_id'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 50],
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
            'invoice_id' => Yii::t('app', 'Invoice ID'),
            'json_response' => Yii::t('app', 'Json Response'),
            'status' => Yii::t('app', 'Status'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
