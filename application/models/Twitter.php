<?php 
// ensure this file is being included by a parent file
if( !defined( 'SITE_URL' ) && !defined( 'SITE_DATE_FORMAT' ) ) die( 'Restricted access' );
// Load the twitter library class
load_library('Twitter');
USE Abraham\TwitterOAuth\TwitterOAuth;

class Twitter {
	
	public $twitterLoginUrl;
	
	public function __construct() {
		
		global $DB, $session;
		
		try {
			$this->db = $DB;
			$this->user_agent = load_class('User_agent', 'libraries');
			$this->session = $session;
			$this->output = "";
			
			// Set additional variables
			$this->twi_consumer_key = "H6no4zSm0uhe1RhXWZc83NT1Q";
			$this->twi_consumer_secret = "dayup0speOQ2flNfUJA41hzjJ0TQJFrJKcRz8sVZbVuwy6EBHL";
			$this->callbackUrl = config_item('manager_dashboard')."Login/Twitter";
			
			
			DEFINE('CONSUMER_KEY', $this->twi_consumer_key);
			DEFINE('CONSUMER_SECRET', $this->twi_consumer_secret);
			DEFINE('OAUTH_CALLBACK', $this->callbackUrl);
			
			IF (ISSET($_REQUEST['oauth_verifier'], $_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] == $_SESSION['oauth_token']) { 
		
				$request_token = [];
				$request_token['oauth_token'] = $_SESSION['oauth_token'];
				$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];
				$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);
				$access_token = $connection->oauth("oauth/access_token", ARRAY("oauth_verifier" => $_REQUEST['oauth_verifier']));
				$_SESSION['access_token'] = $access_token;
			
			}

			IF (!ISSET($_SESSION['access_token'])) {
				
				$connection = NEW TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
				$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
				$_SESSION['oauth_token'] = $request_token['oauth_token'];
				$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
				$this->twitterLoginUrl = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
				
			} ELSE {

				$access_token = $_SESSION['access_token'];
				$connection = NEW TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
				
				$user = $connection->get("account/verify_credentials", ['include_email' => 'true']);
				
				// use the Twitter username to confirm valid login
				$AccountQuery = $this->db->query("select * from _admin where twitter_username='{$user->screen_name}' and activated='1' and status='1'");
				IF(COUNT($AccountQuery) == 1) {
					FOREACH($AccountQuery as $results) {
						#set some sessions for the user
						$this->session->set_userdata(
							ARRAY(
								OFF_SESSION_ID => $results["office_id"],
								UNAME_SESS_ID => $results["username"],
								UID_SESS_ID => $results["id"],
								USER_FULLNAME => $results["firstname"]." ".$results["lastname"],
								USER_EMAIL => $results["email"],
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
									
						$this->db->query("insert into _admin_log_history set username='{$results["username"]}', lastlogin=now(), log_ipaddress='$ip', log_browser='$br', office_id='".$this->session->userdata(OFF_SESSION_ID)."', log_platform='TwitterOAuth: ".$this->user_agent->agent_string()."'");
						
						# redirect the user to the profile page if it has "code" GET variable
						redirect( config_item('manager_dashboard') . 'Dashboard', 'refresh:1000');
					}
				} ELSE {
					redirect( config_item('manager_dashboard') . 'Login/Twitter/Failed', 'refresh:1000');
				}
				
			}

			return $this;
		
		} catch(TwitterOAuthException $e) {
			
		}
		
	}
	
}
?>