<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_product_attributes}}".
 *
 * @property int $id
 * @property int $parent_id
 * @property int $fixed
 * @property int $fixed_id
 * @property string $name
 * @property int $active
 * @property int $added_by_id
 * @property int $sort_order
 * @property int $added_at
 * @property int $updated_at
 */
class ProductAttributes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_product_attributes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'fixed', 'active', 'added_by_id'], 'required'],
            [['parent_id', 'fixed', 'fixed_id', 'active', 'added_by_id', 'sort_order', 'added_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'fixed' => Yii::t('app', 'Field'),
            'fixed_id' => Yii::t('app', 'Fixed ID'),
            'name' => Yii::t('app', 'Name'),
            'active' => Yii::t('app', 'Active'),
            'added_by_id' => Yii::t('app', 'Added By ID'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
