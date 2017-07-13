/**
 * Created by apple on 16/1/27.
 */
var username_token;

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
	
    var v;
    username_token = JSON.parse(window.localStorage.getItem("username"));
    if (username_token) {
        for (v in username_token) {
            break;
        }
        $("#account").val(v);
    }
    
    //input检查回车
    window.document.onkeypress = function(e) {
    		e = e || window.event;
    		var key = e.which || e.keyCode;
    		if (key == 13) {
    			submitLog();
    		}
    }
});

function showUser() {
    if (!username_token) {
        return;
    }
    $(".accounts").css("display", "block");
    for (var v in username_token) {
        var accountInfo = $("#accountInfo");
        var li = $("<li></li>").attr("onclick", "quickLogin(this)");
        var divImg = $("<div></div>");
        var imgUser = $("<img />").attr("src", "http://nongye.zmade.cn/resources/mobileApp/img/user.png").attr("alt", "userIcon");
        var spanUser = $("<span></span>").html(v);
        var divUnSelect = $("<div></div>").addClass("unSelect");
        var divSelected = $("<div></div>").addClass("selected");
        accountInfo.append(li.append(divImg.append(imgUser)).append(spanUser).append(divUnSelect.append(divSelected)));
    }
}

function quickLogin(li) {
    $($(li).children("div")[1]).children("div").css("display", "block");
    var account = $(li).children("span").html();
    $.ajax({
        url: "/API/public/validation",
        method: "post",
        data: {
            token: username_token[account]
        },
        dataType: "json",
		crossDomain: true,
		xhrFields: {
			withCredentials: true
		},
        success: function (data) {
            typeof data == "string" && (data = eval("(" + data + ")"));
            if (data.code != "001") {
                delete username_token[account];
                window.localStorage.setItem("username", JSON.stringify(username_token));
                $(li).remove();
            }
            checkOnline(data, showData, quickError);
        },
        error: function (err) {
            console.error(err);
        }
    });
}

function closeCover() {
    $(".accounts").css("display", "none");
    var accountInfo = $("#accountInfo");
    accountInfo.html("");
}

function submitLog() {
    var account = $("#account").val();
    var password = $("#password").val();


/*    $.ajax({
        url: "http://shequ.zmade.cn/index.php?app=user&ac=api&ts=login",
        method: "post",
        dataType: "jsonp",
        data: {
            username: account,
            sitekey: '6df91e1ba080adff',
            sitename: 'nongye.zmade.cn',
            openid: '500',
            ip: '192.168.111.112'


        },
        success: function (data) {
            console.log(data);
        },
        error: function (err) {
            console.error(err);
        }
    });*/

    $.ajax({
        url: "/API/public/login",
        method: "post",
        data: {
            username: account,
            password: password
        },
        dataType: "json",
		crossDomain: true,
		xhrFields: {
			withCredentials: true
		},
        success: function (data) {
            typeof data == "string" && (data = eval("(" + data + ")"));
            checkOnline(data, showData, showError);
            if (data.code != "001") {
                return ;
            }
            var username = JSON.parse(window.localStorage.getItem("username"));
            if (!username) {
                username = {};
            }
            var strAccount = account.toString();
            username[strAccount] = data.result.token;
            window.localStorage.setItem("username", JSON.stringify(username));
        },
        error: function (err) {
            console.error(err);
        }
    });
}

function showData(data) {
    //console.log(data);
    window.location.href = "http://nongye.zmade.cn/mobileApp/my/index.html";
}

function showError(data) {
    console.error(data.code, data.msg);
    alert("账号密码错误");
}

function quickError(data) {
    console.error(data.code, data.msg);
    alert("账号记录已过期");
}