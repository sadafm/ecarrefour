<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_invoice}}".
 *
 * @property int $id
 * @property int $sub_order_id
 * @property int $added_at
 * @property int $updated_at
 */
class Invoice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_invoice}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sub_order_id', 'added_at', 'updated_at'], 'integer'],
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
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
