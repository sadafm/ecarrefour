<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_commission}}".
 *
 * @property int $id
 * @property int $category_id
 * @property int $sub_category_id
 * @property int $sub_subcategory_id
 * @property string $commission_type
 * @property double $commission
 * @property int $added_by_id
 * @property int $added_at
 * @property int $updated_at
 */
class Commission extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_commission}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'sub_category_id', 'sub_subcategory_id', 'added_by_id', 'added_at', 'updated_at'], 'integer'],
            [['commission_type', 'commission', 'added_by_id'], 'required'],
            [['commission'], 'number'],
            [['commission_type'], 'string', 'max' => 1],
            [['category_id', 'sub_category_id', 'sub_subcategory_id'], 'unique', 'targetAttribute' => ['category_id', 'sub_category_id', 'sub_subcategory_id'], 'message' => Yii::t('app', 'Rule for selected combination already exists - Please define another.') ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category_id' => Yii::t('app', 'Category ID'),
            'sub_category_id' => Yii::t('app', 'Sub Category ID'),
            'sub_subcategory_id' => Yii::t('app', 'Sub Subcategory ID'),
            'commission_type' => Yii::t('app', 'Commission Type'),
            'commission' => Yii::t('app', 'Commission'),
            'added_by_id' => Yii::t('app', 'Added By ID'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
