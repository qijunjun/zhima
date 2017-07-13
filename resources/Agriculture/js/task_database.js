var selectDiv = $("#select");
function showTask(result) {
  if(result.length == 0) {
    selectDiv.append(
      $("<div></div>").text("没有产品！")
    );
  }
  for (var i = 0; i < result.length; i++) {
    if (result[i] != 0) {
		//alert("task" + result[i].productid);
		//alert( result[i].productimage);
      selectDiv.append(
        $("<div></div>").attr("id", "task" + result[i].productid).append(
          $("<div></div>").addClass("picture").append(
            $("<img/>").attr("src", result[i].productimage).attr("alt", result[i].productname).attr("title", result[i].productname)
          )
        ).append(
          $("<div></div>").addClass("name").text(result[i].productname)
        ).attr("onClick", "linkHtml(" + result[i].productid + ", '" + result[i].productname + "')")
      );
    }
  }
	var h = $(window).height();
	$("#background").css("height", h * 0.57 + "px");
	$("#body").css("margin-top", h * 0.54 + "px");
	$("#title").css("line-height", h * 0.0598958 + "px")
		.css("font-size", h * 0.025 + "px");
	selectDiv.css("margin-top", h * 0.07552 + "px");
	$(".picture").css("width", $(window).width() * 0.1867 + "px")
		.css("height", $(window).width() * 0.1867 + "px");
	$(".name").css("font-size", h * 0.025 + "px")
		.css("width", $(window).width() * 0.1867 + "px")
		.css("margin-top", h * 0.015625 + "px");
	$(".h-bottom").css("padding-bottom", $(window).height() * 0.05 + "px");
	$(".h-back-button").css("font-size", $(window).height() * 0.025 + "px");
	if ($("body").height() < $(window).height()){
		// $(".h-bottom").css("position", "fixed");
		$(".h-bottom").css("bottom", "0");
	}
}

function linkHtml(id, name){
	localStorage.setItem('taskid', id);
	localStorage.setItem('taskname', name);
	//alert(localStorage.getItem('taskid'));
	//alert(localStorage.getItem('taskname'));
	window.location.href = "functions.html";
}
