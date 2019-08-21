<?php

namespace multefront\assets;

use yii\web\AssetBundle;

/**
 * Main multefront application asset bundle.
 */
class AppAssetRTL extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/bootstrap.min.css',
		'css/bootstrap-rtl.css',
		'css/font-awesome.css',
		'css/simple-line-icons.css',
		'css/owl.carousel.css',
		'css/owl.theme.css',
		'css/owl.transitions.css',
		'css/owl.carousel.rtl.css',
		'css/animate.css',
		'css/flexslider.css',
		'css/jquery-ui.css',
		'css/revolution-slider.css',
		'css/jPages.css',
		'css/star-rating.css',
		'css/style.css',
		'css/rtl.css',
    ];
    public $js = [
		'js/jquery.min.js',
		'js/owl.carousel.min.js',
		'js/owl.carousel.rtl.js',
		'js/jquery.bxslider.js',
		'js/jquery.flexslider.js',
		'js/revolution-slider.js',
		'js/megamenu.js',
		'js/mobile-menu-rtl.js',
		'js/jquery-ui.min.js',
		'js/bootstrap.min.js',
		'js/main.js',
		'js/countdown.js',
		'js/jPages.js',
		'js/lazyload.js',
		'js/star-rating.js',
		'js/cloud-zoom.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
