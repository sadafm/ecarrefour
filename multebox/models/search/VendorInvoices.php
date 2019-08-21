<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\VendorInvoices as VendorInvoicesModel;

/**
 * VendorInvoices represents the model behind the search form about `\multebox\models\VendorInvoices`.
 */
class VendorInvoices extends VendorInvoicesModel
{
    public function rules()
    {
        return [
            [['id', 'vendor_id', 'paid_ind', 'added_at', 'updated_at'], 'integer'],
            [['total_commission', 'total_order_amount'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = VendorInvoicesModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'paid_ind' => $this->paid_ind,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'total_order_amount', $this->total_amount])
			->andFilterWhere(['like', 'total_commission', $this->total_commission]);

        return $dataProvider;
    }
}
