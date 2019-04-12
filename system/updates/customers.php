<?php
//FILE USED BY THE ADMINISTRATOR
//fetch the global objects already created
global $DB, $admin_user;

// initializing
$firstname=$lastname=$gender=$region=$contact2=$contact=$website=$address=$email="";
$balance=$outstanding=0.00;
//check if user is logged in
if($admin_user->logged_InControlled() == true) {

	if(isset($_POST["parsedform"])) {
		
		$admin_logged = $admin_user->return_username();
		
		$firstname = strtoupper(xss_clean($_POST["firstname"]));
		$customer_id = xss_clean($_POST["customer_id"]);
		$lastname = strtoupper(xss_clean($_POST["lastname"]));
		$gender = trim(xss_clean($_POST["gender"]));
		$region = trim(xss_clean($_POST["region"]));
		$contact = trim(xss_clean($_POST["contact"]));
		$contact2 = xss_clean($_POST["contact2"]);
		$email = xss_clean($_POST["email"]);
		$website = xss_clean($_POST["website"]);
		$balance = (int)xss_clean($_POST["balance"]);
		$outstanding = (int)xss_clean($_POST["outstanding"]);
		$address = strtoupper(xss_clean($_POST["address"]));
		if(isset($_POST["ref"]) and strlen($_POST["ref"]) > 5){
			$ref = xss_clean($_POST["ref"]);
		}
		
		if($gender == "MALE")
			$image = "assets/images/avatar-1.png";
		else
			$image = "assets/images/avatar-4.png";
		
		if(isset($_POST["customerEdit"])) {
			
			$DB->just_exec("update _customers set gender='$gender',firstname='$firstname',lastname='$lastname',email='$email', phone='$contact', contact='$contact', contact2='$contact2', region='$region', website='$website', balance='$balance', outstanding='$outstanding', address='$address', image='$image', last_update=now(), modified_by='$admin_logged' where customer_id='$customer_id' and store_id='".STORE_ID."'");
			
			#record new admin history
			$DB->just_exec("insert into _activity_logs set store_id='".STORE_ID."', full_date=now(), date_recorded=now(), admin_id='$admin_logged', activity_page='customer', activity_id='$customer_id', activity_details='$customer_id', activity_description='Customer: <strong>\"$firstname $lastname ($contact)\"</strong> details has been updated.'");
					
		} elseif(isset($_POST["customerAdd"])) {
			
			$customer_id = (int)($DB->max_all("id","_customers"))+2;
			$DB->just_exec("insert into _customers set store_id='".STORE_ID."', customer_id='LEU$customer_id',gender='$gender',firstname='$firstname', website='$website', lastname='$lastname',email='$email', phone='$contact', contact='$contact', contact2='$contact2', region='$region', balance='$balance', outstanding='$outstanding', address='$address', image='$image', date_recorded=now(), date_added=now(), added_by='$admin_logged'");
			
			#set a new user session for the 
			#customer before checkout
			if(isset($_GET["checkout"])) {
				$_SESSION["Main_guest_Id2:"] = "LEU$customer_id";
			}
			
			#record new admin history
			$DB->just_exec("insert into _activity_logs set store_id='".STORE_ID."', date_recorded=now(), full_date=now(), admin_id='$admin_logged', activity_page='customer', activity_id='LEU$customer_id', activity_details='LEU$customer_id', activity_description='New Customer: <strong>\"$firstname $lastname ($contact)\"</strong> details has been inserted.'");
			
		}
		
		if(isset($_POST["ref"]) and strlen($_POST["ref"]) > 5){
			print "<script>window.location.href='".xss_clean($_POST["ref"])."'</script>";
		} else {
			if(isset($_POST["customerEdit"])) {
				print "<script>window.location.href='".SITE_URL."/customers?msg=1'</script>";
			} elseif(isset($_POST["customerAdd"])) {
				print "<script>window.location.href='".SITE_URL."/customers?msg=3'</script>";
			}
		}
		
	}
	
}