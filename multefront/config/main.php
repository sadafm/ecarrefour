<?php

$params = array_merge(
    require(__DIR__ . '/../../multebox/config/params.php'),
    require(__DIR__ . '/../../multebox/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-multefront',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'multefront\controllers',
    'bootstrap' => ['log'],
    'modules' => [
		'product' => [
            'class' => 'multefront\modules\product\Module',
			],
		'order' => [
            'class' => 'multefront\modules\order\Module',
			],
		'customer' => [
            'class' => 'multefront\modules\customer\Module',
			],
		'review' => [
            'class' => 'multefront\modules\review\Module',
			],
		],
    'components' => [
        'request' => [
			//'cookieValidationKey' => '_R8DJsw17u8V4cyYU65kLjR67GTBcbIO',
           // 'csrfParam' => '_csrf-multefront',
		   /*'csrfCookie' => [
                    'name' => '_csrf-multefront',
                    'path'=> sys_get_temp_dir(), 
                    'httpOnly' => true,
                ],*/
        ],
        'user' => [
            'identityClass' => 'multebox\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-multefront', 'httpOnly' => true],
        ],
		 'session' => [
            'name' => 'advanced-multefront',
            'savePath' => sys_get_temp_dir(),
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
							'class' => 'yii\web\UrlManager',
							// Hide index.php
							'showScriptName' => false,
							// Use pretty URLs
							'enablePrettyUrl' => true,
							'rules' => [
								'home' => 'site/index',
								'login' => 'site/login',
								'product/detail/<inventory_id:\d+>' => 'product/default/detail',
								'product/detail/' => 'product/default/detail',
								'product/listing/<category_id:\d+>/<sub_category_id:\d+>/<sub_subcategory_id:\d+>' => 'product/default/listing',
								'product/listing/<category_id:\d+>/<sub_category_id:\d+>' => 'product/default/listing',
								'product/listing/<category_id:\d+>' => 'product/default/listing',
								'product/listing/' => 'product/default/listing',
							],
		],
    ],
	'as beforeRequest' => [  //if guest user access site so, redirect to login page.
        'class' => 'yii\filters\AccessControl',
        'rules' => [
					[
                        'actions' => ['account', 'information', 'history'],
                        'allow' => false,
						'roles' => ['?'],
                    ],
					[
                        'allow' => true,
						'roles' => ['?', '@'],
                    ],
        ],
    ],
    'params' => $params,
];
