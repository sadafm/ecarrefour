<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\CommissionDetails $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Commission Details',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Commission Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="commission-details-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
