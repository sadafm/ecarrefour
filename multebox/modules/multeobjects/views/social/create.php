<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\Social $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Social',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Social'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="social-create">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
