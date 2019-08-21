<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "tbl_newsletter".
 *
 * @property int $id
 * @property string $email
 * @property int $added_at
 * @property int $updated_at
 */
class Newsletter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_newsletter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['added_at', 'updated_at'], 'integer'],
            [['email'], 'string', 'max' => 255],
			[[ 'email'],'email'],
            [['email'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
