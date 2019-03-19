<?php
namespace effsoft\eff\module\passport\models;

use yii\mongodb\ActiveRecord;
use yii\web\IdentityInterface;

class UserModel extends ActiveRecord implements IdentityInterface {

    public static function collectionName()
    {
        return 'User';
    }

    public function attributes()
    {
        return ['_id', 'first_name', 'last_name', 'username', 'email',
            'password', 'access_token', 'auth_key', 'activated', 'blocked',
            'date_created',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(array('access_token' => $token));
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            if($this->isNewRecord){
                $this->date_created = time();
            }
            return true;
        }
        return false;
    }

    public function register(){
        $this->activated = false;
        $this->blocked = false;
        if($this->isNewRecord){
            $this->auth_key = \Yii::$app->getSecurity()->generateRandomString();
            return $this->insert();
        }else{
            return $this->update();
        }
    }
}