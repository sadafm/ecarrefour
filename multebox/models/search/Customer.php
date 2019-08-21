<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\Customer as CustomerModel;

/**
 * Customer represents the model behind the search form about `multebox\models\Customer`.
 */
class Customer extends CustomerModel
{
    public function rules()
    {
        return [
            [['id', 'customer_type_id', 'added_by_id', 'active', 'added_at', 'updated_at'], 'integer'],
            [['customer_name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = CustomerModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'customer_type_id' => $this->customer_type_id,
            'added_by_id' => $this->added_by_id,
            'active' => $this->active,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'customer_name', $this->customer_name]);

        return $dataProvider;
    }
}
