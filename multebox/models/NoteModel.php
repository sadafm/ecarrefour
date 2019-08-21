<?php

namespace multebox\models;
use multebox\models\Note;
use Yii;
use yii\filters\VerbFilter;
use yii\db\Query;
class NoteModel extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '';
    }
    public static  function noteInsert($entity_id, $entity_type) {
		$addNotes= new Note();
		$addNotes->entity_id=$entity_id;
		$addNotes->entity_type=$entity_type;
		$addNotes->notes=$_REQUEST['notes_ck'];
		$addNotes->user_id=Yii::$app->user->identity->id;
		$addNotes->added_at=time();
		$addNotes->save();
		$nid=$addNotes->id;
		return $nid;
	}
	public static  function noteEdit(){
		$editNote= Note::find()->where(['id' =>$_REQUEST['note_id']])->one();
		$editNote->notes=$_REQUEST['notes_edit_ck'];
		$editNote->updated_at=time();
		$editNote->update();
	}

	public static  function note_Insert($entity_id, $entity_type,$description) {
		//date_default_timezone_set('Asia/Calcutta');
		$addNotes= new Note();
		$addNotes->entity_id=$entity_id;
		$addNotes->entity_type=$entity_type;
		$addNotes->notes=$description;
		$addNotes->user_id=Yii::$app->user->identity->id;
		$addNotes->added_at=time();
		$addNotes->save();
		$nid=$addNotes->id;
		return $nid;
	}
	
}
