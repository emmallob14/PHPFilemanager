<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php print config_item('developer'); ?>">
    <meta name="currentDirectory" id="currentDirectory" value="<?php print $config->base_url(); ?>" content="<?php print current_url(); ?>">
	<meta name="pageurl" id="pageurl" value="<?php print $config->base_url(); ?>" content="<?php print $config->base_url(); ?>">
	<link rel="shortcut icon" href="<?php print $config->base_url(); ?>assets/onepage/assets/ico/favicon.png">
    <title>Homepage: <?php print config_item('site_name'); ?></title>
    <!-- Bootstrap core CSS -->
    <link href="<?php print $config->base_url(); ?>assets/onepage/dist/css/bootstrap.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Custom styles for this template -->
    <link href="<?php print $config->base_url(); ?>assets/onepage/css/default.css" rel="stylesheet">
    <link href="<?php print $config->base_url(); ?>assets/onepage/css/blue.css" rel="stylesheet" id="style">
    <link href="<?php print $config->base_url(); ?>assets/onepage/css/animations.css" rel="stylesheet">
    <link href="<?php print $config->base_url(); ?>assets/onepage/css/prettyPhoto.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/onepage/css/fraction/fractionslider.css">
	<!-- Fonts -->
    <link href="<?php print $config->base_url(); ?>assets/onepage/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Merriweather+Sans:400,800italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Oxygen:400,700,300' rel='stylesheet' type='text/css'>
    <!-- Slider -->
    <noscript>
        <link rel="stylesheet" type="text/css" href="<?php print $config->base_url(); ?>assets/onepage/css/nojs.css" />
    </noscript>
  </head>

  <body>  
  <div class="slide" id="slide0home" data-slide="1" data-stellar-background-ratio="0.5"></div>
    <div id="fixedmenu" class="col-lg-12 bg-white fixed-menu text-center">
	<ul class="flippy navigation">
		<a data-toggle="tooltip" data-placement="bottom" data-original-title="Home" href="#"><li data-slide="1"><span class="glyphicon glyphicon-home flippy-object"></span></li></a>
		<a data-toggle="tooltip" data-placement="bottom" data-original-title="About" href="#"><li data-slide="2"><span class="glyphicon glyphicon-bookmark flippy-object"></span></li></a>
		<a data-toggle="tooltip" data-placement="bottom" data-original-title="Pricing" href="#"><li data-slide="8"><span class="glyphicon glyphicon-shopping-cart flippy-object"></span></li></a>
		<?php if(!$admin_user->logged_InControlled()) { ?>
		<a onclick="window.location.href='<?php print config_item('manager_dashboard'); ?>Login'" href="<?php print config_item('manager_dashboard'); ?>Login"><li><span class="glyphicon glyphicon-user flippy-object"></span></li></a>
		<?php } else { ?>
		<a href="<?php print config_item('manager_dashboard'); ?>Dashboard"><li><span class="glyphicon glyphicon-user" onclick="window.location.href='<?php print config_item('manager_dashboard'); ?>Dashboard'"></span></li></a>
		<?php } ?>
		<a data-placement="bottom" data-original-title="Contact" href="#" data-toggle="tooltip"><li data-slide="6"><span class="glyphicon glyphicon-earphone flippy-object"></span></li></a>
	</ul>
    </div>  
<!--Start Header-->
    <div class="container-fluid overflow bg-blue">
    	<div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-7 col-sm-12 col-xs-12 announcement">
                   	<b>Version:</b> <?php print config_item('serverversion'); ?> is now released 
                </div>
                <div class="header-icons col-lg-4 col-md-5 col-sm-12 col-xs-12">
                  <ul>
                    <li><a title="" href="#" data-original-title="Facebook"><span class="fa fa-facebook"></span></a></li>
                    <li><a title="" href="#" data-original-title="Twitter"><span class="fa fa-twitter"></span></a></li>
                  </ul>                      	
                </div>				
            </div>
        </div>
    </div>
    
    <div class="container-fluid header-line">
    	<div class="container">
             <div class="row header3">
                   
                   <!--Start Logo-->
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 logo flippy">
                        <ul>
                            <li>
                                <a href="#">
                                    <span class="glyphicon flippy-object"><img class="img-responsive" src="<?php print $config->base_url(); ?>assets/onepage/images/logo.png" alt="logo"></span>
                                    <span class="logo-image-text"><img alt="alt text" src="<?php print $config->base_url(); ?>assets/onepage/images/logo-text.png" width="150" height="63"></span>
                                </a>
                            </li>
                        </ul>       
                   </div>
                   <!--End Logo-->
                   <!--Start Menu--> 
                    <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12 nav3">             
                        
                        <nav class="navbar navbar-default hidden-sm hidden-xs" role="navigation">
                                <ul class="nav navbar-nav flippy navigation">
                                    <li data-slide="2"><a href="#"><span class="glyphicon glyphicon-bookmark flippy-object"></span>About</a></li>
                                    <li data-slide="8"><a href="#"><span class="fa fa-shopping-cart flippy-object"></span> Pricing</a></li>
									<li>
									<?php if(!$admin_user->logged_InControlled()) { ?>
									<a onclick="window.location.href='<?php print config_item('manager_dashboard'); ?>Login'" href="<?php print config_item('manager_dashboard'); ?>Login"><span class="fa fa-user flippy-object"></span> Account </a>
									<?php } else { ?>
									<a href="<?php print config_item('manager_dashboard'); ?>Dashboard"><span class="fa fa-user flippy-object"></span> Account</a>
									<?php } ?>									
									</li>
									<li data-slide="6"><a href="#"><span class="glyphicon glyphicon-earphone flippy-object"></span>Contact</a></li>
                                </ul>
                        </nav>
                        
                    </div>
                    <!--End Menu--> 
               </div> 
         </div>       
    </div>
    
    <div class="container-fluid visible-sm visible-xs bg-blue">
    	<div class="row">
        	        <div class="col-lg-12 top-menu text-center">
                        <ul class="flippy navigation">
                            <a data-toggle="tooltip" data-placement="bottom" data-original-title="Home" href="#"><li data-slide="1"><span class="glyphicon glyphicon-home flippy-object"></span></li></a>
                            <a data-toggle="tooltip" data-placement="bottom" data-original-title="About" href="#"><li data-slide="2"><span class="glyphicon glyphicon-bookmark flippy-object"></span></li></a>
                            <a data-toggle="tooltip" data-placement="bottom" data-original-title="Pricing" href="#"><li data-slide="8"><span class="glyphicon glyphicon-shopping-cart flippy-object"></span></li></a>
							
							<?php if(!$admin_user->logged_InControlled()) { ?>
							<a onclick="window.location.href='<?php print config_item('manager_dashboard'); ?>Login'" data-original-title="Account"><li><span class="glyphicon glyphicon-user flippy-object"></span></li></a>
							<?php } else { ?>
							<a href="<?php print config_item('manager_dashboard'); ?>Dashboard" data-original-title="Account"><li><span onclick="window.location.href='<?php print config_item('manager_dashboard'); ?>Dashboard'" class="glyphicon glyphicon-user flippy-object"></span></li></a>
							<?php } ?>
							<a data-toggle="tooltip" data-placement="bottom" data-original-title="Contact" href="#"><li data-slide="6"><span class="glyphicon glyphicon-earphone flippy-object"></span></li></a>
							
                        </ul>
                    </div>
        </div>
    </div>
<!--End Header--> 

<!--Whole page flip start-->
<div class="flipbox-container">
  <div id="flipbox1" class="flipbox">
<!--Whole page flip content start--> 
  
<!--Slider Start-->
<div class="container-fluid overflow">
        <div class="row">
            <div class="col-lg-12">
            <!-- Start fraction Slider -->
                <div class="slider">
                    <div class="fs_loader"></div>
                    <div class="fsslide">

                        <img alt="alt text" src="<?php print $config->base_url(); ?>assets/onepage/images/sliders/bg1.jpg" width="100%" data-position="0,0" data-fixed="">
                                                                                
                        <img alt="alt text" src="<?php print $config->base_url(); ?>assets/onepage/images/sliders/1.png" width="100%" height="600" 		
                                data-position="0,600" data-in="bottom" data-out="bottom" data-delay="0">
                        
                        <p 		class="claim bg-blue"			
                                data-position="150,150" data-in="top" data-out="top" data-ease-in="easeOutBounce">Multipurpose Template</p>
                                                        
                        <p 		class="teaser bg-blue" 	
                                data-position="210,150" data-in="right" data-step="1" data-delay="500" data-out="left">Lorem ipsum dolor sit amet consectetur</p>		
                        <p 		class="teaser bg-blue" 	
                                data-position="210,150" data-in="right" data-step="1" data-special="cycle" data-delay="3000" data-out="left">Lorem ipsum dolor sit amet consectetur</p>		
                        <p 		class="teaser bg-blue"		
                                data-position="210,150" data-in="right" data-step="1" data-special="cycle" data-delay="5500" data-out="left">Lorem ipsum dolor sit amet consectetur</p>
                    
                        <p 		class="teaser bg-blue"		
                                data-position="250,150" data-in="bottom" data-step="1" data-delay="8000" data-out="bottom"><a href="#"><i class="glyphicon glyphicon-edit"></i>Read More</a></p>                                    
                    
                    </div>
                    <div class="fsslide">
                        <img alt="alt text" width="100%" src="<?php print $config->base_url(); ?>assets/onepage/images/sliders/bg1.jpg" 
                                        
                                data-position="0,0" data-fixed="">
                                                                                
                        <img alt="alt text"   width="100%"  src="<?php print $config->base_url(); ?>assets/onepage/images/sliders/2.png" 
                                 height="460" 		
                                data-position="0,600" data-in="bottom" data-out="bottom" data-delay="0">
                        
                        <p 		class="claim bg-blue" 			
                                data-position="100,150" data-in="top" data-step="1" data-out="top">Fully Responsive</p>
                                        
                        <p 		class="teaser bg-blue" 	
                                data-position="170,150" data-in="bottom" data-step="2" data-delay="500">Lorem ipsum dolor sit amet consectetur</p>		
                        <p 		class="teaser bg-blue" 	
                                data-position="210,150" data-in="bottom" data-step="2" data-delay="1500">Lorem ipsum dolor sit amet</p>
                        <p 		class="teaser bg-blue" 	
                                data-position="250,150" data-in="bottom" data-step="2" data-delay="2500">Lorem ipsum dolor</p>	
                        <p 		class="teaser bg-blue" 	
                                data-position="290,150" data-in="bottom" data-step="2" data-delay="3500">Lorem ipsum dolor sit ame</p>
                    </div>
                    <div class="fsslide">

                        <img alt="alt text"  width="100%"   src="<?php print $config->base_url(); ?>assets/onepage/images/sliders/bg1.jpg" 
                                data-position="0,0" data-fixed="">
                                     
                        
                        <img alt="alt text"  width="100%" src="<?php print $config->base_url(); ?>assets/onepage/images/sliders/3.png" 
                                data-position="80,650" data-in="bottom" data-out="bottom" data-delay="0" height="318">

                        <p 		class="claim bg-blue"			
                                data-position="150,150" data-in="top" data-out="top">Multipurpose Template</p>
                                                        
                        <p 		class="teaser bg-blue" 	
                                data-position="210,150" data-in="right" data-step="1" data-out="left">Lorem ipsum dolor sit amet consectetur</p>		
                    
                        <p 		class="teaser bg-blue"		
                                data-position="250,150" data-in="bottom" data-step="2" data-out="bottom"><a href="http://vimeo.com/68433687" rel="prettyPhoto"><i class="glyphicon glyphicon-play-circle"></i>Watch Now</a></p>                                    
                    
                    </div>
                </div>          
            <!-- End fraction Slider -->
            </div>
        </div>    
</div> 
<!--Slider End-->
	
    <div class="slide" id="slide1about" data-slide="2" data-stellar-background-ratio="0.5">
    	<div class="onepage-title text-center">
        	<h1>Store any file</h1>
            <h4> Keep photos, graphic designs, drawings, recordings, musics, videos, zipped files, programming codes and more.<br>Your first 25 GB of storage are free with a MatrixFileManager Account.</h4>

        </div>
    
    </div>
    
    <!-- Start Announcement -->
    <div class="container-fluid bg-blue-trans">
    	<div class="container">
            <div class="row bottom-20">
                <div class="col-lg-12">
                
                   <div class="jumbotron animate-slideup invisible">
                            
                          <div class="container text-center">
                            <h2><span class="fa fa-quote-left"></span><?php print config_item('site_name'); ?> is safe place for all your files  <span class="fa fa-quote-right"></span></h2>
                            <p class="padding-top">
								<?php if(!$admin_user->logged_InControlled()) { ?>
                                    <button onclick="window.location.href='<?php print config_item('manager_dashboard'); ?>Login'" data-placement="bottom"  class="btn btn-default btn-lg">Login Now</button>
								<?php } else { ?>
									<button onclick="window.location.href='<?php print config_item('manager_dashboard'); ?>Dashboard'" class="btn btn-default btn-lg">Account Dashboard</button>
								<?php } ?>
								
							</p>
                            
                          </div>
                        
                        </div>
                    </div>
                
                <div class="col-lg-12">
                	<div class="col-lg-3 text-center about-icons">
							<i class=" col-lg-12 glyphicon glyphicon-headphones flippy-object"></i>
                            <h1>Fully Syncronized</h1>
                            <p>Your files in Drive can be reached from any smartphone, tablet, or computer. So wherever you go, your files follow.</p>
                    </div>
                	<div class="col-lg-3 text-center about-icons">
						<i class=" col-lg-12 glyphicon glyphicon-phone flippy-object"></i>
						<h1>Fully Responsive</h1>
						<p>Make use the fully responsive template that enables you to easily view your uploaded files on any device at any point in time.</p>
                    </div>
                	<div class="col-lg-3 text-center about-icons">
						<i class=" col-lg-12 glyphicon glyphicon-cog flippy-object"></i>
						<h1>Safe</h1>
						<p>The robust file naming mechanism prevents you from accidentally overwriting existing files. Deleted files first moves into the trash folder before they get permanently erased.</p>
                    </div>
                    <div class="col-lg-3 text-center about-icons">
						<i class=" col-lg-12 fa fa-sitemap flippy-object"></i>
						<h1>Secure</h1>
						<p>SQL injections protection, Session Hijacking protection, Brute force login attempts protection, The users input is strictly validated to prevent access outside the allowed folders. 100% support for HTTPS/SSL.</p>
                    </div>
                    
                </div>
                
                </div>
            </div>
        </div>        
    <!-- End Announcement --> 




    <div class="slide" id="slide2team" data-slide="3" data-stellar-background-ratio="0.5">
    
    	<div class="onepage-title text-center">
        	<h1>Pricing Tables</h1>
            <h4>Take advantage of our Exclusive offers </h4>
        </div>
    
    </div>
    
    <div class="slide container-fluid" data-slide="8">
            <div class="container bottom-20">
                <div class="row bottom-20 padding-top">
                
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-top bottom-20">
                       
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 pricing">
                        
                            <div class="panel panel-default text-center">
                                    <!-- Default panel contents -->
                                    <div class="panel-heading"><h4>Standard Package</h4></div>
                                    <div class="panel-body">
                                        <div><h1>GH¢0<sup>00</sup></h1></div>
                                    </div>
                            
                                    <!-- Table -->
                                    <table class="table table-hover">
                                      <tbody>
                                        <tr>
                                          <td>25GB Disk Space</td>
                                        </tr>
                                        <tr>
                                          <td>Unlimited Maximum Daily Uploads</td>
                                        </tr>
                                        <tr>
                                          <td>3 Account Users</td>
                                        </tr>
                                        <tr>
                                          <td>Free Instant Messages</td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <div class="pricing-signup"><a onclick="show_register('standard');" href="#" class="btn btn-flippy" role="button"><i class="glyphicon glyphicon-shopping-cart"></i>Check Out</a></div>
                            </div>
                        
                        </div>
                        
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 pricing">
                        
                            <div id="featured" class="panel panel-default text-center featured">
                            <div class="featured-ribbon"><div class="ribbon">Featured</div></div>
                            
                                    <!-- Default panel contents -->
                                    <div class="panel-heading"><h4>Silver Package</h4></div>
                                    <div class="panel-body">
                                        <div><h1>GH¢14<sup>99</sup></h1></div>
                                    </div>
                            
                                    <!-- Table -->
                                    <table class="table table-hover">
                                      <tbody>
                                        <tr>
                                          <td>120GB Disk Space</td>
                                        </tr>
                                        <tr>
                                          <td>Unlimited Maximum Daily Uploads</td>
                                        </tr>
                                        <tr>
                                          <td>10 Account Users</td>
                                        </tr>
                                        <tr>
                                          <td>Free Instant Messages</td>
                                        </tr>
                                        <tr>
                                          <td>24/7 Tech Support</td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    
                                    <div class="pricing-signup"><a onclick="show_register('silver');" href="#" class="btn btn-flippy" value="platinum" role="button"><i class="glyphicon glyphicon-shopping-cart"></i>Check Out</a></div>
                            </div>
                        
                        </div>
                        
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 pricing">
                        
                            <div class="panel panel-default text-center">
                                    <!-- Default panel contents -->
                                    <div class="panel-heading"><h4>Golden Package</h4></div>
                                    <div class="panel-body">
                                        <div><h1>GH¢24<sup>99</sup></h1></div>
                                    </div>
                            
                                    <!-- Table -->
                                    <table class="table table-hover">
                                      <tbody>
                                        <tr>
                                          <td>500GB Disk Space</td>
                                        </tr>
                                        <tr>
                                          <td>Unlimited Maximum Daily Uploads</td>
                                        </tr>
                                        <tr>
                                          <td>50 Account Users</td>
                                        </tr>
                                        <tr>
                                          <td>Free Instant Messages</td>
                                        </tr>
                                        <tr>
                                          <td>24/7 Tech Support</td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    
                                    <div class="pricing-signup"><a onclick="show_register('golden');" href="#" class="btn btn-flippy" role="button"><i class="glyphicon glyphicon-shopping-cart"></i>Check Out</a></div>
                            </div>
                        
                        </div>
                        
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 pricing">
                        
                            <div class="panel panel-default text-center">
                                    <!-- Default panel contents -->
                                    <div class="panel-heading"><h4>Platinum Package</h4></div>
                                    <div class="panel-body">
                                        <div><h1>GH¢34<sup>99</sup></h1></div>
                                    </div>
                            
                                    <!-- Table -->
                                    <table class="table table-hover">
                                      <tbody>
                                        <tr>
                                          <td>1TB Disk Space</td>
                                        </tr>
                                        <tr>
                                          <td>Unlimited Maximum Daily Uploads</td>
                                        </tr>
                                        <tr>
                                          <td>Unlimited Account Users</td>
                                        </tr>
                                        <tr>
                                          <td>Free Instant Messages</td>
                                        </tr>
                                        <tr>
                                          <td>24/7 Tech Support</td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    
                                    <div class="pricing-signup"><a onclick="show_register('platinum');" href="#" class="btn btn-flippy" role="button"><i class="glyphicon glyphicon-shopping-cart"></i>Check Out</a></div>
                            </div>
                        
                        </div> 
                   
                   </div>
      
                </div>
            </div>
    </div>
	
    <div class="slide" id="slide5contact" data-slide="6" data-stellar-background-ratio="0.5">
    
    	<div class="onepage-title text-center">
        	<h1>Contact us</h1>
            <h4>Free Support 24/7</h4>
        </div>
    
    </div> 
    

    <div class="container-fluid bottom-20 map">
            <div class="row">
            	<div>
                	<iframe width="100%" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=envato&amp;aq=&amp;sll=37.0625,-95.677068&amp;sspn=40.59616,86.572266&amp;ie=UTF8&amp;hq=envato&amp;hnear=&amp;radius=15000&amp;ll=-37.817942,144.964977&amp;spn=0.071946,0.071946&amp;t=m&amp;output=embed"></iframe>
                </div>
            </div>    
    </div>

    <div class="container-fluid">
            <div class="container">
                <div class="row bottom-20 padding-top">

                    <div class="col-lg-8">
                        <p>Do not hesitate to reach out to us. We are always available to help.</p>
                        
                        <div class="post-title">
                           <h4><span class="fa fa-pencil fa-flip-horizontal"></span>Write to us</h4>
                        </div>
                        
                        <form name="sentMessage" id="contactForm" role="form" class="bottom-20" novalidate>
                          <div class="control-group">
                            <div class="formcontrols">
                            <input type="text" id="name" class="form-control" placeholder="Enter Name" required data-validation-required-message="Please enter your name"/>
                          	<p class="help-block"></p>
                          	</div>
                          </div>
                          
                          <div class="control-group">
                            <div class="formcontrols">
                            <input type="email" id="email" class="form-control" placeholder="Enter Email" required data-validation-required-message="Please enter your email">
                          	</div>
                          </div>
                          <div class="control-group">
                            <div class="formcontrols">
                            <input type="text" id="subject" class="form-control" placeholder="Subject" required data-validation-required-message="Please enter the message subject"/>
                          	</div>
                          </div>
                          <div class="control-group">
                          	<div class="formcontrols">
                            <textarea placeholder="Message" id="message" required data-validation-required-message="Please enter your message" minlength="5" data-validation-minlength-message="Min 5 characters"  class="form-control" rows="5"></textarea>
                          	</div>
                          </div> 
                          <div id="success"></div> 
                          <button type="submit" class="btn btn-flippy">Submit</button>
                          <button type="reset" class="btn">Reset</button>
                        </form> 
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="contact-info"><span class="glyphicon glyphicon-map-marker"></span>Address</div><?php print config_item('site_address'); ?>
                        <div class="contact-info padding-top"><span class="glyphicon glyphicon-phone-alt"></span>Phone</div><?php print config_item('site_phone'); ?>
                        <div class="contact-info padding-top"><span class="glyphicon glyphicon-envelope"></span> E-mail</div><?php print config_item('site_email'); ?>
                        <div class="contact-info padding-top"><span class="glyphicon glyphicon-globe"></span> Website</div><?php print config_item('base_url'); ?>
                    </div>  
                
                </div>
            </div>    	
    </div>    



<!--Whole page flip content end-->    
  </div>
</div> 
<!--Whole page flip End-->  

    
<!--Start Footer 2-->
<footer>    
    <div class="container-fluid bg-grey">
    	<div class="container">
            <div class="row">
                <div class="text-left col-lg-8 col-md-8 col-sm-12 col-xs-12 footer">
                    &copy; <?php print config_item('site_name'); ?> - Responsive Multipurpose FileManager
                </div> 
                <div class="text-right col-lg-4 col-md-4 col-sm-12 col-xs-12 footer">
					<a href="#" data-toggle="modal" data-target="#PrivacyPolicy">Privacy Policy</a> | <a href="#" data-toggle="modal" data-target="#TermsAndConditions">Terms & Conditions</a>
                </div> 
            </div>
        </div>    
    </div> 
</footer>    
<!--End Footer 2-->     

<a href="#" class="back-to-top"><span class="fa fa-chevron-up"></span></a> 

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?php print $config->base_url(); ?>assets/onepage/assets/js/jquery.js"></script>
    <script src="<?php print $config->base_url(); ?>assets/onepage/dist/js/bootstrap.min.js"></script>
    
    <script type="text/javascript" src="<?php print $config->base_url(); ?>assets/onepage/js/custome.js"></script>
	<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/onepage/js/fraction/jquery.fractionslider.js"></script>
	<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/onepage/js/jquery.flippy.js"></script>
	<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/onepage/js/jquery.mixitup.min.js"></script>
    <script type="text/javascript" src="<?php print $config->base_url(); ?>assets/onepage/js/jqBootstrapValidation.js"></script>
    <script type="text/javascript" src="<?php print $config->base_url(); ?>assets/onepage/js/contact.js"></script>
    <script src="<?php print $config->base_url(); ?>assets/onepage/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/onepage/js/jquery.tabSlideOut.v1.3.js"></script>
    <script type="text/javascript" src="<?php print $config->base_url(); ?>assets/onepage/js/jquery.style-switcher.js"></script>
	<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/onepage/js/onepage/jquery.stellar.min.js"></script>
	<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/onepage/js/onepage/waypoints.min.js"></script>
	<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/onepage/js/onepage/jquery.easing.1.3.js"></script>
	<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/onepage/js/onepage/scripts.js"></script>
  </body>
</html>