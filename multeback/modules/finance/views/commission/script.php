<?php
use yii\helpers\Url;
?>
<script>
$(document).ready(function(e) {
	$('#commission-category_id').change(function(){
	$('#commission-sub_category_id').html('<option value=""> --Select--</option>');
	$('#commission-sub_subcategory_id').html('<option value=""> --Select--</option>');
    $.post("<?=Url::to(['/product/product/ajax-load-sub-category'])?>", { 'category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#commission-sub_category_id').html(result);
				})
	})

	$('#commission-sub_category_id').change(function(){
	$('#commission-sub_subcategory_id').html('<option value=""> --Select--</option>');
    $.post("<?=Url::to(['/product/product/ajax-load-sub-sub-category'])?>", { 'sub_category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#commission-sub_subcategory_id').html(result);
				})
	})

	$('#commission-sub_category_id').load("<?=Url::to(['/product/product/ajax-load-sub-category', 'category_id' => $model->category_id, 'sub_category_id' => $model->sub_category_id])?>");

	$('#commission-sub_subcategory_id').load("<?=Url::to(['/product/product/ajax-load-sub-sub-category', 'sub_category_id' => $model->sub_category_id, 'sub_subcategory_id' => $model->sub_subcategory_id])?>");
});

</script>