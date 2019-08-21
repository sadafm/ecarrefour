<script>
$(document).ready(function(e) 
{
	if($('#productattributes-fixed').val() =='1')
	{
		$('.field-productattributes-fixed_id').show();
		$('.field-productattributes-name').hide();
	}
	else if($('#productattributes-fixed').val() =='0')
	{
		$('.field-productattributes-fixed_id').hide();
		$('.field-productattributes-name').show();
	}
	else
	{
		$('.field-productattributes-fixed_id').hide();
		$('.field-productattributes-name').hide();
	}

	$('#productattributes-fixed').change(function()
	{
		if($(this).val() =='1')
		{
			$('.field-productattributes-fixed_id').show();
			$('.field-productattributes-name').hide();
		}
		else if($(this).val() =='0')
		{
			$('.field-productattributes-fixed_id').hide();
			$('.field-productattributes-name').show();
		}
		else
		{
			$('.field-productattributes-fixed_id').hide();
			$('.field-productattributes-name').hide();
		}
	})

	$('#w0').submit(function()
	{
		if($('#productattributes-fixed').val() =='1')
		{
			Remove_Error ($('#productattributes-fixed_id'));
			if($('#productattributes-fixed_id').val() == '')
			{
				Add_Error ($('#productattributes-fixed_id'),'<?=Yii::t ('app','This Field is Required!')?>');
				return false;
			}
			else
			{
				Remove_Error ($('#productattributes-fixed_id'));
			}
		}
		else if($('#productattributes-fixed').val() =='0')
		{
			Remove_Error ($('#productattributes-name'));
			if($('#productattributes-name').val() == '')
			{
				Add_Error ($('#productattributes-name'),'<?=Yii::t ('app','This Field is Required!')?>');
				return false;
			}
			else
			{
				Remove_Error ($('#productattributes-name'));
			}
		}
		else
		{
			Remove_Error ($('#productattributes-fixed_id'));
			Remove_Error ($('#productattributes-name'));
		}
	})
});
</script>