/**
 * Created by apple on 16/1/27.
 */
$(document).ready(function () {
    $.ajax({
        url: "http://nongye.zmade.cn/index.php/inspection/index/GetDataByUser",
        method: "post",
        dataType: "json",
        data: {},
        success: function (data) {
            typeof data == "string" && (data = eval("(" + data + ")"));
            checkOnline(data, showData);
        },
        error: function (err) {
            console.error(err);
        }
    });
});

function showData (data) {
//  console.log(data);
    var article = $("article");
    for (var i = 0; i < data.length; i ++) {
        var aLink = $("<a></a>");
        var section = $("<section></section>");
        var divStatus = $("<div></div>").addClass("status");
        var span = $("<span></span>");
        var pTime = $("<p></p>").html(new Date(data[i].createdtime + "000" - 0).toLocaleString());
        var pTitle = $("<p></p>").html(data[i].title);
        var divBgProcess = $("<div></div>").addClass("bgProcess");
        var divprocess = $("<div></div>").addClass("process");
        var pResult = $("<p></p>");

        switch (data[i].status) {
            case "complete" : aLink.attr("href", "http://nongye.zmade.cn/mobileApp/my/report.html?id=" + data[i].workorderid);
                divStatus.addClass("finish");
                divStatus.append(span.html("已完成"));
                divprocess.addClass("finishProcess");
                pResult.html("查看报告&nbsp;&gt;").css("float", "right");
                break;
            case "new" : divStatus.addClass("wrong");
                divStatus.append(span.html("审核中"));
                divprocess.addClass("wrongProcess");
                pResult.html("正在审核中...");
                break;
            case "accept" : divStatus.addClass("deal");
                divStatus.append(span.html("处理中"));
                divprocess.addClass("dealProcess");
                pResult.html("审核通过，正在处理中...");
                break;
            case "reject" : divStatus.addClass("wrong");
                divStatus.append(span.html("未通过：驳回"));
                divBgProcess.css("display", "none");
                pResult.html("审核未通过，如有疑问请联系我们!");
                break;
        }
        article.append(aLink.append(section.append(divStatus).append(pTime).append(pTitle).append(divBgProcess).append(divprocess).append(pResult)));
    }
}