<?php
use yii\helpers\Url;
?>
<script>
$(document).ready(function(e) {

	$('#category_id').change(function(){
	$('#sub_category_id').html('<option value=""> --Select--</option>');
	$('#sub_subcategory_id').html('<option value=""> --Select--</option>');
	$('#bannerdata-product_id').html('<option value=""> --Select--</option>');
	$('#bannerdata-inventory_id').html('<option value=""> --Select--</option>');
	
    $.post("<?=Url::to(['/inventory/inventory/ajax-load-sub-category'])?>", { 'category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#sub_category_id').html(result);
				})
	})

	$('#sub_category_id').change(function(){
	$('#sub_subcategory_id').html('<option value=""> --Select--</option>');
	$('#bannerdata-product_id').html('<option value=""> --Select--</option>');
	$('#bannerdata-inventory_id').html('<option value=""> --Select--</option>');
	
    $.post("<?=Url::to(['/inventory/inventory/ajax-load-sub-sub-category'])?>", { 'sub_category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#sub_subcategory_id').html(result);
				})
	})
	
	$('#sub_subcategory_id').change(function(){
	$('#bannerdata-product_id').html('<option value=""> --Select--</option>');
	$('#bannerdata-inventory_id').html('<option value=""> --Select--</option>');
	
    $.post("<?=Url::to(['/inventory/inventory/ajax-load-products'])?>", { 'sub_subcategory_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>' }) .done(function(result){
					$('#bannerdata-product_id').html(result);
				})
	})

	$('#bannerdata-product_id').change(function(){
	$('#bannerdata-inventory_id').html('<option value=""> --Select--</option>');
	
    $.post("<?=Url::to(['/inventory/inventory/ajax-load-inventory'])?>", { 'product_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>' }) .done(function(result){
					$('#bannerdata-inventory_id').html(result);
				})
	})

	$('#sub_category_id').load("<?=Url::to(['/inventory/inventory/ajax-load-sub-category', 'category_id' => $model->category_id, 'sub_category_id' => $model->sub_category_id])?>");

	$('#sub_subcategory_id').load("<?=Url::to(['/inventory/inventory/ajax-load-sub-sub-category', 'sub_category_id' => $model->sub_category_id, 'sub_subcategory_id' => $model->sub_subcategory_id])?>");

	$('#bannerdata-product_id').load("<?=Url::to(['/inventory/inventory/ajax-load-products', 'sub_subcategory_id' => $model->sub_subcategory_id, 'product_id' => $model->product_id])?>");

	$('#bannerdata-inventory_id').load("<?=Url::to(['/inventory/inventory/ajax-load-inventory', 'sub_subcategory_id' => $model->sub_subcategory_id, 'product_id' => $model->product_id, 'inventory_id' => $model->inventory_id])?>");
});
</script>