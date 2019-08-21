<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\VendorInvoices $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Vendor Invoices',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vendor Invoices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-invoices-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
