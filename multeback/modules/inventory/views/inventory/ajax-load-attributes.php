<?php
use yii\helpers\Json;
?>
<!--<html>
	<body>-->
		<table class="table attribute-values-table" id="mytable">
			<thead>
				<tr>
				<?php
				if($attributes)
					$cnt = count($attributes);
				else
					$cnt = 0;
				$width = floor(100/($cnt+4));
				$attribute_ids = [];
				for($i=0;$i < $cnt; $i++)
				{
					$attribute_ids[$i] = $attributes[$i]['id'];
				?>
					<th style="text-align:left" width="<?=$width?>%"></th>
				<?php
				}
				?>
				</tr>
			</thead>
			
			<tbody>
				<tr>
				<td>
					<label class="control-label"><?=Yii::t('app', 'Price type')?></label>
					<div class="form-group">
						<select class="form-control" name="Inventory[price_type]" aria-required="true" data-validation="required" mandatory-field>
							<option value="">--<?=Yii::t('app', 'select')?>--</option>
							<option value="F"><?=Yii::t('app', 'Fixed')?></option>
							<option value="B"><?=Yii::t('app', 'Base')?></option>
						</select>
					</div>
					<br/>
					<label class="control-label"><?=Yii::t('app', 'Price')?></label>
					<div class="form-group">
						<input type="text" class="form-control" name="Inventory[price]" value="0" placeholder="Enter Value..." aria-required="true" data-validation="required" mandatory-field num-validation-float>
					</div>
				</td>
				<?php
				$i = 0;
				$j = 0;
				foreach($attributes as $row)
				{
				?>
					<td>
					<?php
					if($row['fixed'] == 0)
					{
					?>
						<label class="control-label"><?=$row['name']?></label>
						<div class="form-group">
							<input type="text" class="form-control" name="Inventory[attribute_values][]" placeholder="Enter Value..." aria-required="true" data-validation="required" mandatory-field>
						</div>
						<br/>
						<label class="control-label"><?=Yii::t('app', 'Price')?></label>
						<div class="form-group">
							<input type="text" class="form-control" name="Inventory[attribute_price][]" value="0" placeholder="Enter Price..." aria-required="true" data-validation="required" mandatory-field num-validation-float>
						</div>
					<?php
					}
					else
					{
					?>
						<label class="control-label"><?=$attributeValues[$j]['name']?></label>
						<div class="form-group">
							<select class="form-control" name="Inventory[attribute_values][]" aria-required="true" data-validation="required" mandatory-field>
								<option value="">--<?=Yii::t('app', 'select')?>--</option>
							<?php
							foreach(Json::decode($attributeValues[$j]['values']) as $vrow)
							{
							?>
								<option value="<?=htmlspecialchars($vrow)?>"><?=$vrow?></option>
							<?php
							}
							?>
							</select>
						</div>
						<br/>
						<label class="control-label"><?=Yii::t('app', 'Price')?></label>
						<div class="form-group">
							<input type="text" class="form-control" name="Inventory[attribute_price][]" value="0" placeholder="Enter Price..." aria-required="true" data-validation="required" mandatory-field num-validation-float>
						</div>
					<?php
						$j++;
					}
					?>
					</td>
				<?php
					$i++;
				}
				?>
					<td>
						<label class="control-label"><?=Yii::t('app', 'Stock')?></label>
						<div class="form-group">
						<?php
						if($stock_ind == 0)
						{
						?>
							<input type="text" class="form-control" name="Inventory[stock]" value="0" disabled placeholder="Enter Value..." aria-required="true" data-validation="required" mandatory-field num-validation>
						<?php
						}
						else
						{
						?>
							<input type="text" class="form-control" name="Inventory[stock]" value="0" placeholder="Enter Value..." aria-required="true" data-validation="required" mandatory-field num-validation>
						<?php
						}
						?>
						</div>
					</td>

					<td>
						<label class="control-label"><?=Yii::t('app', 'Discount type')?></label>
						<div class="form-group">
							<select class="form-control" name="Inventory[discount_type] aria-required="true" data-validation="required" mandatory-field>
								<option value="">--<?=Yii::t('app', 'select')?>--</option>
								<option value="F"><?=Yii::t('app', 'Flat')?></option>
								<option value="P"><?=Yii::t('app', 'Percent')?></option>
							</select>
						</div>
						<br/>
						<label class="control-label"><?=Yii::t('app', 'Discount')?></label>
						<div class="form-group">
							<input type="text" class="form-control" name="Inventory[discount]" value="0" placeholder="Enter Value..." aria-required="true" data-validation="required" mandatory-field num-validation-float>
						</div>
					</td>

					<td>
						<label class="control-label"><?=Yii::t('app', 'Shipping Cost')?></label>
						<div class="form-group">
							<input type="text" class="form-control" name="Inventory[shipping_cost]" value="0" placeholder="Enter Value..." aria-required="true" data-validation="required" mandatory-field num-validation-float>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="<?=$cnt+4?>"> 
					  <label class="control-label"><?=Yii::t('app', 'Search Tags (Enter Comma Separated Values)')?></label>
					  <textarea class="form-control" rows="2" name="inventory_tags" maxlength="512" placeholder="Enter search tags..." style="resize:none"></textarea>
					  <label class="control-label"><?=Yii::t('app', 'Max Length: 512 Characters')?></label>
					</td>
				</tr>

				<tr>
					<td>
						<label class="control-label"><?=Yii::t('app', 'Item Length')?></label>
						<div class="form-group">
							<input type="text" class="form-control" name="Inventory[length]" value="0" placeholder="Enter Value..." aria-required="true" data-validation="required" num-validation-float>
						</div>
					</td>
					<td>
						<label class="control-label"><?=Yii::t('app', 'Item Width')?></label>
						<div class="form-group">
							<input type="text" class="form-control" name="Inventory[width]" value="0" placeholder="Enter Value..." aria-required="true" data-validation="required" num-validation-float>
						</div>
					</td>
					<td>
						<label class="control-label"><?=Yii::t('app', 'Item Height')?></label>
						<div class="form-group">
							<input type="text" class="form-control" name="Inventory[height]" value="0" placeholder="Enter Value..." aria-required="true" data-validation="required" num-validation-float>
						</div>
					</td>
					<td>
						<label class="control-label"><?=Yii::t('app', 'Item Weight')?></label>
						<div class="form-group">
							<input type="text" class="form-control" name="Inventory[weight]" value="0" placeholder="Enter Value..." aria-required="true" data-validation="required" num-validation-float>
						</div>
					</td>
				</tr>

				<tr>
					<td colspan="<?=$cnt+4?>"> 
					  <label class="control-label"><?=Yii::t('app', 'Warranty Information')?></label>
					  <input type="text" class="form-control" name="Inventory[warranty]" placeholder="Enter Warranty..." aria-required="true">
					</td>
				</tr>
				
			</tbody>
		</table>

		<input type="hidden" name="attribute_ids" value='<?=Json::encode($attribute_ids)?>'>
	<!--</body>
</html>-->