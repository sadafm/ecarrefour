<?php
use yii\helpers\Url;
use yii\helpers\Html;
use multebox\models\TaskReports;
/**
 * @var yii\web\View $this
 * @var multebox\models\City $model
 */

$this->title = Yii::t('app', 'Create City');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<script>
$(document).ready(function(e) {

	$('#city-country_id').change(function(){
    $.post("<?=Url::to(['/multeobjects/address/ajax-load-states'])?>", { 'country_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#city-state_id').html(result);
				})
	})
});
</script>
<div id="taskStatus"></div>
<div class="city-create">
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
<?php // TaskReports::myTaskStatusChart('taskStatus');?>
