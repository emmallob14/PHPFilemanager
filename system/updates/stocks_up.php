<?php
//FILE USED BY THE ADMINISTRATOR
//fetch the global objects already created
global $DB, $SITEURL, $config, $admin_user;
// initializing
$pagename = $pageslug = $pagecontent = $pageprice = $quantity = $details = $pagealias = $statusnew = "";
$quantity = 1;
$pageprice = $dpageprice = 0.00;

$stocks = load_class('Stocks', 'models');
$products = load_class('products', 'models');
load_helpers(array('url_helper','string_helper'));

//check if user is logged in
if(($admin_user->logged_InControlled() == true) and ($admin_user->confirm_admin_user() == true)):

	if(isset($_POST["parsedform"])):
		//encryption code
		$admin_logged = $admin_user->return_username();
		
		$pagename = strtoupper(xss_clean($_POST["pagename"]));	
		//clean the information parsed by administrator
		$pageslug = trim(xss_clean($_POST["pageslug"]));
		$Product_id = (int)trim(xss_clean($_POST["Product_id"]));
		$pageprice = substr(create_slug($_POST["pageprice"]), 0, -3);
		$dpageprice = substr(create_slug($_POST["dpageprice"]), 0, -3);
		$quantity = substr(create_slug($_POST["quantity"]), 0, -3);
		$shop_id = (int)xss_clean($_POST["shop_id"]);
		$product_idd = xss_clean($_POST["product_idd"]);
		
		if(isset($_POST["status"]))
			$status = xss_clean($_POST["status"]);
		else
			$status = "1";
		
		$pid = trim(xss_clean($_POST["Product_id"]));
		
		$pagecontent = nl2br(xss_clean($_POST["pagecontent"]));
		
		if(isset($_POST["productEdit"]) or isset($_POST["productAdd"])):
			$pagesummary = limit_words(strip_tags($pagecontent), 250);
			$quantity = xss_clean($_POST['quantity']);
			$category = (int)xss_clean($_POST["parent_category2"]);
			
			$categorynew = "<option value='$category' selected='selected'>
					{$products->category_byid($category,"id")->getName}</option>";
			$startdate="";
			$enddate="";
		endif;

		if($dpageprice == 0):
			$dpageprice == $pageprice;
		endif;
		
		if(empty($pagesource)):
			$pagesource=$admin_logged;
		endif;
		
		$postday = date("F");
		$postmonth = date("l");
		$postyear = date("Y");
	
		//check if no field is empty
		if($quantity > $products->product_by_id("product_slug",strtolower($SITEURL[1]))->p_quantity): 
			print "<div class='alert alert-danger'>Sorry! The product quantity specified is above the original quantity in the database. If you want to increase stock please use the <a href='".SITE_URL."/stocks-new'>STOCKS MANAGER</a></div>";
		elseif(!empty($_POST["pagename"])):
			//check the length of pagename
			
			if(str_word_count($_POST["pagename"]) > 0):
				//throw error message if the word count is less than 15 words
				#check if the user wants to edit the article
				if(isset($_POST["updatepage"]) and isset($_POST["productEdit"])):
					# create a new page alias
					$pagealias = $product_idd;
					
					# update the product details
					$process = $DB->just_exec("UPDATE 
							`_products`	SET 
							`product_category`='$category',							
							`product_name`='$pagename',`product_slug`='$pageslug',
							`product_actuals`='$dpageprice', `product_price`='$pageprice',
							`product_quantity`='$quantity',
							`product_supplier`='$shop_id',
							`product_details`='$pagecontent',
							`product_specifications`='$pagecontent',
							modified_date=now(),modified_by='$admin_logged'
							WHERE `product_id`='$product_idd' and store_id='".STORE_ID."'
					");
					
					#update stock and product quantity
					$stocks->_update_stock($quantity, $pid);
					
					#record new admin history
					$DB->just_exec("insert into _activity_logs set store_id='".STORE_ID."', full_date=now(), date_recorded=now(), admin_id='$admin_logged', activity_page='product', activity_id='$pagealias', activity_details='$pagealias', activity_description='Item <strong>\"$pagename\"</strong> details has been updated.'");
					
				endif;
				
					
				if($process):
					
					
					if(isset($_FILES['pageimage']['tmp_name']) and !empty($_FILES['pageimage']['tmp_name'])):
					
					#upload the images
					$path = "assets/images/product";
									
					for($i=0; $i < count($_FILES['pageimage']['name']); $i++){
						
						if(validate_image($_FILES["pageimage"]["name"][$i])==true):
						
						$image = $_FILES['pageimage']['name'][$i];
						$ext = pathinfo($image, PATHINFO_EXTENSION);
						
						$filename = create_slug(date("Y-m-d")."-".SITE_NAME."-".$pagename).mt_rand(12, 12563).".".strtolower($ext);
												
						if(move_uploaded_file($_FILES['pageimage']['tmp_name'][$i], $path."/".$filename )) {
							
							$dir = "assets/images/thumbnail/";
							generate_image_thumbnail($path."/".$filename, $dir.$filename);
							
							#insert the images into the images table
							$DB->query("
								insert into _products_images
								(product_id,image,thumbnail) 
								values 
								('$pid','assets/images/product/$filename','assets/images/thumbnail/$filename')
							");
							
							
						}
						endif;
					}
					
					endif;
					
					
					if(isset($_POST["updatepage"])):
						print "<script>window.location.href='".SITE_URL."/stocks-view/$product_idd?msg=1&edit'</script>";
					elseif(isset($_POST["createpage"])):
						print "<script>window.location.href='".SITE_URL."/stocks-view/$product_idd?msg=3&edit'</script>";
					endif;
					
				
				endif;
					
			
			
			endif;
		
		endif;
		
	endif;
	

endif;