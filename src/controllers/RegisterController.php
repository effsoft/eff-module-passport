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
use effsoft\eff\module\verify\services\VerifyService;
use effsoft\eff\response\JsonResult;
use MongoDB\BSON\Regex;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\web\Response;

class RegisterController extends EffController
{

    function actionIndex()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $register_form = new RegisterForm();
        if (\Yii::$app->request->isPost && \Yii::$app->request->isAjax) {

            \Yii::$app->response->format = Response::FORMAT_JSON;

            if (!\Yii::$app->request->validateCsrfToken()) {
                return JsonResult::getNewInstance()->setStatus(101)->setMessage('Invalid csrf token!')->getResponse();
            }

            $register_form->load(\Yii::$app->request->post());
            if (!$register_form->validate()) {
                return JsonResult::getNewInstance()->setStatus(102)->setMessage($register_form->getErrors())->getResponse();
            }

            $user_model = new UserModel();
            $user_model->username = $register_form->username;
            $user_model->email = strtolower($register_form->email);
            $user_model->password = \Yii::$app->security->generatePasswordHash($register_form->password);
            if (!$user_model->register()) {
                $register_form->addError('cluster', 'Can not regist new user, please try again later!');
                return JsonResult::getNewInstance()->setStatus(103)->setMessage($register_form->getErrors())->getResponse();
            }

            $verify_service = new VerifyService();
            $verify_url = $verify_service->setType(Type::REGISTER)
                ->setProtocol(Protocol::EMAIL)
                ->setFrom(\Yii::$app->params['system_email'])
                ->setTo($user_model->email)
                ->setUrl('/passport/verify/register')
                ->setData(['uid' => strval($user_model->getPrimaryKey())])
                ->setSubject('Get your registration verify code!')
                ->setView('register')
                ->send();
            if (empty($verify_url)) {
                $register_form->addError('verify', $verify_service->getErrorMessage());
                return JsonResult::getNewInstance()->setStatus(104)->setMessage($register_form->getErrors())->getResponse();
            }

            return JsonResult::getNewInstance()->setStatus(0)->setMessage($verify_url)->getResponse();

        }
        return $this->render('//passport/register/index', [
            'register_form' => $register_form,
        ]);
    }

}