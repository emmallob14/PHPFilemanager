<?php
#initial 
global $DB, $functions, $libs;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
			
		IF(($SITEURL[1] == "doSearch") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "searchUser")) {
			#get the items and their values
			$user_name = xss_clean($_POST["Name"]);
			$user_id = $session->userdata(":lifeID");
			$office_id = $session->userdata("officeID");
			
			$Query = $DB->query("SELECT * FROM _admin WHERE fullname LIKE '%$user_name%' AND status='1' AND activated='1' AND office_id='$office_id' AND id !='$user_id'");
			
			IF(COUNT($Query) < 1) {
				PRINT "<div class='alert alert-danger btn-block'>Sorry! No user found with the specified name.</div>";
			} ELSE {
				?>
				<div class="widget-content">
				<div class="todo">
				  <ul>
					<?PHP FOREACH($Query as $Result) { ?>
					<li class="clearfix">
					  <div class="txt"> <span class="by label">Add</span> <?php print $Result["fullname"]; ?> </div>
					  <div class="pull-right"><a class="tip add_user btn btn-primary" href="javascript:add_user('<?php print $Result["id"]; ?>', '<?php print $Result['fullname']; ?>');" title="Add User"><i class="icon-plus"></i> ADD USER</a></div>
					</li>
					<?PHP } ?>
				  </ul>
				</div>
			  </div>
		  <?PHP 
			}
		}
	}
} ELSE {
	// PRINT ERROR MESSAGE
	PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
}
?>