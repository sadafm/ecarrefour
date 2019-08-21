<?php

namespace multebox\models;

use Yii;
use multebox\models\search\MulteModel;

/**
 * This is the model class for table "{{%tbl_banner_data}}".
 *
 * @property int $id
 * @property string $banner_name
 * @property string $text_1
 * @property string $text_2
 * @property string $text_3
 * @property int $category_id
 * @property int $sub_category_id
 * @property int $sub_sub_category_id
 * @property int $product_id
 * @property int $added_at
 * @property int $updated_at
 */
class BannerData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_banner_data}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['banner_type'], 'required'],
            [['category_id', 'sub_category_id', 'sub_subcategory_id', 'product_id', 'inventory_id', 'banner_type', 'added_at', 'updated_at'], 'integer'],
            [['banner_new_name', 'text_1', 'text_2'], 'string', 'max' => 255],
            [['text_3'], 'string', 'max' => 50],
			[['banner_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'checkExtensionByMimeType'=>false],
			[['banner_file'], 'required', 'on'=> 'create']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'banner_file' => Yii::t('app', 'Banner File'),
			'banner_new_name' => Yii::t('app', 'Banner New Name'),
            'text_1' => Yii::t('app', 'Text 1'),
            'text_2' => Yii::t('app', 'Text 2'),
            'text_3' => Yii::t('app', 'Text 3'),
            'category_id' => Yii::t('app', 'Category'),
            'sub_category_id' => Yii::t('app', 'Sub Category'),
            'sub_subcategory_id' => Yii::t('app', 'Sub SubCategory'),
            'product_id' => Yii::t('app', 'Product'),
			'inventory_id' => Yii::t('app', 'Inventory Item'),
			'banner_type' => Yii::t('app', 'Banner Type'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

	public function upload($name)
    {
        if ($this->validate()) {
			//$this->banner_file->saveAs(Yii::getAlias('@multefront').'/web/images/upload/' . $name);
			MulteModel::saveFileToServer($this->banner_file->tempName, $name, Yii::$app->params['web_folder']."/banner");
            return true;
        } else {
			Yii::$app->session->setFlash('error', $this->errors['banner_file'][0]);
            return false;
        }
    }

	public function getProduct()
    {
    	return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}
