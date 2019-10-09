<?php 
// ensure this file is being included by a parent file
if( !defined( 'SITE_URL' ) && !defined( 'SITE_DATE_FORMAT' ) ) die( 'Restricted access' );
class Facebook {
	
	public $result;
	public $loginUrl;
	
	public function __construct() {
		
		global $DB, $session;
		
		$this->db = $DB;
		$this->user_agent = load_class('User_agent', 'libraries');
		$this->session = $session;
		// Load the facebook library class
		load_library('Facebook/autoload');
		// Set additional variables
		$this->fb_app_id = "335003863791299";
		$this->fb_app_secret = "d5972cd5429da17641de1a691cdbce82";
		$this->redirectUrl = config_item('manager_dashboard')."Login/Facebook";
		
	}
	
	public function FacebookLogin() {
		
		$fb = new Facebook\Facebook([
		  'app_id' => $this->fb_app_id,
		  'app_secret' => $this->fb_app_secret, 
		  'default_graph_version' => 'v2.2',
		  ]);

		$helper = $fb->getRedirectLoginHelper();

		$permissions = ['email']; // Optional permissions
			
		TRY {
			
			IF (ISSET($_SESSION['facebook_access_token'])) {
				$accessToken = $_SESSION['facebook_access_token'];
			} ELSE {
				$accessToken = $helper->getAccessToken();
			}
			
		} CATCH(Facebook\Exceptions\facebookResponseException $e) {
			// When Graph returns an error
			PRINT "<div class='alert alert-danger'>Graph returned an error: {$e->getMessage()}</div>";
		} CATCH(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			PRINT "<div class='alert alert-danger'>Facebook SDK returned an error: {$e->getMessage()}</div>";
		}

		IF (ISSET($accessToken)) {
			
			IF (ISSET($_SESSION['facebook_access_token'])) {
				$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
			} ELSE {
				
				// getting short-lived access token
				$_SESSION['facebook_access_token'] = (string) $accessToken;
				
				// OAuth 2.0 client handler
				$oAuth2Client = $fb->getOAuth2Client();
				
				// Exchanges a short-lived access token for a long-lived one
				$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
				
				$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
				
				// setting default access token to be used in script
				$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
				
			}
			
			// getting basic info about user
			TRY {
				
				$profile_request = $fb->get('/me?fields=name,first_name,last_name,email');
				$requestPicture = $fb->get('/me/picture?redirect=false&height=200'); //getting user picture
				$picture = $requestPicture->getGraphUser();
				$profile = $profile_request->getGraphUser();
				$fbid = $profile->getProperty('id');           // To Get Facebook ID
				$fbfullname = $profile->getProperty('name');   // To Get Facebook full name
				$fbemail = $profile->getProperty('email');    //  To Get Facebook email
				$fbpic = "<img src='".$picture['url']."' class='img-rounded'/>";
				
				// confirm that the users email is registered with us
				$AccountQuery = $this->db->query("select * from _admin where email='$fbemail' and activated='1' and status='1'");
				IF(COUNT($AccountQuery) == 1) {
					FOREACH($AccountQuery as $results) {
						#set some sessions for the user
						$this->session->set_userdata(
							ARRAY(
								OFF_SESSION_ID => $results["office_id"],
								UNAME_SESS_ID => $results["username"],
								UID_SESS_ID => $results["id"],
								USER_FULLNAME => $results["firstname"]." ".$results["lastname"],
								USER_EMAIL => $fbemail,
								ROLE_SESS_ID => $results["role"],
								MAIN_SESS => random_string('alnum', 45)
							)
						);
						
						$this->session->set_userdata(LOCKED_OUT, false);
									
						IF($results["role"] == 1001) {
							$this->session->set_userdata(ROLE_SUPER_ROLE, true);
							$this->session->set_userdata(ROLE_SESS_ID, 1001);
						}
						
						#update the table 
						$ip = $this->user_agent->ip_address();
						$br = $this->user_agent->browser()." ".$this->user_agent->platform();
						
						$this->db->query("update _admin set lastaccess=now(), log_ipaddress='$ip', log_browser='$br', log_session='".$this->session->userdata(MAIN_SESS)."', last_login_attempts='1', last_login_attempts_time=now() where id='{$results["id"]}'");
									
						$this->db->query("insert into _admin_log_history set username='{$results["username"]}', lastlogin=now(), log_ipaddress='$ip', log_browser='$br', office_id='".$this->session->userdata(OFF_SESSION_ID)."', log_platform='FacebookOAuth: ".$this->user_agent->agent_string()."'");
						
						# redirect the user to the profile page if it has "code" GET variable
						IF (ISSET($_GET['code'])) {
							redirect( config_item('manager_dashboard') . 'Dashboard', 'refresh:1000');
						}
					}
				} ELSE {
					PRINT "<div style='width:100%' class='alert alert-danger alert-md btn-block'>Sorry! Invalid user credentials provided.</div>";
				}
				
			} CATCH(Facebook\Exceptions\FacebookResponseException $e) {
				// When Graph returns an error
				PRINT "<div class='alert alert-danger'>Graph returned an error: {$e->getMessage()}</div>";
				// Destroy all active sessions
				$this->session->sess_destroy();
				// redirecting user back to app login page
				redirect( config_item('manager_dashboard') . 'Login/Facebook', 'refresh:1000');
			} CATCH(Facebook\Exceptions\FacebookSDKException $e) {
				// When validation fails or other local issues
				PRINT "<div class='alert alert-danger'>Facebook SDK returned an error: {$e->getMessage()}</div>";
			}
		} ELSE {
			// replace your website URL same as added in the developers.Facebook.com/apps e.g. if you used http instead of https and you used            
			$this->loginUrl = $helper->getLoginUrl($this->redirectUrl, $permissions);
		}
		
		return $this;
	}
}
?>