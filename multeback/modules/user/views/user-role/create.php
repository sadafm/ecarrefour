<?php



use yii\helpers\Html;



/**

 * @var yii\web\View $this

 * @var common\models\UserRole $model

 */



$this->title = Yii::t('app', 'Create User Role', [

    'modelClass' => 'User Role',

]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User Roles'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-role-create">

<div class="ibox float-e-margins">

        <div class="ibox-title">

            <h5> <?=$this->title ?></h5>



            <div class="ibox-tools">



                <a class="collapse-link">

                    <i class="fa fa-chevron-up"></i>

                </a>

               

                <a class="close-link">

                    <i class="fa fa-times"></i>

                </a>

            </div>

</div>

         <div class="ibox-content">

    <?= $this->render('_form', [

        'model' => $model,

    ]) ?>



</div></div></div>

