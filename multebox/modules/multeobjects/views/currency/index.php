<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var multebox\models\search\Currency $searchModel
 */
$this->title = Yii::t('app', 'Currencies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-index">
    <!--<div class="page-header">
            <h1><?= Html::encode($this->title) ?></h1>
    </div>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Currency',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>
 
            <?php Yii::$app->request->enableCsrfValidation = true; ?>
    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'responsiveWrap' => false,
'pjax' => true,
        'columns' => [
		['class' => 'yii\grid\SerialColumn'],
			['class' => '\kartik\grid\CheckboxColumn'],
            
            //'id',
								[ 
										'attribute' => 'currency',
										'label' => Yii::t('app', 'Currency'),
										'format' => 'raw',
										//'width' => '50px',
										'value' => function ($model, $key, $index, $widget)
										{
											return ' <a href='. Url::to(['/multeobjects/currency/update', 'id' => $model->id]) .'">'.$model->currency.'</a>';
										} 
								],
           // 'currency',
            'alphabetic_code',
            'numeric_code',
            'minor_unit',
//            'status', 
//            'added_at', 
//            'updated_at', 
            [
                'class' => '\kartik\grid\ActionColumn',
				'template'=>'{update} {view} {delete} {defaultValue}',
                'buttons' => [
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['/multeobjects/currency/update','id' => $model->id]), [
                                                    'title' => Yii::t('app', 'Edit'),
                                                  ]);},
					 'view' => function ($url, $model) {
                                    return '';},
				'defaultValue' => function ($url, $model) {
					if(\multebox\models\DefaultValueModule::checkDefaultValue('currency',$model->id)){
						return Html::a('<span class="fa fa-eraser"></span>', Yii::$app->urlManager->createUrl(['/multeobjects/currency/index','del_id' => $model->id]), [
                                                    'title' => Yii::t('app', 'Delete Default'),
                                                  ]);
					}else{
						return Html::a('<span class="fa fa-tag"></span>', Yii::$app->urlManager->createUrl(['/multeobjects/currency/index','id' => $model->id]), [
                                                    'title' => Yii::t('app', 'Make Default'),
                                                  ]);
					}
                                    }
                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>false,


        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Yii::t('app', Html::encode($this->title)).' </h3>',
            'type'=>'info',
            'before' => '<form action="" method="post" name="frm"><input type="hidden" name="_csrf" value="'.$this->renderDynamic('return Yii::$app->request->csrfToken;').'"> <input type="hidden" name="multiple_del" value="true">'.Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Add'), [
                'create'
            ], [
                'class' => 'btn btn-success btn-sm'
            ]) . ' <a href="javascript:void(0)" onClick="all_del()" class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-trash"></i> ' . Yii::t('app', "Delete Selected") . '</a>',
            'after' => '</form>'.Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('app', 'Reset List'), [
                'index'
            ], [
                'class' => 'btn btn-info btn-sm'
            ]),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>
</div>
<script>
	function all_del(){
		var r = confirm("<?=Yii::t ('app','Are you Sure!')?>");
		if (r == true) {
			document.frm.submit()
		} else {
			
		}	
	}
</script>