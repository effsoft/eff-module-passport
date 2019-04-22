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

class VerifyController extends EffController{

    public function actionRegister(){

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
                $verify_form = new VerifyForm(['scenario' => VerifyForm::SCENARIO_REIGSTER]);
                $verify_form->load(\Yii::$app->request->post());
                if (!$verify_form->validate()) {
                    return JsonResult::getNewInstance()->setStatus(102)->setMessage($verify_form->getErrors())->getResponse();
                }
            }else{
                $verify_form = new VerifyForm(['scenario' => VerifyForm::SCENARIO_REIGSTER]);

                $verify_form->load(\Yii::$app->request->post());
                if (!$verify_form->validate()) {
                    return JsonResult::getNewInstance()->setStatus(102)->setMessage($verify_form->getErrors())->getResponse();
                }
                $verify_service = new VerifyService();
                $verify_data = $verify_service->validate($token, $verify_form->code);
                if (empty($verify_data)){
                    $verify_form->addError('request', $verify_service->getErrorMessage());
                    return JsonResult::getNewInstance()->setStatus(103)->setMessage($verify_form->getErrors())->getResponse();
                }
                if (empty($verify_data['uid'])) {
                    $verify_form->addError('verify', $verify_service->getErrorMessage());
                    return JsonResult::getNewInstance()->setStatus(104)->setMessage($verify_form->getErrors())->getResponse();
                }
                //active user and redirect to home
                $user_model = UserModel::findOne($verify_data['uid']);
                $user_model->activated = true;
                $user_model->updateAttributes(['activated']);
                \Yii::$app->user->login($user_model, 3600 * 24 * 30);

                return JsonResult::getNewInstance()->setStatus(0)->setMessage(Url::to(\Yii::$app->getHomeUrl()))->getResponse();
            }
        }

        return $this->render('//passport/verify/register');
    }

    public function actionPasswordForgotten(){
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
                $verify_form = new VerifyForm(['scenario' => VerifyForm::SCENARIO_PASSWORD_FORGOTTEN]);
                $verify_form->load(\Yii::$app->request->post());
                if (!$verify_form->validate()) {
                    return JsonResult::getNewInstance()->setStatus(102)->setMessage($verify_form->getErrors())->getResponse();
                }
            }else{
                $verify_form = new VerifyForm(['scenario' => VerifyForm::SCENARIO_PASSWORD_FORGOTTEN]);

                $verify_form->load(\Yii::$app->request->post());
                if (!$verify_form->validate()) {
                    return JsonResult::getNewInstance()->setStatus(102)->setMessage($verify_form->getErrors())->getResponse();
                }
                $verify_service = new VerifyService();
                $verify_data = $verify_service->validate($token, $verify_form->code);
                if (empty($verify_data)){
                    $verify_form->addError('request', $verify_service->getErrorMessage());
                    return JsonResult::getNewInstance()->setStatus(103)->setMessage($verify_form->getErrors())->getResponse();
                }
                if (empty($verify_data['uid'])) {
                    $verify_form->addError('verify', $verify_service->getErrorMessage());
                    return JsonResult::getNewInstance()->setStatus(104)->setMessage($verify_form->getErrors())->getResponse();
                }
                //reset user password and redirect to login page
                $user_model = UserModel::findOne($verify_data['uid']);
                $user_model->password = \Yii::$app->security->generatePasswordHash($verify_form->password);
                $user_model->updateAttributes(['password']);

                return JsonResult::getNewInstance()->setStatus(0)->setMessage(Url::to(['/passport/login']))->getResponse();
            }
        }
        return $this->render('//passport/verify/password-forgotten');
    }
}