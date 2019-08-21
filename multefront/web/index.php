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
setMulteSessionParams();

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

		Yii::$app->params['zero_decimal_currencies'] = ['MGA','BIF','CLP','PYG','DJF','RWF','GNF','JPY','VND','VUV','XAF','KMF','KRW','XOF','XPF'];
}

function setMulteSessionParams()
{
	$_SESSION['SHOW_DEBUG_TOOLBAR'] = 'No';
	$_SESSION['LOCALE']='en-US';
}

$application->run();