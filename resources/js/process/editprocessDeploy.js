/**
 * Created by 123 on 2016/6/26.
 */
var imgSrc = {};
$(document).ready(function () {
    var id = getParameterByName("id");
    var functionid;
    methods.ajax({
        url : "/Process/Config/editFunction",
        data :{id : id},
        callback : function(data){
            setData(data.result);
            functionid = data.result.functionid;
            if (data.result.function_image && data.result.function_image != "0") {
                $("#function_image").addClass("getImg").html("已上传");
                imgSrc.function_image = data.result.function_image;
            }
            $(".portrait").click(function () {
                new ImgUpload({title: '上传图片', name: $(this).attr('name')}, $(this).val()).alert();
            });
        }
    });
    methods.addEvent(document.getElementById('submit'),'click',function(){
        var data = new Data();
        if (!data.getInput()) {
            new Inform({title: '通知', content: '数据项不完整<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
        data.common("id",id)
            .common("functionid",functionid)
            .common(imgSrc);
        methods.ajax({
            url : "/Process/Config/updateFunction",
            data : data.data,
            callback : function(){
                new Inform({title : '通知',content : '修改成功!'}).alert(function(){
                    window.location.href = "/Application/Process/View/Index/processDeploy.html";
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