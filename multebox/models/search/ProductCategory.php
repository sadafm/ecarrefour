<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\ProductCategory as ProductCategoryModel;

/**
 * ProductCategory represents the model behind the search form about `multebox\models\ProductCategory`.
 */
class ProductCategory extends ProductCategoryModel
{
    public function rules()
    {
        return [
            [['id', 'active', 'sort_order', 'added_at', 'updated_at'], 'integer'],
            [['name', 'description'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
		/*if(Yii::$app->params['user_role'] == 'admin')
		{
			$query = ProductCategoryModel::find()->orderBy('name');
		}
		else
		{
			if(Yii::$app->user->identity->entity_type == 'vendor')
				$query = ProductCategoryModel::find()->where("added_by_id=".Yii::$app->user->identity->id)->orderBy('name');
			else
				$query = ProductCategoryModel::find()->where("id=0");
		}*/
		$query = ProductCategoryModel::find()->orderBy('name');
        
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

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
