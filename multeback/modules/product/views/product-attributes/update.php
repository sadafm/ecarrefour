<?php

use yii\helpers\Html;
use multebox\models\ProductSubSubCategory;
use multebox\models\ProductSubCategory;

/**
 * @var yii\web\View $this
 * @var multebox\models\ProductAttributes $model
 */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Product Attributes',
]) . ' ' . $model->name;
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Attributes'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', ProductSubSubCategory::find()->where('id='.$model->parent_id)->one()->name), 'url' => ['product-sub-sub-category/view', 'id' => $model->parent_id]];
$this->params['breadcrumbs'][] = $this->title;
include_once("script.php");
?>
<div class="product-attributes-update">

  <!--  <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
