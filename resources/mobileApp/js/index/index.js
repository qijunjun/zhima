$(document).ready(function() {
	$.ajax({
		url: "http://shequ.zmade.cn/index.php?app=ask&ac=api&ts=askQuery",
		method: "post",
		dataType: "jsonp",
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

function agrCheck() {
//	http://nongye.zmade.cn/Home/Agriculture/eventlite.html
	$.ajax({
        url: "/API/public/validation",
        method: "post",
        dataType: "json",
        data: {},
        success: function (data) {
            typeof data == "string" && (data = eval("(" + data + ")"));
            if (data.code == "001") {
                window.location.href = "http://nongye.zmade.cn/Home/Agriculture/eventlite.html";
            } else {
                window.location.href = "http://nongye.zmade.cn/mobileApp/my/unindex.html";
            }
        },
        error: function(err) {
        		console.error(err);
        }
    });
}

function showData(data) {
	console.log(data);
	var content = $("#chatInfo .content");
	for (var i = 0; i < data.length; i ++) {
		var aLink = $("<a></a>").attr("href", "http://shequ.zmade.cn/index.php?app=ask&ac=question&id=" + data[i].askid);
		var divChatArea = $("<div></div>").addClass("chatArea");
		{
			var h4 = $("<h4></h4>");
			var divIsIng = $("<div></div>").addClass("isIng");
			var span = $("<span></span>").html(data[i].title);
			h4.append(divIsIng).append(span);
		}
		var p = $("<p></p>").html("提问人：" + data[i].actionarr.user.username);
		{
			var h5 = $("<h5></h5>");
			span = $("<span></span>").html(data[i].content);
			h5.append(span);
		}
		{
			var divMessage = $("<div></div>").addClass("icon");
			var divBad = $("<div></div>").addClass("icon");
			var divGood = $("<div></div>").addClass("icon");
			var imgMessage = $("<img />").attr("src", "/resources/mobileApp/img/answerNumber.png").attr("alt", "回复数");
			var imgBad = $("<img />").attr("src", "/resources/mobileApp/img/badNumber.png").attr("alt", "差");
			var imgGood = $("<img />").attr("src", "/resources/mobileApp/img/goodNumber.png").attr("alt", "赞");
			span = $("<span></span>").html(data[i].count);
			divMessage.append(imgMessage).append(span);
			divBad.append(imgBad);
			divGood.append(imgGood);
		}
		content.append(aLink.append(divChatArea.append(h4).append(p).append(h5).append(divMessage).append(divBad).append(divGood)));
	}
}

function showError(data) {
	console.error(data.msg, data.code);
}
