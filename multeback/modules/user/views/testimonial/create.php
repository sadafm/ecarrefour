<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var multebox\models\Testimonial $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Testimonial',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Testimonials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="testimonial-create">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
