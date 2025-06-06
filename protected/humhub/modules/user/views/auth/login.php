<?php

use humhub\libs\Html;
use humhub\modules\user\models\forms\Login;
use humhub\modules\user\models\Invite;
use humhub\modules\user\widgets\AuthChoice;
use humhub\widgets\SiteLogo;
use yii\captcha\Captcha;
use yii\widgets\ActiveForm;

$this->pageTitle = Yii::t('UserModule.auth', 'Login');

/* @var $canRegister bool */
/* @var $model Login */
/* @var $invite Invite */
/* @var $info string */
/* @var $passwordRecoveryRoute string|array|null */
/* @var $showLoginForm bool */
/* @var $showRegistrationForm bool */

?>

<div id="user-auth-login" class="container" style="text-align: center;">
    <?= SiteLogo::widget(['place' => 'login']); ?>
    <br>

    <div class="panel panel-default animated bounceIn" id="login-form"
         style="max-width: 300px; margin: 0 auto 20px; text-align: left;">

        <div class="panel-heading"><?= Yii::t('UserModule.auth', '<strong>Please</strong> sign in'); ?></div>

        <div class="panel-body">

            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger" role="alert">
                    <?= Yii::$app->session->getFlash('error') ?>
                </div>
            <?php endif; ?>

            <?php if (AuthChoice::hasClients()): ?>
                <?= AuthChoice::widget(['showOrDivider' => $showLoginForm]) ?>
            <?php else: ?>
                <?php if ($canRegister) : ?>
                    <p><?= Yii::t('UserModule.auth', "If you're already a member, please login with your username/email and password."); ?></p>
                <?php elseif ($showLoginForm): ?>
                    <p><?= Yii::t('UserModule.auth', "Please login with your username/email and password."); ?></p>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($showLoginForm): ?>
                <?php $form = ActiveForm::begin(['id' => 'account-login-form', 'enableClientValidation' => false]); ?>
                <?= $form->field($model, 'username')->textInput(['id' => 'login_username', 'placeholder' => $model->getAttributeLabel('username'), 'aria-label' => $model->getAttributeLabel('username')])->label(false); ?>
                <?= $form->field($model, 'password')
                    ->passwordInput(['id' => 'login_password', 'placeholder' => $model->getAttributeLabel('password'), 'aria-label' => $model->getAttributeLabel('password')])
                    ->label(false); ?>
                <?= $model->hideRememberMe ? '' : $form->field($model, 'rememberMe')->checkbox(); ?>

                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <?= Html::submitButton(Yii::t('UserModule.auth', 'Sign in'), ['id' => 'login-button', 'data-ui-loader' => "", 'class' => 'btn btn-large btn-primary']); ?>
                    </div>
                    <?php if ($passwordRecoveryRoute) : ?>
                        <div class="col-md-8 text-right">
                            <small>
                                <?= Html::a(
                                    Html::tag('br') . Yii::t('UserModule.auth', 'Forgot your password?'),
                                    $passwordRecoveryRoute,
                                    [
                                        'id' => 'password-recovery-link',
                                        'target' => is_array($passwordRecoveryRoute) ? '_self' : '_blank',
                                        'data' => [
                                            'pjax-prevent' => true,
                                        ]
                                    ]
                                ) ?>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
    </div>

    <br>

    <?php if ($canRegister && $showRegistrationForm) : ?>
        <div id="register-form"
             class="panel panel-default animated bounceInLeft"
             style="max-width: 300px; margin: 0 auto 20px; text-align: left;">

            <div class="panel-heading"><?= Yii::t('UserModule.auth', '<strong>Sign</strong> up') ?></div>

            <div class="panel-body">

                <?php if (AuthChoice::hasClients()): ?>
                    <?= AuthChoice::widget() ?>
                    <div class="or-container">
                        <hr>
                        <div>or</div>
                    </div>
                <?php else: ?>
                    <p><?= Yii::t('UserModule.auth', "Don't have an account? Join the network by entering your e-mail address."); ?></p>
                <?php endif; ?>

                <?php $form = ActiveForm::begin(['id' => 'invite-form']); ?>
                <?= $form->field($invite, 'email')->input('email', ['id' => 'register-email', 'placeholder' => $invite->getAttributeLabel('email'), 'aria-label' => $invite->getAttributeLabel('email')])->label(false); ?>
                <?php if ($invite->showCaptureInRegisterForm()) : ?>
                    <div id="registration-form-captcha" style="display: none;">
                        <div><?= Yii::t('UserModule.auth', 'Please enter the letters from the image.'); ?></div>

                        <?= $form->field($invite, 'captcha')->widget(Captcha::class, [
                            'captchaAction' => '/user/auth/captcha',
                        ])->label(false); ?>
                    </div>
                <?php endif; ?>
                <hr>
                <?= Html::submitButton(Yii::t('UserModule.auth', 'Register'), ['class' => 'btn btn-primary', 'data-ui-loader' => '']); ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

    <?php endif; ?>

    <?= humhub\widgets\LanguageChooser::widget(); ?>
</div>

<script <?= Html::nonce() ?>>
    $(function () {
        // set cursor to login field
        $('#login_username').focus();
    });

    // Shake panel after wrong validation
    <?php if ($model->hasErrors()) { ?>
    $('#login-form').removeClass('bounceIn');
    $('#login-form').addClass('shake');
    $('#register-form').removeClass('bounceInLeft');
    $('#app-title').removeClass('fadeIn');
    <?php } ?>

    // Shake panel after wrong validation
    <?php if ($invite->hasErrors()) { ?>
    $('#register-form').removeClass('bounceInLeft');
    $('#register-form').addClass('shake');
    $('#login-form').removeClass('bounceIn');
    $('#app-title').removeClass('fadeIn');
    <?php } ?>

    <?php if ($invite->showCaptureInRegisterForm()) { ?>
    $('#register-email').on('focus', function () {
        $('#registration-form-captcha').fadeIn(500);
    });
    <?php } ?>

</script>
