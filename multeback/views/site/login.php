<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \multebox\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use multebox\widgets\Alert;

$this->title = Yii::t('app', 'Login');
$this->params['breadcrumbs'][] = $this->title;

if(DEMO_MODE)
{
	$value='admin';
}
else
{
	$value='';
}

?>
<?= Alert::widget() ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?=Yii::$app->params['APPLICATION_NAME']?> | <?=Yii::t('app', 'Log in')?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="stylesheet" href="<?=Url::base()?>/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=Url::base()?>/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?=Url::base()?>/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=Url::base()?>/dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?=Url::base()?>/plugins/iCheck/square/blue.css">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b><?=Yii::$app->params['APPLICATION_NAME']?></b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg"><?=Yii::t('app', 'Sign in to start your session')?></p>

   <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

				<?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => '', 'value' => $value])->label(Yii::t('app', 'Username')) ?>
                <?= $form->field($model, 'password')->passwordInput(['placeholder' => '', 'value' => $value])->label(Yii::t('app', 'Password')) ?>
				<?= $form->field($model, 'rememberMe')->checkbox()->label(Yii::t('app', 'Remember Me')) ?>

                <div style="color:#999;margin:1em 0">
                    <?=Yii::t('app', 'If you forgot your password you can')?> <?= Html::a(Yii::t('app', 'reset it'), ['/site/request-password-reset']) ?>.
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>

   <!-- <div align = "center">
		<a href="#"><?=Yii::t('app', 'Forgot Password')?></a><br>
		<a href="register.html" class="text-center"><?=Yii::t('app', 'Register')?></a>
	</div>-->

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<script src="<?=Url::base()?>/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?=Url::base()?>/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?=Url::base()?>/plugins/iCheck/icheck.min.js"></script>

</body>
</html>