<?php
namespace multebox\models;

use Yii;
use yii\base\Model;
use multebox\models\search\UserType as UserTypeSearch;

/**
 * Signup form
 */
class SignupForm extends Model
{
    //public $username;
    public $email;
    public $password;
	public $firstname;
	public $lastname;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            /*['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\multebox\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],*/

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\multebox\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

			['firstname', 'required'],

			['lastname', 'required'],
        ];
    }

	public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'firstname' => Yii::t('app', 'First Name'),
            'lastname' => Yii::t('app', 'Last Name'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
		$customer = new Customer();
		$customer->customer_name = $this->email;
		$customer->customer_type_id = 2; // Regular Customer
		$customer->added_by_id = 0; //System
		$customer->active = 1;
		$customer->added_at = time();

		if($customer->save())
		{
			$user = new User();
			$user->username = $this->email;
			$user->email = $this->email;
			$user->first_name = $this->firstname;
			$user->last_name = $this->lastname;
			$user->user_type_id = UserTypeSearch::getCompanyUserType('Customer')->id;;
			$user->active = 1;
			$user->entity_type = 'customer';
			$user->entity_id = $customer->id;
			$user->setPassword($this->password);
			$user->generateAuthKey();

			return $user->save() ? $user : null;
		}
		else
			return null;
    }
}
