function setWindow()	{
	var h = $(window).height();
	var w = $(window).width();
	$("#background").css("height", h * 0.289 + "px");
	$("#head").css("width", w * 0.3 + "px")
		.css("height", w * 0.3 + "px")
		.css("margin-top", - w * 0.2 + "px");
	$(".timeLine").css("margin-left", parseFloat($("#head").css("margin-left")) + parseFloat($("#head").css("width")) / 2 + parseFloat($("#head").css("border")));
	$(".operatorHead").css("margin-left", -parseFloat($(".timeLine").css("margin-left")) / 2 - 65);
	$("#bottom").css("left", w / 2 - 25 + "px");
	$(".picture").children("img").css("height", $(".picture").children("img").width());
}
$(document).ready(function(){
	$(this).scroll(function(){
		if ($("body")[0].scrollTop + $(window).height() >= $("body")[0].scrollHeight){
			// alert("到底了");
			//uexWindow.toast(0,8,"已经到底了", 3000);
      loadDatas();
		}
	});
});