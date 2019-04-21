var pageurl = $("#pageurl").attr('content');
$("#loading_div").hide();
// list all files 
list_files();
// list the users that have been added 
list_added_users();
// list all items that have been added to be shared
list_added_items();
// confirm if the user wants tot share the files
confirm_share();

// this portion fetches all files that have been uploaded by the current user
// it picks the current row that have been parsed and the page url for processing
// of the form.
function list_files() {
	var _c_row = Number($('#_c_row').val());
	var _pg_url = $('#_pg_url').val();
	$.ajax({
		url: pageurl+'doFetchResults/doListing',
		type: 'POST',
		data: "_c_row="+_c_row+"&pg_url="+_pg_url,
		beforeSend: function() {
			$(".loading-div").html('<div style="width:100%;text-align:center" class="btn btn-primary">Loading All Files and Folders <img src="'+pageurl+'assets/images/loadings.gif"/></div>');
		},
		success: function(response){
			$(".listing-details").html(response);
			$('#more-div').css("display","block");
			$('.loading-div').css("display","none");
			$('.loading-div').css("margin-top","0px");
		}
	});
}

// confirm if the user has clicked on the load more button 
// this will fetch additional items from the list of items 
// uploaded by the user and display them to the page 
// it picks other parameters from the page and submits it 
// for processing by the appropriate file 
$('.load-more').click(function(){
	var _c_row = Number($('#_c_row').val());
	var _p_limit = Number($('#_p_limit').val());
	var _allcount = Number($('#_allcount').val());
	var _row_no = Number(_c_row + _p_limit);
	var _pg_url = $('#_pg_url').val();
	$('.loading-div').css("display","block");
	$.ajax({
		url: pageurl+'doFetchResults/doListing',
		type: 'POST',
		data: "_c_row="+_c_row+"&pg_url="+_pg_url+"&load-more",
		beforeSend: function() {
			$(".loading-div").html('<div style="width:100%;text-align:center" class="btn btn-primary">Loading Results <img src="'+pageurl+'assets/images/loadings.gif"/></div>');
		},success: function(response){
			$(".loading-more").append(response).show().fadeIn("slow");
			$('#more-div').css("display","block");
			$('.loading-div').css("display","none");
			$('.loading-div').css("margin-top","0px");
			if(_row_no > _allcount){
				$('.load-more').css("display","none");
				$('.no-more').css("display","block");
			}
		}
	});
});

// processes the login of a user.
$('#loginForm').on('submit', function (e) {
	e.preventDefault();
	$("#submitButton").attr("disabled", true);
	$.ajax({
		type: 'POST',
		url: $("#loginForm").attr('action'),
		data: $('#loginForm').serialize(),
		beforeSend: function() {
			$("#formResult").html('<div style="width:100%" class="alert alert-warning alert-md alert-block">Please wait <img src="'+pageurl+'/assets/images/loadings.gif" align="absmiddle" /></div>');
		}, success: function(response) {
			$("#formResult").html(response);
			$("#password").val("");
			$("#submitButton").removeAttr("disabled", true);
		}
	});
});

// the part processes the form that enables a user to 
// request for a new password token and a subsequent
// randomly generated password
$('#recoverForm').on('submit', function (e) {
	e.preventDefault();
	$("#submitButton2").attr("disabled", true);
	$.ajax({
		type: 'POST',
		url: $("#recoverForm").attr('action'),
		data: $('#recoverForm').serialize(),
		beforeSend: function() {
			$("#formResult").html('<div style="width:100%" class="alert alert-warning alert-md alert-block">Please wait <img src="'+pageurl+'/assets/images/loadings.gif" align="absmiddle" /></div>');
		}, success: function(response) {
			$("#formResult").html(response);
			$("#request_username").val("");
			$("#submitButton2").removeAttr("disabled", true);
		}
	});
});

// logs the user out from the system by clearing all 
// access sessions that have been created on the browser
$(".logoutUser").on('click', function (e) {
	if(confirm('Are you sure you want to logout?')) {
		$.ajax({
			type: 'POST',
			url: pageurl+"doAuth/doLogout",
			success: function(response) {
				window.location.href=pageurl+"Login/doLogout";
			}
		});
	}
	e.preventDefault();
});

// deletes an item from the database table \
// receives 4 or 5 parameters 
// Action = delete / edit
// Id = Item Id to delete or remove from the list 
// Type = FILE / FOLDER
// Uid = User ID that wants to delete the file 
// confirm whether to redirect the user or not
function process_item(Action, Id, Type, Uid, Redir=null, Redir_Link=null) {
	var msg;
	if((Action == "delete") && (Type == "FILE")) {
		msg  = "Are you sure you want to Delete this File?";
	} else if((Action == "delete") && (Type == "FOLDER")) {
		msg  = "Are you sure you want to Delete this Folder, Sub Folders and Files?";
	} else if((Action == "delete") && (Type == "Shared_File")) {
		msg  = "Are you sure you want to Delete this Shared File?";
	} else if((Action == "start") && (Type == "Shared_File")) {
		msg  = "Are you sure you want to continue sharing these files?";
	}  else if((Action == "stop") && (Type == "Shared_File")) {
		msg  = "Are you sure you want to stop sharing these files?";
	} 
	
	if((Action == "delete")) {
		if(confirm(msg)) {
			$.ajax({
				type: 'POST',
				data: 'Action='+Action+'&Id='+Id+'&Type='+Type+'&Uid='+Uid,
				url: pageurl+"doDelete/doDelete",
				success: function(response) {
					$('.gritter-item-wrapper').css("display","block");
					if($.trim(response) == 'error') {
						var header = 'Delete Error';
						var content = 'Sorry! There was a problem while trying to delete the Specified File.';	
					} else if($.trim(response) == 'success') {
						var header = 'Delete Success';
						var content = 'The Item was sucessfully deleted.';
						$(".File_Info_"+Id).css("display","none");
					}
					$.gritter.add({
						title:	header,
						text:	content,
						sticky: false
					});
					if(Redir == "REDIR") {
						if(Redir_Link != null) {
							setTimeout(function() {
								window.location.href=Redir_Link; 
							}, 2000);
						} else {
							setTimeout(function() {
								window.location.href=pageurl+"ItemsStream"; 
							}, 2000);
						}
					}
				}
			});
		}
	}
	
	if((Action == "start" || Action == "stop")) {
		if(confirm(msg)) {
			$.ajax({
				type: 'POST',
				data: 'Action='+Action+'&Id='+Id+'&Type='+Type+'&Uid='+Uid,
				url: pageurl+"doDelete/doDelete",
				success: function(response) {
					$('.gritter-item-wrapper').css("display","block");
					$.gritter.add({
						title:	'Update Notice',
						text:	response,
						sticky: false
					});
					if(Redir == "REDIR") {
						if(Redir_Link != null) {
							setTimeout(function() {
								window.location.href=Redir_Link; 
							}, 2000);
						} else {
							setTimeout(function() {
								window.location.href=pageurl+"ItemsStream"; 
							}, 2000);
						}
					}
				}
			});
		}		
	}
	
	if((Action == "edit")) {
		setTimeout(function() {
			window.location.href=pageurl+"ItemStream/Id/"+Id;
		}, 1000);
	}
}

function show_item(option_id) {
	$("#option_"+option_id).css("display","block");
}

function hide_item(option_id) {
	$("#option_"+option_id).css("display","none");
}

function editForm() {
	var item_id = $("#rename_Item").attr('value');
	$("#edit_item_div").toggle('slow');
	$("#itemId").val(item_id);
}

$("#rename_Item").on('click', function(e) {
	editForm();
	e.preventDefault();
});

$("#deleteItem").on('click', function(e) {
	e.preventDefault();
});

$("#modifyItem").on('click', function(e) {
	e.preventDefault();
});

$(".add_user").on('click', function(e) {
	e.preventDefault();
});

$(".share_Item").on('click', function(e) {
	e.preventDefault();
	var share_link = $(this).attr('value');
	window.location.href=share_link;
});

$("#cancelButton").on('click', function(e) {
	$("#edit_item_div").hide('slow');
	$("#itemForm")[0].reset();
	e.preventDefault();
});

// this section helps a user with admin level to 
// modify the user account details 
function modify_account(id, content) {
	if(confirm("Are you sure you want to "+content+" this Admin Account?")) {
		$.ajax({
			type: "post",
			url: pageurl+"doAuth/doModify",
			data: "modify_account&type="+content+"&id="+id,
			success: function(response) {
				$(".modify_result").html(response);
			}
		});
	}
}
// search for users and list then in a li format
$("#user_Search").on('submit', function(e) {
	e.preventDefault();
	$(".users_Found").html("");
	var user_name = $("#users-box").val();
	if(user_name.length < 2) {
		$(".users_Found").html("<div class='alert alert-danger'>Sorry! You must enter at least 2 characters.</div>");
	} else {
		$.ajax({
			type: 'POST',
			data: 'Action=searchUser&Name='+user_name,
			url: pageurl+"doSearch/doSearch",
			success: function(response) {
				$(".users_Found").html(response);
			}
		});
	}
});

// process the file sharing form
// this will run after all users have been added and 
// the file sharing permissions have been full set
$("#share_File").on('submit', function(e) {
	e.preventDefault();
	$.ajax({
		type: 'POST',
		data: $('#share_File').serialize(),
		url: pageurl+"doShare/doShare",
		success: function(response) {
			$('.gritter-item-wrapper').css("display","block");
			if($.trim(response) == 'success') {
				var header = 'Sharing Success';
				var content = 'Congrats! The file was successfully shared with the selected users. This page will reload in 5 seconds.';
				$("#share_File")[0].reset();
				$("#share_Comments").css('display', 'none');
				$("#share_Length").css('display', 'none');
				$("#replace_permission").css('display', 'none');		
			} else {
				var header = 'Sharing Error';
				var content = 'Sorry! There was an error while trying to share the this file. Please reload the page to try again.';
			}
			$.gritter.add({
				title:	header,
				text:	content,
				sticky: false
			});
			// list the users again
			if($.trim(response) == 'success') {
				list_added_users();
				list_added_items();
				setTimeout(function() {
					window.location.href=pageurl+"Shared";
				}, 5000);
			}
		}
	});
});

// add users to the file sharing list
function add_user(Uid, UName) {
	$(".users_Found").html("");
	$("#user_Search")[0].reset();
	$.ajax({
		type: 'POST',
		data: 'Action=doAddUser&Uid='+Uid+'&UName='+UName,
		url: pageurl+"doShare/doAdd/doUser",
		success: function(response) {
			$('.gritter-item-wrapper').css("display","block");
			$.gritter.add({
				title:	'New Notification',
				text:	response,
				sticky: false
			});
			list_added_users();
		}
	});
}

// removes users from the file sharing list
function remove_user(Uid, UName) {
	$(".users_Found").html("");
	$("#user_Search")[0].reset();
	$.ajax({
		type: 'POST',
		data: 'Action=doRemove&Uid='+Uid+'&UName='+UName,
		url: pageurl+"doShare/doRemove/execRemove",
		success: function(response) {
			$('.gritter-item-wrapper').css("display","block");
			$.gritter.add({
				title:	'New Notification',
				text:	response,
				sticky: false
			});
			list_added_users();
		}
	});
}

// list all the file sharing users added to the session list
function list_added_users() {
	$.ajax({
		type: 'POST',
		data: 'Action=listUsers&',
		url: pageurl+"doShare/doList/listUsers",
		success: function(response) {
			$(".users_List").html(response);
			confirm_share();
		}
	});
}

// count the users added to the file sharing list and 
// display the columns to use
function confirm_share() {
	$.ajax({
		type: 'POST',
		data: 'Action=countUsers&',
		url: pageurl+"doShare/doList/countUsers",
		success: function(response) {
			$(".confirm_Text").html(response);
			if($(".confirm_Text").text() != "Sorry! You have not yet added any users to the list.") {
				$("#confirm_Share").css('display', 'block');
				$("#share_Comments").css('display', 'block');
				$("#share_Length").css('display', 'block');
				$("#replace_permission").css('display', 'block');
				$("#download_permission").css('display', 'block');
			}
		}
	});
}

// add items to the file sharing list
function add_share_item(Uid, Item_Name) {
	$.ajax({
		type: 'POST',
		data: 'Action=doAddItem&Uid='+Uid+'&Item_Name='+Item_Name,
		url: pageurl+"doShare/doAdd/addItem",
		success: function(response) {
			$('.gritter-item-wrapper').css("display","block");
			$.gritter.add({
				title:	'New Notification',
				text:	response,
				sticky: false
			});
			list_added_items();
		}
	});
}

// list all the files added to the session list
function list_added_items() {
	$.ajax({
		type: 'POST',
		data: 'Action=listItems&',
		url: pageurl+"doShare/doList/listItems",
		success: function(response) {
			$(".share_list").html(response);
		}
	});
}

// empty the list of ites that have been added to the share file list session 
function empty_item_list(redir=null, item_id='000') {
	if(confirm('Are you sure you want to empty the share items list?')) {
		$.ajax({
			type: 'POST',
			data: 'Action=removeItems&item_id='+item_id,
			url: pageurl+"doShare/doEmpty/emptySession",
			success: function(response) {
				$('.gritter-item-wrapper').css("display","block");
				$.gritter.add({
					title:	'New Notification',
					text:	'Items sharing cart has been emptied.',
					sticky: false
				});
				if(item_id !='000') {
					$(".shared_list_"+item_id).hide();
				} else {
					$(".share_listing").slideUp();
				}
				if(redir=='reload') {
					window.location.href=pageurl+"ItemsStream";
				}
				list_added_items();
			}
		});
	}
}

// this section enables a user to edits the file or folder information
// by this you can change the name and description of that 
// particular file. The form is submitted
// to the action attribute specified on the form
$('#editForm').on('submit', function (e) {
	e.preventDefault();
	$("#result_div").html("");
	$("#submitButton").attr("disabled", true);
	$.ajax({
		type: 'POST',
		url: $("#editForm").attr('action'),
		data: $('#editForm').serialize(),
		beforeSend: function() {
			$("#loading_div").show();
		}, success: function(response) {
			$("#result_div").html(response);
			$("#loading_div").hide();
			$("#submitButton").removeAttr("disabled", true);
		}
	});
});

// this section processes the creation of a new folder
// serializes the form and submits all the input and textarea fields 
// to the action attribute specified on the form
$('#addFolder').on('submit', function (e) {
	e.preventDefault();
	$("#result_div").html("");
	$("#submitButton").attr("disabled", true);
	$.ajax({
		type: 'POST',
		url: $("#addFolder").attr('action'),
		data: $('#addFolder').serialize(),
		beforeSend: function() {
			$("#loading_div").show();
		}, success: function(response) {
			$("#result_div").html(response);
			$("#loading_div").hide();
			$("#submitButton").removeAttr("disabled", true);
		}
	});
});

// this section helps the admin to create a new user to the 
// list of users on the web application
// it also aids in updating the user information.
// the advantage is that, it users the action attribute and the form serialize
$('#doProcessUser').on('submit', function (e) {
	e.preventDefault();
	$(".addbutton").attr("disabled", true);
	$.ajax({
		type: 'POST',
		url: $("#doProcessUser").attr('action'),
		data: $('#doProcessUser').serialize(),
		beforeSend: function() {
			$(".j-response").html('<div class="alert alert-warning alert-md alert-block">Please wait <img src="'+pageurl+'/assets/images/loadings.gif" align="absmiddle" /></div>');
		}, success: function(response) {
			$(".j-response").html(response);
			$(".addbutton").removeAttr("disabled", true);
		}
	});
});

// regenerate a random string to be used as a password
$("#regeratePassword").on('click', function(e) {
	$.ajax({
		type: 'POST',
		url: pageurl+"doAuth/doGeratePassword",
		data: "Action=doGeratePassword",
		success: function(response) {
			$("#password").val(response);
		}
	});
});

// change the user upload status
function change_upload_status(user_id, upload_status) {
	if(confirm("Are you sure you want to change user upload status?")) {
		$.ajax({
			type: 'POST',
			url: pageurl+"doUpdate/doEffectChange/uploadStatus",
			data: "Action=doUploadState&user_id="+user_id+"&status="+upload_status,
			success: function(response) {
				$("#file_upload_status").html(response);
			}
		});
	}
}

// update the user upload disk usage
function update_user_disk_usage(user_id) {
	var usage_limit = $("#limit_num").val();
	$.ajax({
		type: 'POST',
		url: pageurl+"doUpdate/doEffectChange/uploadLimit",
		data: "Action=doUploadLimit&user_id="+user_id+"&usage_limit="+usage_limit,
		success: function(response) {
			$(".total_file_uploads").html(response);
		}
	});
}

// update the office disk usage information
function update_total_disk_usage(update_type, office_id, update_div) {
	var daily_usage = $("#daily_usage").val();
	var overall_usage = $("#overall_usage").val();
	$.ajax({
		type: 'POST',
		url: pageurl+"doUpdateOffice/doEffectChange/uploadLimit",
		data: "Action=doUploadLimit&office_id="+office_id+"&daily_usage="+daily_usage+"&overall_usage="+overall_usage+"&update_type="+update_type,
		success: function(response) {
			$("."+update_div).html(response);
		}
	});
}