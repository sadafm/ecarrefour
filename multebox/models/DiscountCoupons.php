<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_discount_coupons}}".
 *
 * @property int $id
 * @property int $category_id
 * @property int $sub_category_id
 * @property int $sub_subcategory_id
 * @property int $inventory_id
 * @property string $coupon_code
 * @property string $discount_type
 * @property double $discount
 * @property int $max_uses
 * @property int $used_count
 * @property int $expiry_datetime
 * @property int $customer_id
 * @property int $added_by_id
 * @property int $added_at
 * @property int $updated_at
 */
class DiscountCoupons extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_discount_coupons}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'sub_category_id', 'sub_subcategory_id', 'inventory_id', 'customer_id', 'used_count', 'added_by_id', 'added_at', 'updated_at'], 'integer'],
            [['coupon_code', 'discount_type', 'discount', 'max_uses', 'added_by_id', 'expiry_datetime'], 'required'],
            [['discount', 'max_discount', 'min_cart_amount', 'max_budget', 'used_budget'], 'number', 'min' => 0],
			[['max_uses'], 'integer', 'min' => 0],
            [['coupon_code'], 'string', 'max' => 255],
			[['expiry_datetime'], 'safe'],
            [['discount_type'], 'string', 'max' => 1],
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
            'inventory_id' => Yii::t('app', 'Inventory ID'),
            'coupon_code' => Yii::t('app', 'Coupon Code'),
            'discount_type' => Yii::t('app', 'Discount Type'),
            'discount' => Yii::t('app', 'Discount'),
			'max_discount' => Yii::t('app', 'Maximum Discount'),
			'min_cart_amount' => Yii::t('app', 'Minimum Cart Amount'),
            'max_uses' => Yii::t('app', 'Maximum Uses'),
			'max_budget' => Yii::t('app', 'Maximum Budget'),
			'used_budget' => Yii::t('app', 'Used Budget'),
			'used_count' => Yii::t('app', 'Used Count'),
            'expiry_datetime' => Yii::t('app', 'Expiry Datetime'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'added_by_id' => Yii::t('app', 'Added By ID'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
