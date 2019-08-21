<?php

namespace multebox\models;

use Yii;
use multebox\models\search\MulteModel;

/**
 * This is the model class for table "tbl_testimonial".
 *
 * @property int $id
 * @property string $writer_image
 * @property string $writer_new_image
 * @property string $testimonial
 * @property string $writer_name
 * @property string $writer_designation
 * @property int $added_at
 * @property int $updated_at
 */
class Testimonial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_testimonial';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['testimonial', 'writer_name'], 'required'],
            [['added_at', 'updated_at'], 'integer'],
            [['writer_new_image', 'testimonial', 'writer_name', 'writer_designation'], 'string', 'max' => 255],
			[['writer_image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'checkExtensionByMimeType'=>false],
			//[['writer_image'], 'required', 'on'=> 'create']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'writer_image' => Yii::t('app', 'Writer Image'),
            'writer_new_image' => Yii::t('app', 'Writer New Image'),
            'testimonial' => Yii::t('app', 'Testimonial'),
            'writer_name' => Yii::t('app', 'Writer Name'),
            'writer_designation' => Yii::t('app', 'Writer Designation'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

	public function upload($name)
    {
        if ($this->validate()) {
			//$this->writer_image->saveAs(Yii::getAlias('@multefront').'/web/images/upload/' . $name);
			MulteModel::saveFileToServer($this->writer_image->tempName, $name, Yii::$app->params['web_folder']."/testimonial");
            return true;
        } else {
			Yii::$app->session->setFlash('error', $this->errors['writer_image'][0]);
            return false;
        }
    }
}
