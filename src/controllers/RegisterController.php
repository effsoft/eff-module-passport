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
use effsoft\eff\response\JsonResult;
use MongoDB\BSON\Regex;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\web\Response;

class RegisterController extends EffController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'verify'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ]
        ];
    }

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

            $user = new UserModel();
            $user->username = $register_form->username;
            $user->email = strtolower($register_form->email);
            $user->password = \Yii::$app->security->generatePasswordHash($register_form->password);
            if (!$user->register()) {
                $register_form->addError('cluster', 'Can not regist new user, please try again later!');
                return JsonResult::getNewInstance()->setStatus(103)->setMessage($register_form->getErrors())->getResponse();
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
            if (empty($verify_url)) {
                $register_form->addError('verify', $verify->getErrorMessage());
                return JsonResult::getNewInstance()->setStatus(104)->setMessage($register_form->getErrors())->getResponse();
            }

            return JsonResult::getNewInstance()->setStatus(0)->setMessage($verify_url)->getResponse();

        }
        return $this->render('//passport/register/index', [
            'register_form' => $register_form,
        ]);
    }

    function actionVerify()
    {

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (\Yii::$app->request->isPost && \Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            if (!\Yii::$app->request->validateCsrfToken()) {
                return JsonResult::getNewInstance()->setStatus(101)->setMessage('Invalid csrf token!')->getResponse();
            }
            $token = \Yii::$app->request->get('token');
            if (empty($token)){
                $verify_form = new VerifyForm(['scenario' => VerifyForm::SCENARIO_WITH_OUT_TOKEN]);
                $verify_form->load(\Yii::$app->request->post());
                if (!$verify_form->validate()) {
                    return JsonResult::getNewInstance()->setStatus(102)->setMessage($verify_form->getErrors())->getResponse();
                }
            }else{
                $verify_form = new VerifyForm(['scenario' => VerifyForm::SCENARIO_WITH_TOKEN]);

                $verify_form->load(\Yii::$app->request->post());
                if (!$verify_form->validate()) {
                    return JsonResult::getNewInstance()->setStatus(102)->setMessage($verify_form->getErrors())->getResponse();
                }
                $verify = new \effsoft\eff\module\verify\Verify();
                $verify_data = $verify->validate($token, $verify_form->code);
                if (empty($verify_data)){
                    $verify_form->addError('request', $verify->getErrorMessage());
                    return JsonResult::getNewInstance()->setStatus(103)->setMessage($verify_form->getErrors())->getResponse();
                }
                if (empty($verify_data['uid'])) {
                    $verify_form->addError('verify', $verify->getErrorMessage());
                    return JsonResult::getNewInstance()->setStatus(104)->setMessage($verify_form->getErrors())->getResponse();
                }
                $user = UserModel::findOne($verify_data['uid']);
                $user->activated = true;
                $user->updateAttributes(['activated']);
                \Yii::$app->user->login($user, 3600 * 24 * 30);

                return JsonResult::getNewInstance()->setStatus(0)->setMessage(\Yii::$app->getHomeUrl())->getResponse();
            }
        }

        return $this->render('//passport/register/verify');

    }
}