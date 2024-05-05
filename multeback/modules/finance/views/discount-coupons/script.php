<?php
use yii\helpers\Url;
?>
<script>
$(document).ready(function(e) {
	$('#discountcoupons-category_id').change(function(){
	$('#discountcoupons-sub_category_id').html('<option value=""> --Select--</option>');
	$('#discountcoupons-sub_subcategory_id').html('<option value=""> --Select--</option>');
    $.post("<?=Url::to(['/product/product/ajax-load-sub-category'])?>", { 'category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#discountcoupons-sub_category_id').html(result);
				})
	})

	$('#discountcoupons-sub_category_id').change(function(){
	$('#discountcoupons-sub_subcategory_id').html('<option value=""> --Select--</option>');
    $.post("<?=Url::to(['/product/product/ajax-load-sub-sub-category'])?>", { 'sub_category_id': $(this).val(), '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
					$('#discountcoupons-sub_subcategory_id').html(result);
				})
	})

	$('#discountcoupons-sub_category_id').load("<?=Url::to(['/product/product/ajax-load-sub-category', 'category_id' => $model->category_id, 'sub_category_id' => $model->sub_category_id])?>");

	$('#discountcoupons-sub_subcategory_id').load("<?=Url::to(['/product/product/ajax-load-sub-sub-category', 'sub_category_id' => $model->sub_category_id, 'sub_subcategory_id' => $model->sub_subcategory_id])?>");
});
</script>