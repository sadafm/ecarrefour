<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_vendor_invoices}}".
 *
 * @property int $id
 * @property int $vendor_id
 * @property double $total_amount
 * @property int $paid_ind
 * @property int $added_at
 * @property int $updated_at
 */
class VendorInvoices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_vendor_invoices}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vendor_id', 'total_commission', 'total_order_amount', 'paid_ind'], 'required'],
            [['vendor_id', 'paid_ind', 'added_at', 'updated_at'], 'integer'],
            [['total_commission', 'total_order_amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'vendor_id' => Yii::t('app', 'Vendor ID'),
            'total_commission' => Yii::t('app', 'Total Commission'),
			'total_order_amount' => Yii::t('app', 'Total Order Amount'),
            'paid_ind' => Yii::t('app', 'Paid Ind'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
