<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_product_subcategory}}".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property int $active
 * @property string $description
 * @property int $added_by_id
 * @property int $sort_order
 * @property int $added_at
 * @property int $updated_at
 */
class ProductSubCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_product_subcategory}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'name', 'active', 'added_by_id'], 'required'],
            [['parent_id', 'active', 'added_by_id', 'sort_order', 'added_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
			[['parent_id', 'name'], 'unique', 'targetAttribute' => ['parent_id', 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Main Category'),
            'name' => Yii::t('app', 'Name'),
            'active' => Yii::t('app', 'Active'),
            'description' => Yii::t('app', 'Description'),
            'added_by_id' => Yii::t('app', 'Added By ID'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
