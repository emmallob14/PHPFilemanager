<?php
defined('BASEPATH') OR exit('No direct script access allowed');

# set the constants for the database connection
defined('DB_HOST')  OR define('DB_HOST', "localhost");
defined('DB_USER')  OR define('DB_USER', "root");
defined('DB_PASS')  OR define('DB_PASS', "");
defined('DB_NAME')  OR define('DB_NAME', "filemanager");


define('TIME_PERIOD', 60);
define('ATTEMPTS_NUMBER', 7);


defined('SITE_DATE_FORMAT') 		OR define('SITE_DATE_FORMAT', 'd M Y H:iA');
defined('SITE_URL') 				OR define('SITE_URL', config_item('base_url'));

defined('userLoggedIn')				OR define("userLoggedIn", "userLoggedIn");
defined('userId')					OR define("userId", "userId");
defined('userName')					OR define("userName", "userName");