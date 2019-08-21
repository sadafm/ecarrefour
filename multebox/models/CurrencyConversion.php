<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "tbl_currency_conversion".
 *
 * @property int $id
 * @property string $from
 * @property string $to
 * @property double $conversion_rate
 */
class CurrencyConversion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_currency_conversion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to', 'conversion_rate'], 'required'],
            [['conversion_rate'], 'number'],
            [['from', 'to'], 'string', 'max' => 100],
            [['from', 'to'], 'unique', 'targetAttribute' => ['from', 'to']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'from' => Yii::t('app', 'From'),
            'to' => Yii::t('app', 'To'),
            'conversion_rate' => Yii::t('app', 'Conversion Rate'),
        ];
    }
}
