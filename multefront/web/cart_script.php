<?php
use yii\helpers\Url;
?>

<style>
.modal.in .modal-dialog 
{
    -webkit-transform: translate(0, calc(50vh - 50%));
    -ms-transform: translate(0, 50vh) translate(0, -50%);
    -o-transform: translate(0, calc(50vh - 50%));
    transform: translate(0, 50vh) translate(0, -50%);
}
</style>

<script type="text/javascript" src="<?=Url::base()?>/js/jquery-2.1.1.min.js"></script>
<script>
$(document).ready(function(e) {
	$(document).on('click', '.addtocart', function () {
        var cart = $('.mycart');
        var imgtodrag = $(this).parent().parent('.product-thumbnail').find("img").eq(0);
        if (imgtodrag) {
            var imgclone = imgtodrag.clone()
                .offset({
                top: imgtodrag.offset().top,
                left: imgtodrag.offset().left
            })
                .css({
                'opacity': '0.5',
                    'position': 'absolute',
                    'height': '150px',
                    'width': '150px',
                    'z-index': '100'
            })
                .appendTo($('body'))
                .animate({
                'top': cart.offset().top + 10,
                    'left': cart.offset().left + 10,
                    'width': 75,
                    'height': 75
            }, 1000, 'easeInOutExpo');
            
           

            imgclone.animate({
                'width': 0,
                    'height': 0
            }, function () {
                $(this).detach()
            });
        }

		 $.post("<?=Url::to(['/order/default/ajax-add-to-cart'])?>", { 'inventory_id': $(this).val(), 'total_items' : '1', '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
								$('.mini-products-list').html(result);
								$('.cartcount').html($('.hiddencartvalue').val() + ' ' + '<?=Yii::t('app', 'Item(s)')?>');
								$('.confirmmodal').modal('show');
								setTimeout(function() {$('.confirmmodal').modal('hide');}, 1500);
				})
    });

	$('.addtocartmini').on('click', function () {
        var cart = $('.mycart');
        var imgtodrag = $(this).closest('.jtv-product').find("img").eq(0);
        if (imgtodrag) {
            var imgclone = imgtodrag.clone()
                .offset({
                top: imgtodrag.offset().top,
                left: imgtodrag.offset().left
            })
                .css({
                'opacity': '0.5',
                    'position': 'absolute',
                    'height': '150px',
                    'width': '150px',
                    'z-index': '100'
            })
                .appendTo($('body'))
                .animate({
                'top': cart.offset().top + 10,
                    'left': cart.offset().left + 10,
                    'width': 75,
                    'height': 75
            }, 1000, 'easeInOutExpo');
            
           

            imgclone.animate({
                'width': 0,
                    'height': 0
            }, function () {
                $(this).detach()
            });
        }

		$.post("<?=Url::to(['/order/default/ajax-add-to-cart'])?>", { 'inventory_id': $(this).val(), 'total_items' : '1', '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
								$('.mini-products-list').html(result);
								$('.cartcount').html($('.hiddencartvalue').val() + ' ' + '<?=Yii::t('app', 'Item(s)')?>');
								$('.confirmmodal').modal('show');
								setTimeout(function() {$('.confirmmodal').modal('hide');}, 1500);
				})
	});

	$('.wish-add-to-cart').on('click', function () {
        var cart = $('.mycart');
        var imgtodrag = $(this).closest('tr').find("img").eq(0);
        if (imgtodrag) {
            var imgclone = imgtodrag.clone()
                .offset({
                top: imgtodrag.offset().top,
                left: imgtodrag.offset().left
            })
                .css({
                'opacity': '0.5',
                    'position': 'absolute',
                    'height': '150px',
                    'width': '150px',
                    'z-index': '100'
            })
                .appendTo($('body'))
                .animate({
                'top': cart.offset().top + 10,
                    'left': cart.offset().left + 10,
                    'width': 75,
                    'height': 75
            }, 1000, 'easeInOutExpo');
            
           

            imgclone.animate({
                'width': 0,
                    'height': 0
            }, function () {
                $(this).detach()
            });
        }

		$.post("<?=Url::to(['/order/default/ajax-add-to-cart'])?>", { 'inventory_id': $(this).val(), 'total_items' : '1', 'wish' : 'true', '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
								$('.mini-products-list').html(result);
								$('.cartcount').html($('.hiddencartvalue').val() + ' ' + '<?=Yii::t('app', 'Item(s)')?>');
								$('.confirmmodal').modal('show');
								setTimeout(function() {$('.confirmmodal').modal('hide');}, 1500);
								setTimeout(function() {location.reload();}, 1500);
				})
	});

	$('.compare-add-to-cart').on('click', function () {
        var cart = $('.mycart');
        var imgtodrag = $(this).closest('table').find("img").eq(0);
        if (imgtodrag) {
            var imgclone = imgtodrag.clone()
                .offset({
                top: imgtodrag.offset().top,
                left: imgtodrag.offset().left
            })
                .css({
                'opacity': '0.5',
                    'position': 'absolute',
                    'height': '150px',
                    'width': '150px',
                    'z-index': '100'
            })
                .appendTo($('body'))
                .animate({
                'top': cart.offset().top + 10,
                    'left': cart.offset().left + 10,
                    'width': 75,
                    'height': 75
            }, 1000, 'easeInOutExpo');
            
           

            imgclone.animate({
                'width': 0,
                    'height': 0
            }, function () {
                $(this).detach()
            });
        }

		$.post("<?=Url::to(['/order/default/ajax-add-to-cart'])?>", { 'inventory_id': $(this).val(), 'total_items' : '1', '_csrf' : '<?=Yii::$app->request->csrfToken?>'}) .done(function(result){
								$('.mini-products-list').html(result);
								$('.cartcount').html($('.hiddencartvalue').val() + ' ' + '<?=Yii::t('app', 'Item(s)')?>');
								$('.confirmmodal').modal('show');
								setTimeout(function() {$('.confirmmodal').modal('hide');}, 1500);
								setTimeout(function() {location.reload();}, 1500);
				})
	});
});
</script>


<div class="modal fade confirmmodal" >
  <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-body">
          <h4><p class="text-center"><?=Yii::t('app', 'Added')?>! <i class="glyphicon glyphicon-ok text-success"></i></p></h4>
        </div>
      </div>
  </div>
</div>