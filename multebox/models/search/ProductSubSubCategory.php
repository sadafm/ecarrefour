<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\ProductSubSubCategory as ProductSubSubCategoryModel;

/**
 * ProductSubSubCategory represents the model behind the search form about `multebox\models\ProductSubSubCategory`.
 */
class ProductSubSubCategory extends ProductSubSubCategoryModel
{
    public function rules()
    {
        return [
            [['id', 'parent_id', 'active', 'sort_order', 'tax_ind', 'tax_id', 'return_window', 'added_at', 'updated_at'], 'integer'],
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
			$query = ProductSubSubCategoryModel::find()->orderBy('name');
		}
		else
		{
			if(Yii::$app->user->identity->entity_type == 'vendor')
				$query = ProductSubSubCategoryModel::find()->where("added_by_id=".Yii::$app->user->identity->id)->orderBy('name');
			else
				$query = ProductSubSubCategoryModel::find()->where("id=0");
		}*/
		$query = ProductSubSubCategoryModel::find()->orderBy('name');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'active' => $this->active,
			'tax_ind' => $this->tax_ind,
            'tax_id' => $this->tax_id,
            'sort_order' => $this->sort_order,
			'return_window' => $this->return_window,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
