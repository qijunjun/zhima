$(document).ready(function() {
	$(".h-bottom").css("padding-bottom", $(window).height() * 0.05 + "px");
	$(".h-save-button").css("font-size", $(window).height() * 0.025 + "px");
	$(".h-back-button").css("font-size", $(window).height() * 0.025 + "px");
	$(".h-photo-take").css("padding-right", Number($(".h-photo-take").css("padding-right").replace("px", "")) - 5 + "px");
	$(".h-photo-take").css("width", Number($(".h-photo-take").css("width").replace("px", "")) + 5 + "px");
	$(".h-photo-take").children("img").css("width", $(window).width() * 0.9 / 4 - 5 + "px");
	$(".h-functions").css("padding-top", $(window).height() * 0.075 + "px");
	$(".h-functions").children(".h-functions-img").css("width", $(window).width() * 0.9 / 4 - 5 + "px");
	$(".h-functions").children(".h-functions-img").children("img").css("width", $(window).width() * 0.9 / 4 - 5 + "px");
	$(".h-functions").children(".h-functions-img").children("p").css("padding-top", $(window).width() * 0.02 + "px");
	$(".h-functions").children(".h-functions-img").children("p").css("padding-bottom", $(window).width() * 0.05 + "px");
	$(".h-functions").css("height", $(".h-functions").children(".h-functions-img").height() * 3 + "px");
	/* if ($("body").height() < $(window).height()){
		$(".h-bottom").css("position", "fixed");
		$(".h-bottom").css("bottom", "0");
	} */
});
