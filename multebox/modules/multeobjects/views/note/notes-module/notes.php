<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\User;
use yii\helpers\ArrayHelper;
?>
    <?php 
	date_default_timezone_set(Yii::$app->params['TIME_ZONE']);
//var_dump($dataProviderNotes);
	?>
	<div class="panel panel-info">
    	<div class="panel-heading">
        	<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> <?= Yii::t('app','Notes')?></h3>
        </div>
        <div class="panel-body" style="padding:0">
				<?php
					if($dataProviderNotes){?>
                    
                    <div class="direct-chat-messages">
				<?php
				foreach($dataProviderNotes as $data){?>
					<div class="direct-chat-msg">
						<div class="direct-chat-info clearfix">
							<span class="direct-chat-name pull-left"><?=$data->user->first_name?> <?=$data->user->last_name?></span>
							<span class="direct-chat-timestamp pull-right"><?=date('jS \of F Y H:i:s',$data->added_at)?>
								<a href="javascript:void(0)" onClick="callJs('<?=$data->id?>')"  title="Edit" ><span class="glyphicon glyphicon-pencil"></span></a> 
								<a href="<?=Url::to(['/'.Yii::$app->controller->route, 'id'=> $_REQUEST['id'], 'note_del_id' => $data->id])?>" onClick="return confirm('Are you Sure!')" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>
							</span>
						</div>
						<img class="direct-chat-img" src="<?=Url::base()?>/users/<?=$data->user->id?>.png" alt="" onerror="this.onerror=null;this.src='<?=Url::base()?>/users/nophoto.jpg'">
						<div class="direct-chat-text">
							<?=$data->notes?>
							
						</div>
                    </div>
				<?php
					if(isset($i))
					$i++;
				}  
				?>
                 </div>
				<?php	}?>
                
        </div>
    </div>
    <script>
	function callJs(id){
		//alert(id);
		$.get("<?=Url::to(['/'.Yii::$app->controller->route, 'id' => $_REQUEST['id']])?>", { 'note_id': id}) .done(function(data){ $( "body" ).html(data);});
	}
	function formSubmit(id){
		var r = confirm("<?=Yii::t ('app','Are you Sure!')?>");
		if (r == true) {
			$('#'+id).submit()
		} else {
			
		}	
	}
	</script>