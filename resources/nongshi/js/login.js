var loginData;
var drop;

function setWindow() {
	var username = localStorage.getItem("zm_username");
	var password = localStorage.getItem("zm_password");
	var h = $(window).height();
	$("#logo").css("margin-top", h * 0.2 + "px")
	.find("img").css("height", h * 0.1 + "px");
	$("#title").css("font-size", h * 0.025 + "px");
	$("#loginInput").css("margin-top", h * 0.088 + "px")
	.find("input").css("line-height", h * 0.057 + "px")
	.css("font-size", h * 0.025 + "px");
	$(".icon").css("font-size", h * 0.025 + "px");
	$("#loginInput").children("input").css("width", $("#loginInput").children("input").width() - Number($("#user").css("border-left-width").replace("px", "")) * 2 + "px");
	$("#button").css("margin-top", h * 0.14 + "px")
	.find("button")
	//.css("height", h * 0.05 + "px")
	.css("margin-bottom", h * 0.015625 + "px").css("font-size", h * 0.025 + "px");
	$("#button").children("button").css("width", $("#button").children("button").width() + Number($("#user").css("border-left-width").replace("px", "")) * 2 + "px");
	if (username != null && username != "") {
		$("#user").val(username);
	}
	if (password != null && password != "") {
		$("#password").val(password);
	}
}

function login() {
   //登录
	uexDevice.getInfo('13');
	if (connectStatus < 0) {
		appcan.window.openToast(connectStatus, 2000, 5);
		if(drop == true)
			animateFold();
		appcan.window.openToast("对不起, 联网错误, 请检查网络", 2000, 5);
	}
	else {
		appcan.window.openToast("正在登录", 2000, 5);
		var username = $("#user").val(); //用户名
		var password = $("#password").val(); //密码
		if (username == "" || username == null) {
			appcan.window.openToast("请输入用户名！", 2000, 5);
			$("#user").focus();
			return;
		}
		if (password == "" || password == null) {
			appcan.window.openToast("请输入密码！", 2000, 5);
			$("#password").focus();
			return;
		}
		$.ajax({
			type : "post",
			//url : "/API/public/login",
			url:"http://dev.zmade.cn/API/user/login",
			dataType : "json",
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			},
			data : {
				username: username,
				password: password
			},
			success : function(data) {
				typeof data == "string" && (data = ('(' + data + ')'));
				if (data.code == "001" && data.result.companyid && data.result.companyid-0 != 0) {
					animateDrop();
					localStorage.setItem("companyid", data.result.companyid); //记住公司ID
					localStorage.setItem("companyName", data.result.name); //记住公司名称
					localStorage.setItem("companyLogo",data.result.logo); //公司logo
					localStorage.removeItem("zm_username",username);
					localStorage.removeItem("zm_password",password);
					localStorage.setItem("zm_username",username);	//如果登陆成功，记录用户名
					localStorage.setItem("zm_password",password);	//如果登陆成功，记录密码
					setTimeout(function() {
						window.location.href = "http://dev.zmade.cn/Home/Agriculture/eventlite";
					},1000);
				} else {
					localStorage.removeItem("zm_username",username);
					localStorage.removeItem("zm_password",password);
					alert(data.code, data.msg);
				}
			},
			error : function(err) {
				console.error(err);
			}
		});
	}
};

function animateDrop(){
	drop = true;
	$("#loginInput").css("display","none");
	$("#button").css("display","none");
	$("#logo").animate({
		marginTop: ($(window).height() - $("#ty_logo").height()) * 0.5 - 102
	});
	$(".ty-loading").css("display","block");

}

function animateFold(){
	drop = false;
	$(".ty-loading").fadeOut();
	//$(".ty-loading").css("display","none");
	$("#logo").animate({
		marginTop: 0
	});
	//$("#loginInput").css("display","block");
	//$("#button").css("display","block");
	$("#loginInput").fadeIn();
	$("#button").fadeIn();
}

function beforeSend(XMLHttpRequest) { //开始登录请求
	$("#login").attr("disabled", true);
	$("#login").html("正在登录");
}

function success(json) { //登录请求成功
	loginData = json;
	if (json.logincode == "001" && json.loginMessage == "success") { //登录成功
		$("#login").removeAttr("disabled");
		$("#login").html("登　录");
		localStorage.setItem("zm_username", $("#user").val()); //记住账号
		localStorage.setItem("zm_password", $("#password").val()); //记住密码
		localStorage.setItem("companyid", json.companyID); //记住公司ID
		localStorage.setItem("companyName", json.companyName); //记住公司名称
		//初始企业信息
		try {
			InitCompanyDatas();
		} catch (err) {
			alert(err.description);
		}

		appcan.window.openToast("登录成功", 2000, 5);
		// setInterval(function () {
		// window.location.replace("eventlite.html");
		// }, 500);
		appcan.window.open({
			name : "main",
			data : "eventlite.html",
			dataType : "0",
			aniId : "10",
			type : "0"
		});
	} else if (json.loginMessage == "username not found or password error") {
		$("#login").removeAttr("disabled");
		$("#login").html("登　录");
		appcan.window.openToast("用户名或密码错误！", 5000, 5);
	} else {
		$("#login").removeAttr("disabled");
		$("#login").html("登　录");
		appcan.window.openToast(json.loginMessage, 5000, 5);
	}
}

function complete(XMLHttpRequest, textStatus) { //登录请求完成
	if (textStatus == "timeout") {
		$("#login").removeAttr("disabled");
		$("#login").html("登　录");
		appcan.window.openToast("登录超时，请检查网络并稍后再试！", 5000, 5);
	} else if (textStatus != "success") {
		$("#login").removeAttr("disabled");
		$("#login").html("登　录");
		appcan.window.openToast("发生未知错误，请稍后再试！", 5000, 5);
	}
}

function error(XMLHttpRequest, textStatus, errorThrown) { //登录请求出错
	$("#login").removeAttr("disabled");
	$("#login").html("登　录");
	appcan.window.openToast("登录失败，请稍后重试！", 3000, 5);
}

function quit() { //退出
	uexWidgetOne.exit();
}
