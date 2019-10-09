<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
| WARNING: You MUST set this value!
|
| If it is not set, then CodeIgniter will try guess the protocol and path
| your installation, but due to security concerns the hostname will be set
| to $_SERVER['SERVER_ADDR'] if available, or localhost otherwise.
| The auto-detection mechanism exists only for convenience during
| development and MUST NOT be used in production!
|
| If you need to allow multiple domains, remember that this file is still
| a PHP script and you can easily do that on your own.
|
*/
$config['base_url'] = 'http://localhost/filemanager';
$config['manager_dashboard'] = 'http://localhost/filemanager/';
$config['rowsperpage'] = 40;
$config['serverversion'] = '2.0';
$config['site_url'] = 'MatrixFileManager.Com';
$config['site_name'] = 'MatrixAdmin FileManager';
$config['site_email'] = 'info@MatrixFileManager.Com';
$config['developer'] = 'Emmanuel Obeng';
$config['update_folder'] = '/filemanager/application/backups/';
$config['daily_uploads'] = 1024*1024*1024*500;
$config['server_space'] = 1024*1024*1024*1024*10;
$config['sionoff'] = 'Off';

// editable files:
$config["editable_ext"] =
	array(".asm", ".rc", ".hh", ".hxx", ".odl", ".idl", ".rc2", ".dlg", ".less"
	,".php", ".php3", ".php4", ".php5", ".phtml", ".inc", ".sql", ".csv"
	,".vb", ".vbs", ".bas", ".frm", ".cls", ".ctl", ".rb", ".htm", ".html", ".shtml", ".dhtml", ".xml"
	,".js", ".css", ".scss", ".cgi", ".cpp", ".c", ".cc", ".cxx", ".hpp", ".h", ".lua"
	,".pas", ".p", ".pl", ".java", ".py", ".sh", ".bat", ".tcl", ".tk"
	,".txt", ".ini", ".conf", ".properties", ".htaccess", ".htpasswd");
	
// common similar files:
$config["images_ext"] = array(".png", ".bmp", ".jpg", ".jpeg", ".gif", ".tif", ".ico" );
$config["audio_files"] = array("mp3","wav","midi","rm","ra","ram","pls","m3u","m3u");
$config["video_files"] = array("mp4","3gp","mpg","mpeg","mov","avi","swf");
$config["microsoft_docs"] = array("doc","docx","xls","xlsx","pdf");
$config["microsoft_docs"] = array("doc","docx","xls","xlsx","pdf");
$config["zipped_files"] = array("zip");
//------------------------------------------------------------------------------
// mime types: (description,image,extension)
$config["super_mimes"] = array(
	// dir, exe, file
	"dir"	=> array(  'dir', 'Dir', "assets/images/extension/folder.png"),
	"exe"	=> array(  'exe', 'exe', "assets/images/extension/exe.png",".exe", ".com", ".bin"),
	"file"	=> array(  'file', 'file', "assets/images/extension/document.png")
);
$config["used_mime_types"] = array(
	// text
	"text"	=> array(  'text', 'Text',   "assets/images/extension/txt.png",   ".txt"),

	// programming
	"php"	=> array(  'php', 'PHP',    "assets/images/extension/php.png",   ".php"),
	"php3"	=> array(  'php3', 'php3',   "assets/images/extension/php.png",  ".php3"),
	"php4"	=> array(  'php4', 'php4',   "assets/images/extension/php.png",  ".php4"),
	"php5"	=> array(  'php5', 'php5',   "assets/images/extension/php.png",  ".php5"),
	"phtml"	=> array(  'phtml', 'phtml',  "assets/images/extension/php.jpg", ".phtml"),
	"inc"	=> array(  'inc', 'inc',    "assets/images/extension/inc.png",   ".inc"),
	"sql"	=> array(  'sql', 'SQL',    "assets/images/extension/sql.jpg",   ".sql"),
	"pl"	=> array(  'pl', 'Perl',     "assets/images/extension/pl.png",    ".pl"),
	"cgi"	=> array(  'cgi', 'CGI',    "assets/images/extension/cgi.png",   ".cgi"),
	"py"	=> array(  'py', 'Python',     "assets/images/extension/py.ico",    ".py"),
	"sh"	=> array(  'sh', 'Shell',     "assets/images/extension/sh.png",    ".sh"),
	"c" 	=> array(  'c', 'C',      "assets/images/extension/c.png",     ".c"),
	"cc"	=> array(  'cc', 'CC',     "assets/images/extension/cc.png",    ".cc"),
	"cpp"	=> array(  'cpp', 'CPP',    "assets/images/extension/cpp.jpg",   ".cpp"),
	"cxx"	=> array(  'cxx', 'CXX',    "assets/images/extension/cxx.png",   ".cxx"),
	"h" 	=> array(  'h', 'H',      "assets/images/extension/h.png",     ".h"),
	"hpp" 	=> array(  'hpp', 'hpp',    "assets/images/extension/hpp.png",   ".hpp"),
	"java"	=> array(  'java', 'Java',   "assets/images/extension/java.jpg",  ".java"),
	"class"	=> array(  'class', 'Class',  "assets/images/extension/class.png", ".class"),
	"jar"	=> array(  'jar', 'Jar',    "assets/images/extension/jar.png",   ".jar"),
	"htaccess" => array( 'htaccess', 'htaccess', "assets/images/icons/text.png", ".htaccess"),
	// browser
	"htm"	=> array(  'htm', 'HTML',    "assets/images/extension/htm.png",   ".htm"),
	"html"	=> array(  'html', 'HTML',   "assets/images/extension/html.png",  ".html"),
	"shtml"	=> array(  'shtml', 'sHTML',  "assets/images/extension/html.png", ".shtml"),
	"dhtml"	=> array(  'dhtml', 'dHTML',  "assets/images/extension/html.png", ".dhtml"),
	"xhtml"	=> array(  'xhtml', 'XHTML',  "assets/images/extension/html.png", ".xhtml"),
	"xml"	=> array(  'xml', 'XML',    "assets/images/extension/xml.png",   ".xml"),
	"js"	=> array(  'js', 'Javascript',     "assets/images/extension/js.jpg",    ".js"),
	"css"	=> array(  'css', 'Cascading StyleSheet',    "assets/images/extension/css.png",   ".css"),
	"scss"	=> array(  'css', 'Cascading StyleSheet',    "assets/images/extension/css.png",   ".scss"),
	
	// images
	"gif"	=> array(  'gif', 'GIF',    "assets/images/extension/gif.png",   ".gif"),
	"jpg"	=> array(  'jpg', 'JPG',    "assets/images/extension/jpg.png",   ".jpg"),
	"jpeg"	=> array(  'jpeg', 'JPEG',   "assets/images/extension/jpeg.png",  ".jpeg"),
	"bmp"	=> array(  'bmp', 'Bitmap',    "assets/images/extension/bmp.png",   ".bmp"),
	"png"	=> array(  'png', 'PNG',    "assets/images/extension/png.png",   ".png"),
	
	// compressed
	"zip"	=> array(  'zip', 'ZIP',    "assets/images/extension/zip.png",   ".zip"),
	"tar"	=> array(  'tar', 'TAR',    "assets/images/extension/tar.png",   ".tar"),
	"tgz"	=> array(  'tgz', 'Tar/GZ',    "assets/images/extension/tgz.png",   ".tgz"),
	"gz"	=> array(  'gz', 'GZip',     "assets/images/extension/gz.png",    ".gz"),


	"bz2"	=> array(  'bz2', 'Bzip2',    "assets/images/extension/bz2.png",   ".bz2"),
	"tbz"	=> array(  'tbz', 'Tar/Bz2',    "assets/images/extension/tbz.png",   ".tbz"),
	"rar"	=> array(  'rar', 'RAR',    "assets/images/extension/rar.png",   ".rar"),

	// music
	"mp3"	=> array(  'mp3', 'Mp3',    "assets/images/extension/audio.png",   ".mp3"),
	"wav"	=> array(  'wav', 'WAV',    "assets/images/extension/wav.png",   ".wav"),
	"midi"	=> array(  'midi', 'Midi',   "assets/images/extension/midi.png",  ".mid"),
	"rm"	=> array(  'real', 'Real Media',   "assets/images/extension/rm.png",    ".rm"),
	"ra"	=> array(  'real', 'Real Audio',   "assets/images/extension/ra.png",    ".ra"),
	"ram"	=> array(  'real', 'Real Media',   "assets/images/extension/ram.png",   ".ram"),
	"pls"	=> array(  'pls', 'pls',    "assets/images/extension/pls.png",   ".pls"),
	"m3u"	=> array(  'm3u', 'm3u',    "assets/images/extension/m3u.png",   ".m3u"),

	// movie
	"mp4"	=> array(  'mp4', 'MP4',    "assets/images/extension/mpeg.png",   ".mp4"),
	"3gp"	=> array(  '3gp', '3GP',    "assets/images/extension/movie.jpg",   ".3gp"),
	"mpg"	=> array(  'mpg', 'MPG',    "assets/images/extension/mpg.png",   ".mpg"),
	"mpeg"	=> array(  'mpeg', 'MPG',  "assets/images/extension/mpeg.png",  ".mpeg"),
	"mov"	=> array(  'mov', 'MOV',    "assets/images/extension/mov.png",   ".mov"),
	"avi"	=> array(  'avi', 'AVI',    "assets/images/extension/avi.png",   ".avi"),
	"swf"	=> array(  'swf', 'SWF',    "assets/images/extension/swf.png",   ".swf"),
	
	// Micosoft / Adobe
	"doc"	=> array(  'doc', 'Word',    "assets/images/extension/doc.jpg",   ".doc"),
	"docx"	=> array(  'docx', 'Word',   "assets/images/extension/docx.jpg",  ".docx"),
	"xls"	=> array(  'xls', 'Excel',    "assets/images/extension/xls.png",   ".xls"),
	"xlsx"	=> array(  'xlsx', 'Excel',   "assets/images/extension/xlsx.png",  ".xlsx"),
	"rtf"	=> array(  'rtf', 'Rich Text Format',   "assets/images/extension/rtf.png",  ".rtf"),
	"txt"	=> array(  'txt', 'Text Document',   "assets/images/extension/txt.png",  ".txt"),
	
	"pdf"	=> array(  'pdf', 'PDF',    "assets/images/extension/pdf.png",   ".pdf")
);

//------------------------------------------------------------------------------
/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
|
| Typically this will be your index.php file, unless you've renamed it to
| something else. If you are using mod_rewrite to remove the page set this
| variable so that it is blank.
|
*/
$config['index_page'] = 'index.php';

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
|
| This item determines which server global should be used to retrieve the
| URI string.  The default setting of 'REQUEST_URI' works for most servers.
| If your links do not seem to work, try one of the other delicious flavors:
|
| 'REQUEST_URI'    Uses $_SERVER['REQUEST_URI']
| 'QUERY_STRING'   Uses $_SERVER['QUERY_STRING']
| 'PATH_INFO'      Uses $_SERVER['PATH_INFO']
|
| WARNING: If you set this to 'PATH_INFO', URIs will always be URL-decoded!
*/
$config['uri_protocol']	= 'REQUEST_URI';

/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
|
| This option allows you to add a suffix to all URLs generated by CodeIgniter.
| For more information please see the user guide:
|
| https://codeigniter.com/user_guide/general/urls.html
*/
$config['url_suffix'] = '';

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| This determines which set of language files should be used. Make sure
| there is an available translation if you intend to use something other
| than english.
|
*/
$config['language']	= 'english';

/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
|
| This determines which character set is used by default in various methods
| that require a character set to be provided.
|
| See http://php.net/htmlspecialchars for a list of supported charsets.
|
*/
$config['charset'] = 'UTF-8';

/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
|
| If you would like to use the 'hooks' feature you must enable it by
| setting this variable to TRUE (boolean).  See the user guide for details.
|
*/
$config['enable_hooks'] = FALSE;

/*
|--------------------------------------------------------------------------
| Class Extension Prefix
|--------------------------------------------------------------------------
|
| This item allows you to set the filename/classname prefix when extending
| native libraries.  For more information please see the user guide:
|
| https://codeigniter.com/user_guide/general/core_classes.html
| https://codeigniter.com/user_guide/general/creating_libraries.html
|
*/
$config['subclass_prefix'] = 'MY_';

/*
|--------------------------------------------------------------------------
| Composer auto-loading
|--------------------------------------------------------------------------
|
| Enabling this setting will tell CodeIgniter to look for a Composer
| package auto-loader script in application/vendor/autoload.php.
|
|	$config['composer_autoload'] = TRUE;
|
| Or if you have your vendor/ directory located somewhere else, you
| can opt to set a specific path as well:
|
|	$config['composer_autoload'] = '/path/to/vendor/autoload.php';
|
| For more information about Composer, please visit http://getcomposer.org/
|
| Note: This will NOT disable or override the CodeIgniter-specific
|	autoloading (application/config/autoload.php)
*/
$config['composer_autoload'] = FALSE;

/*
|--------------------------------------------------------------------------
| Allowed URL Characters
|--------------------------------------------------------------------------
|
| This lets you specify which characters are permitted within your URLs.
| When someone tries to submit a URL with disallowed characters they will
| get a warning message.
|
| As a security measure you are STRONGLY encouraged to restrict URLs to
| as few characters as possible.  By default only these are allowed: a-z 0-9~%.:_-
|
| Leave blank to allow all characters -- but only if you are insane.
|
| The configured value is actually a regular expression character group
| and it will be executed as: ! preg_match('/^[<permitted_uri_chars>]+$/i
|
| DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
|
*/
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';

/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
|
| By default CodeIgniter uses search-engine friendly segment based URLs:
| example.com/who/what/where/
|
| You can optionally enable standard query string based URLs:
| example.com?who=me&what=something&where=here
|
| Options are: TRUE or FALSE (boolean)
|
| The other items let you set the query string 'words' that will
| invoke your controllers and its functions:
| example.com/index.php?c=controller&m=function
|
| Please note that some of the helpers won't work as expected when
| this feature is enabled, since CodeIgniter is designed primarily to
| use segment based URLs.
|
*/
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';

/*
|--------------------------------------------------------------------------
| Allow $_GET array
|--------------------------------------------------------------------------
|
| By default CodeIgniter enables access to the $_GET array.  If for some
| reason you would like to disable it, set 'allow_get_array' to FALSE.
|
| WARNING: This feature is DEPRECATED and currently available only
|          for backwards compatibility purposes!
|
*/
$config['allow_get_array'] = TRUE;

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| You can enable error logging by setting a threshold over zero. The
| threshold determines what gets logged. Threshold options are:
|
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| You can also pass an array with threshold levels to show individual error types
|
| 	array(2) = Debug Messages, without Error Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise
| your log files will fill up very fast.
|
*/
$config['log_threshold'] = 1;

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| application/logs/ directory. Use a full server path with trailing slash.
|
*/
$config['log_path'] = '';

/*
|--------------------------------------------------------------------------
| Log File Extension
|--------------------------------------------------------------------------
|
| The default filename extension for log files. The default 'php' allows for
| protecting the log files via basic scripting, when they are to be stored
| under a publicly accessible directory.
|
| Note: Leaving it blank will default to 'php'.
|
*/
$config['log_file_extension'] = '';

/*
|--------------------------------------------------------------------------
| Log File Permissions
|--------------------------------------------------------------------------
|
| The file system permissions to be applied on newly created log files.
|
| IMPORTANT: This MUST be an integer (no quotes) and you MUST use octal
|            integer notation (i.e. 0700, 0644, etc.)
*/
$config['log_file_permissions'] = 0644;

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
|
| Each item that is logged has an associated date. You can use PHP date
| codes to set your own date formatting
|
*/
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Default Views Template Directory Path
|--------------------------------------------------------------------------
|
|
*/
$config['default_view_path'] = 'application/views/default/';
$config['default_assets_path'] = '/default';
$config['upload_path'] = 'assets/uploads/';
$config['thumbnail_path'] = 'assets/images/thumbnail/';
/*
|--------------------------------------------------------------------------
| Error Views Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| application/views/errors/ directory.  Use a full server path with trailing slash.
|
*/
$config['error_views_path'] = 'application/views/errors/';

/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| application/cache/ directory.  Use a full server path with trailing slash.
|
*/
$config['cache_path'] = '';

/*
|--------------------------------------------------------------------------
| Cache Include Query String
|--------------------------------------------------------------------------
|
| Whether to take the URL query string into consideration when generating
| output cache files. Valid options are:
|
|	FALSE      = Disabled
|	TRUE       = Enabled, take all query parameters into account.
|	             Please be aware that this may result in numerous cache
|	             files generated for the same page over and over again.
|	array('q') = Enabled, but only take into account the specified list
|	             of query parameters.
|
*/
$config['cache_query_string'] = FALSE;

/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
|
| If you use the Encryption class, you must set an encryption key.
| See the user guide for more info.
|
| https://codeigniter.com/user_guide/libraries/encryption.html
|
*/
$config['encryption_key'] = 'I99_Obeng_F109';

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
|
| 'sess_driver'
|
|	The storage driver to use: files, database, redis, memcached
|
| 'sess_cookie_name'
|
|	The session cookie name, must contain only [0-9a-z_-] characters
|
| 'sess_expiration'
|
|	The number of SECONDS you want the session to last.
|	Setting to 0 (zero) means expire when the browser is closed.
|
| 'sess_save_path'
|
|	The location to save sessions to, driver dependent.
|
|	For the 'files' driver, it's a path to a writable directory.
|	WARNING: Only absolute paths are supported!
|
|	For the 'database' driver, it's a table name.
|	Please read up the manual for the format with other session drivers.
|
|	IMPORTANT: You are REQUIRED to set a valid save path!
|
| 'sess_match_ip'
|
|	Whether to match the user's IP address when reading the session data.
|
|	WARNING: If you're using the database driver, don't forget to update
|	         your session table's PRIMARY KEY when changing this setting.
|
| 'sess_time_to_update'
|
|	How many seconds between CI regenerating the session ID.
|
| 'sess_regenerate_destroy'
|
|	Whether to destroy session data associated with the old session ID
|	when auto-regenerating the session ID. When set to FALSE, the data
|	will be later deleted by the garbage collector.
|
| Other session cookie settings are shared with the rest of the application,
| except for 'cookie_prefix' and 'cookie_httponly', which are ignored here.
|
*/
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'mfa_session';
$config['sess_expiration'] = 2500;
$config['sess_save_path'] = "/filemanager/application/sessions/";
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 120;
$config['sess_regenerate_destroy'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
|
| 'cookie_prefix'   = Set a cookie name prefix if you need to avoid collisions
| 'cookie_domain'   = Set to .your-domain.com for site-wide cookies
| 'cookie_path'     = Typically will be a forward slash
| 'cookie_secure'   = Cookie will only be set if a secure HTTPS connection exists.
| 'cookie_httponly' = Cookie will only be accessible via HTTP(S) (no javascript)
|
| Note: These settings (with the exception of 'cookie_prefix' and
|       'cookie_httponly') will also affect sessions.
|
*/
$config['cookie_prefix']	= '';
$config['cookie_domain']	= '';
$config['cookie_path']		= '/';
$config['cookie_secure']	= FALSE;
$config['cookie_httponly'] 	= FALSE;

/*
|--------------------------------------------------------------------------
| Standardize newlines
|--------------------------------------------------------------------------
|
| Determines whether to standardize newline characters in input data,
| meaning to replace \r\n, \r, \n occurrences with the PHP_EOL value.
|
| WARNING: This feature is DEPRECATED and currently available only
|          for backwards compatibility purposes!
|
*/
$config['standardize_newlines'] = FALSE;

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
|
| Determines whether the XSS filter is always active when GET, POST or
| COOKIE data is encountered
|
| WARNING: This feature is DEPRECATED and currently available only
|          for backwards compatibility purposes!
|
*/
$config['global_xss_filtering'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cross Site Request Forgery
|--------------------------------------------------------------------------
| Enables a CSRF cookie token to be set. When set to TRUE, token will be
| checked on a submitted form. If you are accepting user data, it is strongly
| recommended CSRF protection be enabled.
|
| 'csrf_token_name' = The token name
| 'csrf_cookie_name' = The cookie name
| 'csrf_expire' = The number in seconds the token should expire.
| 'csrf_regenerate' = Regenerate token on every submission
| 'csrf_exclude_uris' = Array of URIs which ignore CSRF checks
*/
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();

/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
|
| Enables Gzip output compression for faster page loads.  When enabled,
| the output class will test whether your server supports Gzip.
| Even if it does, however, not all browsers support compression
| so enable only if you are reasonably sure your visitors can handle it.
|
| Only used if zlib.output_compression is turned off in your php.ini.
| Please do not use it together with httpd-level output compression.
|
| VERY IMPORTANT:  If you are getting a blank page when compression is enabled it
| means you are prematurely outputting something to your browser. It could
| even be a line of whitespace at the end of one of your scripts.  For
| compression to work, nothing can be sent before the output buffer is called
| by the output class.  Do not 'echo' any values with compression enabled.
|
*/
$config['compress_output'] = FALSE;

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
|
| Options are 'local' or any PHP supported timezone. This preference tells
| the system whether to use your server's local time as the master 'now'
| reference, or convert it to the configured one timezone. See the 'date
| helper' page of the user guide for information regarding date handling.
|
*/
$config['time_reference'] = 'local';

/*
|--------------------------------------------------------------------------
| Rewrite PHP Short Tags
|--------------------------------------------------------------------------
|
| If your PHP installation does not have short tag support enabled CI
| can rewrite the tags on-the-fly, enabling you to utilize that syntax
| in your view files.  Options are TRUE or FALSE (boolean)
|
| Note: You need to have eval() enabled for this to work.
|
*/
$config['rewrite_short_tags'] = FALSE;

/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
|
| If your server is behind a reverse proxy, you must whitelist the proxy
| IP addresses from which CodeIgniter should trust headers such as
| HTTP_X_FORWARDED_FOR and HTTP_CLIENT_IP in order to properly identify
| the visitor's IP address.
|
| You can use both an array or a comma-separated list of proxy addresses,
| as well as specifying whole subnets. Here are a few examples:
|
| Comma-separated:	'10.0.1.200,192.168.5.0/24'
| Array:		array('10.0.1.200', '192.168.5.0/24')
*/
$config['proxy_ips'] = '';