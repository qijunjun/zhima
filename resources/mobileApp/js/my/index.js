/**
 * Created by apple on 16/1/27.
 */
$(document).ready(function () {
    $.ajax({
        url: "/API/public/validation",
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
    console.log("already logged in!");
    $("#name").html(data.username)
}

function showError(data) {
    window.location.href = "http://nongye.zmade.cn/mobileApp/my/unindex.html";
}