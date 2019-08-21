<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\ProductAttributeValues $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Product Attribute Values',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Attribute Values'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

include_once('script.php');
?>
<div class="product-attribute-values-create">
   <!-- <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div> -->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
