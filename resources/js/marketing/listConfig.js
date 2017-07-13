/**
 * Created by Lenovo on 2016/5/1.
 */
$(document).ready(function () {
    var id =location.href.substr(location.href.lastIndexOf("=") + 1);
    methods.ajax({
        url : "/Marketing/Index/edit",
        data :{id:id},
        callback : function(data){
            data = data.result;
            id = data.promotionid;
            setData(data[0]);
            var time = new Date(data[0].start_time+"000"-0);
            methods.getId('start_time').value = time.getFullYear() + "-" + ((time.getMonth() + 1) < 10 ? ('0' + (time.getMonth() + 1)) : (time.getMonth() + 1)) + "-" + (time.getDate() < 10 ? ('0' + time.getDate()) : time.getDate()) + " " + (time.getHours() < 10 ? ('0' + time.getHours()) : time.getHours()) + ":" + (time.getMinutes() < 10 ? ('0' + time.getMinutes()) : time.getMinutes())+ ":" + (time.getSeconds() < 10 ? ('0' + time.getSeconds()) : time.getSeconds());
            var end_time = new Date(data[0].end_time+"000"-0);
            methods.getId('end_time').value = end_time.getFullYear() + "-" + ((end_time.getMonth() + 1) < 10 ? ('0' + (end_time.getMonth() + 1)) : (end_time.getMonth() + 1)) + "-" + (end_time.getDate() < 10 ? ('0' + end_time.getDate()) : end_time.getDate()) + " " + (end_time.getHours() < 10 ? ('0' + end_time.getHours()) : end_time.getHours()) + ":" + (end_time.getMinutes() < 10 ? ('0' + end_time.getMinutes()) : end_time.getMinutes())+ ":" + (time.getSeconds() < 10 ? ('0' + time.getSeconds()) : time.getSeconds());
        }
    });
    methods.addEvent(document.getElementById('submit'),'click',function(){
        var data = new Data();
        if(!data.getInput()){
            new Inform({title:'通知',content:'数据项不完整<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
        data = data.data;
        data["start_time"] = parseInt(new Date(data["start_time"]).getTime() / 1000);
        data["end_time"] = parseInt(new Date(data["end_time"]).getTime() / 1000);
        methods.ajax({
            url : "/Marketing/Index/update",
            data : data,
            callback : function(data){
                new Inform({title:'通知',content:'修改成功'}).alert(function(){
                    window.location.href = "/Application/Marketing/View/Index/list.html";
                })
            }
        })
    })
});
