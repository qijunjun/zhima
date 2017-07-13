$(document).ready(function() {
	var id = GetQueryString("id");
	
	var codeImgBtn = $("#codeImg");
	var cover = $(".cover");
	codeImgBtn.click(function() {
		cover.css("display", "block");
	});
	cover.click(function() {
		cover.css("display", "none");
	});
	
	$.ajax({
		url: "/ProductCenter/Product/appPushProductDetail/id/" + id,
		type: "post",
		dataType: "json",
		data: {
			productid: id
		},
		success: function(data) {
			typeof data == "string" && (data = eval("(" + data +")"));
			checkOnline(data, showData, showError);
		},
		error: function(err) {
			console.error(err);
		}
	});
});

function showData(data) {
//	console.log(data);
	$("#productImg").attr("src", "http://nongye.zmade.cn/Public/Uploads/" + data.productimage).attr("alt", data.productname);
	$("#productImg").error(function(){
		console.error("img error!");
		$("#productImg").attr("src", "http://nongye.zmade.cn/Public/img/banner_1.jpg").attr("alt", data.productname);
	});
	$("#productName").html(data.productname) || $("#productName").html("无");
	$("#productInfo").html() || $("#productInfo").html("无");
	$("#productPlace").html() || $("#productPlace").html("无");
	$("#productDetails").html(data.productinfo) || $("#productDetails").html("无");
}

function showError(data) {
//	console.error(data.code, data.msg);
	confirm("请先登录！") == false ? window.location.href = "http://nongye.zmade.cn/mobileApp/Index/index.html" : window.location.href = "http://nongye.zmade.cn/mobileApp/my/login.html";
}
