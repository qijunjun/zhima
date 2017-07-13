var scale = 0.7;
var lay = 4;
var fix = 5;

$(document).ready(function() {
	$(".h-bottom").css("padding-bottom", $(window).height() * 0.05 + "px");
	$(".h-save-button").css("font-size", $(window).height() * 0.025 + "px");
	$(".h-back-button").css("font-size", $(window).height() * 0.025 + "px");
	$(".h-photo-take").children("img").css("width", parseInt($(window).width() * scale / lay - fix + "px"));
	$(".h-functions").css("padding-top", $(window).height() * 0.075 + "px");
	$(".h-functions").children(".h-functions-img").css("width", parseInt($(window).width() * scale / lay - fix + "px"));
	$(".h-functions").children(".h-functions-img").children("img").css("width", parseInt($(window).width() * scale / lay - 5 + "px"));
	$(".h-functions").children(".h-functions-img").children("p").css("padding-top", $(window).width() * 0.02 + "px");
	$(".h-functions").children(".h-functions-img").children("p").css("padding-bottom", $(window).width() * 0.05 + "px");
	$(".h-functions").css("height", $(".h-functions").children(".h-functions-img").height() * 3 + "px");
	if ($("body").height() < $(window).height()){
		$(".h-bottom").css("position", "fixed");
		$(".h-bottom").css("bottom", "0");
	}
	InitOutWidth();
});

function InitOutWidth(){
	var element_width, window_fix;
	element_width = parseFloat($(".h-functions").children(".h-functions-img").css("width"));
	window_fix = $(window).width() * 0.02;
	console.log("width:" + window_fix);
	$(".h-functions").children(".h-functions-img").css("margin-left", window_fix);
	$(".h-functions").children(".h-functions-img").css("margin-right", window_fix);

	element_width += window_fix*2;
	$(".h-functions").css("width", element_width*lay);
	$(".h-functions").css("margin-left", ($(window).width()-element_width*lay)/2);
}

function createImg() {
	$("#createimg").before("<img src='images/back.jpg' style='margin-right: 6px;' />");
	$(".h-photo-take").children("img").css("width", $(window).width() * scale / lay - fix + "px");
}