<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\ProductBrand as ProductBrandModel;

/**
 * ProductBrand represents the model behind the search form about `multebox\models\ProductBrand`.
 */
class ProductBrand extends ProductBrandModel
{
    public function rules()
    {
        return [
            [['id', 'active', 'added_by_id', 'sort_order', 'added_at', 'updated_at'], 'integer'],
            [['name', 'brand_image', 'brand_new_image'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = ProductBrandModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'active' => $this->active,
            'added_by_id' => $this->added_by_id,
            'sort_order' => $this->sort_order,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'brand_image', $this->brand_image])
			->andFilterWhere(['like', 'brand_new_image', $this->brand_new_image]);

        return $dataProvider;
    }
}
