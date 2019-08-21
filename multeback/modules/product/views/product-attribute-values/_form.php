<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var multebox\models\ProductAttributeValues $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<style>
.title_block {
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
    margin-bottom: 5px;
}
</style>
<div class="product-attribute-values-form">

    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'name' => [
						'type' => Form::INPUT_TEXT, 
						'label' => Yii::t('app', 'Product Attribute Name'),
						'options' => [
										'placeholder' => Yii::t('app', 'Enter Name...'), 
										'maxlength' => 255
									]
					],

            //'values' => ['type' => Form::INPUT_TEXTAREA, 'options' => ['placeholder' => 'Enter Values...','rows' => 6]],

           // 'added_at' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Added At...']],

           // 'updated_at' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Updated At...']],

        ]

    ]);
	?>
	<div class="table-responsive m-t">
        <input type="hidden" class="del_detail" name="del_detail">
		<table class="table attribute-values-table" id="mytable">
			<thead>
				<tr>
					<th style="text-align:left" width="5%"><?= Yii::t('app','Value')?></th>
					<th style="text-align:left" width="20%"></th>
					<th style="text-align:left" width="75%"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($ProductAttributeValues && count($ProductAttributeValues) > 0)
				{
					foreach($ProductAttributeValues as $value)
					{
					?>
					<tr>
						<td>
							<input type="hidden" name="detail_id[]" value="">
							<button type="button" class="rowRemove btn btn-danger" ><span class="fa fa-times"></span></button>
						</td>
						<td>
							<div class="form-group">
								<input type="text" name="attribute_value[]" class="form-control attribute_value" data-validation="required" mandatory-field value="<?=htmlspecialchars($value)?>">
							</div>
						</td>
					</tr>
					<?php
					}
				}
				else
				{
				?>
					<tr>
						<td>
							<input type="hidden" name="detail_id[]" value="">
							<button type="button" disabled class="rowRemove btn btn-danger" ><span class="fa fa-times"></span></button>
						</td>
						<td>
							<div class="form-group">
								<input type="text" name="attribute_value[]" class="form-control attribute_value" data-validation="required" mandatory-field value="">
							</div>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>

	<div class="row">
		<div  class="pull-left">
			<div class="col-sm-12">
				<input type="button" class="addrow btn btn-primary btn-sm" value="Add Value" />
			 </div>
		 </div>
	</div>
	
	<div class="title_block">
	</div>
	<br/>
	<?php
	if($model->isNewRecord)
	{
	?>
		<input type="hidden" name="ProductAttributeValues[added_by_id]" class="form-control" value="<?=Yii::$app->user->identity->id?>">
	<?php
	}
    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    );
    ActiveForm::end(); ?>

</div>
