<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\EmailTemplate $model
 */

$this->title = Yii::t('app', 'Update Email Template: ', [
    'modelClass' => 'Email Template',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Email Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="email-template-update">
    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>