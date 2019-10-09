
$(document).ready(function(){

	var login = $('#loginForm');
	var recover = $('#recoverForm');
	var speed = 400;
	
	$('#registerForm').slideUp();
	
	$('.to-recover').click(function(){		
		$("#loginForm").slideUp();
		$("#recoverForm").fadeIn();
		$("#formResult").html("");
		$('#registerForm').slideUp();
	});
	
	$('.to-login').click(function(){		
		$("#recoverForm").hide();
		$("#loginForm").fadeIn();
		$("#formResult").html("");
		$('#registerForm').slideUp();
	});
	
	$('.to-register').click(function(){		
		$("#recoverForm").hide();
		$("#loginForm").hide();
		$("#formResult").html("");
		$('#registerForm').fadeIn();
	});
	
	$('#to-login').click(function(){
	
	});
    
    if($.browser.msie == true && $.browser.version.slice(0,3) < 10) {
        $('input[placeholder]').each(function(){ 
       
        var input = $(this);       
       
        $(input).val(input.attr('placeholder'));
               
        $(input).focus(function(){
             if (input.val() == input.attr('placeholder')) {
                 input.val('');
             }
        });
       
        $(input).blur(function(){
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.val(input.attr('placeholder'));
            }
        });
    });

        
        
    }
});