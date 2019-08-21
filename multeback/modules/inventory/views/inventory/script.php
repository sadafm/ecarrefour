<?php
use yii\helpers\Url;
?>
<script>
$(document).ready(function(e) {

	$(".field-inventory-send_as_attachment").hide();
	$(".field-inventory-attachment_file_name").hide();

	$('#category_id').change(function(){
	$('#sub_category_id').html('<option value=""> --Select--</option>');
	$('#sub_subcategory_id').html('<option value=""> --Select--</option>');
	$('#inventory-product_id').html('<option value=""> --Select--</option>');
	$('#mytable').remove();
	$("#inventory-digital_file").attr('type', 'hidden');
	$(".field-inventory-send_as_attachment").hide();
	$(".field-inventory-attachment_file_name").hide();
    $.post("<?=Url::to(['/inventory/inventory/ajax-load-sub-category'])?>", { 'category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#sub_category_id').html(result);
				})
	})

	$('#sub_category_id').change(function(){
	$('#sub_subcategory_id').html('<option value=""> --Select--</option>');
	$('#inventory-product_id').html('<option value=""> --Select--</option>');
	$('#mytable').remove();
	$("#inventory-digital_file").attr('type', 'hidden');
	$(".field-inventory-send_as_attachment").hide();
	$(".field-inventory-attachment_file_name").hide();
    $.post("<?=Url::to(['/inventory/inventory/ajax-load-sub-sub-category'])?>", { 'sub_category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#sub_subcategory_id').html(result);
				})
	})
	
	$('#sub_subcategory_id').change(function(){
	$('#inventory-product_id').html('<option value=""> --Select--</option>');
	$('#mytable').remove();
	$("#inventory-digital_file").attr('type', 'hidden');
	$(".field-inventory-send_as_attachment").hide();
	$(".field-inventory-attachment_file_name").hide();
    $.post("<?=Url::to(['/inventory/inventory/ajax-load-products'])?>", { 'sub_subcategory_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>' }) .done(function(result){
					$('#inventory-product_id').html(result);
				})
	})
});
</script>