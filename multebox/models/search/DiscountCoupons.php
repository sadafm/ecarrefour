<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\DiscountCoupons as DiscountCouponsModel;

/**
 * DiscountCoupons represents the model behind the search form about `multebox\models\DiscountCoupons`.
 */
class DiscountCoupons extends DiscountCouponsModel
{
    public function rules()
    {
        return [
            [['id', 'category_id', 'sub_category_id', 'sub_subcategory_id', 'inventory_id', 'max_uses', 'used_count', 'expiry_datetime', 'customer_id', 'added_by_id', 'added_at', 'updated_at'], 'integer'],
            [['coupon_code', 'discount_type', 'discount', 'max_discount', 'min_cart_amount', 'max_budget', 'used_budget'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = DiscountCouponsModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
			'sub_subcategory_id' => $this->sub_subcategory_id,
            'inventory_id' => $this->inventory_id,
            'max_uses' => $this->max_uses,
			'used_count' => $this->used_count,
            'expiry_datetime' => $this->expiry_datetime,
            'customer_id' => $this->customer_id,
            'added_by_id' => $this->added_by_id,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'coupon_code', $this->coupon_code])
            ->andFilterWhere(['like', 'discount_type', $this->discount_type])
            ->andFilterWhere(['like', 'discount', $this->discount])
			->andFilterWhere(['like', 'max_discount', $this->max_discount])
			->andFilterWhere(['like', 'min_cart_amount', $this->min_cart_amount])
			->andFilterWhere(['like', 'max_budget', $this->max_budget])
			->andFilterWhere(['like', 'used_budget', $this->used_budget]);

        return $dataProvider;
    }
}
