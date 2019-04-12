<?php 
load_file(
	ARRAY(
		'security'=>'core',
		'url_helper'=>'helpers'
	)
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Login: <?php print config_item('site_name'); ?></title><meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/bootstrap.min.css" />
<meta name="author" content="<?php print config_item('developer'); ?>">
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/matrix-login.css" />
<link href="<?php print $config->base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
<meta name="pageurl" id="pageurl" value="<?php print $config->base_url(); ?>" content="<?php print $config->base_url(); ?>">
</head>
<body>
	<div id="loginbox">            
		<form id="loginForm" method="POST" class="form-vertical" action="<?php print $config->base_url(); ?>doAuth/doLogin">
			 <div class="control-group normal_text"> <h3>MYOFFICE FILEMANAGER</h3></div>
			<div class="control-group">
				<div class="controls">
					<div class="main_input_box">
						<span class="add-on bg_lg"><i class="icon-user"> </i></span><input type="text" placeholder="Username" name="username"/>
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
			<?php  PRINT (!confirm_url_id(0, 'Login')) ? "<input name=\"href\" value=\"".current_url()."\" type=\"hidden\" readonly>" : ""; ?>
			<div class="form-actions">
				<span class="pull-left"><a href="#" class="flip-link btn btn-info" id="to-recover">Lost password?</a></span>
				<input type="hidden" name="login_user_yea" value="<?php print sha1(time()); ?>">
				<span class="pull-right"><button id="submitButton" type="submit" class="btn btn-success" > Login</button></span>
			</div>
		</form>
		<form id="recoverForm" method="POST" action="<?php print $config->base_url(); ?>doAuth/doRecover" class="form-vertical">
			<p class="normal_text">Enter your e-mail address below and we will send you instructions how to recover a password.</p>
			
			<div class="controls">
				<div class="main_input_box">
					<span class="add-on bg_lo"><i class="icon-envelope"></i></span><input type="text" placeholder="E-mail address" />
				</div>
			</div>
		   
			<div class="form-actions">
				<span class="pull-left"><a href="#" class="flip-link btn btn-success" id="to-login">&laquo; Back to login</a></span>
				<span class="pull-right"><button class="btn btn-info" id="submitButton2" type="submit">Recover</button></span>
			</div>
		</form>
		<div id="formResult"><?php PRINT (confirm_url_id(1, 'doLogout')) ? "<div class='alert alert-success alert-md btn-block' style='width:100%'>You have successfully logged out of the system.</div>" : ""; ?></div>
	</div>
	
	<script src="<?php print $config->base_url(); ?>assets/js/jquery.min.js"></script>  
	<script src="<?php print $config->base_url(); ?>assets/js/matrix.login.js"></script>
	<script src="<?php print $config->base_url(); ?>assets/js/matrix.script.js"></script>
</body>
</html>