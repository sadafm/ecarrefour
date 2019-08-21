<?php
use yii\helpers\Url;
use multebox\models\Vendor;
use multebox\models\VendorReview;
?>

<div id="container">
    <div class="container">
      <!-- Breadcrumb Start-->
      <ul class="breadcrumb">
        <li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
        <li><a href="<?=Url::to(['/order/default/history'])?>"><?=Yii::t('app', 'Order History')?></a></li>
        <li><?=Yii::t('app', 'Vendor Review')?></li>
      </ul>
      <!-- Breadcrumb End-->
      <div class="row">
       <div id="content" class="col-sm-12">
          <h1 class="title"><?=Yii::t('app', 'Vendor Review')?></h1>
		  <form method="post" id="checkoutform" enctype="multipart/form-data">
		  <input type="hidden" name="_csrf" value="<?php echo $this->renderDynamic('return Yii::$app->request->csrfToken;'); ?>">
		  <?php
		  foreach($vendors as $row)
		  {
			  $vendor = Vendor::findOne($row->vendor_id);
			  $review = VendorReview::find()->where("vendor_id=".$row->vendor_id." and customer_id=".Yii::$app->user->identity->entity_id)->one();
		  ?>
          <div class="row">

            <div class="col-sm-12">
			  <div class="panel panel-default">
                <div class="panel-heading">
                  <h4 class="panel-title"><i class="fa fa-edit"></i> <?=$vendor->vendor_name?></h4>
                </div>
                <!--<div class="panel-body">-->
				  <table class="table table-bordered table-hover">
				    <div class="col-sm-12">
					  <tr>
					    <td>

						  <div class="col-sm-3">
						  
							<?=Yii::t('app', 'Rating')?>:<br>
							<input type="text" class="multe-rating" name="vendorrating[]" value="<?=$review->rating?>">
						  
						  </div>

						  <div class="col-sm-9">
						  <?=Yii::t('app', 'Review')?>:<br>
							<textarea class="form-control" rows="6" name="vendorreview[]" style="resize:none"><?=$review->review?></textarea>
						  
						  </div>
						  
						  <input type="hidden" name="vendorid[]" value="<?=$vendor->id?>">
						</td>
					  </tr>

					</div>
				  </table>
				<!--</div>-->
			  </div>
			</div>

		  </div> <!-- End row-->
		  <?php
		  }
		  ?>
		  <div class="pull-right">
			<button type="submit" class="btn btn-primary" id="submit-review"><?=Yii::t('app', 'Submit')?></button>
		  </div>
		  </form>
	   </div>
	 </div>
  </div>
</div>