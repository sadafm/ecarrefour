<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_customer}}".
 *
 * @property int $id
 * @property string $customer_name
 * @property int $customer_type_id
 * @property int $added_by_id
 * @property int $active
 * @property int $added_at
 * @property int $updated_at
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_customer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_name', 'customer_type_id', 'added_by_id'], 'required'],
            [['customer_type_id', 'added_by_id', 'active', 'added_at', 'updated_at'], 'integer'],
            [['customer_name'], 'string', 'max' => 255],
			[['customer_name'],'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_name' => Yii::t('app', 'Customer Name'),
            'customer_type_id' => Yii::t('app', 'Customer Type'),
            'added_by_id' => Yii::t('app', 'Added By ID'),
            'active' => Yii::t('app', 'Active'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

	public function getCustomerType()
    {
    	return $this->hasOne(CustomerType::className(), ['id' => 'customer_type_id']);
    }
}
