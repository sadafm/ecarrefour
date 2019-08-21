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

$this->title = Yii::t('app', 'Bulk Upload Product and Inventories');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Inventories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="inventory-bulk-upload">

	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Template' ); ?></h3>
		</div>
		<div class="panel-body">
			<div class="col-sm-12">	
				<a href="<?=Url::base()?>/prd_inv_template.xlsx" target="_blank" class="btn btn-warning"><i class="fa fa-download"></i> <?=Yii::t('app', 'Download')?></a>
			</div>
		</div>
	</div>

	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Upload Data' ); ?></h3>
		</div>
		<div class="panel-body">
			<div class="col-sm-12">
				<form method="post" enctype="multipart/form-data">
					<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
					<input type="hidden" name="bulk_upload_prd_inv">
					<div class="form-group">
						<input type="file" name="prd_inv_file">
					</div>
					
					<button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> <?=Yii::t('app', 'Upload')?> </button>
				</form>
			</div>
		</div>
	</div>

</div>

