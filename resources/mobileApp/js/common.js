/**
 * Created by apple on 16/1/27.
 */
function checkOnline(data, callback, errCallback) {
    if (data.code == "001") {
        callback(data.result);
    } else {
        console.error (data.code, data.msg);
        typeof errCallback == "function" || (errCallback = function(){});
        errCallback(data);
    }
}

//所有有footer的页面点击我的执行的函数
function isLogin() {
    $.ajax({
        url: "/API/public/validation",
        method: "post",
        dataType: "json",
        data: {},
        success: function (data) {
            typeof data == "string" && (data = eval("(" + data + ")"));
            if (data.code == "001") {
                window.location.href = "http://nongye.zmade.cn/mobileApp/my/index.html";
            } else {
                window.location.href = "http://nongye.zmade.cn/mobileApp/my/login.html";
            }
        },
        error: function(err) {
        		console.error(err);
        }
    });
}

function logout() {
    $.ajax({
        url: "/API/public/logout",
        method: "post",
        data: {},
        success: function () {
            alert("成功退出");
            window.location.href = "http://nongye.zmade.cn/mobileApp/my/unindex.html";
        },
        error: function (err) {
            console.error(err);
        }
    });
}

//屏蔽功能
function unUse() {
	var infoTip = $("#infoTip");
	infoTip.css("display", "block");
}
//关闭提示窗
function closeInfoTip() {
	var infoTip = $("#infoTip");
	infoTip.css("display", "none");
}

function GetQueryString(name)
{
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
}