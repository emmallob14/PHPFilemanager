<?php
// ensure this file is being included by a parent file
if( !defined( 'SITE_URL' ) && !defined( 'SITE_DATE_FORMAT' ) ) die( 'Restricted access');
class Server {

	function get_php_setting($val, $recommended=1) {
		$value = ini_get($val);
		$r = ( $value == $recommended ? 1 : 0);
		if( empty($value)) {
			$onoff = 1;
		}
		else {
			$onoff = 0;
		}
		return $r ? '<span style="color: green;">On</span>' : '<span style="color: red;">Off</span>';
	}

	function get_server_software() {
		if (isset($_SERVER['SERVER_SOFTWARE'])) {
			return $_SERVER['SERVER_SOFTWARE'];
		} else if (($sf = getenv('SERVER_SOFTWARE'))) {
			return $sf;
		} else {
			return 'n/a';
		}
	}

	function php_info() {
		ob_start();
		phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
		$phpinfo = ob_get_contents();
		ob_end_clean();
		preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
		$output = preg_replace('#<table#', '<table class="phpinfo table"', $output[1][0]);
		$output = '<div class="body-wrap">'.$output.'</div>';
		$output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
		$output = preg_replace('#border="0" cellpadding="3" width="600"#', 'border="0" cellspacing="1" cellpadding="4" width="95%"', $output);
		$output = preg_replace('#<hr />#', '', $output);
		echo $output;
	}

}