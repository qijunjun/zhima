/**
 * Created by apple on 16/1/30.
 */
$(document).ready(function () {
    $.ajax({
        url: "/ProductCenter/Product/appPushProductList",
        method: "post",
        dataType: "json",
        data: {},
        success: function (data) {
            typeof data == "string" && (data = eval("(" + data + ")"));
            checkOnline(data, showData, showError);
        },
        error: function (err) {
            console.error(err);
        }
    });
});

function showData(data) {
    var article = $("article");
    for (var i = 0; i < data.length; i ++) {
        var aLink = $("<a></a>").attr("href", "http://nongye.zmade.cn/mobileApp/product/details.html?id=" + data[i].productid);
        var section = $("<section></section>");
        var img = $("<img />").attr("src", "http://nongye.zmade.cn/Public/Uploads/" + data[i].productimage).attr("alt", data[i].productname);
        var h4 = $("<h4></h4>").html(data[i].productname);
        var p = $("<p></p>").html(data[i].productinfo);
        article.append(aLink.append(section.append(img).append(h4).append(p)));
    }
}

function showError(data) {
//  console.log(data);
//  confirm("请先登录！") == false ? window.location.href = "http://nongye.zmade.cn/mobileApp/Index/index.html" : window.location.href = "http://nongye.zmade.cn/mobileApp/my/login.html";
	$("#loginFirst").css("display", "block");
	if (data.code == "002") {
		$("#infomation").html("暂无数据<br /><br />请登录pc端后台维护产品！");
		$("#loginFirst").children("a").css("display", "none");
	}
}