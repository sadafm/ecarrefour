<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\Social $model
 */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Social',
]) . ' ' . $model->platform;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Social'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="social-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
