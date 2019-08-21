<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_cart}}".
 *
 * @property int $id
 * @property int $inventory_id
 * @property int $total_items
 * @property string $session_id
 * @property int $user_id
 * @property int $added_at
 * @property int $updated_at
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_cart}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inventory_id', 'total_items', 'session_id'], 'required'],
            [['inventory_id', 'total_items', 'user_id', 'added_at', 'updated_at'], 'integer'],
            [['session_id'], 'string', 'max' => 100],
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
            'total_items' => Yii::t('app', 'Total Items'),
            'session_id' => Yii::t('app', 'Session ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

	public function getInventory()
    {
    	return $this->hasOne(Inventory::className(), ['id' => 'inventory_id']);
    }

	public function getProduct()
    {
    	return $this->hasOne(Product::className(), ['id' => 'product_id'])
			->viaTable('tbl_inventory', ['id' => 'inventory_id']);
    }
}
