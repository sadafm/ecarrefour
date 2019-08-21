<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_global_discount}}".
 *
 * @property int $id
 * @property int $category_id
 * @property int $sub_category_id
 * @property int $sub_subcategory_id
 * @property string $discount_type
 * @property double $discount
 * @property int $added_by_id
 * @property int $added_at
 * @property int $updated_at
 */
class GlobalDiscount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_global_discount}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'sub_category_id', 'sub_subcategory_id', 'added_by_id', 'added_at', 'updated_at'], 'integer'],
            [['discount_type', 'discount', 'added_by_id'], 'required'],
            [['discount', 'max_discount', 'min_cart_amount', 'max_budget', 'used_budget'], 'number', 'min' => 0],
            [['discount_type'], 'string', 'max' => 1],
			[['category_id', 'sub_category_id', 'sub_subcategory_id'], 'unique', 'targetAttribute' => ['category_id', 'sub_category_id', 'sub_subcategory_id'], 'message' => Yii::t('app', 'Rule for selected combination already exists - Please define another.')],
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
			'sub_subcategory_id' => Yii::t('app', 'Sub-SubCategory ID'),
            'discount_type' => Yii::t('app', 'Discount Type'),
            'discount' => Yii::t('app', 'Discount'),
			'max_discount' => Yii::t('app', 'Maximum Discount'),
			'min_cart_amount' => Yii::t('app', 'Minimum Cart Amount'),
			'max_budget' => Yii::t('app', 'Maximum Budget'),
			'used_budget' => Yii::t('app', 'Used Budget'),
            'added_by_id' => Yii::t('app', 'Added By ID'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
