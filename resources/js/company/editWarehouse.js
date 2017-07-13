/**
 * Created by apple on 16/5/5.
 */
$(document).ready(function () {
    var id = getParameterByName("id");
    methods.ajax({
        url : "/Company/Warehouse/edit",
        data :{id : id},
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
        data.common("id",id);
        methods.ajax({
            url : "/Company/Warehouse/update",
            data : data.data,
            callback : function(){
               new Inform({title : '通知',content : '修改成功!'}).alert(function(){
                   window.location.href = "/Application/Company/View/Warehouse/warehouse.html";
               })
            }
        })
    });
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