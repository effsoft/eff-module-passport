<?php
$this->context->layout = 'passport.layout.php';
?>
<?php
$this->title = '填写注册码！';
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
                                        <h1 class="h4 text-gray-900 mb-4">填写注册码！</h1>
                                    </div>
                                    <?php
                                    use yii\widgets\ActiveForm;
                                    $form = ActiveForm::begin([
                                        'id' => 'verify_form',
                                        'action' => \Yii::$app->request->url,
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'class' => 'user',
                                        ],
                                    ])
                                    ?>
                                    <div class="form-group">
                                        <?= $form->field($verify_form, 'code',[
                                            'inputOptions' => [
                                                'class' => 'form-control form-control-user'
                                            ],
                                        ])
                                            ->textInput()
                                            ->input('text', ['placeholder' => "注册码"])->label(false)->hint(false);
                                        ?>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        提交
                                    </button>
                                    <?php if (!empty($verify_form->errors)): ?>
                                        <ul class="form_errors mt-2">
                                            <?php foreach ($verify_form->errors as $error): ?>
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
                                            <?=Yii::t('eff-module-passport/app','forgot_password'); ?>？
                                        </a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="<?=\yii\helpers\Url::to(['/passport/register'])?>">
                                            <?=Yii::t('eff-module-passport/app','register')?>！
                                        </a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="<?=\yii\helpers\Url::to(['/passport/login'])?>">
                                            <?=Yii::t('eff-module-passport/app','login')?>！
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