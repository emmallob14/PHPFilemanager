var pageurl = $("#pageurl").attr('content');
fetch_comments();
$('.chat-message input').val('').focus();

$('.chat-message button').click(function(){
	var input = $(this).siblings('span').children('input[type=text]');		
	if(input.val() != ''){
		add_comment(input.val());
	}		
});

$('.chat-message input').keypress(function(e){
	if(e.which == 13) {	
		if($(this).val() != ''){
			add_comment($(this).val());
		}		
	}
});
	
var i = 0;
function add_message(name,img,msg,date,mclass) {
	i = i + 1;
	var inner = $('#chat-messages-inner');
	var id = 'msg-'+i;
	inner.append('<p id="'+id+'" class="'+mclass+'">'
		+'<span class="msg-block"><img src="'+img+'" alt="" /><strong>'+name+'</strong> <span class="time">- '+date+'</span>'
		+'<span class="msg">'+msg+'</span></span></p>');
	$('#'+id).hide().fadeIn(800);
	$('#chat-messages').animate({ scrollTop: inner.height() },1000);
}

function reference_name(name) {
	var inner = $('.chat-message input').val();
	$('.chat-message input').val(inner + '{'+name+'}').focus();
}

function add_comment(message) {
	$.ajax({
		type: 'POST',
		data: 'Action=AddComment&Data='+message,
		url: pageurl+"doChat/doAdd",
		success: function(response) {
			fetch_comments();
		}
	});
	$('.chat-message input').val('').focus();
}

// display the columns to use
function fetch_comments() {
	$.ajax({
		type: 'POST',
		data: 'Action=fetchComments&',
		url: pageurl+"doChat/doList/Comments",
		success: function(response) {
			$(".comments_responses").html(response);
			$('#chat-messages').animate({ scrollTop: inner.height() },1000);
			// fetch new comments after every 10 seconds
			setTimeout(function() {
				fetch_comments();
			}, 10000);
		}
	});
}