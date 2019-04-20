<?php
namespace effsoft\eff\module\passport\models;

class LoginForm extends \yii\base\Model{
    public $email;
    public $password;
    public $remember = true;
    public function rules(){
        return [
            [['email', 'password'], 'required'],
            ['email', 'required', 'message'=>'Email address required!'],
            ['email', 'email'],
            ['email', 'trim'],
            ['email', 'string', 'max'=>50, 'tooLong' => 'Field length too long, maxlength = 50'],
            ['password', 'required', 'message'=>'Password required!'],
            ['password', 'string', 'min' => 6, 'tooShort' => 'Field length too short, minlength = 6'],
            ['password', 'string', 'max' => 20, 'tooLong' => 'Field length too long, maxlength = 20'],
            ['remember', 'boolean'],
        ];
    }
}