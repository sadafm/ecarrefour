<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "tbl_banner_type".
 *
 * @property int $id
 * @property string $type
 * @property int $added_at
 * @property int $updated_at
 */
class BannerType extends \yii\db\ActiveRecord
{
	const _SLIDER = 1;
	const _MIDDLE_LEFT_BANNER = 2;
	const _MIDDLE_CENTER_BANNER = 3;
	const _MIDDLE_RIGHT_TOP_BANNER = 4;
	const _MIDDLE_RIGHT_BOTTOM_BANNNER = 5;
	const _BOTTOM_LEFT_BANNER = 6;
	const _BOTTOM_RIGHT_BANNER = 7;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_banner_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['added_at', 'updated_at'], 'integer'],
            [['type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
