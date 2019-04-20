<?php

namespace effsoft\eff\module\passport\models;

use MongoDB\BSON\Regex;

class RegisterForm extends \yii\base\Model
{
    public $username;
    public $email;
    public $password;
    public $repeat_password;
    public $terms_of_service;

    public function rules()
    {
        return [
            [['username', 'email', 'password', 'repeat_password'], 'trim'],
            [['username'], 'match', 'pattern' => '/^[0-9a-zA-Z\_]*$/', 'message' => '只能用数字、字母和下划线！'],
            ['username', 'required', 'message' => '请填写您的用户名！'],
            ['username', 'string', 'max' => 20, 'tooLong' => '长度错误！'],
            ['email', 'required', 'message' => '请填写您的邮件地址！'],
            ['email', 'email'],
            ['email', 'string', 'max' => 50, 'tooLong' => '长度错误！'],
            ['password', 'required', 'message' => '请填写密码！'],
            ['password', 'string', 'min' => 6, 'tooShort' => '长度错误！'],
            ['password', 'string', 'max' => 20, 'tooLong' => '长度错误！'],
            ['repeat_password', 'required', 'message' => '请再次输入密码！'],
            ['repeat_password', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false, 'message' => "两次输入的密码不一致！"],
            ['username', 'username_exists'],
            ['email', 'email_exists'],
            ['terms_of_service','required', 'message' => 'You have to accept our terms of service!'],
        ];
    }

    public function username_exists($attribute, $params)
    {
        $user = UserModel::findOne(['username' => new Regex("^".$this->$attribute."$", 'i')]);
        if (!empty($user)) {
            $this->addError($attribute, \Yii::t('app', 'Username already taken by the other people!'));
            return;
        }
    }

    public function email_exists($attribute, $params)
    {
        $user = UserModel::findOne(['email' => strtolower($this->$attribute)]);
        if (!empty($user)) {
            $this->addError($attribute, \Yii::t('app', 'Email registed for another people!'));
            return;
        }
    }
}