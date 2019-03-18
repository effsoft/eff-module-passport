<?php
namespace effsoft\eff\module\passport\models;

class VerifyForm extends \yii\base\Model{
    public $code;
    public function rules(){
        return [
            ['code', 'trim'],
            ['code', 'required', 'message' => '请填写注册码！'],
            ['code', 'string', 'min' => 4, 'tooShort' => '注册码长度错误！'],
            ['code', 'string', 'max' => 4, 'tooLong' => '注册码长度错误！'],
        ];
    }
}