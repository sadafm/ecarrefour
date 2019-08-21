<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\Inventory as InventoryModel;

/**
 * Inventory represents the model behind the search form about `multebox\models\Inventory`.
 */
class Inventory extends InventoryModel
{
    public function rules()
    {
        return [
            [['id', 'product_id', 'vendor_id', 'stock', 'slab_discount_ind', 'slab_1_range', 'slab_2_range', 'slab_3_range', 'slab_4_range', 'added_by_id', 'sort_order', 'added_at', 'updated_at', 'send_as_attachment', 'special', 'featured', 'hot', 'total_sale', 'active'], 'integer'],
            [['price_type', 'product_name', 'price', 'discount_type', 'discount', 'attribute_values', 'attribute_price', 'shipping_cost', 'slab_discount_type', 'slab_1_discount', 'slab_2_discount', 'slab_3_discount', 'slab_4_discount', 'product_rating', 'vendor_rating', 'digital_file', 'digital_file_name', 'attachment_file_name', 'length', 'width', 'height', 'weight', 'warranty'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
		if(Yii::$app->params['user_role'] == 'admin')
		{
			$query = InventoryModel::find();
		}
		else
		{
			if(Yii::$app->user->identity->entity_type == 'vendor')
				$query = InventoryModel::find()->where("vendor_id=".Yii::$app->user->identity->entity_id);
			else
				$query = InventoryModel::find()->where("id=0");
		}
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'product_id' => $this->product_id,
            'vendor_id' => $this->vendor_id,
            'stock' => $this->stock,
            'slab_discount_ind' => $this->slab_discount_ind,
            'slab_1_range' => $this->slab_1_range,
            'slab_2_range' => $this->slab_2_range,
            'slab_3_range' => $this->slab_3_range,
            'slab_4_range' => $this->slab_4_range,
            'added_by_id' => $this->added_by_id,
			'special' => $this->special,
            'featured' => $this->featured,
			'hot' => $this->hot,
            'total_sale' => $this->total_sale,
			'send_as_attachment' => $this->send_as_attachment,
            'sort_order' => $this->sort_order,
			'active' => $this->active,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'price_type', $this->price_type])
            ->andFilterWhere(['like', 'price', $this->price])
			->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'discount_type', $this->discount_type])
            ->andFilterWhere(['like', 'discount', $this->discount])
            ->andFilterWhere(['like', 'attribute_values', $this->attribute_values])
            ->andFilterWhere(['like', 'attribute_price', $this->attribute_price])
            ->andFilterWhere(['like', 'shipping_cost', $this->shipping_cost])
            ->andFilterWhere(['like', 'slab_discount_type', $this->slab_discount_type])
            ->andFilterWhere(['like', 'slab_1_discount', $this->slab_1_discount])
			->andFilterWhere(['like', 'digital_file', $this->digital_file])
            ->andFilterWhere(['like', 'digital_file_name', $this->digital_file_name])
			->andFilterWhere(['like', 'attachment_file_name', $this->attachment_file_name])
            ->andFilterWhere(['like', 'slab_2_discount', $this->slab_2_discount])
            ->andFilterWhere(['like', 'slab_3_discount', $this->slab_3_discount])
            ->andFilterWhere(['like', 'slab_4_discount', $this->slab_4_discount])
			->andFilterWhere(['like', 'product_rating', $this->product_rating])
			->andFilterWhere(['like', 'vendor_rating', $this->vendor_rating])
			->andFilterWhere(['like', 'length', $this->length])
            ->andFilterWhere(['like', 'width', $this->width])
            ->andFilterWhere(['like', 'height', $this->height])
			->andFilterWhere(['like', 'weight', $this->weight])
			->andFilterWhere(['like', 'warranty', $this->warranty]);

        return $dataProvider;
    }
}
