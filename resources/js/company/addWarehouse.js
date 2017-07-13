/**
 * Created by apple on 16/4/16.
 */
$(document).ready(function(){
    methods.addEvent(document.getElementById('submitData'),'click',function(){
        var data = new Data();
        if(!data.getInput()){
            new Inform({title:'通知',content:'数据项不完整<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
        methods.ajax({
            url : "/Company/Warehouse/add",
            data : data.data,
            callback :function(){
                new Inform({title:'通知',content:'新增仓库成功'}).alert(function(){
                    window.location.href = "/Application/Company/View/Warehouse/warehouse.html";
                })
            }
        })
    })

});