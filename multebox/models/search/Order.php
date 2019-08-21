<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\Order as OrderModel;

/**
 * Order represents the model behind the search form about `multebox\models\Order`.
 */
class Order extends OrderModel
{
    public function rules()
    {
        return [
            [['id', 'customer_id', 'added_at', 'updated_at'], 'integer'],
            [['cart_snapshot', 'discount_coupon_snapshot', 'global_discount_snapshot', 'total_cost', 'total_site_discount', 'total_coupon_discount', 'discount_coupon_type', 'address_snapshot', 'contact_snapshot', 'delivery_method', 'payment_method', 'order_status', 'order_currency_code', 'order_currency_symbol', 'total_converted_cost', 'conversion_rate'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = OrderModel::find()->orderBy("id desc");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'cart_snapshot', $this->cart_snapshot])
			->andFilterWhere(['like', 'order_currency_code', $this->order_currency_code])
            ->andFilterWhere(['like', 'order_currency_symbol', $this->order_currency_symbol])
            ->andFilterWhere(['like', 'discount_coupon_snapshot', $this->discount_coupon_snapshot])
            ->andFilterWhere(['like', 'global_discount_snapshot', $this->global_discount_snapshot])
            ->andFilterWhere(['like', 'total_cost', $this->total_cost])
			->andFilterWhere(['like', 'conversion_rate', $this->conversion_rate])
			->andFilterWhere(['like', 'total_converted_cost', $this->total_converted_cost])
            ->andFilterWhere(['like', 'total_site_discount', $this->total_site_discount])
            ->andFilterWhere(['like', 'total_coupon_discount', $this->total_coupon_discount])
            ->andFilterWhere(['like', 'discount_coupon_type', $this->discount_coupon_type])
            ->andFilterWhere(['like', 'address_snapshot', $this->address_snapshot])
            ->andFilterWhere(['like', 'contact_snapshot', $this->contact_snapshot])
            ->andFilterWhere(['like', 'delivery_method', $this->delivery_method])
            ->andFilterWhere(['like', 'payment_method', $this->payment_method])
            ->andFilterWhere(['like', 'order_status', $this->order_status]);

        return $dataProvider;
    }
}
