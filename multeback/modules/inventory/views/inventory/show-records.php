<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use multebox\models\LicenseKeyCode;
use yii\helpers\ArrayHelper;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\Inventory $searchModel
 */
//$this->title = Yii::t('app', 'License Key Code');
//$this->params['breadcrumbs'][] = $this->title;
?>
   
<div class="licensekeycode-index">
    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
		'responsive' => true,
		'responsiveWrap' => false,
		'pjax' => true,
        'columns' => [
			['class' => '\kartik\grid\CheckboxColumn'],
            ['class' => 'yii\grid\SerialColumn'],
			            
            'license_key_code',
			[
                'attribute'=> 'used',
                'label' => Yii::t('app','Used'),                
                'format'=>'raw',
                'value'=>function ($model, $key, $index, $widget){
					if($model->used == 0)
						return "<span class=\"label label-success\">".Yii::t('app', 'Unused')."</span>";
					else
						return "<span class=\"label label-warning\">".Yii::t('app', 'Used')."</span>";
                }
            ],
	  
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>false,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode(Yii::t('app', 'Existing Records')).' </h3>',
            'type'=>'info',
            'before' => '<form action="" method="post" name="frmx"><input type="hidden" name="_csrf" value="'.$this->renderDynamic('return Yii::$app->request->csrfToken;').'"> <input type="hidden" name="delete_multiple_recs" value="true"> <a href="javascript:void(0)" onClick="delete_mult()" class="btn btn-info btn-sm"><i class="glyphicon glyphicon-trash"></i> ' . Yii::t('app', "Delete Selected") . '</a>',
            'after' => '</form>',
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>
</div>

<script>
	function delete_mult(){
		var r = confirm("<?=Yii::t ('app','Are you Sure!')?>");
		if (r == true) {
			document.frmx.submit()
		} else {
			
		}	
	}
</script>
