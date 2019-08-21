<?php

namespace multebox\models;
use multebox\models\File;
use multebox\models\search\MulteModel;
use Yii;
use yii\filters\VerbFilter;
use yii\db\Query;
class FileModel extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '';
    }

	public static function fileInsert($entity_id, $entity_type, $resize_image = false) {
		/* Check for invalid file extensions - if found than dont save the information */
		$file_extension = pathinfo($_FILES['attach']['name'], PATHINFO_EXTENSION);
		/*if (in_array(strtoupper($file_extension), Yii::$app->params['invalid_ext']))
		{
			unlink($_FILES['attach']['tmp_name']);
			return 0;
		}*/

		foreach(Yii::$app->params['invalid_ext'] as $blocked)
		{
			if(strstr(strtoupper($file_extension), $blocked))
			{
				unlink($_FILES['attach']['tmp_name']);
				return 0;
			}
		}

		if (intval(Yii::$app->params['FILE_SIZE']) > 0 && filesize($_FILES['attach']['tmp_name']) > intval(Yii::$app->params['FILE_SIZE'])*1024*1024)
		{
			unlink($_FILES['attach']['tmp_name']);
			return -1;
		}

		$filetitle=$_REQUEST['filetitle']?$_REQUEST['filetitle']:$_FILES['attach']['name'];
		if($_FILES['attach']['tmp_name'])
		{
			$connection = Yii::$app->db;
			$transaction = $connection->beginTransaction();

			$addFile= new File();
			$addFile->entity_id=$entity_id;
			$addFile->entity_type=$entity_type;
			$addFile->file_title=$filetitle;
			$addFile->file_name=$_FILES['attach']['name'];
			$addFile->file_path='multeback/web/attachments';
			$addFile->file_type=$_FILES['attach']['type'];
			$addFile->added_by_user_id =Yii::$app->user->identity->id;
			$addFile->added_at=time();
			$addFile->save();
			$aid=$addFile->id;
			//move_uploaded_file($_FILES['attach']['tmp_name'],"attachments/$aid.".$file_extension);
			if(!MulteModel::saveFileToServer($_FILES['attach']['tmp_name'], $aid.".".$file_extension, Yii::$app->params['web_folder']))
			{
				$transaction->rollback();
			}
			else
			{
				try
				{
					if($resize_image)
					{
						MulteModel::resizeImage(Yii::$app->params['web_folder'].'/'.$aid.".".$file_extension, Yii::$app->params['web_folder'].'/'.$aid."_small.".$file_extension);
						$addFile->new_file_name=$aid."_small.".$file_extension;
					}
					else
					{
						$addFile->new_file_name=$aid.".".$file_extension;
					}

					$addFile->update();
					$transaction->commit();
				}
				catch (\Exception $e)
				{
					unlink(Yii::$app->params['web_folder'].'/'.$aid.".".$file_extension);
					Yii::$app->session->getFlash('success'); // clear success message
					Yii::$app->session->setFlash('error', Yii::t('app', 'File Upload Failed!').$e->getMessage()); // set error message
					$transaction->rollback();
				}
			}
		}

		return $aid;
	}
	
	public static function fileEdit()
	{
		$filetitle=$_REQUEST['filetitle']?$_REQUEST['filetitle']:$_FILES['attach']['name'];
		
		if($_FILES['attach']['name'])
		{
			$connection = Yii::$app->db;
			$transaction = $connection->beginTransaction();

			$addFile = File::find()->where(['id' =>$_REQUEST['att_id']])->one();
			$addFile->file_title=$filetitle;

			$addFile->file_name=$_FILES['attach']['name'];
			$addFile->file_type=$_FILES['attach']['type'];

			$addFile->updated_at=time();
			$addFile->update();

			$aid=$_REQUEST['att_id'];
			
			//move_uploaded_file($_FILES['attach']['tmp_name'],"attachments/$aid".strrchr($_FILES['attach']['name'], "."));
			
			if(!MulteModel::saveFileToServer($_FILES['attach']['tmp_name'], $aid.strrchr($_FILES['attach']['name'], "."), Yii::$app->params['web_folder']))
			{
				$transaction->rollback();
			}
			else
			{
				$transaction->commit();
			}
		}
		return $aid;
	}
	
	public static function getAttachmentCount($entity_type,$entity_id){
		return File::find()->where("entity_type='$entity_type' and entity_id='$entity_id'")->count();
	}

	public static  function create_zip($files = array(),$destination = '',$overwrite = false) {
			//if the zip file already exists and overwrite is false, return false
			if(file_exists($destination) && !$overwrite) { return false; }
			//vars
			$valid_files = array();
			//if files were passed in...
			if(is_array($files)) {
				//cycle through each file
				foreach($files as $file) {
					//make sure the file exists
					if(file_exists($file)) {
						$valid_files[] = $file;
					}
				}
			}
			//if we have good files...
			if($valid_files) {
				//create the archive
				$zip = new \ZipArchive();
				if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
					return false;
				}
				//add the files
				foreach($valid_files as $file) {
					$zip->addFile($file,$file);
				}
				//debug
				//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
				
				//close the zip -- done!
				$zip->close();
				
				//check to make sure the file exists
				return file_exists($destination);
			}
			else
			{
				return false;
			}
		}

	public static function getAttachmentFiles($entity_type,$entity_id){
		return 	File::find()->where("entity_type='$entity_type' and entity_id='$entity_id'")->asArray()->all();
	}
}
