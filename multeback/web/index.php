<?php
if (floatval(PHP_VERSION) < 7.0)
{
	die('PHP version must be 7.0 or above');
}
include_once("env.php");

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../multebox/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../multebox/config/main.php'),
    require(__DIR__ . '/../../multebox/config/main-local.php'),
    require(__DIR__ . '/../../multebox/config/datecontrol-module.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);

$server_details = require(__DIR__ . '/../../multebox/config/server-details.php');

$application = new yii\web\Application($config);

Yii::$app->params['frontend_url'] = $server_details['frontend_url'];
Yii::$app->params['backend_url'] = $server_details['backend_url'];

if($server_details['aws']['enabled'])
{
	Yii::$app->params['aws'] = true;
	Yii::$app->params['aws_s3_bucket'] = $server_details['aws']['s3_bucket'];
	Yii::$app->params['aws_region'] = $server_details['aws']['region'];
	Yii::$app->params['aws_user_key'] = $server_details['aws']['user_key'];
	Yii::$app->params['aws_user_secret'] = $server_details['aws']['user_secret'];
	Yii::$app->params['web_folder'] = $server_details['aws']['web_folder'];
	Yii::$app->params['web_url'] = 'https://'.Yii::$app->params['aws_s3_bucket'].'.s3.'.Yii::$app->params['aws_region'].'.amazonaws.com/'.Yii::$app->params['web_folder'];
}
else
{
	Yii::$app->params['aws'] = false;
	if($server_details['ftp']['enabled'])
	{
		Yii::$app->params['ftp'] = true;
		Yii::$app->params['ftp_protocol'] = $server_details['ftp_server_details']['ftp_protocol'];
		Yii::$app->params['ftp_url'] = $server_details['ftp_server_details']['ftp_url'];
		Yii::$app->params['ftp_user'] = $server_details['ftp_server_details']['ftp_user'];
		Yii::$app->params['ftp_password'] = $server_details['ftp_server_details']['ftp_password'];
		Yii::$app->params['ftp_port'] = $server_details['ftp_server_details']['ftp_port'];
		Yii::$app->params['web_folder'] = $server_details['web_folder_details']['web_folder'];
		Yii::$app->params['web_url'] = $server_details['web_folder_details']['web_url'];
	}
	else
	{
		Yii::$app->params['ftp'] = false;
		Yii::$app->params['web_folder'] = 'attachments';
		Yii::$app->params['web_url'] = $server_details['backend_url'].'/'.Yii::$app->params['web_folder'];
	}
}

loadMulteDBConfigItems();
//loadMulteAppLic();
setMulteSessionParams();

if(!Yii::$app->user->isGuest)
{
	multebox\models\SessionVerification::checkSessionDetails();
}

function loadMulteDBConfigItems()
{
		$items = multebox\models\ConfigItem::find()->asArray()->all();
		foreach ($items as $item)
        {
            if ($item['config_item_name'])
			{
				Yii::$app->params[$item['config_item_name']] = $item['config_item_value'];
				Yii::$app->params[$item['config_item_name']."_description"] = $item['config_item_description'];
			}
        }
		
		$company = multebox\models\Company::find()->asArray()->one();
		Yii::$app->params['company'] = $company;
		Yii::$app->params['address']= multebox\models\search\Address::companyAddress($company['id']);

		$role = '';

		if(isset(Yii::$app->user->identity))
		{
			$role = multebox\models\AuthAssignment::find()->where("item_name='Admin' and user_id='".Yii::$app->user->identity->id."'")->asArray()->one();
		}	

		if($role){
			Yii::$app->params['user_role']= 'admin';
		}else{
			Yii::$app->params['user_role']= 'guest';	
		}

		Yii::$app->params['invalid_ext'] = array("PH", "JS", "EXE", "VB", "CMD", "BAT", "CGI", "PERL", "PY"); //All extentions with mentioned keywords in them will be blocked
		Yii::$app->params['zero_decimal_currencies'] = ['MGA','BIF','CLP','PYG','DJF','RWF','GNF','JPY','VND','VUV','XAF','KMF','KRW','XOF','XPF'];
}

function loadMulteAppLic()
{
		//Application Licence
		$licences = multebox\models\PrdLic::find()->where("prd_lic_status=1")->asArray()->all();
		if($licences){
			$lid= array();
			foreach($licences as $licence){
				$lid[]=$licence['id'];
			}
			$ids =implode(',',$lid);
			$sql ="select * from tbl_prd_mdl_lic,tbl_prd_mdl,tbl_prd_lic where tbl_prd_mdl_lic.prd_lic_id=tbl_prd_lic.id and  tbl_prd_mdl_lic.prd_mdl_id=tbl_prd_mdl.id and tbl_prd_mdl_lic.prd_lic_id IN($ids)";
			$connection = \Yii::$app->db;
			$command=$connection->createCommand($sql);
			$modules=$command->queryAll();
			$moduleArray = array();
			foreach($modules as $module){
				$moduleArray[]=$module['mdl_name'];
			}
			Yii::$app->params['modules'] =$moduleArray;
		}
}

function setMulteSessionParams()
{
		$_SESSION['SHOW_DEBUG_TOOLBAR'] = Yii::$app->params['SHOW_DEBUG_TOOLBAR'];
		$_SESSION['IDLE_SESSION_TIMEOUT_PERIOD'] = Yii::$app->params['IDLE_SESSION_TIMEOUT_PERIOD'];
		$_SESSION['LOCALE']=Yii::$app->params['LOCALE'];
}

$application->run();