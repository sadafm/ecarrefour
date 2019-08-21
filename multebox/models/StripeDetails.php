<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_stripe_details}}".
 *
 * @property int $id
 * @property int $order_id
 * @property double $amount
 * @property string $charge_id
 * @property string $json_response
 * @property int $added_at
 * @property int $updated_at
 */
class StripeDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_stripe_details}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'amount', 'charge_id', 'json_response'], 'required'],
            [['order_id', 'added_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['json_response'], 'string'],
            [['charge_id'], 'string', 'max' => 255],
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
            'charge_id' => Yii::t('app', 'Charge ID'),
            'json_response' => Yii::t('app', 'Json Response'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
