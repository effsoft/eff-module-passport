<?php

namespace effsoft\eff\module\passport\models;

class RegisterForm extends \yii\base\Model{
    public $first_name;
    public $last_name;
    public $username;
    public $email;
    public $password;
    public $repeat_password;
    public $captcha;

    public function rules()
    {
        return [
            [['first_name','last_name','username','email','password','repeat_password'],'trim'],
            [['first_name','last_name','username'], 'match', 'pattern' => '/^[0-9a-zA-Z\_]*$/', 'message' => '只能用数字、字母和下划线！'],
            ['first_name', 'required', 'message'=>'请填写您的名字！'],
            ['first_name', 'string', 'max'=>20, 'tooLong' => '长度错误！'],
            ['last_name', 'required', 'message'=>'请填写您的姓！'],
            ['last_name', 'string', 'max'=>20, 'tooLong' => '长度错误！'],
            ['username', 'required', 'message'=>'请填写您的用户名！'],
            ['username', 'string', 'max'=>20, 'tooLong' => '长度错误！'],
            ['email', 'required', 'message'=>'请填写您的邮件地址！'],
            ['email', 'email'],
            ['email', 'string', 'max'=>50, 'tooLong' => '长度错误！'],
            ['password', 'required', 'message'=>'请填写密码！'],
            ['password', 'string', 'min' => 6, 'tooShort' => '长度错误！'],
            ['password', 'string', 'max' => 20, 'tooLong' => '长度错误！'],
            ['repeat_password', 'required', 'message'=>'请再次输入密码！'],
            ['repeat_password', 'compare', 'compareAttribute'=>'password', 'skipOnEmpty' => false, 'message'=>"两次输入的密码不一致！" ],
            ['captcha', 'required', 'message'=>'请填写验证码！'],
            ['captcha', 'captcha', 'captchaAction' => 'passport/register/captcha', 'message' => '验证码输入错误！'],
        ];
    }
}