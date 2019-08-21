<?php

namespace multebox\models;
use yii\data\ActiveDataProvider;

use Yii;

/**
 * This is the model class for table "tbl_license_key_code".
 *
 * @property int $id
 * @property int $inventory_id
 * @property string $license_key_code
 * @property int $used
 */
class LicenseKeyCode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_license_key_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inventory_id', 'license_key_code', 'used'], 'required'],
            [['inventory_id', 'used', 'sub_order_id'], 'integer'],
            [['license_key_code'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'inventory_id' => Yii::t('app', 'Inventory ID'),
			'sub_order_id' => Yii::t('app', 'SubOrder ID'),
            'license_key_code' => Yii::t('app', 'License Key Code'),
            'used' => Yii::t('app', 'Used'),
        ];
    }

    public function getCodesForInventory($params, $inventory_id)
    {
        $query = LicenseKeyCode::find()->where("inventory_id=".$inventory_id);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
    }
}
