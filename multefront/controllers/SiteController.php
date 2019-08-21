<?php
namespace multefront\controllers;

use Yii;
use yii\helpers\Json;
use multebox\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use multebox\models\LoginForm;
use multebox\models\Cart;
use multebox\models\Currency;
use multebox\models\Vendor;
use multebox\models\ImageUpload;
use multebox\models\SendEmail;
use yii\base\UserException;
use multebox\models\SignupForm;
use multefront\models\PasswordResetRequestForm;
use multefront\models\ResetPasswordForm;
use multebox\models\AddressModel;
use multebox\models\ContactModel;
use multebox\models\Address;
use multebox\models\Contact;
use multebox\models\AuthAssignment;
use multebox\models\User;
use multebox\models\Wishlist;
use multebox\models\Comparison;
use multebox\models\Newsletter;
use multebox\models\search\MulteModel;
use multebox\models\search\UserType as UserTypeSearch;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'signup', 'vendor-signup', 'request-password-reset'],
                        'allow' => true,
						'roles' => ['?'],
                    ],
					[
                        'actions' => ['error', 'index', 'about', 'delivery', 'privacy', 'returns', 'tnc', 'faq', 'contact', 'convert-system-currency', 'convert-system-language', 'news-signup', 'add-to-wishlist', 'compare', 'add-to-comparelist', 'delete-compare', 'ajax-unset-news-popup'],
                        'allow' => true,
						'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['logout', 'wishlist', 'delete-wishlist'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

	public function actionAjaxUnsetNewsPopup()
	{
		if(!isset($_SESSION['newspopup']))
			$_SESSION['newspopup'] = '1';
		else
			unset($_SESSION['newspopup']);
	}

	public function actionWishlist()
	{
		if (Yii::$app->user->isGuest) {
			Yii::$app->session->setFlash('info', Yii::t('app', 'Please login to view your wishlist!'));
            return $this->redirect(['/site/login']);
        }

		return $this->render('wishlist');
	}

	public function actionDeleteWishlist()
	{
		if (Yii::$app->user->isGuest) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not authorised to perform this action!'));
            return $this->redirect(['/site/login']);
        }

		$model = Wishlist::findOne($_REQUEST['id']);

		if($model->customer_id != Yii::$app->user->identity->entity_id)
		{
			throw new UserException(Yii::t('app', 'You are not authorised to perform this action!'));
		}
		else
		{
			$model->delete();
		}

		return $this->redirect(['/site/wishlist']);
	}

	public function actionAddToWishlist()
	{
		if (Yii::$app->user->isGuest) {
			Yii::$app->session->setFlash('info', Yii::t('app', 'Please login to add item to wishlist!'));
            //return $this->redirect(['/site/login']);
			return -1;
        }

		$wishlist = new Wishlist();

		$wishlist->inventory_id = $_REQUEST['id'];
		$wishlist->customer_id = Yii::$app->user->identity->entity_id;

		if($wishlist->save())
		{
			//Yii::$app->session->setFlash('success', Yii::t('app', 'Item successfully added to your wishlist!'));
			return MulteModel::getCountWishlist();
		}
		else
		{
			//Yii::$app->session->setFlash('error', Yii::t('app', 'There was a problem trying to add item to your wishlist: '.$wishlist->errors['inventory_id'][0]));
			//Yii::$app->session->setFlash('info', Yii::t('app', 'Item is already in your wishlist!'));
			return -2;
		}

		//return $this->redirect(['/site/index']);
	}

	public function actionCompare()
	{
		return $this->render('compare');
	}

	public function actionDeleteCompare()
	{
		if(Yii::$app->user->isGuest)
		{
			$comparelist = Comparison::findOne(['session_id' => session_id()]);
		}
		else
		{
			$comparelist = Comparison::findOne(['customer_id' => Yii::$app->user->identity->entity_id]);
		}

		$inventory_list = Json::decode($comparelist->inventory_list);
		$tmp = [];

		foreach ($inventory_list as $list)
		{
			if(intval($list) == $_REQUEST['id'])
				continue;
			else
				array_push($tmp, $list);
		}

		$comparelist->inventory_list = Json::encode($tmp);
		$comparelist->count = $tmp?count($tmp):0;
		
		if ($comparelist->count == 0)
		{
			$comparelist->delete();
		}
		else
		{
			$comparelist->save();
		}

		return $this->redirect(['/site/compare']);
	}

	public function actionAddToComparelist()
	{
		if(Yii::$app->user->isGuest)
		{
			$comparelist = Comparison::findOne(['session_id' => session_id()]);
		}
		else
		{
			$comparelist = Comparison::findOne(['customer_id' => Yii::$app->user->identity->entity_id]);
		}

		if(!$comparelist)
		{
			$comparelist = new Comparison();

			$comparelist->session_id = session_id();

			if(!Yii::$app->user->isGuest)
				$comparelist->customer_id = Yii::$app->user->identity->entity_id;

			$comparelist->count = 1;
				
			$tmp = [$_REQUEST['id']];
			$comparelist->inventory_list = Json::encode($tmp);

			$comparelist->save();

			//Yii::$app->session->setFlash('success', Yii::t('app', 'Item successfully added to your compare list!'));
			return $comparelist->count;
		}
		else
		{
			if ($comparelist->count == 4)
			{
				//Yii::$app->session->setFlash('error', Yii::t('app', 'You already have maximum number of items added to your compare list!'));
				return -2;
			}
			else
			{
				$tmp = Json::decode($comparelist->inventory_list);

				if(in_array($_REQUEST['id'], $tmp))
				{
					//Yii::$app->session->setFlash('error', Yii::t('app', 'Item already added to your compare list!'));
					return -1;
				}
				else
				{
					array_push($tmp, $_REQUEST['id']);
					$comparelist->inventory_list = Json::encode($tmp);

					$comparelist->count++;

					$comparelist->save();

					//Yii::$app->session->setFlash('success', Yii::t('app', 'Item successfully added to your compare list!'));
					return $comparelist->count;
				}
			}
		}

		//return $this->redirect(['/site/index']);
		return 0;
	}

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
		//print_r(Yii::$app->user->identity->id);exit;
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

		$old_session_id = session_id();
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
			
			if (Yii::$app->user->identity->entity_type != 'customer')
			{
				Yii::$app->user->logout();
				throw new UserException('You are not an authenticated user of frontend system!');
			}
			else
			{
				$old_cart = Cart::find()->where("session_id = '".$old_session_id."'")->all();

				foreach($old_cart as $cart_item)
				{
					$currentcart = Cart::find()->where("user_id = ".Yii::$app->user->identity->id." and inventory_id = ".$cart_item->inventory_id)->one();

					if($currentcart)
					{
						$currentcart->total_items += $cart_item->total_items;
						$currentcart->updated_at = time();
						$currentcart->save();
						Cart::findOne($cart_item->id)->delete();
					}
					else
					{
						$cart_item->session_id = session_id();
						$cart_item->user_id = Yii::$app->user->identity->id;
						$cart_item->updated_at = time();
						$cart_item->save();
					}
				}
				
				Comparison::deleteAll(['=', 'customer_id', Yii::$app->user->identity->entity_id]);
				Comparison::updateAll(['customer_id' => Yii::$app->user->identity->entity_id], ['=', 'session_id', $old_session_id]);

	            return $this->goBack();
			}
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

	public function actionSignup()
    {
		$password = $_REQUEST['SignupForm']['password'];

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) 
		{
            if ($user = $model->signup()) 
			{
				//MulteModel::saveFileToServer('nophoto.jpg', $user->id.'.png', Yii::$app->params['web_folder']."/users");
				SendEmail::sendNewUserEmail($user->email,$user->first_name." ".$user->last_name, $user->username, $password);
                if (Yii::$app->getUser()->login($user)) 
				{
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

	public function actionVendorSignup()
    {
        $model = new Vendor;
		$img = new ImageUpload();
		$emailObj = new SendEmail;
		
		$connection = Yii::$app->db;

		try
		{
			$transaction = $connection->beginTransaction();

			if ($model->load(Yii::$app->request->post()) && $model->save()) 
			{
				$address_id = AddressModel::addressInsertWithCity($model->id,'vendor');
				
				$model->added_at = strtotime(date('Y-m-d H:i:s'));
				$model->update();

				//Vendor Add Contact
				$contact_id = ContactModel::contactInsert($model->id,'vendor', $address_id, true); //primary
				$contact = Contact::findOne($contact_id);

				//Create Vendor User to Login to Backend
				if(User::find()->where("email='".$contact->email."'")->count() > 0)
				{
					throw new \Exception($contact->email.": ".Yii::t('app', 'User can not be Created Email Already Exists!'));
				}
				else
				{
					$userModel = new User;
					$userModel->first_name = $contact->first_name;
					$userModel->last_name = $contact->last_name;
					$userModel->email = $contact->email;
					$userModel->username = $contact->email;
					$userModel->active = 0;
					$userModel->user_type_id = UserTypeSearch::getCompanyUserType('Vendor')->id;
					$userModel->entity_id = $model->id;
					$userModel->entity_type = 'vendor';
					$userModel->added_at = time();
					$new_password = Yii::$app->security->generateRandomString (8);
					$userModel->password_hash=Yii::$app->security->generatePasswordHash($new_password);
					$userModel->save();
					/*if(count($userModel->errors) >0){
						var_dump($userModel->errors);
					}*/
					$authModel = new AuthAssignment;
					$authModel->item_name = 'Vendor';
					$authModel->user_id = $userModel->id;
					$authModel->save();
					/*$img->loadImage('../../multeback/web/users/nophoto.jpg')->saveImage("../../multeback/web/users/".$userModel->id.".png");
					$img->loadImage('../../multeback/web/users/nophoto.jpg')->resize(30, 30)->saveImage("../../multeback/web/users/user_".$userModel->id.".png");*/
					if(!MulteModel::saveFileToServer('nophoto.jpg', $userModel->id.'.png', Yii::$app->params['web_folder']."/users"))
					{
						throw new \Exception (Yii::$app->session->getFlash('error'));
					}
					if(!MulteModel::saveFileToServer('nophoto.jpg', $model->id.'.png', Yii::$app->params['web_folder']."/vendors"))
					{
						throw new \Exception (Yii::$app->session->getFlash('error'));
					}
					SendEmail::sendNewVendorEmailFromFrontend($userModel->email,$userModel->first_name." ".$userModel->last_name, $userModel->username,$new_password);
				}
				
				Yii::$app->session->setFlash('success', Yii::t('app', 'Registration completed - awaiting admin approval!'));

				$transaction->commit();
				return $this->redirect(['/site/index']);
			} 
			else 
			{
				
				return $this->render('vendor-signup', [
					'model' => $model,
				]);
			}
		}
		catch (\Exception $e)
		{
			$transaction->rollback();
			Yii::$app->session->setFlash('error', $e->getMessage());
			$model = new Vendor;
			return $this->render('vendor-signup', [
					'model' => $model,
				]);
		}
    }

	/**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Check your email for further instructions.'));

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry, we are unable to reset password for the provided email address.'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

	/**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'New password saved.'));

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

	public function actionConvertSystemCurrency()
	{
		$currency = $_REQUEST['currency'];

		$cur_row = Currency::findOne(['currency_code' => $currency]);

		if($cur_row)
		{
			$_SESSION['CONVERTED_CURRENCY_CODE'] = $cur_row->currency_code;
			$_SESSION['CONVERTED_CURRENCY_SYMBOL'] = $cur_row->currency_symbol;
		}

		return $this->redirect(['/site/index']);
	}

	public function actionConvertSystemLanguage()
	{
		$rtl_array = ['ar-AR', 'fa-FA', 'ha-HA', 'iw-IW', 'ku-KU', 'ps-PS', 'ur-UR', 'yi-YI'];
		$_SESSION['CONVERTED_SYSTEM_LANGUAGE'] = $_REQUEST['language'];

		if(in_array($_REQUEST['language'], $rtl_array))
		{
			$_SESSION['RTL_THEME'] = 'Yes';
		}
		else
		{
			$_SESSION['RTL_THEME'] = 'No';
		}

		return $this->redirect(['/site/index']);
	}

	public function actionAbout()
	{
		return $this->render('about');
	}

	public function actionDelivery()
	{
		return $this->render('delivery');
	}

	public function actionPrivacy()
	{
		return $this->render('privacy');
	}

	public function actionReturns()
	{
		return $this->render('returns');
	}

	public function actionTnc()
	{
		return $this->render('tnc');
	}

	public function actionFaq()
	{
		return $this->render('faq');
	}

	public function actionContact()
	{
		if($_REQUEST['sendenquiry'] == '1')
		{
			SendEmail::sendEnquiryEmail($_REQUEST['name'], $_REQUEST['email'], $_REQUEST['phone'], $_REQUEST['message']);
			Yii::$app->session->setFlash('success', Yii::t('app', 'Enquiry successfully submitted!'));
		}
		return $this->render('contact');
	}

	public function actionNewsSignup()
	{
		$email = $_REQUEST['newsemail'];

		$newsletter = new Newsletter();

		$newsletter->email = $email;

		if($newsletter->save())
		{
			Yii::$app->session->setFlash('success', Yii::t('app', 'You have successfully signed-up for our newsletter!'));
		}
		else
		{
			Yii::$app->session->setFlash('error', Yii::t('app', 'There was a problem trying to signup for newsletter: '.$newsletter->errors['email'][0]));
		}
		return $this->redirect(['/site/index']);
	}
}
