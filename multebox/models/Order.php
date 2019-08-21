<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_order}}".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $cart_snapshot
 * @property string $discount_coupon_snapshot
 * @property string $global_discount_snapshot
 * @property double $total_cost
 * @property double $total_site_discount
 * @property double $total_coupon_discount
 * @property string $discount_coupon_type
 * @property int $address_snapshot
 * @property int $contact_snapshot
 * @property int $delivery_method
 * @property int $payment_method
 * @property int $order_status_id
 * @property int $added_at
 * @property int $updated_at
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'order_currency_code', 'order_currency_symbol', 'total_cost', 'total_site_discount', 'total_coupon_discount', 'delivery_method', 'payment_method', 'order_status', 'conversion_rate'], 'required'],
            [['customer_id', 'added_at', 'updated_at'], 'integer'],
            [['cart_snapshot', 'discount_coupon_snapshot', 'global_discount_snapshot', 'address_snapshot', 'contact_snapshot', 'order_currency_code', 'order_currency_symbol'], 'string'],
            [['total_cost', 'total_site_discount', 'total_coupon_discount', 'total_converted_cost', 'conversion_rate'], 'number'],
            [['discount_coupon_type'], 'string', 'max' => 1],
			[['delivery_method', 'payment_method', 'order_status'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
			'order_currency_code' => Yii::t('app', 'Order Currency Code'),
			'order_currency_symbol' => Yii::t('app', 'Order Currency Symbol'),
            'cart_snapshot' => Yii::t('app', 'Cart Snapshot'),
            'discount_coupon_snapshot' => Yii::t('app', 'Discount Coupon Snapshot'),
            'global_discount_snapshot' => Yii::t('app', 'Global Discount Snapshot'),
            'total_cost' => Yii::t('app', 'Total Cost'),
			'conversion_rate' => Yii::t('app', 'Conversion Rate'),
			'total_converted_cost' => Yii::t('app', 'Total Converted Cost'),
            'total_site_discount' => Yii::t('app', 'Total Site Discount'),
            'total_coupon_discount' => Yii::t('app', 'Total Coupon Discount'),
            'discount_coupon_type' => Yii::t('app', 'Discount Coupon Type'),
            'address_snapshot' => Yii::t('app', 'Address Snapshot'),
			'contact_snapshot' => Yii::t('app', 'Contact Snapshot'),
            'delivery_method' => Yii::t('app', 'Delivery Method'),
            'payment_method' => Yii::t('app', 'Payment Method'),
            'order_status' => Yii::t('app', 'Order Status'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

	public function getStatus()
    {
    	return $this->hasOne(OrderStatus::className(), ['status' => 'order_status']);
    }
}
