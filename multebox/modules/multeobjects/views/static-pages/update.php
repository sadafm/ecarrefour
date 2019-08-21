<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\StaticPages $model
 */

$this->title = Yii::t('app', 'Update Static Page', [
    'modelClass' => 'Static Pages',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Static Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="static-pages-update">
    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>