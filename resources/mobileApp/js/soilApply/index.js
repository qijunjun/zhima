/**
 * Created by apple on 16/1/30.
 */
var tip = $('.tip');
var loading = $("#loading");
var access = $("#access");
var wrong = $("#wrong");
var word = $("#word");

$(document).ready(function () {
    $.ajax({
        url: "/API/public/validation",
        method: "post",
        dataType: "json",
        data: {},
        success: function (data) {
            typeof data == "string" && (data = eval("(" + data + ")"));
            checkOnline(data, function (data) {
				$("#dataShow").css("display", "block");
                console.log("哎哟不错哟,登录了哟");
            }, function (data) {
                console.error(data.code, data.msg);
				$("#loginFirst").css("display", "block");
//              if (confirm("请先登录!")) {
//                  window.location.href = "http://nongye.zmade.cn/mobileApp/my/login.html";
//              } else {
//                  window.history.back(-1);
//              }
            });
        },
        error: function (err) {
            console.error(err);
        }
    });
});

function submitInfo() {

    tip.css("display", "block");
    access.css("display", "none");
    wrong.css("display", "none");

    var type = $("#type").val(), name = $("#name").val(), phone = $("#phone").val(), address = $("#address").val(), title = $("#content").val();

    if (type == 0 || name == '' || phone == '' || address == '' || title == '') {
        loading.css("display", "none");
        wrong.css("display", "inline-block");
        word.text("数据不许为空");
        setTimeout(function () {
            tip.css("display", "none");
            loading.css("display", "none");
            access.css("display", "none");
            wrong.css("display", "none");
        }, 500);
        return ;
    }

    $.ajax({
        url: "http://nongye.zmade.cn/index.php/Inspection/Index/NewWorkorder",
        method: "post",
        dataType: "json",
        data: {
            proposaltype: type,
            customername: name,
            customerphone: phone,
            customergroup: address,
            title: title
        },
        success: function (data) {
            typeof data == "string" && (data = eval("(" + data + ")"));
            checkOnline(data, showData, showError);
        },
        error: function (err) {
            console.error(err);
        }
    });
}

function showData(data) {
    console.log(data);
    loading.css("display", "none");
    access.css("display", "inline-block");
    word.text("提交成功");
    setTimeout(function () {
        window.location.href = "http://nongye.zmade.cn/mobileApp/my/soilInfo.html";
    }, 500);
}

function showError(data) {
    console.error(data.code, data.error);
    loading.css("display", "none");
    wrong.css("display", "inline-block");
    word.text(data.msg);
    setTimeout(function () {
        tip.css("display", "none");
        loading.css("display", "none");
        access.css("display", "none");
        wrong.css("display", "none");
    }, 500);
}