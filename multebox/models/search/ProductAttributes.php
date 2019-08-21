<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\ProductAttributes as ProductAttributesModel;

/**
 * ProductAttributes represents the model behind the search form about `multebox\models\ProductAttributes`.
 */
class ProductAttributes extends ProductAttributesModel
{
    public function rules()
    {
        return [
            [['id', 'parent_id', 'active', 'fixed', 'fixed_id', 'added_by_id', 'sort_order', 'added_at', 'updated_at'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = ProductAttributesModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
			'fixed' => $this->fixed,
			'fixed_id' => $this->fixed_id,
            'active' => $this->active,
            'added_by_id' => $this->added_by_id,
            'sort_order' => $this->sort_order,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
