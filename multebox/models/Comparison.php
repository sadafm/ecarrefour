<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "tbl_comparison".
 *
 * @property int $id
 * @property string $inventory_list
 * @property int $count
 * @property string $session_id
 * @property int $customer_id
 * @property int $added_at
 * @property int $updated_at
 */
class Comparison extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_comparison';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inventory_list', 'count', 'session_id'], 'required'],
            [['inventory_list'], 'string'],
            [['count', 'customer_id', 'added_at', 'updated_at'], 'integer'],
            [['session_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'inventory_list' => Yii::t('app', 'Inventory List'),
            'count' => Yii::t('app', 'Count'),
            'session_id' => Yii::t('app', 'Session ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
