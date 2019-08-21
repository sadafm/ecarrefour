<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\Country;
use multebox\models\State;
use multebox\models\City;
use multebox\models\search\UserType as UserTypeSearch;
use yii\helpers\ArrayHelper;

function isPrimary($id){
	$sql="select * from  tbl_contact  where id='$id' and is_primary=1"; 
	$connection = \Yii::$app->db;
	$command=$connection->createCommand($sql);
	$contact=$command->queryAll();
	return $contact?count($contact):0;
}

?>
						
    <?php 
	 Yii::$app->request->enableCsrfValidation = true;
    $csrf=$this->renderDynamic('return Yii::$app->request->csrfToken;');
	Pjax::begin(); 
	
	echo GridView::widget([
        'dataProvider' => $dataProviderContacts,
		'responsiveWrap' => false,
		'toolbar' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			
			[ 
					'attribute' => 'first_name',
					'format' => 'raw'
			],
			[ 
					'attribute' => 'last_name'
			],
			[ 
					'attribute' => 'email'
			],
			[ 
					'attribute' => 'phone'
			],
			[ 
					'attribute' => 'mobile',
					'width' => '10%',
			],
			[ 
					'attribute' => 'fax',
					'width' => '10%',
			],
			
			[ 
					'label'=>Yii::t('app','Is Primary'),
					'width' => '10%',
					'attribute' => 'id',
					'format'=>'raw',
					'value'=>function($model){
						if(isPrimary($model->id)){
							return '<span class="label label-primary">'.Yii::t('app','Primary').'</span>	';
						}else{
							return '<span class="label label-danger">'.Yii::t('app','Secondary').'</span>	';
						}
					}
			],
			
			
			 
            [
               'class' => '\kartik\grid\ActionColumn',
				'width' => '10%',
    			'template'=>'{update}  {delete} {primary}',
                'buttons' => [
				'width' => '100px',
								'update' => function ($url, $model)
								{
									 return '<a href="'. Url::to(['/'.Yii::$app->controller->route, 'id' => $_REQUEST['id'], 'contact_edit' => $model->id]) .'" onClick="return load_contact();" title="'.Yii::t('app', 'Delete').'"><span class="glyphicon glyphicon-pencil"></span></a>';										
								} ,
              
				
								'delete' => function ($url, $model)
								{
									
									if(isPrimary($model->id)){
										
										return '';
									} else {
										
										return '<a href="' . Url::to(['/'.Yii::$app->controller->route, 'id' => $_REQUEST['id'], 'contact_del' => $model->id]) .'" onClick="return get_confirm();" title="'.Yii::t('app', 'Delete').'"><span class="glyphicon glyphicon-trash"></span></a>';
									}
								},

												  'primary'=>function($url,$model){
														  if(!isPrimary($model->id)){
															   return '<a href="'. Url::to(['/'.Yii::$app->controller->route, 'id' => $_REQUEST['id'], 'primary' => $model->id]) .'" onClick="return get_confirm();" title="'.Yii::t('app', 'Make Primary').'"><span class="fa fa-diamond"></span></a>';
														  }else{
															 return ''; 
														  }
												  }
				
                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Yii::t('app', 'Contacts').'</h3>',
            'type'=>'info',
            'before'=>'<form action="" method="post" name="frm">
            <input type="hidden" name="_csrf" value="'.$csrf.'">
            <input type="hidden" name="make_users" value="true">
            <a href="javascript:void(0)" class="btn btn-success btn-sm" onClick="$(\'.contactae\').modal(\'show\');"><i class="glyphicon glyphicon-phone"></i>'.Yii::t('app', 'New Contact').' </a> '.$btn, 
            
            'after' => '</form>',
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>
     <script>
	 	function create_users(){
			if($('.con_ids').is(":checked")){
				var r = confirm("<?=Yii::t ('app','Are you Sure!')?>");
					if (r == true) {
						document.frm.submit()
					} else { }	
			}else{
				alert("<?=Yii::t ('app','Please Select Row')?>");
			}
		
	
		}
		function get_confirm(){
		return confirm("<?=Yii::t ('app','Are you Sure!')?>");
	}
	function load_contact(){
		return window.location.reload(true);
	}
	 </script>