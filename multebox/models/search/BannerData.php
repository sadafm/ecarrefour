<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\BannerData as BannerDataModel;

/**
 * BannerData represents the model behind the search form about `\multebox\models\BannerData`.
 */
class BannerData extends BannerDataModel
{
    public function rules()
    {
        return [
            [['id', 'category_id', 'sub_category_id', 'sub_subcategory_id', 'product_id', 'inventory_id', 'banner_type', 'added_at', 'updated_at'], 'integer'],
            [['banner_file', 'banner_new_name', 'text_1', 'text_2', 'text_3'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = BannerDataModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'sub_subcategory_id' => $this->sub_subcategory_id,
            'product_id' => $this->product_id,
			'inventory_id' => $this->inventory_id,
			'banner_type' => $this->banner_type,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'banner_file', $this->banner_file])
			->andFilterWhere(['like', 'banner_new_name', $this->banner_new_name])
            ->andFilterWhere(['like', 'text_1', $this->text_1])
            ->andFilterWhere(['like', 'text_2', $this->text_2])
            ->andFilterWhere(['like', 'text_3', $this->text_3]);

        return $dataProvider;
    }
}
