<?php
//FILE USED BY THE ADMINISTRATOR
//fetch the global objects already created
global $DB, $config, $admin_user;
$stocks = load_class('Stocks', 'models');
$products = load_class('products', 'models');
load_helpers(array('url_helper','string_helper'));

//check if user is logged in
if($admin_user->logged_InControlled() == true){

	if(isset($_POST["parsedform"])){
		//encryption code
		$admin_logged = $admin_user->return_username();
		$goodtogo = true;
		
		$pagename = strtoupper(xss_clean($_POST["pagename"]));	
		//clean the information parsed by administrator
		$pageslug = trim(create_slug($pagename));
		$Product_id = (int)trim(xss_clean($_POST["Product_id"]));
		$pageprice = substr(create_slug($_POST["pageprice"]), 0, -3);
		$dpageprice = substr(create_slug($_POST["dpageprice"]), 0, -3);
		$quantity = substr(create_slug($_POST["quantity"]), 0, -3);
		$shop_id = (int)xss_clean($_POST["shop_id"]);
		
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
					{$products->category_byid($category, "id")->getName}</option>";
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
	
		//check if the page name already exists
		$pagealias = (int)($DB->max_all("id", "_products"))+1;
		
		if(count($DB->query("select * from _products where product_slug='$pageslug' and status='1'")) > 0) {
			$goodtogo = false;
		}
		
		//check if no field is empty
		if(!empty($_POST["pagename"])){
			//check the length of pagename
			if((str_word_count($_POST["pagename"]) > 0) and ($goodtogo == true)){
				//throw error message if the word count is less than 15 words
				#check if the user wants to edit the article
				if(isset($_POST["updatepage"]) and isset($_POST["productAdd"])){
					
					$product_id = (int)($DB->max_all("id", "_products"))+1;
					# update the product details
					$process = $DB->just_exec("
						INSERT INTO `_products` SET 
						`product_category`='$category',
						`store_id`='".STORE_ID."',
						`product_name`='$pagename',
						`product_slug`='$pageslug',
						`product_actuals`='$dpageprice', `product_price`='$pageprice',
						`product_id`='LEP$pagealias',`product_quantity`='$quantity',
						`product_supplier`='$shop_id',
						`product_details`='$pagecontent',
						`product_specifications`='$pagecontent',
						`product_owner`='$admin_logged',modified_date=now(),
						modified_by='$admin_logged'
					");
					
					$product_id = $DB->max_all("id", "_products");
					
					#update stock and product quantity
					$DB->just_exec("insert into _stocks set store_id='".STORE_ID."', product_id='$product_id', quantity='$quantity'");
					
					$stock_id = (int)($DB->max_all("id", "_stocks_details"))+1;
					
					#insert a new stocks history with an id
					$DB->just_exec("insert into _stocks_details set store_id='".STORE_ID."', stock_id='LEST$stock_id', pid='LEP$pagealias', quantity='$quantity', old_quantity='0', new_quantity='$quantity', purchase='$pageprice', selling='$dpageprice', supplier='$shop_id', date_added=now(), added_by='$admin_logged'");
			
					#record new admin history
					$DB->just_exec("insert into _activity_logs set store_id='".STORE_ID."', full_date=now(), date_recorded=now(), admin_id='$admin_logged', activity_page='product', activity_id='LEP$pagealias', activity_details='LEP$pagealias', activity_description='Item <strong>\"$pagename\"</strong> details has been inserted.'");
					
				}
				
					
				if($process){
						
					if(isset($_FILES['pageimage']['tmp_name']) and !empty($_FILES['pageimage']['tmp_name'])){
					
					#upload the images
					$path = "assets/images/product";
									
					for($i=0; $i < count($_FILES['pageimage']['name']); $i++){
						
						if(validate_image($_FILES["pageimage"]["name"][$i])==true){
						
						$image = $_FILES['pageimage']['name'][$i];
						$ext = pathinfo($image, PATHINFO_EXTENSION);
						
						$filename = create_slug(date("Y-m-d")."-".SITE_NAME."-".$pagename).mt_rand(12, 12563).".".strtolower($ext);
												
						if(move_uploaded_file($_FILES['pageimage']['tmp_name'][$i], $path."/".$filename )) {
							
							$dir = "assets/images/thumbnail/";
							generate_image_thumbnail($path."/".$filename, $dir.$filename);
							
							#insert the images into the images table
							$DB->just_exec("
								insert into _products_images
								(product_id,image,thumbnail) 
								values 
								('$pid','assets/images/product/$filename','assets/images/thumbnail/$filename')
							");
							
							
						}
						}
					}
					
					}
						
					if(isset($_POST["updatepage"])):
						print "<script>window.location.href='".SITE_URL."/stocks?msg=3'</script>";
					elseif(isset($_POST["createpage"])):
						print "<script>window.location.href='".SITE_URL."/stocks?msg=3'</script>";
					endif;
				
				}
					
			
			
			} else {
				print "<div class='alert alert-danger'>Sorry! There is a product with the same name in the database.</div>";
			}
		
		}
		
	}

}