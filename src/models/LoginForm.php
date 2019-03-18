<?php
namespace effsoft\eff\module\passport\models;

class LoginForm extends \yii\base\Model{
    public $email;
    public $password;
    public $remember = true;
    public function rules(){
        return [
            [['email', 'password'], 'required'],
            ['email', 'required', 'message'=>'请填写您的邮件地址！'],
            ['email', 'email'],
            ['email', 'string', 'max'=>50, 'tooLong' => '长度错误！'],
            ['password', 'required', 'message'=>'请填写密码！'],
            ['password', 'string', 'min' => 6, 'tooShort' => '长度错误！'],
            ['password', 'string', 'max' => 20, 'tooLong' => '长度错误！'],
            ['remember', 'boolean'],
        ];
    }
}