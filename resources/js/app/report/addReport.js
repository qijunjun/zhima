/**
 * Created by Lenovo on 2016/5/4.
 */
//$("#submit").click(function () {
//    var data = new Data();
//    data.getInput();
//    data.common({content:$("#content").val()});
//    $.ajax({
//        url: "/Faker/Report/addReport",
//        method: "post",
//        dataType: "json",
//        data: data.data,
//        success: function (data) {
//            if (data.code === "001") {
//                alert("提交成功!");
//            } else {
//                console.error(data.code, data.msg);
//            }
//        },
//        error: function (err) {
//            console.error(err);
//        }
//    });
//});
$("#submit").click(function () {
    var c = getParameterByName("c");
    var b = getParameterByName("b");
    var productid = getParameterByName("id");
    var content = $("#contentInfo").val();
    var tel = $("#tel").val();
    var pic = "";



    $.ajax({
        url: "/Faker/Report/addReport",
        method: "post",
        dataType: "json",
        data: {
            content: content,
            tel: tel,
            pic: pic,
            productid: productid,
            b: b,
            c: c
        },
        success: function (data) {
            if (data.code === "001") {
                new Inform({title:'通知',content:'提交成功，感谢您对我们工作的支持！'}).alert(function(){
                    window.history.back();
                });
            } else {
                console.error(data.code, data.message);
            }
        },
        error: function (err) {
            console.error(err);
        }
    });
});
$("#phone").click(function(){
    var calling = getParameterByName("call");
    $("#phone").attr("href","tel:"+calling);
});
var getParameterByName = function (name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};
// var gotMsg = function () {
//     window.location.href = "/Application/Faker/View/Index/report.html";
// };