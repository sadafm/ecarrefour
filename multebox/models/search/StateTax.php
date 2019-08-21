<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\StateTax as StateTaxModel;

/**
 * StateTax represents the model behind the search form about `\multebox\models\StateTax`.
 */
class StateTax extends StateTaxModel
{
    public function rules()
    {
        return [
            [['id', 'tax_id', 'state_id', 'country_id', 'added_at', 'updated_at'], 'integer'],
            [['tax_percentage'], 'number'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = StateTaxModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tax_id' => $this->tax_id,
			'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'tax_percentage' => $this->tax_percentage,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }

	public function searchTax($params, $tax_id)
    {
        $query = StateTaxModel::find()->where(['tax_id' => $tax_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tax_id' => $this->tax_id,
			'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'tax_percentage' => $this->tax_percentage,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
