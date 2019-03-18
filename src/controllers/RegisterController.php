<?php

namespace effsoft\eff\module\passport\controllers;

use effsoft\eff\EffController;
use effsoft\eff\module\passport\models\RegisterForm;
use effsoft\eff\module\passport\models\User;
use effsoft\eff\module\passport\models\Verify;
use effsoft\eff\module\passport\models\VerifyForm;
use yii\helpers\Url;

class RegisterController extends EffController{

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

            $user = User::findOne(['username' => $register_form->username]);
            if (!empty($user)){
                $register_form->addError('cluster', '该用户名已被占用，请选用其他用户名！');
                return $this->render('index.php',[
                    'register_form' => $register_form,
                ]);
            }
            $user = User::findOne(['email' => $register_form->email]);
            if (!empty($user)){
                $register_form->addError('cluster', '该邮箱已被占用，请选用其他邮箱！');
                return $this->render('index.php',[
                    'register_form' => $register_form,
                ]);
            }

            //生成激活码
            $factory = new \RandomLib\Factory();
            $generator = $factory->getGenerator(new \SecurityLib\Strength(\SecurityLib\Strength::MEDIUM));
            $verify_code = $generator->generateString(4,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
            if (empty($verify_code)){
                $register_form->addError('verify_code', '无法生成注册码，请稍后重试！');
                return $this->render('index.php',[
                    'register_form' => $register_form,
                ]);
            }

            //添加新用户
            $user = new User();
            $user->first_name = $register_form->first_name;
            $user->last_name = $register_form->last_name;
            $user->username = $register_form->username;
            $user->email = $register_form->email;
            $user->password = \Yii::$app->security->generatePasswordHash($register_form->password);
            if(!$user->register()){
                $register_form->addError('cluster', '无法添加新用户，请稍后重试！');
                return $this->render('index.php',[
                    'register_form' => $register_form,
                ]);
            }

            //存入数据库
            $verify = new Verify();
            $verify->uid = strval($user->getPrimaryKey());
            $verify->token = \Yii::$app->getSecurity()->generateRandomString();
            $verify->code = $verify_code;
            if (!$verify->save()){
                $register_form->addError('verify_code', '无法保存注册码，请稍后重试！');
                return $this->render('index.php',[
                    'register_form' => $register_form,
                ]);
            }

            //发送邮件
            $verify_url = Url::to(['/passport/register/verify',
                'token' => $verify->token,
            ],true);
            $register_email = \Yii::$app->mailer->compose('register',[
                'verify_code' => $verify_code,
                'verify_url' =>  $verify_url,
            ]);
            $register_email->setTo('flcgame@gmail.com')//TODO 更改成注册人的email
            ->setFrom(\Yii::$app->params['admin_email'])
                ->setSubject('获取['.\Yii::$app->name.']的注册码！');
            @$register_email->send();

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

        $verify = Verify::findOne(['token' => $token]);
        if (empty($verify) || $verify->isExpired()){
            $verify_form->addError('expiration','注册码不存在或已过期，请重新获取！');
            return $this->render('verify.php',[
                'verify_form' => $verify_form
            ]);
        }

        if (empty($verify->uid)){
            $verify_form->addError('request','无法解析您的请求页面！');
            return $this->render('verify.php',[
                'verify_form' => $verify_form
            ]);
        }

        $user = User::findOne($verify->uid);
        if (empty($user)){
            $verify_form->addError('request','该用户不存在！');
            return $this->render('verify.php',[
                'verify_form' => $verify_form
            ]);
        }

        //检查该用户是否被禁用
        if($user->blocked){
            $verify_form->addError('request','该帐号已被禁用！');
            return $this->render('verify.php',[
                'verify_form' => $verify_form
            ]);
        }

        if (\Yii::$app->request->isPost){
            $verify_form->load(\Yii::$app->request->post());
            if(!$verify_form->validate()){
                return $this->render('verify.php',[
                    'verify_form' => $verify_form
                ]);
            }
            if ($verify_form->code === $verify->code){

                //删除注册码
                if (!$verify->delete()){
                    $verify_form->addError('request','删除激活码出错，请稍后重试！');
                    return $this->render('verify.php',[
                        'verify_form' => $verify_form
                    ]);
                }
                //更新用户为激活状态
                $user->activated = true;
                $user->updateAttributes(['activated']);

                //登录帐号
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