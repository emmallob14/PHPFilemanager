<?php 
// create new objects
$encrypt = load_class('encrypt', 'libraries');
$user_agent = load_class('user_agent', 'libraries');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Reset Your Password: <?php print config_item('site_name'); ?></title><meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/bootstrap.min.css" />
<meta name="author" content="<?php print config_item('developer'); ?>">
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/matrix-login.css" />
<link href="<?php print $config->base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
<meta name="pageurl" id="pageurl" value="<?php print $config->base_url(); ?>" content="<?php print $config->base_url(); ?>">
</head>
<body style="height:500px;">
	<div id="loginbox" style="height:200px;">            
		<form method="POST" class="form-vertical">
			<div class="control-group">
				<div class="controls">
					<div class="control-group normal_text"> <h3><?php print config_item('site_name'); ?></h3></div>
					<?PHP
					// INITIALIZING
					$ErrorMsg = "<div class='alert alert-danger'><i style='size:50px' class='icon icon-question-sign'></i> Sorry! You have submitted a wrong Password Token for processing.</div>";
					$ExpiredMsg = "<div class='alert alert-danger'><i style='size:50px' class='icon icon-question-sign'></i> Sorry! The Password Token submitted has expired.</div>";
					// CONFIRM THE URL PARSED BY THE USER
					IF(!confirm_url_id(2)) {
						PRINT $ErrorMsg;
					} ELSE {
						// ASSIGN VARIABLES TO THE DATA PARSED
						$Username = xss_clean($SITEURL[1]);
						$PasswordToken = xss_clean($SITEURL[2]);
						// QUERY THE DATABASE FOR THE INFORMATION PROVIDED
						$TokenConfirm = $DB->query("SELECT * FROM _admin_request_change WHERE username='$Username' AND request_token='$PasswordToken'");
						// count the number of rows 
						IF(COUNT($TokenConfirm) > 0) {
							// using foreach to get the information 
							FOREACH($TokenConfirm AS $Result) {
								$Expiry = $Result["expiry_time"];
								// confirm that the token hasnt expired
								IF(TIME() > $Expiry) {
									// PRINT Expired message
									PRINT $ExpiredMsg;
															
									// UPDATE THE DATABASE AND SET THE TOKEN STATUS AS EXPIRED
									$DB->query("UPDATE _admin_request_change SET token_status='EXPIRED' WHERE request_token='$PasswordToken' AND username='$Username'");
								} ELSE {
									// CREATE THE NEW PASSWORD FOR THE USER
									$NewPassword = random_string('alnum', 10);
									
									// hash the passwords
									$NPassword = $encrypt->password_hash($NewPassword);
									
									// update the information 
									$DB->just_exec("UPDATE _admin SET password='$NPassword', lastresetdate='".TIME()."' WHERE email='$Username' AND admin_deleted='0'");
									
									// UPDATE THE DATABASE AND SET THE TOKEN STATUS AS EXPIRED
									$DB->query("UPDATE _admin_request_change SET token_status='USED', request_token=NULL, change_date=now() WHERE request_token='$PasswordToken' AND username='$Username'");
									
									//FORM THE MESSAGE TO BE SENT TO THE USER
									$message = 'Hello '.$Username.',<br><br>Your password was successfully changed at '.config_item('site_name');
									$message .= '<br><br><strong>EMAIL:</strong> '.$Username;
									$message .= '<br><strong>PASSWORD:</strong> '.$NewPassword;
									$message .= '<br><br><strong>Browser:</strong> '.$user_agent->browser()." ".$user_agent->platform();
									$message .= '<br><strong>IP Address:</strong> '.$user_agent->ip_address();
									$message .= '<br><strong>Server Host:</strong> '.$_SERVER["HTTP_HOST"];
									$message .= "<br><br>Please do Contact <a href='".SITE_URL."/Support'>Support</a> if you did not initiate this Password Change.";
									
									#send email to the user
									send_email(
										$Username, "[".config_item('site_name')."] Password Changed", 
										$message, config_item('site_name'), config_item('site_email'), 
										NULL, 'default', $Username
									);
							
									// record the password change
									print "<div class='alert alert-success'>Password change request was successfully processed. Please check your email for the new password.</div>";									
									
								}
							}
						} ELSE {
							PRINT $ExpiredMsg;
						}
					}
					?>
				</div>
			</div>
		</form>
	</div>
</body>
</html>