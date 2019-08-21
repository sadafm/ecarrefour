<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_vendor}}".
 *
 * @property int $id
 * @property string $vendor_name
 * @property int $vendor_type_id
 * @property int $added_by_id
 * @property int $active
 * @property int $added_at
 * @property int $updated_at
 */
class Vendor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_vendor}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vendor_name', 'vendor_type_id', 'added_by_id'], 'required'],
            [['vendor_type_id', 'added_by_id', 'active', 'added_at', 'updated_at'], 'integer'],
			[['rating'], 'number', 'min' => 0],
            [['vendor_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'vendor_name' => Yii::t('app', 'Vendor Name'),
            'vendor_type_id' => Yii::t('app', 'Vendor Type'),
            'added_by_id' => Yii::t('app', 'Added By ID'),
            'active' => Yii::t('app', 'Active'),
			'rating' => Yii::t('app', 'Rating'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

	public function getVendorType()
    {
    	return $this->hasOne(VendorType::className(), ['id' => 'vendor_type_id']);
    }

	public function afterDelete()
	{
		/*Delete All Inventory Items */
		foreach (Inventory::find()->where(['vendor_id'=> $this->id])->all() as $record)
		{
			$record->delete();
		}
	}
}
