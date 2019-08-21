<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\ProductAttributeValues as ProductAttributeValuesModel;

/**
 * ProductAttributeValues represents the model behind the search form about `multebox\models\ProductAttributeValues`.
 */
class ProductAttributeValues extends ProductAttributeValuesModel
{
    public function rules()
    {
        return [
            [['id', 'added_at', 'updated_at', 'added_by_id'], 'integer'],
            [['name', 'values'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = ProductAttributeValuesModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
			'added_by_id' => $this->added_by_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'values', $this->values]);

        return $dataProvider;
    }
}
