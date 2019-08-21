<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\CommissionDetails as CommissionDetailsModel;

/**
 * CommissionDetails represents the model behind the search form about `\multebox\models\CommissionDetails`.
 */
class CommissionDetails extends CommissionDetailsModel
{
    public function rules()
    {
        return [
            [['id', 'sub_order_id', 'vendor_id', 'inventory_id', 'invoiced_ind', 'vendor_invoice_id', 'added_at', 'updated_at'], 'integer'],
            [['commission', 'sub_order_total'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = CommissionDetailsModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sub_order_id' => $this->sub_order_id,
            'vendor_id' => $this->vendor_id,
            'inventory_id' => $this->inventory_id,
            'invoiced_ind' => $this->invoiced_ind,
            'vendor_invoice_id' => $this->vendor_invoice_id,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'commission', $this->commission])
			->andFilterWhere(['like', 'sub_order_total', $this->sub_order_total]);

        return $dataProvider;
    }
}
