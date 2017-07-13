/**
 * Created by apple on 16/5/26.
 */
$(document).ready(function () {

});
$("#submit").click(function () {
    var data = new Data();
    $("#submit").attr("style","display:none;");
    var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
    if(!myreg.test($("#mobile").val()))
    {
        $("#mobile").val('手机号无效！');
        $("#submit").attr("style","display:block;");
        return false;
    }
    if (!data.getInput(false)) {
        return ;
    }
        $.post("http://hongbao.zmade.cn/hongbao.php", data.data)

            .done(function (data) {
                var show = eval("(" + data + ")");
                //if(show.code == "002"){
                //    $("#result").attr("style","display:none;");
                //    $("#p1").attr("style","display:none;");
                //    $("#p2").attr("style","display:none;");
                //    $("#mobile").attr("style","display:none;");
                //}
                //if(show.code == "003"){
                //    $("#result").attr("style","display:none;");
                //    $("#p1").attr("style","display:none;");
                //    $("#p2").attr("style","display:none;");
                //    $("#mobile").attr("style","display:none;");
                //    $("#show").html(show.msg).attr("style","padding-top:100px;");
                //}
                document.getElementById("show").innerHTML = show.msg;
            })
            //.fail(function () {
            //    alert("error");
            //})
            //.always(function () {
            //    alert("finished");
            //});
});

var gotMsg = function () {
    window.location.href = "/Marketing/View/Index/getWXInfo.html";
};
