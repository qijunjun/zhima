/**
 * Created by apple on 16/1/27.
 */
$(document).ready(function() {
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

function submitReg() {
	var bad = true;

    var account = $("#account").val() || (alert("账户不可为空")) || (bad = false);
    var password = $("#password").val() || (alert("密码不可为空")) || (bad = false);
    var mobile = $("#mobile").val() || (alert("手机号不可为空")) || (bad = false);
    var company = $("#company").val() || (alert("企业名称不可为空")) || (bad = false);

    if (!bad) {
        return ;
    }

/*    $.ajax({
        url: "http://shequ.zmade.cn/index.php?app=user&ac=api&ts=reg",
        method: "post",
        dataType: "jsonp",
        data: {

            username: account,
            phone: mobile,
            sitekey: '6df91e1ba080adff',
            sitename: 'nongye.zmade.cn',
            openid: '500',
            ip: '192.168.111.112',
            company: company
        },
        success: function (data) {
            console.log(data);
        },
        error: function (err) {
            console.error(err);
        }
    });*/

    $.ajax({
        url: "/API/public/register",
        method: "post",
        dataType: "json",
        data: {
            username: account,
            password: password,
            phone: mobile,
            company: company
        },
        success: function (data) {
            typeof data == "string" && (data = eval("(" + data + ")"));
            checkOnline(data, function (data) {
                console.log(data);
                window.location.href = "http://nongye.zmade.cn/mobileApp/my/index.html";
            }, function(data) {
            		alert(data.msg);
            });
        },
        error: function (err) {
            console.error(err);
        }
    });
}