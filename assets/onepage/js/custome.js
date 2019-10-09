var pageurl = $("#pageurl").attr('content');
$(window).load(function(){
	$('.slider').fractionSlider({
		'fullWidth': 			true,
		'controls': 			true, 
		'pager': 				true,
		'responsive': 			true,
		'dimensions': 			"1380,460",
	    'increase': 			false,
		'pauseOnHover': 		false
	});

});

$(document).ready(function(){
  $("#btn-top").click(function(){    
    $("#flip-back").show();
    $("#btn-top").hide();

  });
  $("#flip-back").click(function(){
    $("#btn-top").show();
    $("#flip-back").hide();
  });
});

$(window).scroll(function() {
		$('.animate-slideup').each(function(){
		var imagePos = $(this).offset().top;

		var topOfWindow = $(window).scrollTop();
			if (imagePos < topOfWindow+400) {
				$(this).addClass("slideUp");
			}
		});
			
		
		if ($(this).scrollTop() > 550) {
		$('#fixedmenu').fadeIn('slow');
	} else {
		$('#fixedmenu').hide();
	}
});

$(document).ready(function(){
    $("a[rel^='prettyPhoto']").prettyPhoto({social_tools:false});
});

$(function(){
     
    $('#portfolio').mixitup();
     
});

$("#btn-top").on("click",function(e){
	$(".flipbox").flippy({
		direction: "top",
		duration: "750",
		verso: "<iframe src=\"back.html\" frameborder=\"0\" scrolling=\"no\" width=\"100%\" height=\"924\"/></iframe>",
	 });
	 e.preventDefault();
});

jQuery(document).ready(function() {
    var offset = 220;
    var duration = 500;
    jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.back-to-top').fadeIn(duration);
        } else {
            jQuery('.back-to-top').fadeOut(duration);
        }
    });
    
    jQuery('.back-to-top').click(function(event) {
        event.preventDefault();
        jQuery('html, body').animate({scrollTop: 0}, duration);
        return false;
    })
});

<!--Hover Dropdown Menu-->
$(document).ready(function(){
    $('ul.nav li.dropdown').hover(function() {
      $(this).find('.dropdown-menu').stop(true, true).fadeIn(500);
    }, function() {
      $(this).find('.dropdown-menu').stop(true, true).fadeOut(100);
    });  
});

<!--//Hover Dropdown Menu//-->

<!--Accordion-->
    $("#accordion").collapse();
    $('.panel-title > a').click(function () {
        $('.active .accordain-icon').addClass('icon-plus', 200).removeClass('icon-minus', 200);
        $('.active .accordain-icon').addClass('icon-plus', 200).removeClass('icon-minus', 200);
        $('.panel-title > a').removeClass('selected');
        $(this).addClass('selected');
        $('.active .accordain-icon').removeClass('icon-plus', 200).addClass('icon-minus', 200);
    });
<!--//Accordion//-->

<!--Featured Pricing Class-->

function checkWidth(init)
{
    /*If browser resized, check width again */
    if ($(window).width() < 991) {
        $('#featured').removeClass('featured');
    }
    else {
        if (!init) {
            $('#featured').addClass('featured');
        }
    }
}

$(document).ready(function() {
    checkWidth(true);

    $(window).resize(function() {
        checkWidth(false);
    });
});


$(function(){
	$('.slide-out-div').tabSlideOut({
		tabHandle: '.handle',                     //class of the element that will become your tab
		pathToTabImage: 'images/cog.png', //path to the image for the tab //Optionally can be set using css
		imageHeight: '40px',                     //height of tab image           //Optionally can be set using css
		imageWidth: '40px',                       //width of tab image            //Optionally can be set using css
		tabLocation: 'left',                      //side of screen where tab lives, top, right, bottom, or left
		speed: 300,                               //speed of animation
		action: 'click',                          //options: 'click' or 'hover', action to trigger animation
		topPos: '250px',                          //position from the top/ use if tabLocation is left or right
		leftPos: '20px',                          //position from left/ use if tabLocation is bottom or top
		fixedPosition: true                      //options: true makes it stick(fixed position) on scroll
	});

});

$(".register_account").on('click', function(){
	$("#loginModal").modal('hide');
	$("#registerModal").modal('show');
	$(".userAccountForm")[0].reset();
	$(".formResult").html('');
});

$(".login_account").on('click', function(){
	$("#loginModal").modal('show');
	$("#registerModal").modal('hide');
	$(".userAccountForm")[0].reset();
	$(".formResult").html('');
});

function show_register(type) {
	$(".userAccountForm")[0].reset();
	$("#registerModal").modal('show');
	$(".formResult").html('');
}

$("#package").on('change', function(){
	var selected = $(this).val();
});

// processes the login of a user.
$('.userAccountForm').on('submit', function (e) {
	e.preventDefault();
	$(".SubmitButton").attr("disabled", true);
	$.ajax({
		type: 'POST',
		url: $(".userAccountForm").attr('action'),
		data: $('.userAccountForm').serialize(),
		beforeSend: function() {
			$(".formResult").html('<div style="width:100%" class="alert alert-warning alert-md alert-block">Please wait <img src="'+pageurl+'/assets/images/loadings.gif" align="absmiddle" /></div>');
		}, success: function(response) {
			$(".formResult").html(response);
			$(".SubmitButton").removeAttr("disabled", true);
		}
	});
});