/**
 * Created by apple on 16/1/31.
 */
$(document).ready(function () {
    var id = GetQueryString("id");
    $.ajax({
        url: "http://nongye.zmade.cn/index.php/Inspection/Index/reportview/id/" + id,
        method: "post",
        data: {
            workorderid: id
        },
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
    console.log(data);
    $("#comment").html(data.comment);
    var content = $("#content");

    for (var i = 0; i < data.productslist.length; i ++) {
        var aLink = $("<a></a>").attr("href", data.productslist[i].commoditylink);
        var div = $("<div></div>");
        var img = $("<img />").attr("src", data.productslist[i].commodityphoto).attr("alt", data.productslist[i].commodityname);
        var span = $("<span></span>").html(data.productslist[i].commodityname);
        content.append(aLink.append(div.append(img).append(span)));
    }
}

function showError(data) {
    console.error(data.code, data.msg);
    alert(data.msg);
    window.location.href = "http://nongye.zmade.cn/mobileApp/my/login.html";
}