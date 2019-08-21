<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_inventory_details}}".
 *
 * @property int $id
 * @property int $inventory_id
 * @property int $attribute_id
 * @property string $attribute_value
 * @property double $attribute_price
 */
class InventoryDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_inventory_details}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inventory_id', 'attribute_id'], 'required'],
            [['inventory_id', 'attribute_id'], 'integer'],
            [['attribute_price'], 'number'],
            [['attribute_value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'inventory_id' => Yii::t('app', 'Inventory ID'),
            'attribute_id' => Yii::t('app', 'Attribute ID'),
            'attribute_value' => Yii::t('app', 'Attribute Value'),
            'attribute_price' => Yii::t('app', 'Attribute Price'),
        ];
    }

	public function getproductAttributes()
    {
    	return $this->hasMany(ProductAttributes::className(), ['id' => 'attribute_id']);
    }
}
