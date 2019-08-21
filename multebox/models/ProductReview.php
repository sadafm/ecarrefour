<?php

namespace multebox\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_product_review}}".
 *
 * @property int $id
 * @property int $product_id
 * @property int $customer_id
 * @property string $review
 * @property int $rating
 * @property int $added_at
 * @property int $updated_at
 */
class ProductReview extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_product_review}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'customer_id', 'rating'], 'required'],
            [['product_id', 'customer_id', 'rating', 'added_at', 'updated_at'], 'integer'],
            [['review'], 'string'],
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
            'customer_id' => Yii::t('app', 'Customer ID'),
            'review' => Yii::t('app', 'Review'),
            'rating' => Yii::t('app', 'Rating'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
