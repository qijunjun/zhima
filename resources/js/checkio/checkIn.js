/**
 * Created by Lenovo on 2016/4/30.
 */

$(document).ready(function () {
    $("#checkIn").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/CheckIO/Checkin/listall",
        identifier: "_id",
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
        },
        formatters: {
            delete: function (column, row)
            {
                return "<div class=\"delete\" data-id='" + row._id + "'>" + methods.getSvg() + "</div>"
            }
        }
    }).on("loaded.rs.jquery.bootgrid", function()
    {
        $('.delete').click(function(){
            var id = $(this).data("id");
            $(this).parent().parent().attr("id",id);
            new Delete({title:'删除提示',content:'是否确认删除?<br /><span class="red">一旦删除将不可恢复</span>'}).alert(id,'/CheckIO/Checkin/delete');
        });
    });
    methods.addEvent(methods.getId('export'),'click',function(){
        window.location.href="/Exportexcel/Exportexcel/index/name/checkininfo";

    });
    methods.addEvent(methods.getId('searchResult'),'click',function(){
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var start = parseInt(new Date(startDate).getTime() / 1000);
        var end = parseInt(new Date(endDate).getTime() / 1000);
        if(start > end){
            new Inform({title:'通知',content:'起始时间不能大于终止时间！'}).alert();
            return ;
        }else{
            $("#switchcheckIn").css('display','table');
            $("#checkIn-footer").css('display','none');
            $("#checkIn").css('display','none');
            $("#switchcheckIn").bootgrid({
                ajax: true,
                ajaxSettings: {
                    dataType: "json"
                },
                url: "/CheckIO/Checkin/SearchCheckin",
                post:{
                    startdate:startDate,
                    enddate:endDate
                },
                identifier: "_id",
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
                },
                formatters: {
                    delete: function (column, row)
                    {
                        return "<div class=\"delete\" data-id='" + row._id + "'>" + methods.getSvg() + "</div>"
                    }
                }
            }).on("loaded.rs.jquery.bootgrid", function()
            {
                $('.delete').click(function(){
                    var id = $(this).data("id");
                    $(this).parent().parent().attr("id",id);
                    new Delete({title:'删除提示',content:'是否确认删除?<br /><span class="red">一旦删除将不可恢复</span>'}).alert(id,'/CheckIO/Checkin/delete');
                });
            });
        }
    });
    // methods.addEvent(methods.getId('searchResult'),'click',function(){
    //     var startDate = $("#startDate").val();
    //     var endDate = $("#endDate").val();
    //     var start = parseInt(new Date(startDate).getTime() / 1000);
    //     var end = parseInt(new Date(endDate).getTime() / 1000);
    //     if(start > end){
    //         new Inform({title:'通知',content:'起始时间不能大于终止时间！'}).alert();
    //         return ;
    //     }else{
    //         $("#checkIn tbody").empty();
    //         $.ajax({
    //             url : "/Exportexcel/Exportexcel/index/name/checkininfo/startdate/"+startDate+ "/enddate/" + endDate+"/sign/"+1,
    //             method:"post",
    //             data:{},
    //             dataType:"json",
    //             success:function(data){
    //                 if(data.code === "001"){
    //                     var str = "";
    //                     var tr;
    //                     var td =[];
    //                     data = data.result;
    //                     for(var i = 0 ;i<data.length;i++){
    //                         var time = new Date(data[i].create_time+"000"-0);
    //                         var creatTime = time.getFullYear() + "年" + ((time.getMonth() + 1) < 10 ? ('0' + (time.getMonth() + 1)) : (time.getMonth() + 1)) + "月" + (time.getDate() < 10 ? ('0' + time.getDate()) : time.getDate())+"日"+(time.getHours() < 10 ? ('0' + time.getHours()) : time.getHours())+":"+(time.getMinutes() < 10 ? ('0' + time.getMinutes()) : time.getMinutes())+":"+(time.getSeconds() < 10 ? ('0' + time.getSeconds()) : time.getSeconds());
    //                         td[0] = "<td>"+data[i].product_name+"</td>";
    //                         td[1] = "<td>"+data[i].spec+"</td>";
    //                         td[2] = "<td>"+data[i].p+"</td>";
    //                         td[3] = "<td>"+data[i].destination+"</td>";
    //                         td[4] = "<td>"+creatTime+"</td>";
    //                         td[5] = "<td><div class='delete' data-id='"+data[i]._id+"'>"+methods.getSvg()+"</div></td>";
    //                         tr = $("<tr></tr>");
    //                         tr.append(td[0]).append(td[1]).append(td[2]).append(td[3]).append(td[4]).append(td[5])
    //                         $("#checkIn").append(tr);
    //                     }
    //                 }
    //             },
    //             error:function(err){
    //                 console.log(err);
    //             }
    //         })
    //     }
    // })
});