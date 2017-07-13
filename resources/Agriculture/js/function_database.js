var functionsDiv = $(".h-functions");
function showFunction(result) {
	for (var i = 0; i < result.length; i++) {
		if (result[i] != 0) {
			//alert(result[i].image);
			//alert(result[i].name);
		  functionsDiv.append(
			$("<div></div>").addClass("h-functions-img").addClass("h-float-left").attr("id", "function" + result[i].id).append(
			  $("<img/>").attr("src", result[i].image)
			).append(
				$("<p></p>").html(result[i].name)
			).attr("onClick", "linkHtml(" + result[i].id + ", '" + result[i].name + "')")
		  );
		}
	}
  functionsDiv.append($("<div></div>").addClass("h-clear"));
	$(".h-bottom").css("padding-bottom", $(window).height() * 0.05 + "px");
	$(".h-save-button").css("font-size", $(window).height() * 0.025 + "px");
	$(".h-back-button").css("font-size", $(window).height() * 0.025 + "px");
	$(".h-photo-take").children("img").css("width", $(window).width() * 0.9 / 5 - 5 + "px");
	functionsDiv.css("padding-top", $(window).height() * 0.075 + "px");
	// functionsDiv.children(".h-functions-img").css("width", $(window).width() * 0.9 / 5 - 5 + "px");
	// functionsDiv.children(".h-functions-img").children("img").css("width", $(window).width() * 0.9 / 5 - 5 + "px");
	// functionsDiv.children(".h-functions-img").children("p").css("padding-top", $(window).width() * 0.02 + "px");
	// functionsDiv.children(".h-functions-img").children("p").css("padding-bottom", $(window).width() * 0.05 + "px");
	// functionsDiv.css("height", functionsDiv.children(".h-functions-img").height() * 3 + "px");
	if ($("body").height() < $(window).height()) {
		$(".h-bottom").css("position", "fixed");
		$(".h-bottom").css("bottom", "0");
	}
}

function linkHtml(id, name) {
		localStorage.setItem('functionid', id);
		localStorage.setItem('functionname', name);
		window.location.href = "photo.html";
}
