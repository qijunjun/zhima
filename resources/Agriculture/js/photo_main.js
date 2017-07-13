var dataTime = Date.now();
$(document).ready(function () {
	$("#backimg").css("display", "none");
	$(".h-photo-take").css("background-color", "rgb(221, 221, 221)");
	$(".h-bottom").css("background-color", "rgb(221, 221, 221)");
	$(".h-Title-p-front").text(localStorage.getItem('taskname'));
	$(".h-Title-p-behind").text(localStorage.getItem('functionname'));
	if (localStorage.getItem('operator') == null) {
		$(".h-bg-img-p").before("<img id='userImg' style='position: fixed; z-index: -1;' src='/resources/Agriculture/images/back.webp' />");
		$(".h-bg-img").children("img").css("top", $(".hTitle").height());
		$(".h-bg-img").children("img").css("max-height", "400px");
		$(".h-bg-img-p").css("margin-top", "400px");
	} else
	{
		$(".h-bg-img-p").before("<img id='userImg' style='position: fixed; z-index: -1;' src='" + localStorage.getItem('operator') + "' />");
		//$(".h-bg-img-p").before("<img style='position: fixed; z-index: -1;' src='" + localStorage.getItem('operator') + "' />");
		$(".h-bg-img").children("img").css("top", $(".hTitle").height());
		$(".h-bg-img").children("img").css("max-height", "400px");
		$(".h-bg-img-p").css("margin-top", "400px");
	}
  
	$("#createimg").mousedown(function () {
		createImg();
	});

	$(".h-back-button").click(function () {
		window.location.href = "functions.html";
	});
	$(".h-save-button").click(function () {
		if (imageList.length > 0) {
			addEventRecord();
		} else {
			alert("请拍摄至少1张照片后再保存");
		}
	});
});

window.uexOnload = function () {
	getLocation();
}