<?php

namespace multebox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\History as HistoryModel;

/**
 * History represents the model behind the search form about `\multebox\models\History`.
 */
class History extends HistoryModel
{
    public function rules()
    {
        return [
            [['id', 'entity_id', 'added_at', 'updated_at'], 'integer'],
            [['notes', 'user_id', 'entity_type'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = HistoryModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'entity_id' => $this->entity_id,
            'added_at' => $this->added_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'entity_type', $this->entity_type]);

        return $dataProvider;
    }
	public function searchSessionActivities($params){

		date_default_timezone_set(Yii::$app->params['TIME_ZONE']);

		$start =$_GET['start'];

		$end =$_GET['end'] == '0'?time():$_GET['end'];
		
		$session_id=$_GET['session_id'];

		//var_dump($start);

		//var_dump($end);

		$query = HistoryModel::find()->where("user_id =$_GET[id] and added_at >='$start' and added_at <='$end' and session_id='$session_id'")->orderBy('id DESC');

		

		$dataProvider = new ActiveDataProvider ( [ 

				'query' => $query 

		] );

		

		if (! ($this->load ( $params ) && $this->validate ()))

		{

			return $dataProvider;

		}

		

		return $dataProvider;

		

	}

	public static function getUserActivities($id){

		return HistoryModel::find()->where("user_id=$id")->asArray()->orderBy('added_at desc')->all();

	}
}
