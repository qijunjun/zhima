/**
 * Created by Lenovo on 2016/5/1.
 */

$(document).ready(function () {
    //缓存起来所有会使用到的class或id
    var select,option,i;
    option = [];
    select = $("#warehouseid");
    methods.ajax({
        url : "/CheckIO/Checkin/getWhList",
        callback : function(data){
            data = data.result;
            for(i=0;i<data.length;i++){
                option[i] = $("<option></option>").attr("value",data[i].id).html(data[i].warehouse_name);
                $("<select></select>").attr("name",data[i].warehouse_name);
                select.append(option[i]);
            }
        }
    });
    methods.addEvent(document.getElementById('submit'),'click',function(){
        var data = new Data();
        if(!data.getInput()){
            new Inform({title : '通知',content : '数据项不完整<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
        data.common({warehouseid:$("#warehouseid").val()});
        methods.ajax({
            url : "/CheckIO/Checkin/add",
            data : data.data,
            callback :function(){
                new Inform({title : '通知',content: '新增入库成功'}).alert(function(){
                    window.location.href = "/Application/CheckIO/View/Index/checkIn.html";
                })
            }
        })
    })
});