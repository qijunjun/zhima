/**
 * Created by Lenovo on 2016/5/27.
 */
var imgSrc ={};
var product_id;
var select = document.getElementById('data_name');
$(document).ready(function () {
    var functionInfo =document.getElementById('functionInfo');
    var str = "";
    functionInfo.innerHTML = null;
    var id = getParameterByName("id");
    var str1 = "";
    methods.ajax({
       url :"/Process/Index/edit",
        data:{id:id},
        callback: function(data){
            data =data.result;
            str1 = data.function_operateid;
            setData(data);
            product_id = data.productid;
            var event_time1 = parseInt(new Date(data.event_time).getTime() / 1000);
            var time = new Date(event_time1+"000"-0);
            var event_time= time.getFullYear() + "-" + ((time.getMonth() + 1) < 10 ? ('0' + (time.getMonth() + 1)) : (time.getMonth() + 1)) + "-" + (time.getDate() < 10 ? ('0' + time.getDate()) : time.getDate()) + " " + (time.getHours() < 10 ? ('0' + time.getHours()) : time.getHours()) + ":" + (time.getMinutes() < 10 ? ('0' + time.getMinutes()) : time.getMinutes())+ ":" + (time.getSeconds() < 10 ? ('0' + time.getSeconds()) : time.getSeconds());
            methods.getId('event_time').value = event_time;
            if (data.operatorimage)
                $("#operatorimage").addClass("getImg").html("已上传");
            if (data.image_path[0]) {
                $("#content_image").val(data.image_path[0]).addClass("getImg").html("已上传");
            }
            if (data.image_path[1]) {
                $("#content_image1").val(data.image_path[1]).addClass("getImg").html("已上传");
            }
            if (data.image_path[2]) {
                $("#content_image2").val(data.image_path[2]).addClass("getImg").html("已上传");
            }
            if (data.image_path[3]) {
                $("#content_image3").val(data.image_path[3]).addClass("getImg").html("已上传");
            }
            if (data.image_path[4]) {
                $("#content_image4").val(data.image_path[4]).addClass("getImg").html("已上传");
            }
            $(".portrait").click(function () {
                new ImgUpload({title: '上传图片', name: $(this).attr('name')}, $(this).val()).alert();
            });
            //填充产品操作列表
            methods.ajax({
                url: "/Process/Index/getOperation",
                data: {productid: product_id},
                callback : function(data){
                    for (var i = 0; i < data.result.length; i ++) {
                        str += "<option value='" + data.result[i].functionid + "'>" + data.result[i].function_name + "</option>";
                    }
                    functionInfo.innerHTML += str;
                    $("#functionInfo").val(str1);
                }
            })
        }
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
            id:$("#id").val(),
            functionid: str1,
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
            url: "/Process/Index/update",
            data: _data,
            callback : function(data){
                new Inform({title:'通知',content:'修改生产过程成功'}).alert(function(){
                    window.location.href = "/Application/Process/View/Index/process.html";
                })
            }
        })
    });
});

var getImgUpload = function (data) {
    for (var i in data.result) {
        imgSrc[i] = data.result[i].savepath + data.result[i].savename;
        $("#" + i).val(imgSrc[i]).addClass("getImg").html("已上传");
    }
    $(".popMsg").remove();
};

var getParameterByName = function (name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};