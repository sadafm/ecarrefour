<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\ArrayHelper;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use multebox\models\ProductCategory;
use multebox\models\ProductBrand;
use multebox\models\ProductAttributes;
use multebox\models\ProductAttributeValues;
use multebox\models\search\MulteModel;

$this->title = Yii::t('app', 'Bulk Upload Inventories');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Inventories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="inventory-bulk-upload">

	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Bulk Create Inventories - Upload Data' ); ?></h3> <h5><?=Yii::t('app', '(Download template for same from manage products section)')?></h5>
		</div>
		<div class="panel-body">
			<div class="col-sm-12">
				<form method="post" enctype="multipart/form-data">
					<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
					<input type="hidden" name="bulk_create_inventory">
					<div class="form-group">
						<input type="file" name="inventory_file">
					</div>
					
					<button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> <?=Yii::t('app', 'Upload')?> </button>
				</form>
			</div>
		</div>
	</div>

	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Bulk Update Inventories - Upload Data' ); ?></h3> <h5><?=Yii::t('app', '(Download template for same from manage inventories section)')?></h5>
		</div>
		<div class="panel-body">
			<div class="col-sm-12">
				<form method="post" enctype="multipart/form-data">
					<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
					<input type="hidden" name="bulk_update_inventory">
					<div class="form-group">
						<input type="file" name="inventory_file">
					</div>
					
					<button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> <?=Yii::t('app', 'Upload')?> </button>
				</form>
			</div>
		</div>
	</div>

</div>

