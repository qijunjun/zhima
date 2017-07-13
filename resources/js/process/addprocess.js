/**
 * Created by 123 on 2016/5/24.
 */

    var imgSrc = {};
    $(document).ready(function () {
        $(".portrait").click(function(){
            new ImgUpload({title: '上传图片', name: $(this).attr('name')}, $(this).val()).alert();
        });
        var produceInfoList = {};
        var functionInfo =document.getElementById('functionInfo');
        var select = document.getElementById('data_name');
        select.innerHTML = null;
        methods.ajax({
            url: "/admin/profile/fetch",
            callback : function(data){
                produceInfoList = data.result;
            }
        });
        methods.ajax({
            url: "/Product/api/fieldsList",
            callback : function(data){
                data = data.result;
                var str = "<option value='0' disabled='disabled' selected='selected'>--请选择产品--</option>";
                for(var i = 0;i < data.length;i++){
                    str +="<option value='"+data[i].productid+"'>"+data[i].productname + "-" + data[i].guige+"</option>";
                }
                select.innerHTML += str;
            }
        });
        methods.addEvent(select,'change',function(){
            functionInfo.innerHTML = null;
            methods.ajax({
                url: "/Process/Index/getOperation",
                data: {productid: methods.getId('data_name').value},
                callback : function(data){
                    var str1="";
                    for (var i = 0; i < data.result.length; i ++) {
                        str1 += "<option value='" + data.result[i].functionid + "'>" + data.result[i].function_name + "</option>";
                    }
                    functionInfo.innerHTML += str1;
                }
            });
        });
        methods.addEvent(document.getElementById('submit'),'click',function(){
    var data = new Data();
    if(!data.getInput()){
        new Inform({title:'通知',content:'数据项不完整<br /><span class="red">请检查</span>'}).alert();
        return ;
    }
    data = data.data;
    var event_time1 = parseInt(new Date(data.event_time).getTime() / 1000);
    var time = new Date(event_time1+"000"-0);
    var event_time= time.getFullYear() + "-" + ((time.getMonth() + 1) < 10 ? ('0' + (time.getMonth() + 1)) : (time.getMonth() + 1)) + "-" + (time.getDate() < 10 ? ('0' + time.getDate()) : time.getDate()) + " " + (time.getHours() < 10 ? ('0' + time.getHours()) : time.getHours()) + ":" + (time.getMinutes() < 10 ? ('0' + time.getMinutes()) : time.getMinutes())+ ":" + (time.getSeconds() < 10 ? ('0' + time.getSeconds()) : time.getSeconds());
    var _data ={
        productid:$("#data_name").val(),
        functionid: $("#functionInfo").val(),
        function_name: $("#functionInfo").find("option:selected").text(),
        userlocation: $("#userlocation").val(),
        operatorimage: $("#operatorimage").val(),
        event_time:event_time,
        event_details: $("#event_details").val(),
        content_image: $("#content_image").val(),
        content_image1: $("#content_image1").val(),
        content_image2: $("#content_image2").val(),
        content_image3: $("#content_image3").val(),
        content_image4: $("#content_image4").val()
    };
    methods.ajax({
        url: "/Process/Index/add",
        data: _data,
        callback : function(data){
            new Inform({title:'通知',content:'新增生产过程成功'}).alert(function(){
                window.location.href = "/Application/Process/View/Index/process.html";
            })
        }
    })
})
});

var getImgUpload = function (data) {
    for (var i in data.result) {
        imgSrc[i] = data.result[i].savepath + data.result[i].savename;
        $("#" + i).val(imgSrc[i]).addClass("getImg").html("已上传");
    }
    $(".popMsg").remove();
};