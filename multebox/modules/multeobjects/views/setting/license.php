<?php
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\helpers\Html;
use kartik\builder\Form;
use kartik\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\Address $searchModel
 */
 
$this->title = Yii::t ( 'app', 'License').' Mult-e-Commerce Version '.Yii::$app->params['APPLICATION_VERSION'];
$this->params ['breadcrumbs'] [] = $this->title;
?>

<div class="logo-index">
    <div class="box box-default">
		<div class="box-header with-border">
			<div class="box-title">
				<h5><?php echo Yii::t ( 'app', 'License').' Mult-e-Commerce Version '.Yii::$app->params['APPLICATION_VERSION'] ; ?></h5>
			</div>

		    <div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
		    </div>
		</div>	
		<div class="box-body">
			<?php 
			try
			{
				echo file_get_contents(Yii::$app->params['LICENSE']);
			}
			catch (\Exception $e)
			{
				echo "";
			}
			?>
	   </div>
	</div>
</div>
