<?php

namespace multebox\models;

use Yii;
use multebox\models\search\MulteModel;

/**
 * This is the model class for table "{{%tbl_product_brand}}".
 *
 * @property int $id
 * @property string $name
 * @property int $active
 * @property int $added_by_id
 * @property int $sort_order
 * @property int $added_at
 * @property int $updated_at
 */
class ProductBrand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_product_brand}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'active', 'added_by_id'], 'required'],
            [['active', 'added_by_id', 'sort_order', 'added_at', 'updated_at'], 'integer'],
            [['name', 'brand_new_image'], 'string', 'max' => 255],
			[['name'], 'unique'],
			[['brand_image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'checkExtensionByMimeType'=>false],
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
			'brand_image' => Yii::t('app', 'Brand Image'),
			'brand_new_image' => Yii::t('app', 'Brand New Image'),
            'active' => Yii::t('app', 'Active'),
            'added_by_id' => Yii::t('app', 'Added By ID'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

	public function upload($name)
    {
        if ($this->validate()) {
			//$this->brand_image->saveAs(Yii::getAlias('@multefront').'/web/images/upload/' . $name);
			MulteModel::saveFileToServer($this->brand_image->tempName, $name, Yii::$app->params['web_folder']."/brand");
            return true;
        } else {
			Yii::$app->session->setFlash('error', $this->errors['brand_image'][0]);
            return false;
        }
    }

}
