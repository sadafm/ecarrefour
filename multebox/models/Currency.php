<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_currency}}".
 *
 * @property int $id
 * @property string $currency_name
 * @property string $currency_symbol
 * @property string $currency_code
 */
class Currency extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_currency}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['currency_name', 'currency_symbol', 'currency_code'], 'required'],
            [['currency_name', 'currency_symbol', 'currency_code'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'currency_name' => Yii::t('app', 'Currency Name'),
            'currency_symbol' => Yii::t('app', 'Currency Symbol'),
            'currency_code' => Yii::t('app', 'Currency Code'),
        ];
    }
}
