<?php

defined('BASEPATH') OR exit('No direct script access allowed');

const INVENTORY = '1.0.1';

#display errors
error_reporting(E_ALL);
ini_set("display_errors", 0);

#set new places for my error recordings
ini_set("log_errors","1");
ini_set("error_log", "error_log");

require_once(BASEPATH.'core/Common.php');

load_file(
	array('constants'=>'config')
);

if (is_php('5.6')) {
	ini_set('php.internal_encoding', config_item('charset'));
}

$DB = load_class('DB', 'core');	
$config = load_class('config', 'core');
$session = load_class('Session', 'libraries\Session');
$server = load_class('server', 'models');
load_core('Security');
load_helpers(ARRAY('string_helper','email_helper','url_helper','upload_helper','file_helper','time_helper'));
load_lang(ARRAY('globals'));

global $DB, $lang;