<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var multebox\models\GlobalDiscount $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Global Discount',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Global Discounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
include_once("script.php");
?>
<div class="global-discount-create">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
