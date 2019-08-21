<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\ProductSubCategory $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Product Sub Category',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Categories'), 'url' => ['product-category/view', 'id' => $_REQUEST['parent_id']]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-sub-category-create">
  <!--  <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div> -->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
