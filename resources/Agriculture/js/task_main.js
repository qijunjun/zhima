$(document).ready(function () {
  $.ajax({
    url: "/API/Company/fetchproducts",
    method: "get",
    dataType: "json",
    success: function(data) {
      typeof data == "string" && (data = eval("(" + data + ")"));
      if(data.code === "001") {
        showTask(data.result);
      } else if(confirm("请登录有效的用户！")) {
        window.location.href = loginURL;
      } else {
        window.history.back();
      }
    },
    error: function(err) {
      console.error(err);
      appcan.window.openToast("加载失败！", 2000, 5);
      window.history.back();
    }
  });
	if (localStorage.getItem('operator') == null) {
		$("#background").css("background-image", "url('/resources/Agriculture/images/back.webp')");
	} else {
		$("#background").css("background-image", "url('" + localStorage.getItem('operator') + "')");
	}

	$(".h-back-button").click(function () {
		// window.history.back(-1);
		window.location.href = "eventlite.html";
	});
});
