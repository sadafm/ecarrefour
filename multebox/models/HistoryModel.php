<?php

namespace multebox\models;
use multebox\models\History;
use Yii;
use yii\filters\VerbFilter;
use yii\db\Query;
class HistoryModel extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '';
    }
    public static  function historyInsert($entity_type,$entity_id,$notes){
		date_default_timezone_set(Yii::$app->params['TIME_ZONE']);
		$addHistory = new History;
		$addHistory->entity_id=$entity_id;
		$addHistory->entity_type=$entity_type;
		$addHistory->notes=$notes;
		$addHistory->user_id=Yii::$app->user->identity->id;
		$addHistory->added_at=strtotime(date('Y-m-d H:i:s'));
		$addHistory->session_id=session_id();
		$addHistory->save();
		if($addHistory->errors){
		var_dump($addHistory->errors);
		exit();
		}
	}
}
