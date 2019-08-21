<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\GlobalDiscount as GlobalDiscountModel;

/**
 * GlobalDiscount represents the model behind the search form about `multebox\models\GlobalDiscount`.
 */
class GlobalDiscount extends GlobalDiscountModel
{
    public function rules()
    {
        return [
            [['id', 'category_id', 'sub_category_id', 'sub_subcategory_id', 'added_by_id', 'added_at', 'updated_at'], 'integer'],
            [['discount_type', 'discount', 'max_discount', 'min_cart_amount', 'max_budget', 'used_budget'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GlobalDiscountModel::find();

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
            'added_by_id' => $this->added_by_id,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'discount_type', $this->discount_type])
            ->andFilterWhere(['like', 'discount', $this->discount])
			->andFilterWhere(['like', 'max_discount', $this->max_discount])
			->andFilterWhere(['like', 'min_cart_amount', $this->min_cart_amount])
			->andFilterWhere(['like', 'max_budget', $this->max_budget])
			->andFilterWhere(['like', 'used_budget', $this->used_budget]);

        return $dataProvider;
    }
}
