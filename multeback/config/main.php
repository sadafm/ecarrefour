<?php

use kartik\mpdf\Pdf;
$params = array_merge(
    require(__DIR__ . '/../../multebox/config/params.php'),
    require(__DIR__ . '/../../multebox/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-multeback',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'multeback\controllers',
    'bootstrap' => ['log'],
    'modules' => [
		'gii' => [
		    'class' => 'yii\gii\Module',
		    'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '*'] // adjust this to your needs
		],

		'gridview' => [
            'class' => 'kartik\grid\Module',
        ],
		'vendor' => [
            'class' => 'multeback\modules\vendor\Module',
        ],
        'user' => [
            'class' => 'multeback\modules\user\Module',
        ],
		'product' => [
            'class' => 'multeback\modules\product\Module',
        ],
		'inventory' => [
            'class' => 'multeback\modules\inventory\Module',
        ],
		'finance' => [
            'class' => 'multeback\modules\finance\Module',
        ],
		'customer' => [
            'class' => 'multeback\modules\customer\Module',
        ],
		'order' => [
            'class' => 'multeback\modules\order\Module',
        ],
		'support' => [
            'class' => 'multeback\modules\support\Module',
        ],
		'bulk' => [
            'class' => 'multeback\modules\bulk\Module',
        ],
	],
    'components' => [
	 'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '_W9DJsw87u8W4cyNT65kPjRG82HxcbYT',
			//'csrfParam' => '_multebackCSRF',
			/*'csrfCookie' => [
                    'name' => '_csrf-multeback',
                    'path'=> sys_get_temp_dir(), 
                    'httpOnly' => true,
                ],*/
        ],
        'user' => [
            'identityClass' => 'multebox\models\User',
            'enableAutoLogin' => true,
			'identityCookie' => ['name' => '_identity-multeback', 'httpOnly' => true],
            'authTimeout' => 24*60*60, 
        ],
		 'session' => [
            'name' => 'advanced-multeback',
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
		'pdf' => [
			'class' => Pdf::classname(),
			'format' => Pdf::FORMAT_A4,
			'orientation' => Pdf::ORIENT_PORTRAIT,
			'destination' => Pdf::DEST_BROWSER,
			// refer settings section for all configuration options
		],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
		'authManager'=>[
			'class' => 'yii\rbac\DbManager',
			'defaultRoles' =>['guest'],
		],
		'as access' => [
			'class' => 'mdm\admin\components\AccessControl',
			'allowActions' => [
				'site/*', // add or remove allowed actions to this list
			]
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
								'rights' => 'multeobjects/setting/rights',
							],
						],
    ],
	'as beforeRequest' => [  //if guest user access site so, redirect to login page.
        'class' => 'yii\filters\AccessControl',
        'rules' => [
            [
                'actions' => ['login', 'error', 'request-password-reset', 'reset-password', 'register'],
                'allow' => true,
            ],
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'params' => $params,
];