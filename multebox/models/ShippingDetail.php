<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "tbl_shipping_detail".
 *
 * @property int $id
 * @property int $sub_order_id
 * @property string $tracking_number
 * @property string $carrier
 * @property string $tracking_url
 * @property int $added_at
 * @property int $updated_at
 */
class ShippingDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_shipping_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sub_order_id', 'tracking_number', 'carrier'], 'required'],
            [['sub_order_id', 'added_at', 'updated_at'], 'integer'],
            [['tracking_number', 'carrier', 'tracking_url'], 'string', 'max' => 255],
            [['sub_order_id'], 'unique'],
			[['tracking_url'], 'url', 'defaultScheme' => 'http'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sub_order_id' => Yii::t('app', 'Sub Order ID'),
            'tracking_number' => Yii::t('app', 'Tracking Number'),
            'carrier' => Yii::t('app', 'Carrier'),
            'tracking_url' => Yii::t('app', 'Tracking Url'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
