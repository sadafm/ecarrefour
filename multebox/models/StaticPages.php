<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "tbl_static_pages".
 *
 * @property int $id
 * @property string $page_name
 * @property string $content
 * @property int $added_at
 * @property int $updated_at
 */
class StaticPages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_static_pages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['page_name'], 'required'],
            [['content'], 'string'],
            [['added_at', 'updated_at'], 'integer'],
            [['page_name'], 'string', 'max' => 255],
            [['page_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'page_name' => Yii::t('app', 'Page Name'),
            'content' => Yii::t('app', 'Content'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
