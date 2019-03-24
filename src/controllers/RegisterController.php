<?php

namespace effsoft\eff\module\passport\controllers;

use effsoft\eff\EffController;
use effsoft\eff\module\passport\models\RegisterForm;
use effsoft\eff\module\passport\models\UserModel;
use effsoft\eff\module\passport\models\Verify;
use effsoft\eff\module\passport\models\VerifyForm;
use effsoft\eff\module\verify\enums\Protocol;
use effsoft\eff\module\verify\enums\Type;
use effsoft\eff\module\verify\models\VerifyModel;
use MongoDB\BSON\Regex;
use yii\helpers\Url;
use yii\filters\AccessControl;

class RegisterController extends EffController{

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','verify'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ]
        ];
    }

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
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $register_form = new RegisterForm();
        if (\Yii::$app->request->isPost){
            $register_form->load(\Yii::$app->request->post());
            if(!$register_form->validate()){
                return $this->render('index.php',[
                    'register_form' => $register_form,
                ]);
            }

            $user = UserModel::findOne(['username' => new Regex("^$register_form->username$",'i')]);
            if (!empty($user)){
                $register_form->addError('cluster', '该用户名已被占用，请选用其他用户名！');
                return $this->render('index.php',[
                    'register_form' => $register_form,
                ]);
            }
            $user = UserModel::findOne(['email' => strtolower($register_form->email)]);
            if (!empty($user)){
                $register_form->addError('cluster', '该邮箱已被占用，请选用其他邮箱！');
                return $this->render('index.php',[
                    'register_form' => $register_form,
                ]);
            }

            //添加新用户
            $user = new UserModel();
            $user->first_name = $register_form->first_name;
            $user->last_name = $register_form->last_name;
            $user->username = $register_form->username;
            $user->email = strtolower($register_form->email);
            $user->password = \Yii::$app->security->generatePasswordHash($register_form->password);
            if(!$user->register()){
                $register_form->addError('cluster', '无法添加新用户，请稍后重试！');
                return $this->render('index.php',[
                    'register_form' => $register_form,
                ]);
            }

            $verify = new \effsoft\eff\module\verify\Verify();
            $verify_url = $verify->setType(Type::REGISTER)
                ->setProtocol(Protocol::EMAIL)
                ->setFrom(\Yii::$app->params['admin_email'])
                ->setTo($user->email)
                ->setUrl('/passport/register/verify')
                ->setData(['uid' => strval($user->getPrimaryKey())])
                ->setSubject('Get your registration verify code!')
                ->setView('register')
                ->send();
            if (empty($verify_url)){
                $register_form->addError('verify', $verify->getErrorMessage());
                return $this->render('index.php',[
                    'register_form' => $register_form,
                ]);
            }

            //跳转至填写注册码的页面
            return \Yii::$app->response->redirect($verify_url);
        }
        return $this->render('index.php',[
            'register_form' => $register_form,
        ]);
    }

    function actionVerify(){

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $verify_form = new VerifyForm();

        $token = \Yii::$app->request->get('token');

        if (\Yii::$app->request->isPost){
            $verify_form->load(\Yii::$app->request->post());
            if(!$verify_form->validate()){
                return $this->render('verify.php',[
                    'verify_form' => $verify_form
                ]);
            }
            $verify = new \effsoft\eff\module\verify\Verify();
            $verify_data = $verify->validate($token, $verify_form->code);
            if (!empty($verify_data)){

                if (empty($verify_data['uid'])){
                    $verify_form->addError('verify','无法获取有效的data，请重新获取激活邮件！');
                    return $this->render('verify.php',[
                        'verify_form' => $verify_form
                    ]);
                }

                $user = UserModel::findOne($verify_data['uid']);

                //更新用户为激活状态
                $user->activated = true;
                $user->updateAttributes(['activated']);

                //登录帐号
                $user->password = '';
                \Yii::$app->user->login($user, 3600*24*30);

                return $this->goHome();
            }else{
                $verify_form->addError('request','请检查您的注册码！');
                return $this->render('verify.php',[
                    'verify_form' => $verify_form
                ]);
            }
        }

        return $this->render('verify.php',[
            'verify_form' => $verify_form
        ]);

    }
}