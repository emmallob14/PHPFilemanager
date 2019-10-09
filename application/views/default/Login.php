<?php
$user_agent = load_class('user_agent', 'libraries');
$fb_auth = load_class('facebook', 'models');
//$tw_auth = load_class('twitter', 'models');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Login: <?php print config_item('site_name'); ?></title><meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/bootstrap.min.css" />
<meta name="author" content="<?php print config_item('developer'); ?>">
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/bootstrap-responsive.min.css" />
<link rel="shortcut icon" href="<?php print $config->base_url(); ?>assets/onepage/assets/ico/favicon.png">
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/matrix-login.css" />
<link href="<?php print $config->base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
<!--<style href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>-->
<meta name="pageurl" id="pageurl" value="<?php print $config->base_url(); ?>" content="<?php print $config->base_url(); ?>">
</head>
<body style="height:500px;">
	<div id="loginbox">
		<div class="control-group normal_text"> <h3><?php print config_item('site_name'); ?></h3></div>
		
		<?php
		// call the facebook class and process the information
		$fb_auth->FacebookLogin();
		
		IF((ISSET($SITEURL[1]) AND $SITEURL[1]=="Facebook")) {
			?>
			<form id="facebookLogin" method="POST" autocomplete="Off" class="form-vertical" action="">
			<?php PRINT (confirm_url_id(2, 'Failed')) ? "<div class='alert alert-danger alert-md btn-block' style='width:100%'>Facebook Login Authentification failed permanently.</div>" : NULL; ?>
			<?php PRINT (confirm_url_id(2, 'Delete')) ? @$session->sess_destroy()."<div class='alert alert-success alert-md btn-block' style='width:100%'>Facebook Login Session has been successfully deleted.</div>" : NULL; ?>
			<p class="normal_text">Click on the button below to complete the process of login using your Facebook Account.</p>
			<div class="form-actions">
				<span class="pull-left"><a href="<?php print $fb_auth->loginUrl; ?>" class="flip-link btn btn-primary" id="facebook-login"><i class="icon icon-facebook"></i> Login with Facebook</a></span>
				<span class="pull-right"><a href="<?php print config_item('manager_dashboard')."Login"; ?>" class="flip-link btn btn-success" id="facebook-login"><i class="icon icon-signin"></i> Login</a></span>
			</div>
			</form>
			<?php
		}
		?>
		<?php
		// call the facebook class and process the information
		IF((ISSET($SITEURL[1]) AND $SITEURL[1]=="Twitter")) {
			?>
			<form id="facebookLogin" method="POST" autocomplete="Off" class="form-vertical" action="">
			<p class="normal_text">Click on the button below to complete the process of login using your Twitter Account.</p>
			<div class="form-actions">
				<span class="pull-left"><a href="<?php //print //$tw_auth->twitterLoginUrl; ?>" class="flip-link btn btn-primary" id="twitter-login"><i class="icon icon-twitter"></i> Login with Twitter</a></span>
				<span class="pull-right"><a href="<?php print config_item('manager_dashboard')."Login"; ?>" class="flip-link btn btn-success" id="twitter-login"><i class="icon icon-signin"></i> Login</a></span>
			</div>
			</form>
			<?php
		}
		?>
		<?PHP IF(!ISSET($SITEURL[1]) OR (ISSET($SITEURL[1]) AND !IN_ARRAY($SITEURL[1], ARRAY("Facebook","Twitter")))) { ?>
		<form id="loginForm" method="POST" autocomplete="Off" class="form-vertical" action="<?php print $config->base_url(); ?>doAuth/doLogin">
			<div class="control-group">
				<div class="controls">
					<div class="main_input_box">
						<span class="add-on bg_lg"><i class="icon-user"> </i></span><input type="text" placeholder="Username" value="<?php print (ISSET($_GET["User"])) ? xss_clean($_GET["User"]) : ""; ?>" name="username" id="username"/>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<div class="main_input_box">
						<span class="add-on bg_ly"><i class="icon-lock"></i></span><input type="password" placeholder="Password"  name="password" id="password"/>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<div class="main_input_box" style="background:#fff">
						<span style="text-align:left;">
						<?php print nl2br(file_get_contents('README.txt')); ?>
						</span>
					</div>
				</div>
			</div>
			<?php  PRINT (!confirm_url_id(0, 'Login')) ? "<input name=\"href\" value=\"".base64_encode(current_url())."\" type=\"hidden\" readonly>" : ""; ?>
			<div class="form-actions">
				<span class="pull-left"><a href="#" class="flip-link btn btn-info to-recover"><i class="icon icon-key"></i> Lost password?</a></span>
				&nbsp;&nbsp; <span class="pull-left"><a href="#" class="flip-link btn btn-danger to-register"><i class="icon icon-user"></i> Signup?</a></span>
				<input type="hidden" name="login_user_yea" value="<?php print sha1(time()); ?>">
				<span class="pull-right"><button id="submitButton" type="submit" class="btn btn-success" ><i class="icon icon-signin"></i> Login</button></span>
			</div>
			<div class="form-actions">
				<span class="pull-left"><a href="<?php print $fb_auth->loginUrl; ?>" class="flip-link btn btn-primary" id="facebook-login"><i class="icon icon-facebook"></i> Facebook Login </a></span> <span class="pull-left"><a href="<?php //print $tw_auth->twitterLoginUrl; ?>" class="flip-link btn btn-info" id="facebook-login"><i class="icon icon-twitter"></i> Twitter Login </a></span>
				<span class="pull-right"><a href="<?php print $config->base_url(); ?>" class="flip-link btn btn-warning">&laquo; <i class="icon icon-globe"></i> Back to Website</a></span>
			</div>
		</form>
		<form id="recoverForm" autocomplete="Off" method="POST" action="<?php print $config->base_url(); ?>doAuth/doRequestPasswordChange" class="form-vertical">
			<p class="normal_text">Enter your e-mail address below and we will send you instructions how to recover a password.</p>
			<div class="controls">
				<div class="main_input_box">
					<span class="add-on bg_lo"><i class="icon-envelope"></i></span><input type="email" placeholder="E-mail address" name="request_username" id="request_username" />
				</div>
			</div>		   
			<div class="form-actions">
				<span class="pull-left"><a href="#" class="flip-link btn btn-success to-login">&laquo; Back to login</a></span>
				<input type="hidden" id="request_password_change" name="request_password_change">
				<span class="pull-right"><button class="btn btn-info" id="submitButton2" type="submit"><i class="icon icon-signin"></i> Recover</button></span>
			</div>
		</form>
		<form id="registerForm" autocomplete="Off" method="POST" action="<?php print $config->base_url(); ?>doAuth/doRegisterAccount" class="form-vertical">
			<div class="control-group">
				<div class="controls">
					<div class="main_input_box">
						<span class="add-on bg_lg"><i class="icon-user"> </i></span><input type="text" placeholder="Office Name" value="" name="office_name" id="office_name"/>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<div class="main_input_box">
						<span class="add-on bg_lr"><i class="icon-phone"> </i></span><input type="text" placeholder="Office Contact" value="" name="office_contact" id="office_contact"/>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<div class="main_input_box">
						<span class="add-on bg_lb"><i class="icon-globe"> </i></span><input type="text" placeholder="Office Address" value="" name="office_address" id="office_address"/>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<div class="main_input_box">
						<span class="add-on bg_ls"><i class="icon-cloud"> </i></span>
						<select style="min-width:280px;max-width:300px;height:40px" name="package" id="package">
							<option value="null">Select Option</option>
							<option value="standard">Standard</option>
							<option value="silver">Silver</option>
							<option value="golden">Golden</option>
							<option value="platinum">Platinum</option>
						</select>
					</div>
				</div>
			</div>			
			<div class="control-group">
				<div class="controls">
					<div class="main_input_box">
						<span class="add-on bg_lo"><i class="icon-user"> </i></span><input type="text" placeholder="Admin Username" value="" name="admin_username" id="admin_username"/>
					</div>
				</div>
			</div>
			<div class="controls">
				<div class="main_input_box">
					<span class="add-on bg_ly"><i class="icon-envelope"></i></span><input type="text" placeholder="Admin E-mail address" name="admin_email" id="admin_email" />
				</div>
			</div>		   
			<div class="form-actions">
				<span class="pull-left"><a href="#" class="flip-link btn btn-success to-login">&laquo; Back to login</a></span>
				<input type="hidden" id="register_account" name="register_account">
				<span class="pull-right"><button class="btn btn-info" id="submitButton3" type="submit"><i class="icon icon-signin"></i> Register</button></span>
			</div>
		</form>
		<?PHP } ?>
		<div id="formResult"><?php PRINT (confirm_url_id(1, 'doLogout')) ? "<div class='alert alert-success alert-md btn-block' style='width:100%'>You have successfully logged out of the system.</div>" : NULL;
		// automatically destroy all sessions if the url matches the logout url
		(confirm_url_id(1, 'doLogout')) ? @$session->sess_destroy() : NULL;
		?></div>
	</div>	
	<script src="<?php print $config->base_url(); ?>assets/js/jquery.min.js"></script>  
	<script src="<?php print $config->base_url(); ?>assets/js/matrix.login.js"></script>
	<script src="<?php print $config->base_url(); ?>assets/js/matrix.script.js"></script>
	<script>
	$("#username").val('').focus();
	</script>
</body>
</html>