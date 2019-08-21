<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\BannerData $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Banner Data',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banner Data'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

include_once("script.php");
?>
<div class="banner-data-create">
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
