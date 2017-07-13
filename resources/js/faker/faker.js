/**
 * Created by apple on 16/5/7.
 */
$(document).ready(function () {
    $("#faker").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/Code/Api/fake",
        identifier: "id",
        responseHandler: function (response)
        {
            var rows;
            rows = {
                total: response.result.length,
                current: 1,
                rows: response.result
            };
            response.result = rows;
            return response;
        }
    });
    methods.addEvent(methods.getId('export'),'click',function(){
        // var startDate = $("#startDate").val();
        // var endDate = $("#endDate").val();
        // var start = parseInt(new Date(startDate).getTime() / 1000);
        // var end = parseInt(new Date(endDate).getTime() / 1000);
        // if(start > end){
        //     new Inform({title:'通知',content:'起始时间不能大于终止时间！'}).alert();
        //     return ;
        // }else{
        //     window.location.href="/Exportexcel/Exportexcel/index/name/fakeinfo/startdate/"+startDate+ "/enddate/" + endDate;
        // }
        window.location.href="/Exportexcel/Exportexcel/index/name/fakeinfo";
    })
});