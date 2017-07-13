/**
 * Created by Lenovo on 2016/5/1.
 */
$(document).ready(function () {
   methods.addEvent(document.getElementById('submit'),'click',function(){
       var data = new Data();
       if(!data.getInput()){
           new Inform({title:'通知',content:'数据项不完整<br /><span class="red">请检查</span>'}).alert();
           return ;
       }
       data = data.data;
       data["start_time"] = parseInt(new Date(data["start_time"]).getTime() / 1000);
       data["end_time"] = parseInt(new Date(data["end_time"]).getTime() / 1000);
       // data["start_time"] = parseInt(new Date(data["start_time"]).getTime() / 1000)-28800;
       // data["end_time"] = parseInt(new Date(data["end_time"]).getTime() / 1000)-28800;
       methods.ajax({
           url : "/Marketing/Index/add",
           data:data,
           callback : function(data){
               new Inform({title:'通知',content:'新增成功'}).alert(function(){
                   window.location.href = "/Application/Marketing/View/Index/list.html";
               })
           }
       })
   })
});