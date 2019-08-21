<?php

namespace multebox\models;

use Yii;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use multebox\models\SessionDetails;

class SessionVerification extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
	
	public static function checkSessionDetails()
	{
		if(Yii::$app->controller->route == 'site/login')
			return;
		
		if((session_status() != PHP_SESSION_ACTIVE) || (!Yii::$app->user->identity->id))
		{
			session_reset();
			Yii::$app->user->logout();
			Yii::$app->getResponse()->redirect(['/site/login']);
		}
	}

	public static function validateSession()
	{
		if(Yii::$app->controller->route == 'site/login')
			return;

		$now = time();
		$max_session_time = Yii::$app->params['DEFAULT_SESSION_TIMEOUT_PERIOD']; //Hours
		$limit = $now - $max_session_time*60*60;
		$result = SessionDetails::updateall(['logged_out'=>time()], 'logged_in < '.$limit.' and logged_out = 0');

		if(!empty($result))
		{
			$session_details = SessionDetails::find()->where(['=', 'user_id', Yii::$app->user->identity->id])
													->andWhere(['=', 'session_id', session_id()])
													->andWhere(['=', 'logged_out', 0])
													->one();
			if(empty($session_details))
			{
				session_reset();
				Yii::$app->user->logout();
				Yii::$app->getResponse()->redirect(['/site/login']);
			}
		}
	}
}