<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_payment_methods}}".
 *
 * @property int $id
 * @property string $method
 * @property string $label
 * @property int $added_at
 * @property int $updated_at
 */
class PaymentMethods extends \yii\db\ActiveRecord
{
	const _COD = 'COD';
	const _PAYPAL = 'PPL';
	const _STRIPE = 'STP';
	const _BITCOIN = 'BTC';
	const _RAZORPAY = 'RZP';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_payment_methods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['method', 'label', 'active', 'sort_order'], 'required'],
            [['added_at', 'updated_at', 'active', 'sort_order'], 'integer'],
            [['method'], 'string', 'max' => 3],
            [['label'], 'string', 'max' => 255],
            [['method'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'method' => Yii::t('app', 'Method'),
            'label' => Yii::t('app', 'Label'),
			'active' => Yii::t('app', 'Active'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

	public static function getLabelByMethod($method)
    {
    	return PaymentMethods::find()->where("method='".$method."'")->one()->label;
    }
}
