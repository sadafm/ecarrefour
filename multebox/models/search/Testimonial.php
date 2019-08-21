<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\Testimonial as TestimonialModel;

/**
 * Testimonial represents the model behind the search form about `\multebox\models\Testimonial`.
 */
class Testimonial extends TestimonialModel
{
    public function rules()
    {
        return [
            [['id', 'added_at', 'updated_at'], 'integer'],
            [['writer_image', 'writer_new_image', 'testimonial', 'writer_name', 'writer_designation'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = TestimonialModel::find();

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
        ]);

        $query->andFilterWhere(['like', 'writer_image', $this->writer_image])
            ->andFilterWhere(['like', 'writer_new_image', $this->writer_new_image])
            ->andFilterWhere(['like', 'testimonial', $this->testimonial])
            ->andFilterWhere(['like', 'writer_name', $this->writer_name])
            ->andFilterWhere(['like', 'writer_designation', $this->writer_designation]);

        return $dataProvider;
    }
}
