<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2018, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2018, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

# GET THE RIGHT URL
load_file(
	ARRAY(
		'string_helper'=>'helpers'
	)
);

/**
 * CodeIgniter Email Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/helpers/email_helper.html
 */

// ------------------------------------------------------------------------

// --------------------------------------------------------------------

$_headers		= array();
$_charset		= 'UTF-8';
$_crlf			= "\n";
$_useragent		= config_item('site_name');

if ( ! function_exists('valid_email'))
{
	/**
	 * Validate email address
	 *
	 * @deprecated	3.0.0	Use PHP's filter_var() instead
	 * @param	string	$email
	 * @return	bool
	 */
	function valid_email($email)
	{
		return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
	}
}

function validate_email($email)
{
	$validate = load_class('form_validation', 'libraries');
	
	if ( ! is_array($email))
	{
		return FALSE;
	}

	foreach ($email as $val)
	{
		if ( ! $validate->valid_email($val))
		{
			return FALSE;
		}
	}

	return TRUE;
}

function template($temp_id = null, $subject = null, $message = null) {
	
	global $config, $DB, $_useragent;
	
	load_helpers('url_helper');
	
	// if the admin user choses to send the mail using the default template
	// display this portion
	if($temp_id == 'default') {
		
		// parse the default template 
		$template = '<section style="font-size:15px">
				<div class="email-tem" style="background: #efefef;position: relative;overflow: hidden;">
					<div class="email-tem-inn" style="width: 90%;margin: 0 auto;padding: 50px; background: #ffffff;">
						<div class="email-tem-main" style="background: #fdfdfd;box-shadow: 0px 10px 24px -10px rgba(0, 0, 0, 0.8);margin-bottom: 50px;border-radius: 10px;">
							<div class="email-tem-head" style="width: 100%;background: #006df0 url(\''.$config->base_url().'assets/images/mail/bg.png\') repeat;padding: 50px;box-sizing: border-box;border-radius: 5px 5px 0px 0px;">
								<h2 style="color: #fff;font-size: 32px;text-transform: capitalize;">
									<img style="float: left;padding-right: 25px;width: 100px;" src="'.$config->base_url().'assets/images/logo.png" alt=""> '.$subject.'
								</h2>
							</div>
							<div class="email-tem-body" style="padding: 50px;">
								'.$message.'
							</div>
						</div>
						<div class="email-tem-foot">
							<h4 style="text-align: center;">Stay in touch</h4>
							<p style="margin-bottom: 0px;padding-top: 5px;font-size: 13px;text-align: center;">Email sent by '.$_useragent.'</p>
							<p style="margin-bottom: 0px;padding-top: 5px;font-size: 13px;text-align: center;">copyrights &copy; '.date('Y').' '.$_useragent.'. All rights reserved.</p>
						</div>
					</div>
				</div>
			</section>';
	}
	
	// confirm the email template id that the user has parsed 
	if(preg_match("/^[0-9]+$/", $temp_id)) {
		
		// set the email_template_id to parse 
		$template_id = (int)$temp_id;
		
		//query the database for the id that you want to select
		$query = $DB->where(
			'sd_email_templates', 
			'*',
			array(
				'id'=>"='$template_id'",
				'status'=>"='1'"
			)
		);
		
		// get the number of rows found
		if($DB->num_rows($query)) {
			
			// continue and fetch the template information using 
			// the foreach loop function
			foreach($query as $template) {
				
				// assign a variable to the template 
				$temp = $template['body'];
				
				// confirm that the $subject and $message variables were parsed
				if(!empty($subject) and !empty($message)) {
					// replace some items that have been listed in the
					// database template and replace it.
					$temp = str_ireplace(
						array('{subject}','{base_url}','{site_name}','{copy_date}','{message}'),
						array($subject, $config->base_url(), $_useragent, date('Y'), $message),
						$template['body']
					);
				}
				
			}
			
			// set the template to send out to the user.
			$template = auto_link($temp);
			
		} else {
			$template = 'Failed';
		}
	}
	
	return $template;
}

// ------------------------------------------------------------------------
if ( ! function_exists('message'))
{
	function message($body) {
		$_body = rtrim(str_replace("\r", '', $body));

		/* strip slashes only if magic quotes is ON
		   if we do it with magic quotes OFF, it strips real, user-inputted chars.

		   NOTE: In PHP 5.4 get_magic_quotes_gpc() will always return 0 and
			 it will probably not exist in future versions at all.
		*/
		if ( ! is_php('5.4') && get_magic_quotes_gpc())
		{
			$_body = stripslashes($_body);
		}
		
		return $_body;
	}
}


// --------------------------------------------------------------------

/**
 * Prep Q Encoding
 *
 * Performs "Q Encoding" on a string for use in email headers.
 * It's related but not identical to quoted-printable, so it has its
 * own method.
 *
 * @param	string
 * @return	string
 */
function _prep_q_encoding($str)
{
	global $_charset, $_crlf;
	
	$str = str_replace(array("\r", "\n"), '', $str);
	
	if ($_charset === 'UTF-8')
	{
		// Note: We used to have mb_encode_mimeheader() as the first choice
		//       here, but it turned out to be buggy and unreliable. DO NOT
		//       re-add it! -- Narf
		if (ICONV_ENABLED === TRUE)
		{
			$output = @iconv_mime_encode('', $str,
				array(
					'scheme' => 'Q',
					'line-length' => 76,
					'input-charset' => $_charset,
					'output-charset' => $_charset,
					'line-break-chars' => $_crlf
				)
			);

			// There are reports that iconv_mime_encode() might fail and return FALSE
			if ($output !== FALSE)
			{
				// iconv_mime_encode() will always put a header field name.
				// We've passed it an empty one, but it still prepends our
				// encoded string with ': ', so we need to strip it.
				return substr($output, 2);
			}

			$chars = iconv_strlen($str, 'UTF-8');
		}
		elseif (MB_ENABLED === TRUE)
		{
			$chars = mb_strlen($str, 'UTF-8');
		}
	}

	// We might already have this set for UTF-8
	isset($chars) OR $chars = strlen($str);

	$output = '=?'.$_charset.'?Q?';
	for ($i = 0, $length = strlen($output); $i < $chars; $i++)
	{
		$chr = ($_charset === 'UTF-8' && ICONV_ENABLED === TRUE)
			? '='.implode('=', str_split(strtoupper(bin2hex(iconv_substr($str, $i, 1, $_charset))), 2))
			: '='.strtoupper(bin2hex($str[$i]));

		// RFC 2045 sets a limit of 76 characters per line.
		// We'll append ?= to the end of each line though.
		if ($length + ($l = strlen($chr)) > 74)
		{
			$output .= '?='.$_crlf // EOL
				.' =?'.$_charset.'?Q?'.$chr; // New line
			$length = 6 + strlen($_charset) + $l; // Reset the length for the new line
		}
		else
		{
			$output .= $chr;
			$length += $l;
		}
	}

	// End the header
	return $output.'?=';
}

if ( ! function_exists('_str_to_array'))
{
	/**
	 * Convert a String to an Array
	 *
	 * @param	string
	 * @return	array
	 */
	function _str_to_array($email)
	{
		if ( ! is_array($email))
		{
			return (strpos($email, ',') !== FALSE)
				? preg_split('/[\s,]/', $email, -1, PREG_SPLIT_NO_EMPTY)
				: (array) trim($email);
		}

		return $email;
	}
}

function set_header($header, $value)
{
	global $_headers;
	
	$_headers[$header] = str_replace(array("\n", "\r"), '', $value);
	return $_headers[$header];
}

/**
 * Clean Extended Email Address: Joe Smith <joe@smith.com>
 *
 * @param	string
 * @return	string
 */
function clean_email($email)
{
	if ( ! is_array($email))
	{
		return preg_match('/\<(.*)\>/', $email, $match) ? $match[1] : $email;
	}

	$clean_email = array();

	foreach ($email as $addy)
	{
		$clean_email[] = preg_match('/\<(.*)\>/', $addy, $match) ? $match[1] : $addy;
	}

	return $clean_email;
}

function subject($subject)
{
	$subject = _prep_q_encoding($subject);
	set_header('Subject', $subject);
	return $this;
}

function _get_message_id()
{
	global $_headers;
	$from = str_replace(array('>', '<'), '', $_headers['Return-Path']);
	return '<'.uniqid('').strstr($from, '@').'>';
}
	
function _build_headers()
{
	global $_useragent, $_headers;
	set_header('User-Agent', $_useragent);
	set_header('X-Sender', clean_email($_headers['From']));
	set_header('X-Mailer', $_useragent);
	set_header('X-Priority', 1);
	set_header('Message-ID', _get_message_id());
	set_header('Mime-Version', '1.0');
	set_header('Content-Type', 'text/html; charset=ISO-8859-1');
}
	
// ------------------------------------------------------------------------
if ( ! function_exists('send_email'))
{
	
	/**
	 * Send an email
	 *
	 * @deprecated	3.0.0	Use PHP's mail() instead
	 * @param	array 	$to
	 * @param	string	$subject
	 * @param	string	$message
	 * @param	string	$name
	 * @param	string	$from
	 * @param	array	$cc
	 * @param	int		$temp_id
	 * @param	(bool)	$save_copy	default=true
	 * @return	(bool)
	 */
	function send_email($to, $_subject, $_message, $name=NULL, $from, $cc = NULL, $temp_id = 'default', $user_id=NULL)
	{
		$save_copy = true;
		
		# create random strings 
		load_helpers('string_helper');
		$random_string = random_string('alnum', 45);

		# initialize the mail sender variables
		$errors_found 	= true;
		$_header_str 	= '';
		global $_headers, $DB;
		
		//clean the sender of the email
		$from = clean_email(_str_to_array($from));
		set_header('From', implode(', ', $from));
		set_header('Reply-To', implode(', ', $from));
		set_header('Return-Path', '<'.$from[0].'>');
		
		if(!empty($name)) {
			$name = $name;
		}
		
		//clean the receipient of the email
		$to = clean_email(_str_to_array($to));
		set_header('To', implode(', ', $to));
		$_recipients = $to;
		
		// clean the persons whom we want to copy the message to.
		$cc = clean_email(_str_to_array($cc));
		set_header('Cc', implode(', ', $cc));
		
	
		//build all the headers
		_build_headers();
		
		//confirm that the receipients are set 
		if (is_array($_recipients)) {
			$_recipients = implode(', ', $_recipients);
		}
		
		// clean the senders email address 
		$from = clean_email($_headers['Return-Path']);
		
		
		//clean the message to get a neater one 
		$_message = message($_message);
		
		// submit the subject and message to the template function 
		// this will call the default template to be used 
		$_finalbody = template($temp_id, $_subject, $_message);
		
		//using foreach loop to get the headers
		foreach ($_headers as $key => $val) {
			$val = trim($val);
			if ($val !== '') {
				$_header_str .= $key.': '.$val."\r\n";
			}
		}
		
		$_finalbody = wordwrap($_finalbody, 70);
		
		if($_finalbody == 'Failed') {
			return;
		} else {
			
			if($save_copy) {
				$encrypt = load_class('encrypt', 'libraries');
				$slug = random_string('alnum', mt_rand(20, 25));
				// record the email in the database 
				$DB->touch(EMAIL_TABLE, 
					array(
						'slug'=>$slug,
						'send_to'=>$_recipients, 'user_id'=>$user_id, 'sent_from'=>$from, 
						'subject'=>$encrypt->encode($_subject, $slug), 'body'=>$encrypt->encode($_message, $slug), 'template'=>$temp_id,
						'sent_by'=>$from, 'headers'=>$encrypt->encode($_header_str, $slug)
					), 
					NULL, 'INSERT'
				);
			}
			// send the email
			@mail($_recipients, $_subject, $_finalbody, $_header_str, '-f '.$from);
			return true;
		}
		
	}
}