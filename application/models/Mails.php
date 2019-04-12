<?php 

class Mails {
	
	public function __construct() {
		
		global $db, $functions;
		
		$this->functions = $functions;
		$this->db = $db;
		
		$this->config = $this->db->call_connection();
		
	}
	
	public function send_mail($fullname, $from_email, $to_name, $to_email, $subject, $message) {
		
		global $libs;
		
		$headers = "MIME-Version: 1.0\r\n"; 
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
		$headers .= "X-Priority: 1\r\n"; 
		$headers .= "To: ".strtoupper($to_name)." <$to_email>" . "\r\n";
		$headers .= "From: $fullname <$from_email>" . "\r\n";
		$headers .= "Reply-To: ".SITE_NAME." <".SITE_EMAIL.">" . "\r\n";
		$headers .= "X-Mailer: PHP/" . phpversion();
		
		
		$template = $message."<hr>";
		
		$template .= "Thank you.<br>";
		$template .= $libs->fetch()->name;
		$template .= "<br> Tech Team.";
		
		
		$message = wordwrap($template, 70);

		@mail($to_email, $subject, $message, $headers);
		
		
		return true;
	}
	
	
	public function send_mail2($from_name, $from_email, $to_name, $to_email, $subject, $message) {
	
			
			global $libs;
		
			$headers = "MIME-Version: 1.0\r\n"; 
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
			$headers .= "X-Priority: 1\r\n"; 
			$headers .= "To: ".strtoupper($to_name)." <$to_email>" . "\r\n";
			$headers .= "From: $from_name <$from_email>" . "\r\n";
			$headers .= "Reply-To: ".SITE_NAME." <".SITE_EMAIL.">" . "\r\n";
			$headers .= "X-Mailer: PHP/" . phpversion();
		
			$messageOutput = "<div marginwidth=\"0\" marginheight=\"0\">
				<div dir=\"ltr\" style=\"background-color:#f5f5f5;margin:0;padding:70px 0 70px 0;width:100%\">
					<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" height=\"100%\" width=\"100%\">
					<tr>
						<td align=\"center\" valign=\"top\">
							<div>
							</div>
							<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"700\" style=\"background-color:#fdfdfd;border:1px solid #dcdcdc;border-radius:3px!important\">
							<tr>
							<td align=\"center\" valign=\"top\">
							<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"700\" style=\"background-color:#557da1;border-radius:3px 3px 0 0!important;color:#ffffff;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif\"><tr>
							<td style=\"padding:36px 48px;display:block;background-color:#6097c9\">";
						$messageOutput .= "<h1 style=\"color:#ffffff;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:25px;font-weight:300;line-height:70%;margin:0;text-align:left\">$subject</h1>";
						$messageOutput .= "</td>
							</tr></table>
							</td>
							</tr>
							<tr>
							<td align=\"center\" valign=\"top\">
							<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"700\"><tr>
							<td valign=\"top\" style=\"background-color:#fdfdfd\">
							<table border=\"0\" cellpadding=\"20\" cellspacing=\"0\" width=\"100%\"><tr>
							<td valign=\"top\" style=\"padding:48px\">
							<div style=\"color:#737373;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:14px;line-height:150%;text-align:left\">";
							
				$messageOutput .= $message;				
					
				$messageOutput .= "<hr><br>Thank you.<br>";
				$messageOutput .= $libs->fetch()->name;
				$messageOutput .= "<br> Tech Team.";
				$messageOutput .= "
							</div></td></tr></table></td></tr></table>
							</td></tr><tr><td align=\"center\" valign=\"top\">
							<table border=\"0\" cellpadding=\"10\" cellspacing=\"0\" width=\"700\"><tr>
							<td valign=\"top\" style=\"padding:0\">
							<table border=\"0\" cellpadding=\"10\" cellspacing=\"0\" width=\"100%\"><tr>
							<td colspan=\"2\" valign=\"middle\" style=\"padding:0 48px 48px 48px;border:0;color:#99b1c7;font-family:Arial;font-size:12px;line-height:125%;text-align:center\">
							<p>".SITE_NAME." - Powered by ViSaMi Net Solutions</p>
							</td></tr></table></td></tr></table></td></tr></table>
						</td></tr></table></div></div>";
		
				$template = $messageOutput;
				$message = wordwrap($template, 70);
		
				@mail($to_email, "[GoodInventory.com] ".$subject, $message, $headers);
				
				return true;
		}
	
}
?>