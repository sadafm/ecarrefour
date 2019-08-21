<?php

namespace multebox\modules\multeobjects\controllers;

use Yii;
use yii\web\Controller; // Important - don't replace it with multebox/Controller
use multebox\models\Registration;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

	public function actionRegister()
	{
		$common = Yii::getAlias('@multebox');
		$file = $common."/config/loadlic.dat";

		if(is_file($file))
		{
			$lic_data = unserialize(base64_decode((file_get_contents($file))));

			//if (isset($lic_data['purchase_code']) && isset($lic_data['domain']) && $lic_data['domain'] == $_SERVER['HTTP_HOST'])
			if (isset($lic_data['purchase_code']) && isset($lic_data['domain']))
			{
				$this->redirect(['/site/index']);
			}
			/*else
			{
				unlink($file);
			}*/
		}

		$this->layout = false;

		$model = new Registration();

		if($model->load(Yii::$app->request->post()) && $model->validate())
		{
			$postdata = array(
								'purchase_code' => '',
								'firstname' => $model->firstname,
								'lastname' => $model->lastname,
								'email' => $model->email,
								'phone' => $model->phone,
								'domain' => $_SERVER['HTTP_HOST']
							);

			$url = "http://www.techraft.in/multecart/license/check.php";

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-type: application/x-www-form-urlencoded')
			);
			$result = curl_exec($ch);
			curl_close($ch);
			$myresult = json_decode($result, true);
                        $myresult['code']='0';
			if ($myresult['code'] == '0')
			{
				$common = Yii::getAlias('@multebox');
				$file = $common."/config/loadlic.dat";

				/* success - generate license file */
				$lic_data = ['purchase_code' => $model->purchase_code, 'domain' => $_SERVER['HTTP_HOST']];
				file_put_contents($file, base64_encode(serialize($lic_data)));

				Yii::$app->session->setFlash('success', Yii::t('app', $myresult['message']));
				
				return $this->redirect(['/site/index']);
			}
			else
			{
				Yii::$app->session->setFlash('error', Yii::t('app', $myresult['message']));
			}
		}

		return $this->render('register', [
                'model' => $model,
            ]);
	}
}
