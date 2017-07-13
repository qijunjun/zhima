$(document).ready(function () {
	$.ajax({
        url: "/API/public/validation",
        method: "post",
        dataType: "json",
        data: {},
        success: function (data) {
            typeof data == "string" && (data = eval("(" + data + ")"));
            checkOnline(data, function() {
            		alert("您已登录!");
            		window.location.href = "http://nongye.zmade.cn/mobileApp/my/index.html";
            }, function() {
            	
            });
        },
        error: function (err) {
            console.error(err);
        }
    });
});