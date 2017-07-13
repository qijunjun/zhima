var path;

function newEvent() {
	uexCamera.open();
}

function newEventTest() {
	localStorage.setItem("operator", "images/operator.png");
	window.location.href = "task.html";
}

function cbCamera(opCode, dataType, data) {
	// alert(data);
	switch (dataType) {
	case cText:
    path = data;
    uploadImage();
		break;
	case cJson:
		alert("uex.cJson");
		break;
	case cInt:
		alert("uex.cInt");
		break;
	default:
		alert("error");
	}
}

function uploadImage() {
  var uploadID = localStorage.getItem("uploadID") - 0;
  if(uploadID == 0 || isNaN(uploadID)) {
    localStorage.setItem("uploadID", 1);
    uploadID = 1;
  } else {
    localStorage.setItem("uploadID", uploadID + 1);
  }
  uexUploaderMgr.cbCreateUploader = function(opId, dataType, data) {
    if (dataType == 2 && data == 0) {
      appcan.window.openToast('正在上传图片,请稍候...', 5000, 5);
      uexUploaderMgr.uploadFile(opId, path, 'fileInfo', '1', 1024);
    }
  };
  uexUploaderMgr.onStatus = function(opId, fileSize, percent, serverResponse, status) {
    switch (status) {
      case 0:
        appcan.window.openToast(percent + "%", 500, 8);
        break;
      case 1:
        uexUploaderMgr.closeUploader(opId);
        appcan.window.openToast('上传成功！', 5000, 5);
        localStorage.setItem("operator", $.parseJSON(serverResponse).result.message);
          //alert(localStorage.getItem("operator"));
        window.location.href = "task.html";
        break;
      case 2:
        alert("上传Event失败!");
        appcan.window.openToast('正在重试,请稍候...', 5000, 5);
        uexUploaderMgr.closeUploader(opId);
        uploadImage(); //重传
        break;
      default:
        break;
    }
  };
  uexUploaderMgr.createUploader(uploadID, "http://" + window.location.hostname + '/Agriculture/Createnew/uploadTemp?type=head');
}