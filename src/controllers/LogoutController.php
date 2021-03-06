<?php

namespace effsoft\eff\module\passport\controllers;

use effsoft\eff\EffController;
use yii\filters\AccessControl;
use yii\helpers\Url;

class LogoutController extends EffController{

    function actionIndex(){
        \Yii::$app->user->logout(true);
        return \Yii::$app->response->redirect(Url::to(['/passport/login']));
    }
}