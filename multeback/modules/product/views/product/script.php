<?php
use yii\helpers\Url;
?>
<script>
$(document).ready(function(e) {
	$('#product-category_id').change(function(){
	$('#product-sub_category_id').html('<option value=""> --Select--</option>');
	$('#product-sub_subcategory_id').html('<option value=""> --Select--</option>');
    $.post("<?=Url::to(['/product/product/ajax-load-sub-category'])?>", { 'category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#product-sub_category_id').html(result);
				})
	})

	$('#product-sub_category_id').change(function(){
	$('#product-sub_subcategory_id').html('<option value=""> --Select--</option>');
    $.post("<?=Url::to(['/product/product/ajax-load-sub-sub-category'])?>", { 'sub_category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#product-sub_subcategory_id').html(result);
				})
	})

	$('#product-sub_category_id').load("<?=Url::to(['/product/product/ajax-load-sub-category', 'category_id' => $model->category_id, 'sub_category_id' => $model->sub_category_id])?>");

	$('#product-sub_subcategory_id').load("<?=Url::to(['/product/product/ajax-load-sub-sub-category', 'sub_category_id' => $model->sub_category_id, 'sub_subcategory_id' => $model->sub_subcategory_id])?>");


	$('#product-digital').change(function(){
		if($('#product-digital').val() == '1')
		{
			$('#product-license_key_code').removeAttr('disabled');
		}
		else
		{
			$('#product-license_key_code').attr('disabled', 0);
		}
	})
});
</script>