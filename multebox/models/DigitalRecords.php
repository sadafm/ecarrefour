<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "tbl_digital_records".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $sub_order_id
 * @property string $token
 * @property string $file_name
 * @property string $orig_name
 * @property int $added_at
 * @property int $updated_at
 */
class DigitalRecords extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_digital_records';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'sub_order_id', 'token', 'file_name', 'orig_name'], 'required'],
            [['customer_id', 'sub_order_id', 'added_at', 'updated_at'], 'integer'],
            [['file_name', 'orig_name'], 'string'],
            [['token'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'sub_order_id' => Yii::t('app', 'Sub Order ID'),
            'token' => Yii::t('app', 'Token'),
            'file_name' => Yii::t('app', 'File Name'),
            'orig_name' => Yii::t('app', 'Orig Name'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
