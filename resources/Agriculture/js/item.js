function setWindow()	{
	var h = $(window).height();
	$("#background").css("height", h * 0.57 + "px");
	$("#body").css("margin-top", h * 0.54 + "px");
	$("#title").css("line-height", h * 0.0598958 + "px")
		.css("font-size", h * 0.025 + "px");
	$("#select").css("margin-top", h * 0.07552 + "px");
	$(".picture").css("width", $(window).width() * 0.1867 + "px")
		.css("height", $(window).width() * 0.1867 + "px");
	$(".name").css("font-size", h * 0.025 + "px")
		.css("margin-top", h * 0.015625 + "px");
	$(".h-bottom").css("padding-bottom", $(window).height() * 0.05 + "px");
	$(".h-back-button").css("font-size", $(window).height() * 0.025 + "px");
	if ($("body").height() < $(window).height()){
		$(".h-bottom").css("position", "fixed");
		$(".h-bottom").css("bottom", "0");
	}
}