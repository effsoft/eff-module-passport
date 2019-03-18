<?php

namespace effsoft\eff\module\passport\controllers;

use effsoft\eff\EffController;
use effsoft\eff\module\passport\models\LoginForm;
use effsoft\eff\module\passport\models\User;

class LoginController extends EffController{

    public function actions() {
        return [
            'captcha' => [
                'class' => 'effsoft\eff\actions\CaptchaAction',
                'maxLength' => 6,
                'minLength' => 6,
            ],
        ];
    }

    function actionIndex(){

        if(!\Yii::$app->user->isGuest){
            return $this->goHome();
        }
        $login_form = new LoginForm();

        if (\Yii::$app->request->isPost){
            $login_form->load(\Yii::$app->request->post());
            if(!$login_form->validate()){
                return $this->render('index.php',[
                    'login_form' => $login_form,
                ]);
            }
            $user = User::findOne(['email' => $login_form->email]);
            if (empty($user)){
                $login_form->addError('request','用户名或密码错误！');
                return $this->render('index.php',[
                    'login_form' => $login_form,
                ]);
            }

            if (!\Yii::$app->security->validatePassword($login_form->password,$user->password)){
                $login_form->addError('request','用户名或密码错误！');
                return $this->render('index.php',[
                    'login_form' => $login_form,
                ]);
            }

            if(empty($user->activated)){
                $login_form->addError('request','您的帐号还未激活！');
                return $this->render('index.php',[
                    'login_form' => $login_form,
                ]);
            }

            if($user->blocked){
                $login_form->addError('request','您的帐号已被禁用！');
                return $this->render('index.php',[
                    'login_form' => $login_form,
                ]);
            }

            \Yii::$app->user->login($user, $login_form->remember ? 3600*24*30 : 0);
            return $this->goHome();
        }

        return $this->render('index.php',[
            'login_form' => $login_form,
        ]);
    }
}