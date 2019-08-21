<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_sub_order}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int $vendor_id
 * @property int $inventory_id
 * @property int $total_items
 * @property int $discount_coupon_id
 * @property int $global_discount_id
 * @property int $tax_id
 * @property string $inventory_snapshot
 * @property string $discount_coupon_snapshot
 * @property string $global_discount_snapshot
 * @property string $tax_snapshot
 * @property double $total_cost
 * @property double $total_shipping
 * @property double $total_site_discount
 * @property double $total_coupon_discount
 * @property string $discount_coupon_type
 * @property double $total_tax
 * @property int $delivery_method
 * @property int $payment_method
 * @property int $sub_order_status_id
 * @property int $added_at
 * @property int $updated_at
 */
class SubOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_sub_order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'order_currency_code', 'order_currency_symbol', 'vendor_id', 'inventory_id', 'total_items', 'inventory_snapshot', 'total_cost', 'delivery_method', 'payment_method', 'sub_order_status', 'conversion_rate'], 'required'],
            [['order_id', 'vendor_id', 'inventory_id', 'total_items', 'discount_coupon_id', 'global_discount_id', 'tax_id', 'is_processed', 'added_at', 'updated_at'], 'integer'],
            [['order_currency_code', 'order_currency_symbol', 'inventory_snapshot', 'discount_coupon_snapshot', 'global_discount_snapshot', 'tax_snapshot', 'state_tax_snapshot'], 'string'],
            [['total_cost', 'total_shipping', 'total_site_discount', 'total_coupon_discount', 'total_tax', 'total_converted_cost', 'conversion_rate'], 'number'],
            [['discount_coupon_type'], 'string', 'max' => 1],
			[['delivery_method', 'payment_method', 'sub_order_status'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order_id' => Yii::t('app', 'Order ID'),
			'order_currency_code' => Yii::t('app', 'Order Currency Code'),
			'order_currency_symbol' => Yii::t('app', 'Order Currency Symbol'),
			'conversion_rate' => Yii::t('app', 'Conversion Rate'),
            'vendor_id' => Yii::t('app', 'Vendor ID'),
            'inventory_id' => Yii::t('app', 'Inventory ID'),
            'total_items' => Yii::t('app', 'Total Items'),
            'discount_coupon_id' => Yii::t('app', 'Discount Coupon ID'),
            'global_discount_id' => Yii::t('app', 'Global Discount ID'),
            'tax_id' => Yii::t('app', 'Tax ID'),
            'inventory_snapshot' => Yii::t('app', 'Inventory Snapshot'),
            'discount_coupon_snapshot' => Yii::t('app', 'Discount Coupon Snapshot'),
            'global_discount_snapshot' => Yii::t('app', 'Global Discount Snapshot'),
            'tax_snapshot' => Yii::t('app', 'Tax Snapshot'),
			'state_tax_snapshot' => Yii::t('app', 'State Tax Snapshot'),
            'total_cost' => Yii::t('app', 'Total Cost'),
			'total_converted_cost' => Yii::t('app', 'Total Converted Cost'),
            'total_shipping' => Yii::t('app', 'Total Shipping'),
            'total_site_discount' => Yii::t('app', 'Total Site Discount'),
            'total_coupon_discount' => Yii::t('app', 'Total Coupon Discount'),
            'discount_coupon_type' => Yii::t('app', 'Discount Coupon Type'),
            'total_tax' => Yii::t('app', 'Total Tax'),
			'is_processed' => Yii::t('app', 'Is Processed'),
            'delivery_method' => Yii::t('app', 'Delivery Method'),
            'payment_method' => Yii::t('app', 'Payment Method'),
            'sub_order_status' => Yii::t('app', 'Sub Order Status'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

	public function getStatus()
    {
    	return $this->hasOne(OrderStatus::className(), ['status' => 'sub_order_status']);
    }

	public function getInventory()
    {
    	return $this->hasOne(Inventory::className(), ['id' => 'inventory_id']);
    }
}
