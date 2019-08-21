<option value=""> --<?=Yii::t('app', 'Select')?>--</option>

<?php

	foreach($products as $row){?>

	<option value="<?=$row['id']?>" <?=$product_id==$row['id']?'selected':''?>><?=$row['name']?></option>	

<?php	}

?>