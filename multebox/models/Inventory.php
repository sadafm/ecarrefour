<?php

namespace multebox\models;

use Yii;
use yii\web\UploadedFile;
use multebox\models\search\MulteModel;

/**
 * This is the model class for table "{{%tbl_inventory}}".
 *
 * @property int $id
 * @property int $product_id
 * @property string $product_name
 * @property int $vendor_id
 * @property int $stock
 * @property string $price_type
 * @property double $price
 * @property string $discount_type
 * @property double $discount
 * @property string $attribute_values
 * @property string $attribute_price
 * @property double $shipping_cost
 * @property int $slab_discount_ind
 * @property string $slab_discount_type
 * @property int $slab_1_range
 * @property double $slab_1_discount
 * @property int $slab_2_range
 * @property double $slab_2_discount
 * @property int $slab_3_range
 * @property double $slab_3_discount
 * @property int $slab_4_range
 * @property double $slab_4_discount
 * @property int $added_by_id
 * @property int $sort_order
 * @property int $added_at
 * @property int $updated_at
 */
class Inventory extends \yii\db\ActiveRecord
{
    const SCENARIO_DIGITAL = 'digital';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_inventory}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'product_name', 'vendor_id', 'price_type', 'price', 'discount_type', 'slab_discount_ind', 'added_by_id', 'active'], 'required'],
            [['product_id', 'vendor_id', 'slab_discount_ind', 'added_by_id', 'sort_order', 'send_as_attachment', 'special', 'featured', 'hot', 'total_sale', 'active', 'added_at', 'updated_at'], 'integer'],
            [['attribute_values', 'attribute_price', 'product_name', 'digital_file_name', 'attachment_file_name', 'warranty'], 'string'],
            [['price_type', 'discount_type', 'slab_discount_type'], 'string', 'max' => 1],
			[['stock', 'slab_1_range', 'slab_2_range', 'slab_3_range', 'slab_4_range'], 'integer', 'min' => 0],
			[['discount', 'shipping_cost', 'slab_1_discount', 'slab_2_discount', 'slab_3_discount', 'slab_4_discount', 'product_rating', 'vendor_rating', 'price', 'length', 'width', 'height', 'weight'], 'number', 'min' => 0],
			[['digital_file'], 'file', 'skipOnEmpty' => false, 'on' => self::SCENARIO_DIGITAL],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'product_id' => Yii::t('app', 'Product ID'),
			'product_name' => Yii::t('app', 'Product Name'),
            'vendor_id' => Yii::t('app', 'Vendor ID'),
            'stock' => Yii::t('app', 'Stock'),
            'price_type' => Yii::t('app', 'Price Type'),
            'price' => Yii::t('app', 'Price'),
            'discount_type' => Yii::t('app', 'Discount Type'),
            'discount' => Yii::t('app', 'Discount'),
            'attribute_values' => Yii::t('app', 'Attribute Values'),
            'attribute_price' => Yii::t('app', 'Attribute Price'),
            'shipping_cost' => Yii::t('app', 'Shipping Cost'),
            'slab_discount_ind' => Yii::t('app', 'Slab Discount Ind'),
            'slab_discount_type' => Yii::t('app', 'Slab Discount Type'),
            'slab_1_range' => Yii::t('app', 'Slab 1 Range'),
            'slab_1_discount' => Yii::t('app', 'Slab 1 Discount'),
            'slab_2_range' => Yii::t('app', 'Slab 2 Range'),
            'slab_2_discount' => Yii::t('app', 'Slab 2 Discount'),
            'slab_3_range' => Yii::t('app', 'Slab 3 Range'),
            'slab_3_discount' => Yii::t('app', 'Slab 3 Discount'),
            'slab_4_range' => Yii::t('app', 'Slab 4 Range'),
            'slab_4_discount' => Yii::t('app', 'Slab 4 Discount'),
			'product_rating' => Yii::t('app', 'Product Rating'),
			'vendor_rating' => Yii::t('app', 'Vendor Rating'),
            'added_by_id' => Yii::t('app', 'Added By ID'),
			'special' => Yii::t('app', 'Special'),
			'featured' => Yii::t('app', 'Featured'),
			'hot' => Yii::t('app', 'Hot'),
			'total_sale' => Yii::t('app', 'Total Sale'),
            'sort_order' => Yii::t('app', 'Sort Order'),
			'digital_file' => Yii::t('app', 'Digital File'),
			'digital_file_name' => Yii::t('app', 'Digital File Name'),
			'send_as_attachment' => Yii::t('app', 'Send As Attachment'),
			'attachment_file_name' => Yii::t('app', 'Attachment File Name'),
			'length' => Yii::t('app', 'Length'),
			'width' => Yii::t('app', 'Width'),
			'height' => Yii::t('app', 'Height'),
			'weight' => Yii::t('app', 'Weight'),
			'active' => Yii::t('app', 'Active'),
			'warranty' => Yii::t('app', 'Warranty'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

	public function getinventoryProducts()
    {
    	return $this->hasMany(Product::className(), ['id' => 'product_id']);
    }

	public function getProduct()
    {
    	return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

	public function upload($name)
    {
        if ($this->validate()) {
            //$this->digital_file->saveAs('uploads/' . $name . '.' . $this->digital_file->extension);
			//$this->digital_file->saveAs('uploads/' . $name);
			MulteModel::saveFileToServer($this->digital_file->tempName, $name, "digital_uploads", true);
            return true;
        } else {
            return false;
        }
    }

	public function afterDelete()
	{
		/*Delete Cart */
		foreach (Cart::find()->where(['inventory_id'=> $this->id])->all() as $rec)
		{
			$rec->delete();
		}
	}
}
