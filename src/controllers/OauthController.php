<?php

namespace effsoft\eff\module\passport\controllers;

use effsoft\eff\EffController;
use yii\authclient\clients\Google;
use yii\helpers\ArrayHelper;

class OauthController extends EffController{

    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'successCallback'],
            ],
        ];
    }

    public function successCallback($client){
        /* google
         * {
  "id": "101000536634103037016",
  "email": "flcgame@gmail.com",
  "verified_email": true,
  "name": "Bruce Lu",
  "given_name": "Bruce",
  "family_name": "Lu",
  "link": "https://plus.google.com/101000536634103037016",
  "picture": "https://lh4.googleusercontent.com/-pwiPmQxXDsg/AAAAAAAAAAI/AAAAAAAAAAA/ACHi3reVjCPTUf_lVbiMSRVauotb4qLUWg/mo/photo.jpg",
  "locale": "zh-CN"
}

         */
//        $attributes = $client->getUserAttributes();

//        $access_token = $client->fetchAccessToken(\Yii::$app->request->get('code'));

//        $social_name = $client->getId();
//        if (empty($social_name)){
//            return $this->render_error('1', 'Wrong parameter!');
//        }
//
//        if ($social_name === 'Google'){
//            //Delete when finished
//            $client = new Google();
//
//            $attributes = $client->getUserAttributes();
//            $social_id = ArrayHelper::getValue($attributes,'id');
//            $email = ArrayHelper::getValue($attributes,'email');
//            $username = ArrayHelper::getValue($attributes,'username');
//            $first_name = ArrayHelper::getValue($attributes,'first_name');
//            $last_name = ArrayHelper::getValue($attributes,'last_name');
//            $avatar = ArrayHelper::getValue($attributes,'avatar');
//            $locale = ArrayHelper::getValue($attributes,'locale');
//
//
//
//        }

    }
}