<?php
namespace effsoft\eff\module\passport\models;

class VerifyForm extends \yii\base\Model{

    const SCENARIO_WITH_TOKEN= 'token';
    const SCENARIO_WITH_OUT_TOKEN = 'without_token';

    public $email;
    public $code;

    public function scenarios()
    {
        return [
            self::SCENARIO_WITH_TOKEN => ['code'],
            self::SCENARIO_WITH_OUT_TOKEN => ['email', 'code'],
        ];
    }

    public function rules(){
        return [
            ['code', 'trim'],
            ['code', 'required', 'message' => '请填写注册码！'],
            ['code', 'string', 'min' => 4, 'tooShort' => '注册码长度错误！'],
            ['code', 'string', 'max' => 4, 'tooLong' => '注册码长度错误！'],

            ['email', 'trim', 'on' => self::SCENARIO_WITH_OUT_TOKEN],
            ['email', 'required', 'on' => self::SCENARIO_WITH_OUT_TOKEN],
            ['email', 'email', 'on' => self::SCENARIO_WITH_OUT_TOKEN],
        ];
    }
}