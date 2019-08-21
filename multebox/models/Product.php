<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_product}}".
 *
 * @property int $id
 * @property int $category_id
 * @property int $sub_category_id
 * @property int $sub_subcategory_id
 * @property string $name
 * @property string $description
 * @property int $brand_id
 * @property int $active
 * @property int $added_by_id
 * @property int $sort_order
 * @property int $added_at
 * @property int $updated_at
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_product}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'sub_category_id', 'sub_subcategory_id', 'name', 'digital', 'license_key_code', 'active', 'added_by_id'], 'required'],
            [['upc_code', 'category_id', 'sub_category_id', 'sub_subcategory_id', 'brand_id', 'digital', 'license_key_code', 'active', 'added_by_id', 'sort_order', 'added_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
			[['rating'], 'number', 'min' => 0],
            [['name'], 'string', 'max' => 255],
			[['upc_code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
			'upc_code' => Yii::t('app', 'UPC Code'),
            'category_id' => Yii::t('app', 'Category ID'),
            'sub_category_id' => Yii::t('app', 'Sub Category ID'),
			'sub_subcategory_id' => Yii::t('app', 'Sub-SubCategory ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'brand_id' => Yii::t('app', 'Brand ID'),
			'digital' => Yii::t('app', 'Digital'),
			'license_key_code' => Yii::t('app', 'License-Key-Code'),
            'active' => Yii::t('app', 'Active'),
			'rating' => Yii::t('app', 'Rating'),
            'added_by_id' => Yii::t('app', 'Added By ID'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

	public function afterDelete()
	{
		/*Delete Inventory */
		foreach (Inventory::find()->where(['product_id'=> $this->id])->all() as $record)
		{
			$record->delete();

			/*Delete Cart */
			foreach (Cart::find()->where(['inventory_id'=> $record->id])->all() as $rec)
			{
				$rec->delete();
			}
		}
	}
}
