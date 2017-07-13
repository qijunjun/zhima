/**
 * Created by GUOQH on 2016/5/28.
 */
$(document).ready(function () {
    var id = getParameterByName("id");
    methods.ajax({
        url: "/Admin/User/Get",
        data: {id: id},
        callback : function(data){
            setData(data.result);
        }
    });
    methods.addEvent(document.getElementById('submit'),'click',function(){
        var data = new Data();
        if (!data.getInput()) {
            new Inform({title: '通知', content: '数据项不完整<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
        data.common({id : id});
        methods.ajax({
            url: "/Admin/User/Edit",
            data: data.data,
            callback: function(){
                new Inform({title: '通知', content: '修改成功!'}).alert(function () {
                    window.location.href = "/Application/Admin/View/User/user.html";
                });
            }
        })
    })
});
var getParameterByName = function (name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};