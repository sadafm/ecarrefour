<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\InvoiceStatus as InvoiceStatusModel;

/**
 * InvoiceStatus represents the model behind the search form about `multebox\models\InvoiceStatus`.
 */
class InvoiceStatus extends InvoiceStatusModel
{
    public function rules()
    {
        return [
            [['id', 'active', 'sort_order', 'added_at', 'updated_at'], 'integer'],
            [['status', 'label'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = InvoiceStatusModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'active' => $this->active,
            'sort_order' => $this->sort_order,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'label', $this->label]);

        return $dataProvider;
    }
}
