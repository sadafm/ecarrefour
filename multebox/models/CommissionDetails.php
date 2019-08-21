<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_commission_details}}".
 *
 * @property int $id
 * @property int $sub_order_id
 * @property int $vendor_id
 * @property int $inventory_id
 * @property string $commission_snapshot
 * @property double $commission
 * @property int $invoiced_ind
 * @property int $vendor_invoice_id
 * @property int $added_at
 * @property int $updated_at
 */
class CommissionDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_commission_details}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sub_order_id', 'vendor_id', 'inventory_id', 'commission_snapshot', 'commission', 'sub_order_total','invoiced_ind'], 'required'],
            [['sub_order_id', 'vendor_id', 'inventory_id', 'invoiced_ind', 'vendor_invoice_id', 'added_at', 'updated_at'], 'integer'],
            [['commission_snapshot'], 'string'],
            [['commission', 'sub_order_total'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sub_order_id' => Yii::t('app', 'Sub Order ID'),
            'vendor_id' => Yii::t('app', 'Vendor ID'),
            'inventory_id' => Yii::t('app', 'Inventory ID'),
            'commission_snapshot' => Yii::t('app', 'Commission Snapshot'),
            'commission' => Yii::t('app', 'Commission'),
			'sub_order_total' => Yii::t('app', 'Sub Order Total'),
            'invoiced_ind' => Yii::t('app', 'Invoiced Ind'),
            'vendor_invoice_id' => Yii::t('app', 'Vendor Invoice ID'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
