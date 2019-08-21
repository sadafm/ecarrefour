<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\SubOrder as SubOrderModel;

/**
 * SubOrder represents the model behind the search form about `multebox\models\SubOrder`.
 */
class SubOrder extends SubOrderModel
{
    public function rules()
    {
        return [
            [['id', 'order_id', 'vendor_id', 'inventory_id', 'total_items', 'discount_coupon_id', 'global_discount_id', 'tax_id', 'is_processed', 'added_at', 'updated_at'], 'integer'],
            [['inventory_snapshot', 'discount_coupon_snapshot', 'global_discount_snapshot', 'tax_snapshot', 'state_tax_snapshot', 'total_cost', 'total_shipping', 'total_site_discount', 'total_coupon_discount', 'discount_coupon_type', 'total_tax', 'delivery_method', 'payment_method', 'sub_order_status', 'order_currency_code', 'order_currency_symbol', 'total_converted_cost', 'conversion_rate'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = SubOrderModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'vendor_id' => $this->vendor_id,
            'inventory_id' => $this->inventory_id,
            'total_items' => $this->total_items,
            'discount_coupon_id' => $this->discount_coupon_id,
            'global_discount_id' => $this->global_discount_id,
            'tax_id' => $this->tax_id,
			'is_processed' => $this->is_processed,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'inventory_snapshot', $this->inventory_snapshot])
            ->andFilterWhere(['like', 'discount_coupon_snapshot', $this->discount_coupon_snapshot])
            ->andFilterWhere(['like', 'global_discount_snapshot', $this->global_discount_snapshot])
			->andFilterWhere(['like', 'order_currency_code', $this->order_currency_code])
            ->andFilterWhere(['like', 'order_currency_symbol', $this->order_currency_symbol])
            ->andFilterWhere(['like', 'tax_snapshot', $this->tax_snapshot])
			->andFilterWhere(['like', 'state_tax_snapshot', $this->state_tax_snapshot])
            ->andFilterWhere(['like', 'total_cost', $this->total_cost])
			->andFilterWhere(['like', 'conversion_rate', $this->conversion_rate])
			->andFilterWhere(['like', 'total_converted_cost', $this->total_converted_cost])
            ->andFilterWhere(['like', 'total_shipping', $this->total_shipping])
            ->andFilterWhere(['like', 'total_site_discount', $this->total_site_discount])
            ->andFilterWhere(['like', 'total_coupon_discount', $this->total_coupon_discount])
            ->andFilterWhere(['like', 'discount_coupon_type', $this->discount_coupon_type])
            ->andFilterWhere(['like', 'total_tax', $this->total_tax])
            ->andFilterWhere(['like', 'delivery_method', $this->delivery_method])
            ->andFilterWhere(['like', 'payment_method', $this->payment_method])
            ->andFilterWhere(['like', 'sub_order_status', $this->sub_order_status]);

        return $dataProvider;
    }

	public function orderSearch($params, $id)
    {
		if(Yii::$app->user->identity->entity_type == 'employee')
		{
			$query = SubOrderModel::find()->where("order_id=".$id);
		}
		else
		{
			if(Yii::$app->user->identity->entity_type == 'vendor')
			{
				$query = SubOrderModel::find()->where("vendor_id=".Yii::$app->user->identity->entity_id." and order_id=".$id);
			}
			else
			{
				$query = SubOrderModel::find()->where("id=0");
			}
		}    

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'vendor_id' => $this->vendor_id,
            'inventory_id' => $this->inventory_id,
            'total_items' => $this->total_items,
            'discount_coupon_id' => $this->discount_coupon_id,
            'global_discount_id' => $this->global_discount_id,
            'tax_id' => $this->tax_id,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'inventory_snapshot', $this->inventory_snapshot])
            ->andFilterWhere(['like', 'discount_coupon_snapshot', $this->discount_coupon_snapshot])
            ->andFilterWhere(['like', 'global_discount_snapshot', $this->global_discount_snapshot])
			->andFilterWhere(['like', 'order_currency_code', $this->order_currency_code])
            ->andFilterWhere(['like', 'order_currency_symbol', $this->order_currency_symbol])
            ->andFilterWhere(['like', 'tax_snapshot', $this->tax_snapshot])
			->andFilterWhere(['like', 'state_tax_snapshot', $this->state_tax_snapshot])
            ->andFilterWhere(['like', 'total_cost', $this->total_cost])
			->andFilterWhere(['like', 'conversion_rate', $this->conversion_rate])
			->andFilterWhere(['like', 'total_converted_cost', $this->total_converted_cost])
            ->andFilterWhere(['like', 'total_shipping', $this->total_shipping])
            ->andFilterWhere(['like', 'total_site_discount', $this->total_site_discount])
            ->andFilterWhere(['like', 'total_coupon_discount', $this->total_coupon_discount])
            ->andFilterWhere(['like', 'discount_coupon_type', $this->discount_coupon_type])
            ->andFilterWhere(['like', 'total_tax', $this->total_tax])
            ->andFilterWhere(['like', 'delivery_method', $this->delivery_method])
            ->andFilterWhere(['like', 'payment_method', $this->payment_method])
            ->andFilterWhere(['like', 'sub_order_status', $this->sub_order_status]);

        return $dataProvider;
    }

	public function vendorSearch($params)
    {
		if(Yii::$app->user->identity->entity_type == 'vendor')
			$query = SubOrderModel::find()->where("vendor_id=".Yii::$app->user->identity->entity_id);
		else
			$query = SubOrderModel::find()->where("id=0");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'vendor_id' => $this->vendor_id,
            'inventory_id' => $this->inventory_id,
            'total_items' => $this->total_items,
            'discount_coupon_id' => $this->discount_coupon_id,
            'global_discount_id' => $this->global_discount_id,
            'tax_id' => $this->tax_id,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'inventory_snapshot', $this->inventory_snapshot])
            ->andFilterWhere(['like', 'discount_coupon_snapshot', $this->discount_coupon_snapshot])
            ->andFilterWhere(['like', 'global_discount_snapshot', $this->global_discount_snapshot])
			->andFilterWhere(['like', 'order_currency_code', $this->order_currency_code])
            ->andFilterWhere(['like', 'order_currency_symbol', $this->order_currency_symbol])
            ->andFilterWhere(['like', 'tax_snapshot', $this->tax_snapshot])
			->andFilterWhere(['like', 'state_tax_snapshot', $this->state_tax_snapshot])
            ->andFilterWhere(['like', 'total_cost', $this->total_cost])
			->andFilterWhere(['like', 'conversion_rate', $this->conversion_rate])
			->andFilterWhere(['like', 'total_converted_cost', $this->total_converted_cost])
            ->andFilterWhere(['like', 'total_shipping', $this->total_shipping])
            ->andFilterWhere(['like', 'total_site_discount', $this->total_site_discount])
            ->andFilterWhere(['like', 'total_coupon_discount', $this->total_coupon_discount])
            ->andFilterWhere(['like', 'discount_coupon_type', $this->discount_coupon_type])
            ->andFilterWhere(['like', 'total_tax', $this->total_tax])
            ->andFilterWhere(['like', 'delivery_method', $this->delivery_method])
            ->andFilterWhere(['like', 'payment_method', $this->payment_method])
            ->andFilterWhere(['like', 'sub_order_status', $this->sub_order_status]);

        return $dataProvider;
    }
}
