function setWindow()	{
	$(".h-back-button").css("margin-bottom", $(window).height() * 0.05 + "px");
	$(".h-save-button").css("font-size", $(window).height() * 0.025 + "px");
	$(".h-back-button").css("font-size", $(window).height() * 0.025 + "px");
	$("#scroll").css("padding-bottom", $(".h-bottom").height());
}
$(document).ready(function(){
	$(this).scroll(function(){
		if ($("body")[0].scrollTop + $(window).height() >= $("body")[0].scrollHeight){
			alert("到底啦到底啦！");
		}
	});
	
	$(".h-back-button").click(function(){
		window.history.back(-1);
	});
});