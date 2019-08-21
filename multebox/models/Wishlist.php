<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "tbl_wishlist".
 *
 * @property int $id
 * @property int $inventory_id
 * @property int $customer_id
 * @property int $added_at
 * @property int $updated_at
 */
class Wishlist extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_wishlist';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inventory_id', 'customer_id'], 'required'],
            [['inventory_id', 'customer_id', 'added_at', 'updated_at'], 'integer'],
            [['inventory_id', 'customer_id'], 'unique', 'targetAttribute' => ['inventory_id', 'customer_id']],
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
            'customer_id' => Yii::t('app', 'Customer ID'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
