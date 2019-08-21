<option value=""> --<?=Yii::t('app', 'Select')?>--</option>

<?php

	foreach($inventories as $row){?>

	<option value="<?=$row['id']?>" <?=$inventory_id==$row['id']?'selected':''?>><?=$row['product_name']?> (<?=$row['attribute_values']!=''?$row['attribute_values']:'No Attributes'?>)</option>	

<?php	}

?>