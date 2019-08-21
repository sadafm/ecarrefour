<?php
function darken_color($rgb, $darker=1.2) {

    $hash = (strpos($rgb, '#') !== false) ? '#' : '';
    $rgb = (strlen($rgb) == 7) ? str_replace('#', '', $rgb) : ((strlen($rgb) == 6) ? $rgb : false);
    if(strlen($rgb) != 6) return $hash.'000000';
    $darker = ($darker > 1) ? $darker : 1;

    list($R16,$G16,$B16) = str_split($rgb,2);

    $R = sprintf("%02X", floor(hexdec($R16)/$darker));
    $G = sprintf("%02X", floor(hexdec($G16)/$darker));
    $B = sprintf("%02X", floor(hexdec($B16)/$darker));

    return $hash.$R.$G.$B;
}
?>
<style>
a:hover {
	color: <?=$css_color?>
}

.newsletter-popup .close {
	background: <?=$css_color?> none repeat scroll 0 0;
}

#newsletter-form .actions .button-subscribe {
	background-color: <?=$css_color?>;
}

.welcome-info .page-header p em {
	border-bottom: 2px <?=$css_color?> solid;
}

.welcome-info .page-header .text-main {
	color: <?=$css_color?>
}

.header-top a:hover {
	color: <?=$css_color?>;
}

.mini-cart .actions .btn-checkout {
	background: <?=$css_color?>;
}

.mini-cart .actions .view-cart:hover {
	background: <?=$css_color?>;
}

.page-header .text-main {
	color: <?=$css_color?>
}

.hot-deal .title-text {
	background-color: <?=$css_color?>;
}

.jtv-banner-box .button {
	background-color: <?=$css_color?>;
}

.icon-new-label {
	background: <?=$css_color?>;
}

.add-to-cart-mt {
	background: <?=$css_color?>;
}

@media (max-width:1024px){
	.add-to-cart-mt {
	background: <?=$css_color?>;
	}
}

.pr-button .mt-button a:hover {
	background: <?=$css_color?>;
}

.product-item .item-inner .item-info .item-title a:hover {
	color: <?=$css_color?>;
}

.slider-items-products .owl-buttons a:hover {
	background: <?=$css_color?>;
	border: 1px <?=$css_color?> solid
}

.home-testimonials strong.name {
	color: <?=$css_color?>;
}

.our-clients {
	background-color: <?=$css_color?>;
}

.blog-content-jtv a:hover {
	color: <?=$css_color?>;
}

@media (max-width: 1024px) {
		.bottom-banner-img .shop-now-btn {
		background-color: <?=$css_color?>;
		border-color: <?=$css_color?>;
	}
}

.bottom-banner-img:hover .shop-now-btn {
	background-color: <?=$css_color?>;
	border-color: <?=$css_color?>;
}

.jtv-category-area .button-cart button {
	border: 1px solid <?=$css_color?>;
	background-color: <?=$css_color?>;
}

.jtv-category-area .jtv-extra-link a:hover, .jtv-category-area .button-cart button:hover {
	background: <?=$css_color?> none repeat scroll 0 0;
	border-color: <?=$css_color?>;
}

.cat-title::before {
	background: <?=$css_color?>;
}

.totop {
	background: none repeat scroll 0 0 <?=$css_color?>;
}

.footer-newsletter {
	background: <?=$css_color?>;
}

nav {
	background: <?=$css_color?>
}

.mtmegamenu>ul>li.active, .menu-bottom .menu-bottom-dec a {
	background-color: <?=$css_color?>;
}

.mtmegamenu>ul>li.active:hover, .menu-bottom .menu-bottom-dec a:hover {
	border-color: <?=$css_color?>
}

.menu > li > a:hover, .menu > li > a:focus, .menu > li.active > a {
	color: <?=$css_color?>;
}

.mega-menu-category > .nav > li > a:hover, .mega-menu-category > .nav > li > a:focus, .mega-menu-category > .nav > li.active > a {
	background-color: <?=$css_color?>;
}

.box-banner .price-sale {
	color: <?=$css_color?>;
}

.box-banner a:hover {
	color: <?=$css_color?>;
}

.navbar-primary {
	background-color: <?=$css_color?>;
}

.view-mode li.active a {
	color: <?=$css_color?>
}

.pagination-area ul li a.active {
	background: <?=$css_color?>;
	border: 1px solid <?=$css_color?>;
}

.filter-price .ui-slider .ui-slider-handle {
	border: 2px solid <?=$css_color?>;
}

button.button {
	background: <?=$css_color?>;
	border: 2px solid <?=$css_color?>;
}

a.my-button {
	background: <?=$css_color?>;
	border: 2px solid <?=$css_color?>;
}

.category-sidebar .sidebar-title {
	background-color: <?=$css_color?>;
}

.sidebar-bar-title h3 {
	border-bottom: 2px <?=$css_color?> solid;
}

.product-price-range .slider-range-price {
	background: <?=$css_color?>;
}

.product-price-range .slider-range-price .ui-slider-handle {
	background: <?=$css_color?>;
}

.check-box-list label:hover {
	color: <?=$css_color?>
}

.check-box-list input[type="checkbox"]:checked+label span.button {
	background: <?=$css_color?> url("../images/checked.png") no-repeat center center
}
.check-box-list input[type="checkbox"]:checked+label {
	color: <?=$css_color?>
}

.size li a:hover {
	border-color: <?=$css_color?>
}

.popular-tags-area .tag li a:hover {
	background: <?=$css_color?>;
}

.special-product a.link-all {
	background: <?=$css_color?>;
	border: 2px solid <?=$css_color?>;
}

.category-description a.info:hover {
	background: <?=$css_color?>;
}

.products-list .desc a.link-learn {
	color: <?=$css_color?>
}

.product-view-area .flexslider-thumb .flex-direction-nav a:hover {
	background-color: <?=$css_color?>;
}

.dec.qtybutton:hover, .inc.qtybutton:hover {
	background-color: <?=$css_color?>;
}

button.button.pro-add-to-cart {
	background: <?=$css_color?>;
	border: 2px <?=$css_color?> solid;
}

.product-cart-option ul li a:hover, .product-cart-option ul li a:hover i {
	color: <?=$css_color?>
}

.product-tabs li.active a {
	border: 2px solid <?=$css_color?>;
	background: <?=$css_color?>;
}

.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
	background: <?=$css_color?>;
	border: 2px solid <?=$css_color?>;
}

button.button.add-tags {
	background: <?=$css_color?>;
	border: 2px solid <?=$css_color?>;
}

.review-ratting p a {
	color: <?=$css_color?>;
}

.reviews-content-right h3 span {
	color: <?=$css_color?>
}

.page-order .cart_navigation a.checkout-btn:hover {
	border: 2px solid <?=$css_color?>;
	background: <?=$css_color?>
}

.cart_summary .qty a:hover {
	background: <?=$css_color?>;
}

.wishlist-item table .td-add-to-cart > a {
	background: <?=$css_color?>;
}

.wishlist-item table .td-add-to-cart > a:hover, .wishlist-item .all-cart:hover {
	background: <?=$css_color?>;
}

.error_pagenotfound em {
	color: <?=$css_color?>;
}

a.button-back {
	background: <?=$css_color?>;
}

.about-page ul li a:hover {
	color: <?=$css_color?>;
}

.about-page .text_color {
	color: <?=$css_color?>;
}

.align-center-btn a.button {
	border: 2px <?=$css_color?> solid;
	background: <?=$css_color?>;
}

.align-center-btn a.button.buy-temp {
	background: <?=$css_color?>;
	border: 2px <?=$css_color?> solid;
}

.panel-info>.panel-heading {
    color: <?=$css_color?>;
    background-color: <?=$css_color?>;
    border-color: <?=$css_color?>;
}

.panel-info>.panel-heading h3{
   color: <?=$css_color?>;
}

.panel-info>.panel-heading .summary{
   color: <?=$css_color?>;
}

   .panel-info {
    border-color: <?=$css_color?>;
}

.btn-primary { 
	background-color: <?=$css_color?>;
}

.label-primary {
	background-color: <?=$css_color?>;
}

#mobile-menu {
	background: <?=$css_color?>;
}

.mobile-menu li li {
	background: <?=$css_color?>;
}

.mobile-menu li {
	display: block;
	border-top: 1px solid <?=darken_color($css_color)?>;
}

</style>