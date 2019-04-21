<?php

defined('BASEPATH') OR exit('No direct script access allowed');

const INVENTORY = '1.0.1';

require_once(BASEPATH.'core/Common.php');

load_file(
	array('constants'=>'config')
);

if (is_php('5.6')) {
	ini_set('php.internal_encoding', config_item('charset'));
}

$DB = load_class('DB', 'core');	
$config = load_class('config', 'core');
$session = load_class('session', 'libraries\Session');
load_core('Security');
load_helpers(ARRAY('string_helper','email_helper','url_helper'));

global $DB;