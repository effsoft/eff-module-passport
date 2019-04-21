<?php
namespace effsoft\eff\module\passport\controllers;

use effsoft\eff\EffController;
use effsoft\eff\module\passport\models\UserModel;
use effsoft\eff\module\verify\enums\Protocol;
use effsoft\eff\module\verify\enums\Type;
use effsoft\eff\module\verify\services\VerifyService;
use effsoft\eff\response\JsonResult;
use yii\web\Response;

class PasswordController extends EffController{

    public function actionForgotten(){
        
        if (\Yii::$app->request->isPost && \Yii::$app->request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;

            if (!\Yii::$app->request->validateCsrfToken()) {
                return JsonResult::getNewInstance()->setStatus(101)->setMessage('Invalid csrf token!')->getResponse();
            }

            $email = \Yii::$app->request->post('email');
            if (empty($email)){
                return JsonResult::getNewInstance()->setStatus(102)->setMessage('Can not get email address!')->getResponse();
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                return JsonResult::getNewInstance()->setStatus(103)->setMessage('Invalid email address!')->getResponse();
            }

            $user_model = UserModel::findOne(['email' => $email]);
            if (empty($user_model)){
                return JsonResult::getNewInstance()->setStatus(104)->setMessage('Invalid email passport!')->getResponse();
            }

            $verify_service = new VerifyService();
            $verify_url = $verify_service->setType(Type::PASSWORD_FORGOT)
                ->setProtocol(Protocol::EMAIL)
                ->setFrom(\Yii::$app->params['system_email'])
                ->setTo($user_model->email)
                ->setUrl('/passport/verify/password-forgotten')
                ->setData(['uid' => strval($user_model->getPrimaryKey())])
                ->setSubject('Get your password reset verify code!')
                ->setView('password_forgotten')
                ->send();
            if (empty($verify_url)) {
                return JsonResult::getNewInstance()->setStatus(105)->setMessage($register_form->getErrors())->getResponse();
            }
            //redirect to verify from with token
            return JsonResult::getNewInstance()->setStatus(0)->setMessage($verify_url)->getResponse();
        }

        return $this->render('//passport/password/forgotten');
    }
}