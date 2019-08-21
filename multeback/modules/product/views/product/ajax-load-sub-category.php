<option value=""> --<?=Yii::t('app', 'Select')?>--</option>

<?php

	foreach($subcategories as $row){?>

	<option value="<?=$row['id']?>" <?=$sub_category_id==$row['id']?'selected':''?>><?=$row['name']?></option>	

<?php	}

?>