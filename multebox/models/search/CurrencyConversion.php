<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\CurrencyConversion as CurrencyConversionModel;

/**
 * CurrencyConversion represents the model behind the search form about `\multebox\models\CurrencyConversion`.
 */
class CurrencyConversion extends CurrencyConversionModel
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['from', 'to', 'conversion_rate'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = CurrencyConversionModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'from', $this->from])
            ->andFilterWhere(['like', 'to', $this->to])
            ->andFilterWhere(['like', 'conversion_rate', $this->conversion_rate]);

        return $dataProvider;
    }
}
