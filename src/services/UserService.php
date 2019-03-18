<?php
namespace effsoft\eff\module\passport\services;

use effsoft\eff\EffService;
use Hashids\Hashids;

class UserService extends EffService{

    public static function encodeId($uid){
        return \Yii::$container->get(Hashids::class,[
            'salt' => \Yii::$app->components['request']['cookieValidationKey'],
        ])->encodeHex($uid);
    }

    public static function decodeId($hex){
        return \Yii::$container->get(Hashids::class,[
            'salt' => \Yii::$app->components['request']['cookieValidationKey'],
        ])->decodeHex($hex);
    }
}