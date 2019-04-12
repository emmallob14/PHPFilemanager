var pageurl = $("#pageurl").attr('content');
$("#loading_div").hide();
list_files();
list_added_users();
confirm_share();
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
	}  else if((Action == "delete") && (Type == "Shared_Folder")) {
		msg  = "Are you sure you want to Delete this Shared Folder?";
	} 
	
	if((Action == "delete")) {
		if(confirm(msg)) {
			$.ajax({
				type: 'POST',
				data: 'Action='+Action+'&Id='+Id+'&Type='+Type+'&Uid='+Uid,
				url: pageurl+"doUpdate/doDelete",
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

$(".add_user").on('click', function(e) {
	e.preventDefault();
});

$(".share_Item").on('click', function(e) {
	e.preventDefault();
	var share_link = $(this).attr('value');
	window.location.href=share_link;
});

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
			url: pageurl+"doProcess/doSearch",
			success: function(response) {
				$(".users_Found").html(response);
			}
		});
	}
});

// process the file sharing form
$("#share_File").on('submit', function(e) {
	e.preventDefault();
	$.ajax({
		type: 'POST',
		data: $('#share_File').serialize(),
		url: pageurl+"doProcess/doShare",
		success: function(response) {
			$('.gritter-item-wrapper').css("display","block");
			if($.trim(response) == 'success') {
				var header = 'Sharing Success';
				var content = 'Congrats! The file was successfully shared with the selected users.';
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
			list_added_users();
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
		url: pageurl+"doProcess/doAdd/doUser",
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
		url: pageurl+"doProcess/doRemove/execRemove",
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
		url: pageurl+"doProcess/doList/listUsers",
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
		url: pageurl+"doProcess/doList/countUsers",
		success: function(response) {
			$(".confirm_Text").html(response);
			if($(".confirm_Text").text() != "Sorry! You have not yet added any users to the list.") {
				$("#confirm_Share").css('display', 'block');
				$("#share_Comments").css('display', 'block');
				$("#share_Length").css('display', 'block');
				$("#replace_permission").css('display', 'block');
			}
		}
	});
}

$("#cancelButton").on('click', function(e) {
	$("#edit_item_div").hide('slow');
	$("#itemForm")[0].reset();
	e.preventDefault();
});

$('#itemForm').on('submit', function (e) {
	e.preventDefault();
	$("#result_div").html("");
	$("#submitButton").attr("disabled", true);
	$.ajax({
		type: 'POST',
		url: $("#itemForm").attr('action'),
		data: $('#itemForm').serialize(),
		beforeSend: function() {
			$("#loading_div").show();
		}, success: function(response) {
			$("#result_div").html(response);
			$("#loading_div").hide();
			$("#submitButton").removeAttr("disabled", true);
		}
	});
});

$("#addFolder").validate({
	rules:{
		folder_name:{
			required: true,
			minlength:2,
			maxlength:255
		},
		parent_folder:{
			required:false
		}
	},
	errorClass: "help-inline",
	errorElement: "span",
	highlight:function(element, errorClass, validClass) {
		$(element).parents('.control-group').addClass('error');
	},
	unhighlight: function(element, errorClass, validClass) {
		$(element).parents('.control-group').removeClass('error');
		$(element).parents('.control-group').addClass('success');
	}
});

$("#itemForm").validate({
	rules:{
		item_name:{
			required: true,
			minlength:2,
			maxlength:255
		}
	},
	errorClass: "help-inline",
	errorElement: "span",
	highlight:function(element, errorClass, validClass) {
		$(element).parents('.control-group').addClass('error');
	},
	unhighlight: function(element, errorClass, validClass) {
		$(element).parents('.control-group').removeClass('error');
		$(element).parents('.control-group').addClass('success');
	}
});

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