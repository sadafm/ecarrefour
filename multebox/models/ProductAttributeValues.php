<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_product_attribute_values}}".
 *
 * @property int $id
 * @property string $name
 * @property string $values
 * @property int $added_at
 * @property int $updated_at
 */
class ProductAttributeValues extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_product_attribute_values}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'values'], 'required'],
            [['values'], 'string'],
            [['added_at', 'updated_at', 'added_by_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'values' => Yii::t('app', 'Values'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
			'added_by_id' => Yii::t('app', 'Added By'),
        ];
    }

	public function getProductAttributes()
    {
    	return $this->hasMany(ProductAttributes::className(), ['fixed_id' => 'id']);
    }
}
