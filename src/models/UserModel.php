<?php
namespace effsoft\eff\module\passport\models;

use effsoft\eff\EffMysqlActiveRecord;
use yii\mongodb\ActiveRecord;
use yii\web\IdentityInterface;

class UserModel extends EffMysqlActiveRecord implements IdentityInterface {

//    public static function collectionName()
//    {
//        return 'User';
//    }

    public static function tableName()
    {
        return 'user';
    }

    public function attributes()
    {
        return ['id', 'username', 'email', 'profile',
            'password', 'access_token', 'auth_key', 'activated', 'blocked',
            'date_created', 'date_updated', 'role',
        ];
    }

    public static function findIdentity($id)
    {
//        if ($id === (array) $id && isset($id['$oid'])){
//            $id = $id['$oid'];
//        }
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(array('access_token' => $token));
    }

    public function getId()
    {
//        return ($this->_id);
        return $this->id;
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
                $this->date_created = $this->date_updated = time();
            }
            return true;
        }
        return false;
    }

    public function register(){
        $this->activated = false;
        $this->blocked = false;
        $this->auth_key = \Yii::$app->getSecurity()->generateRandomString();
        return $this->save();
    }
}