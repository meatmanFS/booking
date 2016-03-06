var this_avelable = undefined;
jQuery(document).ready(function($){
	$(".avelable").css({
		"cursor" : "pointer",
		"color" : "green"
	});
	$(".close-btn").css({
		"cursor" : "pointer",
	});
	var jVal = {
		"check" : function() {
			var ele1 = $(".booking_email");
			var ele2 = $(".booking_tel");

			var patt1 = /^.+@.+[.].{2,}$/i;
			var patt2 = /^\+[0-9]{10,}$/i;

			if(!patt1.test(ele1.val()))  {
				jVal.errors = true;
				alert( mi_booking.tamplate_booking_mail );	

			} else {
				if(!patt2.test(ele2.val()))  {
					jVal.errors = true;
					alert( mi_booking.tamplate_booking__tel );
				}
				else {
					alert( mi_booking.tamplate_booking_conf );
				}
			}
		},

		"sendIt" : function (){
			if(!jVal.errors) {
				$("#booking_form").submit();
				this_avelable.removeClass();
			}
		}
	};
	$(".avelable").click(function(){
		this_avelable = $(this);
		$(".booking_date").attr("value" ,$(this).attr("date"));
		$(".booking_time").attr("value" , $(this).attr("time"));
		document.getElementById("envelope").style.display="block";document.getElementById("fade").style.display="block";
	});
	$(".booking_submit").click(function(){
		jVal.errors = false;
		jVal.check();
		jVal.sendIt();
		if(jVal.errors) {
			return false;
		}
	});
	$(".close-btn").click(function(){
		document.getElementById("envelope").style.display="none";document.getElementById("fade").style.display="none"
	});
});