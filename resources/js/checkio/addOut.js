/**
 * Created by Lenovo on 2016/5/1.
 */
$(document).ready(function(){
   //缓存能够用到的id或class
    var select,div,label,option,i, j;
    option = [];
    select = document.getElementById('warehouseid');
    select.innerHTML = null;
    var str = "";
    methods.ajax({
        url : "/CheckIO/Checkout/getWhList",
        callback : function(data){
            data = data.result;
            // select.append($("<option></option>").attr({disabled:"disabled",value:"0"}).html("--请选择仓库--"));
            str = "<option value='0' disabled='disabled'>--请选择仓库--</option>";
            for(i=0; i<data.length;i++){
                // option[i] = $("<option></option>").attr("value","w"+data[i].id).html(data[i].warehouse_name);
                str += "<option value='w" + data[i].id + "'>" + data[i].warehouse_name + "</option>";
                // select.append(option[i]);
            }
            select.innerHTML += str;
        }
    });
    methods.ajax({
        url : "/CheckIO/Checkout/getAGList",
        callback : function(data){
            data = data.result;
            // select.append($("<option></option>").attr({disabled:"disabled",value:"0"}).html("--请选择经销商--"));
            str = "<option value='0' disabled='disabled'>--请选择经销商--</option>";
            for(i=0; i<data.length;i++){
                // option[j] = $("<option></option>").attr("value","a"+data[j].id).html(data[j].agent_name);
                str += "<option value='a" + data[i].id + "'>" + data[i].agent_name + "</option>";
                // select.append(option[j]);
            }
            select.innerHTML += str;
        }
    });
   methods.addEvent(document.getElementById('submit'),'click',function(){
       var data = new Data();
       if(!data.getInput()){
           new Inform({title:'通知',content:'数据项不完整<br /><span class="red">请检查</span>'}).alert();
           return ;
       }
       data.common({warehouseid : $("#warehouseid").val()});
       methods.ajax({
           url : "/CheckIO/Checkout/add",
           data : data.data,
           callback : function(){
               new Inform({title:'通知',content:'新增出库成功'}).alert(function(){
                   window.location.href = "/Application/CheckIO/View/Index/checkOut.html";
               })
           }
       })
   })
});