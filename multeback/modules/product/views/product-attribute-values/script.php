<?php
ob_start();
?>
<tr>
	<td>
		<input type="hidden" name="detail_id[]" value="">
		<button type="button" class="rowRemove btn btn-danger" ><span class="fa fa-times"></span></button>
	</td>
	<td>
		<div class="form-group">
			<input type="text" name="attribute_value[]" class="form-control attribute_value" data-validation="required" mandatory-field value="">
		</div>
	</td>
</tr>
<?php
$html = ob_get_clean();
//$html = str_replace(PHP_EOL, '', $html);
$html = str_replace("\r\n", '', $html);
$html = str_replace("\n", '', $html);
?>
<script>
	$(function(){
		//Disabled First Row in Update case
		if('<?= isset($_GET['id'])?'yes':'no'?>' =='yes'){
			if($('#mytable tbody tr').length =='1'){
				$('.rowRemove').attr('disabled',true);	
			}
		}
		$('.addrow').click(function(){
			$('#mytable tbody tr:last').after('<?= $html ?>');
			if('<?= isset($_GET['id'])?'yes':'no'?>' =='yes'){
				if($('#mytable tbody tr').length =='1'){
					$('.rowRemove').attr('disabled',true);	
				}else{
					$('.rowRemove').removeAttr('disabled');	
				}
			}
		});
		
		$(document).on("click", ".rowRemove", function (e) {
			var target = e.target;
			$(target).closest('tr').remove();
			if('<?= isset($_GET['id'])?'yes':'no'?>' =='yes'){
				if($('#mytable tbody tr').length =='1'){
					$('.rowRemove').attr('disabled',true);	
				}else{
					$('.rowRemove').removeAttr('disabled');	
				}
			}
		});
	})
	
	
</script>