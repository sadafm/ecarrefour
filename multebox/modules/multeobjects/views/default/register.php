<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use multebox\widgets\Alert;

$this->title = Yii::t('app', 'Login');
$this->params['breadcrumbs'][] = $this->title;

?>
<?= Alert::widget() ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Mult-e-Cart | <?=Yii::t('app', 'Register')?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="stylesheet" href="<?=Url::base()?>/register/bootstrap.min.css">
  <link rel="stylesheet" href="<?=Url::base()?>/register/AdminLTE.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>Mult-e-</b>Cart</a>
  </div>

  <div class="login-box-body">
    <p class="login-box-msg"><?=Yii::t('app', 'Please fill below fields to register your copy of MulteCart E-Commerce')?></p>

   <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

				<?= $form->field($model, 'purchase_code')->textInput(['autofocus' => true, 'placeholder' => '']) ?>
                <?= $form->field($model, 'firstname')->textInput(['placeholder' => '']) ?>
				<?= $form->field($model, 'lastname')->textInput(['placeholder' => '']) ?>
				<?= $form->field($model, 'email')->textInput(['placeholder' => '']) ?>
				<?= $form->field($model, 'phone')->textInput(['placeholder' => '']) ?>

                <div class="form-group">
                    <?= Html::submitButton('Register', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>


  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<script src="<?=Url::base()?>/register/jquery.min.js"></script>
<script src="<?=Url::base()?>/register/bootstrap.min.js"></script>

</body>
</html>