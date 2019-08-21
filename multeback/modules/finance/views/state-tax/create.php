<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\StateTax $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'State Tax',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'State Taxes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="state-tax-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
