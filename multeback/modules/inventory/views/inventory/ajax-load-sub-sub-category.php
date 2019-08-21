<option value=""> --<?=Yii::t('app', 'Select')?>--</option>

<?php

	foreach($subsubcategories as $row){?>

	<option value="<?=$row['id']?>" <?=$sub_subcategory_id==$row['id']?'selected':''?>><?=$row['name']?></option>	

<?php	}

?>