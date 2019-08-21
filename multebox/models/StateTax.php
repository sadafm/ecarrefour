<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "tbl_state_tax".
 *
 * @property int $id
 * @property int $tax_id
 * @property int $state_id
 * @property string $tax_percentage
 * @property int $added_at
 * @property int $updated_at
 */
class StateTax extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_state_tax';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tax_id', 'state_id', 'tax_percentage'], 'required'],
            [['tax_id', 'state_id', 'country_id', 'added_at', 'updated_at'], 'integer'],
            [['tax_percentage'], 'number'],
            [['tax_id', 'country_id', 'state_id'], 'unique', 'targetAttribute' => ['tax_id', 'country_id', 'state_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tax_id' => Yii::t('app', 'Tax ID'),
			'country_id' => Yii::t('app', 'Country ID'),
            'state_id' => Yii::t('app', 'State ID'),
            'tax_percentage' => Yii::t('app', 'Tax Percentage'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
