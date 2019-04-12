<?php
#start a new session
if (!isset($_SESSION)) {
    session_start();
}
//FILE USED BY THE ADMINISTRATOR
//fetch the global objects already created
global $DB, $admin_user;
$products = new Products;

//check if user is logged in
if($admin_user->logged_InControlled() == true) {

	if(isset($_POST["parsedform"])) {
		
		$admin_logged = $admin_user->return_username();
		
		$fullname = strtoupper(xss_clean($_POST["fullname"]));
		$supplier_id = xss_clean($_POST["supplier_id"]);
		$region = trim(xss_clean($_POST["region"]));
		$contact = trim(xss_clean($_POST["contact"]));
		$contact2 = xss_clean($_POST["contact2"]);
		$email = strtoupper(xss_clean($_POST["email"]));
		$website = strtoupper(xss_clean($_POST["website"]));
		$balance = (int)xss_clean($_POST["balance"]);
		$address = strtoupper(xss_clean($_POST["address"]));
		
		if(isset($_POST["supplierEdit"])) {
			
			$DB->just_exec("update _suppliers set fullname='$fullname',email='$email', contact='$contact', contact2='$contact2', website='$website', region='$region', balance='$balance',address='$address', last_update=now(), modified_by='$admin_logged' where supplier_id='$supplier_id' and store_id='".STORE_ID."'");
			
			#record new admin history
			$DB->just_exec("insert into _activity_logs set store_id='".STORE_ID."', full_date=now(), date_recorded=now(), admin_id='$admin_logged', activity_page='supplier', activity_id='$supplier_id', activity_details='$supplier_id', activity_description='Supplier: <strong>\"$fullname ($contact)\"</strong> details has been updated.'");
			
		} elseif(isset($_POST["supplierAdd"])) {
			
			$supplier_id = (int)($DB->max_all("id","_suppliers"))+2;
			$DB->just_exec("insert into _suppliers set store_id='".STORE_ID."', supplier_id='LES$supplier_id',fullname='$fullname',email='$email', contact='$contact', website='$website', contact2='$contact2', region='$region', balance='$balance', address='$address', date_added=now(), added_by='$admin_logged'");	
			
			#record new admin history
			$DB->just_exec("insert into _activity_logs set store_id='".STORE_ID."', full_date=now(), date_recorded=now(), admin_id='$admin_logged', activity_page='supplier', activity_id='LES$supplier_id', activity_details='LES$supplier_id', activity_description='New Supplier: <strong>\"$fullname ($contact)\"</strong> details has been inserted.'");
			
		}
		
		if(isset($_POST["supplierEdit"])) {
			print "<script>window.location.href='".SITE_URL."/suppliers?msg=1'</script>";
		} elseif(isset($_POST["supplierAdd"])) {
			print "<script>window.location.href='".SITE_URL."/suppliers?msg=3'</script>";
		}
		
	}
	
}