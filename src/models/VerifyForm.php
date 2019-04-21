<?php
namespace effsoft\eff\module\passport\models;

class VerifyForm extends \yii\base\Model{

    const SCENARIO_REIGSTER= 'register';
    const SCENARIO_PASSWORD_FORGOTTEN = 'password_forgotten';

    public $code;

    public $password;
    public $confirm_password;

    public function scenarios()
    {
        return [
            self::SCENARIO_REIGSTER => ['code'],
            self::SCENARIO_PASSWORD_FORGOTTEN => ['code', 'password', 'confirm_password'],
        ];
    }

    public function rules(){
        return [
            ['code', 'trim'],
            ['code', 'required', 'message' => 'Verify code requried!'],
            ['code', 'string', 'min' => 4, 'tooShort' => "Please check verify code's length!"],
            ['code', 'string', 'max' => 4, 'tooLong' => "Please check verify code's length!"],

            ['password', 'required', 'on' => self::SCENARIO_PASSWORD_FORGOTTEN],
            ['password', 'string', 'min' => 6, 'on' => self::SCENARIO_PASSWORD_FORGOTTEN],
            ['password', 'string', 'max' => 20, 'on' => self::SCENARIO_PASSWORD_FORGOTTEN],
            ['confirm_password', 'required', 'on' => self::SCENARIO_PASSWORD_FORGOTTEN],
            ['confirm_password', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false, 'on' => self::SCENARIO_PASSWORD_FORGOTTEN],

        ];
    }
}