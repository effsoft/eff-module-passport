
<?php
$this->context->layout = 'passport.layout.php';
?>
<?php
$this->title = Yii::t('eff-module-passport/app','login');
?>
<div class="bg-gradient-primary h-100">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-6 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4"><?=Yii::t('eff-module-passport/app','welcome_back');?></h1>
                                    </div>
                                    <?php
                                    use yii\widgets\ActiveForm;
                                    $form = ActiveForm::begin([
                                        'id' => 'login_form',
                                        'action' => \yii\helpers\Url::to(['/passport/login']),
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'class' => 'user',
                                        ],
//                                        'enableAjaxValidation' => true,
                                    ])
                                    ?>
                                    <div class="form-group">
                                        <?= $form->field($login_form, 'email',[
                                            'inputOptions' => [
                                                'class' => 'form-control form-control-user'
                                            ],
                                        ])
                                            ->textInput()
                                            ->input('email', ['placeholder' => "邮件地址"])->label(false)->hint(false);
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?= $form->field($login_form, 'password',[
                                            'inputOptions' => [
                                                'class' => 'form-control form-control-user'
                                            ],
                                        ])
                                            ->textInput()
                                            ->input('password', ['placeholder' => "密码"])->label(false)->hint(false);
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">

                                            <?= $form->field($login_form, 'remember',[
                                                'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} <label class=\"custom-control-label\" for=\"loginform-remember\">{label}</label></div>\n<div class=\"col-lg-8\">{error}</div>",
                                            ])->checkbox([
                                                'label' => '记住我！',
                                                'class' => 'custom-control-input',
                                                'labelOptions' => [
                                                    'class' => 'custom-control-label',
                                                    'for' => 'loginform-remember',
                                                ],
                                            ],false) ?>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        登录
                                    </button>
                                    <?php if (!empty($login_form->errors)): ?>
                                        <ul class="form_errors mt-2">
                                            <?php foreach ($login_form->errors as $error): ?>
                                                <?php if (!empty($error)): ?>
                                                    <?php foreach ($error as $val): ?>
                                                        <li><?=$val?></li>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                    <?php ActiveForm::end() ?>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="<?=\yii\helpers\Url::to(['/passport/password/forgot']) ?>">
                                            <?=Yii::t('eff-module-passport/app','forgot_password'); ?>？
                                        </a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="<?=\yii\helpers\Url::to(['/passport/register']) ?>">
                                            <?=Yii::t('eff-module-passport/app','register')?>！
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>