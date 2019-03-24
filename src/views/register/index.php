<?php
$this->context->layout = 'passport.layout.php';
?>
<?php
$this->title = \Yii::t('passport/app','register');
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
                                        <h1 class="h4 text-gray-900 mb-4"><?=Yii::t('passport/app','create_an_account');?></h1>
                                    </div>
                                    <?php
                                    use yii\widgets\ActiveForm;
                                    $form = ActiveForm::begin([
                                        'id' => 'register_form',
                                        'action' => \yii\helpers\Url::to(['/passport/register']),
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'class' => 'user',
                                        ],
                                    ])
                                    ?>
                                    <div class="row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <?= $form->field($register_form, 'first_name',[
                                                'inputOptions' => [
                                                    'class' => 'form-control form-control-user'
                                                ],
                                            ])
                                                ->textInput()
                                                ->input('text', ['placeholder' => "名字"])->label(false)->hint(false);
                                            ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <?= $form->field($register_form, 'last_name',[
                                                'inputOptions' => [
                                                    'class' => 'form-control form-control-user'
                                                ],
                                            ])
                                                ->textInput()
                                                ->input('text', ['placeholder' => "姓"])->label(false)->hint(false);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?= $form->field($register_form, 'username',[
                                            'inputOptions' => [
                                                'class' => 'form-control form-control-user'
                                            ],
                                        ])
                                            ->textInput()
                                            ->input('text', ['placeholder' => "用户名"])->label(false)->hint(false);
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?= $form->field($register_form, 'email',[
                                            'inputOptions' => [
                                                'class' => 'form-control form-control-user'
                                            ],
                                        ])
                                            ->textInput()
                                            ->input('email', ['placeholder' => "邮件地址"])->label(false)->hint(false);
                                        ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <?= $form->field($register_form, 'password',[
                                                'inputOptions' => [
                                                    'class' => 'form-control form-control-user'
                                                ],
                                            ])
                                                ->textInput()
                                                ->input('password', ['placeholder' => "密码"])->label(false)->hint(false);
                                            ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <?= $form->field($register_form, 'repeat_password',[
                                                'inputOptions' => [
                                                    'class' => 'form-control form-control-user'
                                                ],
                                            ])
                                                ->textInput()
                                                ->input('password', ['placeholder' => "重复密码"])->label(false)->hint(false);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?=\yii\captcha\Captcha::widget([
                                            'name' => 'RegisterForm[captcha]',
                                            'captchaAction'=> '/passport/register/captcha',
                                            'template' => '<div class="row"><div class="col-lg-6 text-center">{image}</div><div class="col-lg-6">{input}</div></div>',
                                            'imageOptions' => [
                                                'class' => 'captcha-image',
                                            ],
                                            'options' => [
                                                'class' => 'form-control form-control-user',
                                                'placeholder' => '验证码'
                                            ],
                                        ]);?>
                                        <?php
                                        ?>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        <?=Yii::t('passport/app','register');?>
                                    </button>
                                    <?php if (!empty($register_form->errors)): ?>
                                        <ul class="form_errors mt-2">
                                            <?php foreach ($register_form->errors as $error): ?>
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
                                        <a class="small" href="<?=\yii\helpers\Url::to(['/passport/password/forgot'])?>">
                                            <?=Yii::t('passport/app','forgot_password');?>？
                                        </a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="<?=\yii\helpers\Url::to(['/passport/login'])?>">
                                            <?=Yii::t('passport/app','login');?>！
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