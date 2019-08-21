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

$this->title = Yii::t('app', 'Bulk Upload Products');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="product-bulk-upload">

	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Bulk Create Products - Reference Template' ); ?></h3>
		</div>
		<div class="panel-body">
			<div class="col-sm-12">

				<p>
					<?=Yii::t('app', 'Download updated template - use this sheet to create products.')?> <?=Yii::t('app', 'This template contains the list of all available Product categories, sub-categories and sub sub-categories that you can easily select from available dropdown list.')?>
				</p>
				<form method="post" enctype="multipart/form-data">
					<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
					<input type="hidden" name="bulk_download">
					
					<button type="submit" class="btn btn-success"><i class="fa fa-download"></i> <?=Yii::t('app', 'Download Template')?> </button>
				</form>

			</div>
		</div>
	</div>

	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Yii::t ( 'app', 'Bulk Create Products - Upload Data' ); ?></h3>
		</div>
		<div class="panel-body">
			<div class="col-sm-12">
				<form method="post" enctype="multipart/form-data">
					<input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
					<input type="hidden" name="bulk_upload">
					<div class="form-group">
						<input type="file" name="product_file">
					</div>
					
					<button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> <?=Yii::t('app', 'Upload')?> </button>
				</form>
			</div>
		</div>
	</div>

</div>

