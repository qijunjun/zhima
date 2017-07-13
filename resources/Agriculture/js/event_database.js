var preEventID = null;
// 显示事件(时间线)信息
function showEvent(result) {
  for (var i = 0; i < result.length; i++) {
    if (result[i] != 0) {
      var eid = result[i].id;
      var eventDiv = $("<div></div>").addClass("timeLine").attr("id", eid);
      if (preEventID == null) {
        $("#user").after(eventDiv);
      } else {
        $("#" + preEventID).after(eventDiv);
      }
      preEventID = eid;
      var imagesDiv = $("<div></div>").addClass("picture").attr("id", "image" + eid);
      eventDiv.append(
        $("<div></div>").addClass("operatorHead").attr("id", "operator" + eid).append(
          $("<img/>").attr("src", result[i].operatorimage)
        )
      ).append(
        $("<div></div>").addClass("timePoint").attr("id", "taskImage" + eid).append(
          $("<img/>").attr("src", result[i].productimage)
        )
      ).append(
        $("<div></div>").addClass("operation").attr("id", "functionName" + eid).append(
          $("<strong></strong>").text(result[i].function_name)
        )
      ).append(imagesDiv).append(
        $("<div></div>").attr("id", "eventName" + eid).text(result[i].event_details)
      ).append(
        $("<div></div>").addClass("date").attr("id", "eventTime" + eid).append(
          $("<small></small>").text(result[i].event_time)
        )
      );
      var imgs = result[i].image_path;
      for (var ii = 0; ii < imgs.length; ii++) {
        if (imgs[ii] != 0) {
          imagesDiv.append(
            $("<img/>").attr("src", imgs[ii]).data("url", imgs[ii]).click(function (event) {
              openBigImg(event);
            })
          );
        }
      }
    }
  }
	var h = $(window).height();
	var w = $(window).width();
	$("#background").css("height", h * 0.289 + "px");
	$(".head").css("width", w * 0.3 + "px")
	.css("height", w * 0.3 + "px")
	.css("margin-top",  - w * 0.2 + "px");
	$(".timeLine").css("margin-left", parseFloat($(".head").css("margin-left")) + parseFloat($(".head").css("width")) / 2 + parseFloat($(".head").css("border")));
	$(".operatorHead").css("margin-left", -parseFloat($(".timeLine").css("margin-left")) / 2 - 65);
	$("#bottom").css("left", w / 2 - 25 + "px");
	$(".picture").children("img").css("height", $(".picture").children("img").width());
  $(".picture").children("img").css("margin-right", "5px");
}
function openBigImg(event) {
  var url = $(event.target).data("url");
	$("body").after("<div id='bigImg' style='width: 100%; height:100%; position:fixed; margin:0; left:0; top:0; display:block;'><img width='100%' src='" + url + "' /></div>");
	$("#bigImg").css("background-color", "rgba(0, 0, 0, 0.5)");
	$("#bigImg").children("img").css("margin-top", $(window).height() * 0.5 - $("#bigImg").children("img").height() / 2 + "px");
	$("#bigImg").click(function () {
		$(this).remove();
	});
}
