<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "tbl_social".
 *
 * @property int $id
 * @property string $platform
 * @property string $link
 * @property int $active
 * @property int $added_at
 * @property int $updated_at
 */
class Social extends \yii\db\ActiveRecord
{
	const _FACEBOOK = 1;
	const _GOOGLE_PLUS = 2;
	const _TWITTER = 3;
	const _LINKEDIN = 4;
	const _YOUTUBE = 5;
	const _INSTAGRAM = 6;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_social';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['platform', 'link', 'active'], 'required'],
            [['active', 'added_at', 'updated_at'], 'integer'],
            [['platform', 'link'], 'string', 'max' => 255],
			[['link'],'url', 'defaultScheme' => 'http'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'platform' => Yii::t('app', 'Platform'),
            'link' => Yii::t('app', 'Link'),
            'active' => Yii::t('app', 'Active'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
