<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\User;
use multebox\models\FileModel;
use yii\helpers\ArrayHelper;
?>
    <?php 
	date_default_timezone_set(Yii::$app->params['TIME_ZONE']);

	 Yii::$app->request->enableCsrfValidation = true;
    $csrf=$this->renderDynamic('return Yii::$app->request->csrfToken;');
	Pjax::begin(['id' => 'samle', 'linkSelector' => 'a:not(.target-blank)']); echo GridView::widget([
        'dataProvider' => $dataProviderAttach,
		'toolbar' => false,
		'responsiveWrap' => false,
        //'filterModel' => $searchModelAttch,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[ 
					'attribute' => 'file_title',
					'width' => '20%',
					'format' => 'raw',
					'value' => function ($model, $key, $index, $widget) {
						$icons['.php']='glyphicon glyphicon-file';
						$icons['.txt']='glyphicon glyphicon-file';
						$icons['.xlsx']='fa fa-file-excel-o';
						$icons['.xls']='fa fa-file-excel-o';
						$icons['.gif']='fa fa-image';
						$icons['.png']='fa fa-image';
						$icons['.jpg']='fa fa-image';
						$icons['.jpeg']='fa fa-image';
						$icons['.docx']='fa fa-file-word-o';
						$icons['.doc']='fa fa-file-word-o';
						$iconClass = array_key_exists(strrchr($model->file_name, "."),$icons)?$icons[strrchr($model->file_name, ".")]:'glyphicon glyphicon-file';

						return Html::a('<i class="'.$iconClass.'"></i> '.$model->file_title, Yii::$app->params['web_url']."/".$model->new_file_name, ['target'=>'_blank', 'class' => 'target-blank']);
					
				}, 
			],
			[ 
				'attribute' => 'file_name',
				'width' => '20%' 
			],
			[ 
					'attribute' => 'added_at',
					'label'=>Yii::t('app', 'Added'),
					'width' => '25%' ,
					'format'=>'raw',
					'value' => function ($model, $key, $index, $widget) {
					if($model->added_at !='0') {
						if(strlen($model->added_at) >4){
							return date('jS \of F Y H:i:s',$model->added_at);
						}else{
							return $model->added_at;
						}
					} else{
						return '<i class="not-set">'.Yii::t('app', 'not set').'</i>';
					}
				}
			],

			[ 
					'attribute' => 'added_by_user_id',
					'width' => '25%' ,
					'format'=>'raw',
					'value' => function ($model, $key, $index, $widget) {
					if(isset($model->user)) {
						return $model->user->first_name." ".$model->user->last_name." (".$model->user->username.")";
					}
				}
			],
            [
               'class' => '\kartik\grid\ActionColumn',
    			//'template'=>'{update} {view} {mail} {delete}',
				'template'=>'{view} {mail} {delete}',
                'buttons' => [
				'width' => '100px',

				'view' => function ($url, $model) {
									return Html::a("<span class='glyphicon glyphicon-eye-open'></span>", Yii::$app->params['web_url']."/".$model->new_file_name, ['target'=>'_blank', 'class' => 'target-blank']);
					
				},
				'mail' => function($url,$model){
					 return '<a href="javascript:void(0)" onClick="sendAttachment(\''.$model->file_name.'\',\''.$model->new_file_name.'\')" title="'.Yii::t('app', 'Mail').'"><span class="glyphicon glyphicon-envelope"></span></a>';
				},
				'delete' => function ($url, $model) {
					return '<a href="'. Url::to(['/'.Yii::$app->controller->route, 'id' => $_REQUEST['id'], 'attachment_del_id' => $model->id]) .'" onClick="return get_confirm();" title="'.Yii::t('app', 'Delete attachment').'"><span class="glyphicon glyphicon-trash"></span></a>';
												  }
                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        //'floatHeader'=>true,
        'panel' => [
            'heading'=>'<i class="glyphicon glyphicon-th-list"></i> '.Yii::t('app', 'Attachments'),
            'type'=>'info',
			
			'before'=> '<a href="javascript:void(0)" class="btn btn-primary btn-sm" onClick="$(\'.savepopup\').modal(\'show\');"><i class="fa fa-upload"></i> '.Yii::t('app', 'New Attachment').'</a>',

			'showFooter'=>false
        ],
    ]); Pjax::end(); ?>
	<?php

	/*foreach(FileModel::getAttachmentFiles($entity_type,$_GET['id']) as $row){
		$attachment_files[]="attachments/".$row['id'].strrchr($row['file_name'], ".");
	}
	
	$fileModel = new FileModel();*/
	?>
	<script>

	function formSubmit(id){
		var r = confirm("<?=Yii::t ('app','Are you Sure!')?>");
		if (r == true) {
			$('#'+id).submit()
		} else {
			
		}	
	}
	function get_confirm(){
		return confirm("<?=Yii::t ('app','Are you Sure!')?>");
	}
	</script>