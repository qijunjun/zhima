$(document).ready(function () {
  $(".h-Title-p").text(localStorage.getItem('taskname'));
  $.ajax({
    url: "/API/company/fetchoperation",
    method: "post",
    data: {
      productId: localStorage.getItem('taskid')
    },
    dataType: "json",
    success: function(data) {
      typeof data == "string" && (data = eval("(" + data + ")"));
      if(data.code == "001") {
        showFunction(data.result);
      } else if(confirm("请登录有效的用户！")) {
        window.location.href = loginURL;
      } else {
        window.history.back();
      }
    },
    error: function(err) {
      console.error(err);
      appcan.window.openToast("操作加载失败，请稍后重试！", 2000, 5);
    },
  });
	$(".h-back-button").click(function(){
		// window.history.back(-1);
		window.location.href = "task.html";
	});
});
