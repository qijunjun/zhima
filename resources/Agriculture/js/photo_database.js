function addEventRecord() {
  $.ajax({
    url: "/Agriculture/Createnew/addData",
    method: "post",
    dataType: "json",
    data: {
      images: imageList,
      productID: localStorage.getItem('taskid'),
      operatorID: localStorage.getItem('functionid'),
      dataName: localStorage.getItem('functionname'),
      eventTime: dataTime,
      dataDetails: $("#description").val(),
      companyID: localStorage.getItem('companyid'),
      longitude: longitude,
      latitude: latitude,
      userlocation: userlocation,
      operator: localStorage.getItem('operator')

    },
    beforeSend: function() {
      appcan.window.openToast('正在提交，请稍候...', 1000, 5);
    },
    success: function(data) {
      typeof data == "string" && (data = eval("(" + data + ")"));
      if(data.result.code == 0) {
        appcan.window.openToast("提交成功！", 2000, 5);
        window.location.href = "eventlite.html";
      } else {
        alert(data.result.message);
      }
    },
    error: function(err) {
      alert(JSON.stringify(err));
      console.error(err);
      appcan.window.openToast("提交失败，请重试！", 2000, 5);
    }
  });
}