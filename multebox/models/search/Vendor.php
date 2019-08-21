<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\Vendor as VendorModel;

/**
 * Vendor represents the model behind the search form about `multebox\models\Vendor`.
 */
class Vendor extends VendorModel
{
    public function rules()
    {
        return [
            [['id', 'vendor_type_id', 'added_by_id', 'active', 'added_at', 'updated_at'], 'integer'],
            [['vendor_name'], 'safe'],
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
        $query = VendorModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'vendor_type_id' => $this->vendor_type_id,
            'added_by_id' => $this->added_by_id,
            'active' => $this->active,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'vendor_name', $this->vendor_name])
			->andFilterWhere(['like', 'rating', $this->rating]);

        return $dataProvider;
    }
}
