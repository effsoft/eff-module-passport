<?php

namespace effsoft\eff\module\passport\controllers;

use effsoft\eff\EffController;
use yii\filters\AccessControl;
use yii\helpers\Url;

class LogoutController extends EffController{

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    function actionIndex(){
        \Yii::$app->user->logout(true);
        return \Yii::$app->response->redirect(Url::to(['/passport/login']));
    }
}