<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\SubOrder $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Sub Order',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sub Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-order-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
