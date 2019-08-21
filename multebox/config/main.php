<?php

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
    '@bower' => '@vendor/bower-asset',
    '@npm'   => '@vendor/npm-asset',
	],
	'language' => 'en-US',
	'modules' => [
			'multeobjects' => [
	            'class' => 'multebox\modules\multeobjects\Module',
	        ],
    ],
    'components' => [
		'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=multicarte',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

		'i18n' => [
					'translations' => [
						'app*' => [
							'class' => 'yii\i18n\PhpMessageSource',
							'basePath' => '@multebox/messages',
							'fileMap' => [
								'app' => 'app.php',
							],
						],
					],
				],
    ],
];