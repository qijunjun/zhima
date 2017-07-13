/**
 * Created by apple on 16/1/26.
 */
$(document).ready(function () {
	$.ajax({
        url: "/API/public/validation",
        method: "post",
        dataType: "json",
        data: {},
        success: function (data) {
            typeof data == "string" && (data = eval("(" + data + ")"));
            checkOnline(data, function (data) {
                console.log("哎哟不错哟,登录了哟");
				$("#productInfo").children("span").css("display", "none");
				showData();
				$("#agrInfo").children("span").css("display", "none");
            }, function (data) {
                console.error(data.code, data.msg);
				$("#productInfo").children("span").html("暂无数据");
				$("#agrInfo").children("span").html("暂无数据");
            });
        },
        error: function (err) {
            console.error(err);
        }
    });
	
	
    if ($("#environmentCanvas") == true) {
    		var ctx = $("#environmentCanvas").get(0).getContext("2d");
	    var data = {
	        labels: ["13时", "14时", "15时", "16时", "17时", "18时"],
	        datasets: [{
	            fillColor : "rgba(220,220,220,0.5)",
	            strokeColor : "rgba(220,220,220,1)",
	            pointColor : "rgba(220,220,220,1)",
	            pointStrokeColor : "#fff",
	            data : [6, 8, 12, 3, 0, -5, -8]
	        }]
	    };
	    var environmentChart = new Chart(ctx).Line(data, {
	        responsive: true,
	        scaleLineColor: "rgba(255, 255, 255, 1)",
	        pointLabelFontColor : "rgba(255, 255, 255, 1)",
	        angleLineColor: "rgba(255, 255, 255, 1)"
	    });
    }
});

function showData() {
	$.ajax({
		url: "/ProductCenter/Product/appPushProductList",
		method: "post",
		dataType: "json",
		data: {},
		success: function (data) {
			typeof data == "string" && (data = eval("(" + data + ")"));
			console.log(data.result);
			var productInfo =  $("#productInfo");
			for (var i = 0; i < 3; i ++) {
				var aLink = $("<a></a>").attr("href", "http://nongye.zmade.cn/mobileApp/product/details.html?id=" + data.result[i].productid);
				var div = $("<div></div>");
				var img = $("<img />").attr("src", "http://nongye.zmade.cn/Public/Uploads/" + data.result[i].productimage).attr("alt", data.result[i].productname);
				productInfo.append(aLink.append(div.append(img)));
			}
        },
		error: function(err) {
			console.log(err);
		}
	});
}
