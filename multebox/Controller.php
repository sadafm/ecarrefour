<?php

namespace multebox;

use Yii;
use yii\helpers\Url;

/**
 * Controller for Mult-e-cart system.
 */
class Controller extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
		parent::beforeAction($action);

		/** Added by TechRaft Solutions for language/timezone change at runtime **/
		Yii::$app->language = isset($_SESSION['CONVERTED_SYSTEM_LANGUAGE'])?$_SESSION['CONVERTED_SYSTEM_LANGUAGE']:Yii::$app->params['LOCALE'];
		Yii::$app->timezone = Yii::$app->params['TIME_ZONE'];

		$common = Yii::getAlias('@multebox');
		$file = $common."/config/loadlic.dat";

		if(!is_file($file))
		{
			return $this->redirect(['/multeobjects/default/register']);
		}
		else
		{
			$lic_data = unserialize(base64_decode((file_get_contents($file))));

			//if (isset($lic_data['purchase_code']) && isset($lic_data['domain']) && $lic_data['domain'] == $_SERVER['HTTP_HOST'])
			if (isset($lic_data['purchase_code']) && isset($lic_data['domain']))
			{
				return true;
			}
			else
			{
				//unlink($file);
				//return $this->redirect(['/multeobjects/default/register']);
                                return true;
			}
		}
    }
}
