<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\Product as ProductModel;

/**
 * Product represents the model behind the search form about `multebox\models\Product`.
 */
class Product extends ProductModel
{
    public function rules()
    {
        return [
            [['id', 'upc_code', 'category_id', 'sub_category_id', 'sub_subcategory_id', 'brand_id', 'active', 'digital', 'license_key_code', 'added_by_id', 'sort_order', 'added_at', 'updated_at'], 'integer'],
            [['name', 'description'], 'safe'],
			[['rating'], 'number'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = ProductModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
			'upc_code' => $this->upc_code,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
			'sub_subcategory_id' => $this->sub_subcategory_id,
            'brand_id' => $this->brand_id,
			'digital' => $this->digital,
			'license_key_code' => $this->license_key_code,
            'active' => $this->active,
            'added_by_id' => $this->added_by_id,
            'sort_order' => $this->sort_order,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
			->andFilterWhere(['like', 'rating', $this->rating]);

        return $dataProvider;
    }
}
