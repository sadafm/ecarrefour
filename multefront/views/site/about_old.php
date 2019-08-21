<?php
use yii\helpers\Url;
use multebox\models\ProductBrand;
use multebox\models\BannerData;
use multebox\models\BannerType;
use multebox\models\File;
use multebox\models\Testimonial;
?>
<!-- Breadcrumb Start-->
  <ul class="breadcrumb">
	<li><a href="<?=Url::to(['/site/index'])?>"><i class="fa fa-home"></i></a></li>
	<li><?=Yii::t('app', 'About Us')?></li>
  </ul>
  <!-- Breadcrumb End-->
  
  <!-- Main Container -->
  
  <div class="main container">
 
     <div class="about-page">
        <div class="col-xs-12 col-sm-6"> 
          
          <h1>Welcome to <span class="text_color"><?=Yii::$app->params['APPLICATION_NAME']?></span></h1>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras lacus metus, convallis ut leo nec, tincidunt eleifend justo. Ut felis orci, hendrerit a pulvinar et, gravida ac lorem. Sed vitae molestie sapien, at sollicitudin tortor.<br>
            <br>
            Duis id volutpat libero, id vestibulum purus.Donec euismod accumsan felis, <a href="#">egestas lobortis velit tempor</a> vitae. Integer eget velit fermentum, dignissim odio non, bibendum velit.</p>
          <ul>
            <li><i class="fa fa-arrow-right"></i>&nbsp; <a href="#">Suspendisse potenti. Morbi mollis tellus ac sapien.</a></li>
            <li><i class="fa fa-arrow-right"></i>&nbsp; <a href="#">Cras id dui. Nam ipsum risus, rutrum vitae, vestibulum eu.</a></li>
            <li><i class="fa fa-arrow-right"></i>&nbsp; <a href="#">Phasellus accumsan cursus velit. Pellentesque egestas.</a></li>
            <li><i class="fa fa-arrow-right"></i>&nbsp; <a href="#">Lorem Ipsum generators on the Internet tend to repeat predefined.</a></li>
          </ul>
        </div>
        <div class="col-xs-12 col-sm-6">
          <div class="single-img-add sidebar-add-slider">
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel"> 
              
              <!-- Wrapper for slides -->
              <div class="carousel-inner" role="listbox">
                <div class="item active"> <img src="<?=Url::base()?>/images/about_us_slide1.jpg" alt="slide1"> </div>
                <div class="item"> <img src="<?=Url::base()?>/images/about_us_slide2.jpg" alt="slide2"> </div>
                <div class="item"> <img src="<?=Url::base()?>/images/about_us_slide3.jpg" alt="slide3"> </div>
              </div>
            </div>
          </div>
        </div>
      </div>

  </div>
  
  <div class="our-team"> 

    
   
      
    <div class="container"> <div class="page-header">
        <h2>Our Team</h2>
      </div>
      <div class="row">
        <div class="col-xs-6 col-sm-3 col-md-3">
          <div class="wow bounceInUp" data-wow-delay="0.2s">
            <div class="team">
              <div class="inner">
                <div class="avatar"><img src="<?=Url::base()?>/images/team-img01.jpg" alt="" class="img-responsive img-circle" /></div>
                <h5>Joana Doe</h5>
                <p class="subtitle">Art-director</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-3 col-md-3">
          <div class="wow bounceInUp" data-wow-delay="0.5s">
            <div class="team">
              <div class="inner">
                <div class="avatar"><img src="<?=Url::base()?>/images/team-img02.jpg" alt="" class="img-responsive img-circle" /></div>
                <h5>Josefine</h5>
                <p class="subtitle">Team Leader</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-3 col-md-3">
          <div class="wow bounceInUp" data-wow-delay="0.8s">
            <div class="team">
              <div class="inner">
                <div class="avatar"><img src="<?=Url::base()?>/images/team-img03.jpg" alt="" class="img-responsive img-circle" /></div>
                <h5>Paulo Moreira</h5>
                <p class="subtitle">Senior Web Developer</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-3 col-md-3">
          <div class="wow bounceInUp" data-wow-delay="1s">
            <div class="team">
              <div class="inner">
                <div class="avatar"><img src="<?=Url::base()?>/images/team-img04.jpg" alt="" class="img-responsive img-circle" /></div>
                <h5>Tom Joana</h5>
                <p class="subtitle">Digital Creative Director</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
 <div class="container">
    <div class="row">
      <div class="col-md-6"> 
        <!-- Testimonials Box -->
        <div class="testimonials">
          <div class="slider-items-products">
            <div id="testimonials-slider" class="product-flexslider hidden-buttons home-testimonials">
              <div class="slider-items slider-width-col4 ">
                <?php
				$testimonials = Testimonial::find()->all();

				foreach($testimonials as $testimonial)
				{
				?>
				<div class="holder">
                  <p><?=$testimonial->testimonial?> </p>
                  <div class="thumb"> <img src="<?=Url::base()?>/images/upload/<?=$testimonial->writer_new_image?>" alt="<?=$testimonial->writer_image?>"> </div>
                  <strong class="name"><?=$testimonial->writer_name?></strong> <strong class="designation"><?=$testimonial->writer_designation?></strong> 
				</div>
				<?php
				}
				?>
                
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- End Testimonials Box --> 
      <!-- our clients Slider -->
      <div class="col-md-6">
        <div class="our-clients">
          <div class="slider-items-products">
            <div id="our-clients-slider" class="product-flexslider hidden-buttons">
              <div class="slider-items slider-width-col6"> 

			    <?php
				$brands = ProductBrand::find()->where("active = 1")->all();
				if ($brands)
				{
					for ($i = 0; $i < count($brands); $i++)
					{
					?>
					<!-- Item -->
					<div class="item"> 
						<a href="javascript:void(0)"><img class="brand-img" src="<?=Url::base()?>/images/upload/<?=$brands[$i]['brand_new_image']?>" alt="<?=$brands[$i]['name']?>"></a>
					<?php
					if($brands[$i+1])
					{
					?>
						<br>
						<a href="javascript:void(0)"><img class="brand-img" src="<?=Url::base()?>/images/upload/<?=$brands[$i+1]['brand_new_image']?>" alt="<?=$brands[$i]['name']?>"></a>
						<?php
						if($brands[$i+2])
						{
						?>
						<br>
						<a href="javascript:void(0)"><img class="brand-img" src="<?=Url::base()?>/images/upload/<?=$brands[$i+2]['brand_new_image']?>" alt="<?=$brands[$i]['name']?>"></a>
						<?php
						}
						?>
					<?php
					}
					?>
					</div>
					<!-- End Item --> 
					<?php
						$i = $i+2;
					}
				}
				?>
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
  <!-- Section: services -->
  <section id="service" class="text-center"> 
    
   
    
    <div class="container">
     
      <div class="row">
        <div class="col-sm-3 col-md-3">
          <div class="wow fadeInUp" data-wow-delay="0.2s">
            <div class="service-box">
              <div class="service-icon"> <i class="fa fa-magic"></i> </div>
              <div class="service-desc">
                <h4>Web Design</h4>
                <p>Lorem ipsum dolor sit amet set, consectetur utes anet adipisicing elit, sed do eiusmod tempor incidist.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-3 col-md-3">
          <div class="wow fadeInLeft" data-wow-delay="0.2s">
            <div class="service-box">
              <div class="service-icon"> <i class="fa fa-cogs"></i> </div>
              <div class="service-desc">
                <h4>Programming</h4>
                <p>Lorem ipsum dolor sit amet set, consectetur utes anet adipisicing elit, sed do eiusmod tempor incidist.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-3 col-md-3">
          <div class="wow fadeInUp" data-wow-delay="0.2s">
            <div class="service-box">
              <div class="service-icon"> <i class="fa fa-instagram"></i> </div>
              <div class="service-desc">
                <h4>Photography</h4>
                <p>Lorem ipsum dolor sit amet set, consectetur utes anet adipisicing elit, sed do eiusmod tempor incidist.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-3 col-md-3">
          <div class="wow fadeInRight" data-wow-delay="0.2s">
            <div class="service-box">
              <div class="service-icon"> <i class="fa fa-search-plus"></i> </div>
              <div class="service-desc">
                <h4>SEO</h4>
                <p>Lorem ipsum dolor sit amet set, consectetur utes anet adipisicing elit, sed do eiusmod tempor incidist.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /Section: services --> 
 