<?php

namespace multebox\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "tbl_registration".
 *
 * @property int $id
 * @property string $purchase_code
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $phone
 * @property string $domain
 * @property int $active
 * @property int $added_at
 * @property int $updated_at
 */
class Registration extends Model
{
	public $purchase_code;
	public $firstname;
	public $lastname;
	public $email;
	public $phone;
	public $domain;
	public $active;
	public $added_at;
	public $updated_at;
	public $buyer;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'firstname', 'lastname', 'email', 'phone'], 'required'],
            [['active', 'added_at', 'updated_at'], 'integer'],
            [[ 'firstname', 'lastname', 'email', 'phone', 'domain', 'buyer'], 'string', 'max' => 255],
			[['email'], 'email'],
			['phone', 'match', 'pattern' => '/^[+\(\)\- 0-9]+$/', 'message' => Yii::t('app', 'Only numeric characters and +, -, (, ) symbols are allowed')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
//            'purchase_code' => Yii::t('app', 'CodeCanyon Purchase Code'),
            'firstname' => Yii::t('app', 'Firstname'),
            'lastname' => Yii::t('app', 'Lastname'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
            'domain' => Yii::t('app', 'Domain'),
			'buyer' => Yii::t('app', 'Buyer'),
            'active' => Yii::t('app', 'Active'),
            'added_at' => Yii::t('app', 'Added At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
