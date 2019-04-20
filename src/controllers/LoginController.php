<?php

namespace effsoft\eff\module\passport\controllers;

use effsoft\eff\EffController;
use effsoft\eff\module\passport\models\LoginForm;
use effsoft\eff\module\passport\models\UserModel;
use effsoft\eff\module\verify\enums\Protocol;
use effsoft\eff\module\verify\enums\Type;
use effsoft\eff\module\verify\models\VerifyModel;
use effsoft\eff\module\verify\Verify;
use effsoft\eff\response\JsonResult;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Response;

class LoginController extends EffController
{

    function actionIndex()
    {

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $login_form = new LoginForm();

        if (\Yii::$app->request->isPost && \Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            if (!\Yii::$app->request->validateCsrfToken()) {
                return JsonResult::getNewInstance()->setStatus(101)->setMessage('Invalid csrf token!')->getResponse();
            }

            $login_form->load(\Yii::$app->request->post());
            if (!$login_form->validate()) {
                return JsonResult::getNewInstance()->setStatus(102)->setMessage($login_form->getErrors())->getResponse();
            }

            $user = UserModel::findOne(['email' => strtolower($login_form->email)]);
            if (empty($user)) {
                $login_form->addError('email', 'Please check your email and password!');
                return JsonResult::getNewInstance()->setStatus(103)->setMessage($login_form->getErrors())->getResponse();
            }

            if (!\Yii::$app->security->validatePassword($login_form->password, $user->password)) {
                $login_form->addError('password', 'Please check your email and password!');
                return JsonResult::getNewInstance()->setStatus(104)->setMessage($login_form->getErrors())->getResponse();
            }

            if (empty($user->activated)) {

                $verify_model = VerifyModel::findOne(['to' => $login_form->email]);
                if (empty($verify_model) || $verify_model->isExpired()) {
                    //发送新的验证邮件
                    $verify = new Verify();
                    $verify_url = $verify->setType(Type::REGISTER)
                        ->setProtocol(Protocol::EMAIL)
                        ->setFrom(\Yii::$app->params['admin_email'])
                        ->setTo($user->email)
                        ->setUrl('/passport/register/verify')
                        ->setData(['uid' => strval($user->getPrimaryKey())])
                        ->setSubject('Get your registration verify code!')
                        ->setView('register')
                        ->send();
                    if (empty($verify_url)) {
                        $register_form->addError('verify', $verify->getErrorMessage());
                        return JsonResult::getNewInstance()->setStatus(1001)->setMessage($register_form->getErrors())->getResponse();
                    }
                } else {
                    $verify_url = Url::to(['/passport/register/verify',
                        'token' => $verify_model->token,
                    ], true);
                }

                return JsonResult::getNewInstance()->setStatus(105)->setMessage($verify_url)->getResponse();
            }

            if ($user->blocked) {
                $login_form->addError('email', 'Your account has been disabled!');
                return JsonResult::getNewInstance()->setStatus(106)->setMessage($login_form->getErrors())->getResponse();
            }

            \Yii::$app->user->login($user, $login_form->remember ? 3600 * 24 * 30 : 0);
            return JsonResult::getNewInstance()->setStatus(0)->setMessage(\Yii::$app->getHomeUrl())->getResponse();
        }

        return $this->render('//passport/login/index', [
            'login_form' => $login_form,
        ]);
    }
}