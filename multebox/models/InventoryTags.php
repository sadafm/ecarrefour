<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_inventory_tags}}".
 *
 * @property int $inventory_id
 * @property int $tag_id
 */
class InventoryTags extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_inventory_tags}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inventory_id', 'tag_id'], 'required'],
            [['inventory_id', 'tag_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'inventory_id' => Yii::t('app', 'Inventory ID'),
            'tag_id' => Yii::t('app', 'Tag ID'),
        ];
    }
}
