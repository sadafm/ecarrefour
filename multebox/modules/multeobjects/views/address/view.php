<?php
use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
/**
 * @var yii\web\View $this
 * @var multebox\models\Address $model
 */
$this->title = Yii::t('app', 'Address');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Addresses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
	if(!empty($_GET['added'])){?>
		<div class="alert alert-success"><?=$this->title." ".Yii::t('app', 'is Added')?> </div>
<?php	}
?>
<div class="address-view">
    <!--<div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>-->

    <?= DetailView::widget([
            'model' => $model,
            'condensed'=>false,
            'hover'=>true,
            'mode'=>Yii::$app->request->get('edit')=='t' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'panel'=>[
            'heading'=>$this->title,
            'type'=>DetailView::TYPE_INFO,
        ],
        'attributes' => [
     //       'id',
            'address_1',
            'address_2',
            'country_id',
            'state_id',
            'city_id',
            'zipcode',
            //'created_at',
           // 'updated_at',
        ],
        'deleteOptions'=>[
        'url'=>['delete', 'id' => $model->id],
        'data'=>[
        'confirm'=>Yii::t('app', 'Are you sure you want to delete this item?'),
        'method'=>'post',
        ],
        ],
        'enableEditMode'=>false,
    ]) ?>
</div>
