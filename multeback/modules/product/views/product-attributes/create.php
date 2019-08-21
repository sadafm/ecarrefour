<?php

use yii\helpers\Html;
use yii\helpers\Url;
use multebox\models\ProductSubSubCategory;

/**
 * @var yii\web\View $this
 * @var multebox\models\ProductAttributes $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Product Attributes',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', ProductSubSubCategory::find()->where('id='.$_REQUEST['parent_id'])->one()->name), 'url' => ['product-sub-sub-category/view', 'id' => $_REQUEST['parent_id']]];
$this->params['breadcrumbs'][] = $this->title;
include_once("script.php");
?>

<div class="product-attributes-create">
  <!--  <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div> -->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
