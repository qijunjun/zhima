/**
 * Created by 123 on 2016/8/19.
 */
$(document).ready(function () {
    $("#Process").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        post: {
            page: "all"
        },
        url: "/Government/Supervise/showProcess",
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
        },
        formatters: {
            productimage: function (column, row) {
                return "<img class='magnifyImg' src='" + row.productimage + "' />";
            },
            operator_image: function (column, row) {
                return "<img class='magnifyImg' src='" + row.operatorimage + "' />";
            },
            content_image: function (column, row) {
                var str = "";
                if (!(row.image_path instanceof Array))
                {
                    return "";
                }
                for(var k = 0; k < row.image_path.length; k++) {
                    str += "<img class='magnifyImg' src='" + row.image_path[k] + "' />";
                }
                return str;
            }
        }
    }).on("loaded.rs.jquery.bootgrid", function()
    {
        $(".magnifyImg").click(function(){
            var body = $(document.getElementsByTagName('body'));
            var cover = $(document.getElementById('cover'));
            var content = cover.children('#cover-content');
            var src = $(this).attr("src");
            $(".content").attr("src",src);
            cover.removeClass('hidden');
            cover.one('mouseover',function(e) {
                $(".enlarge").one('click',function (e) {
                    cover.addClass('hidden');
                    body.css('overflow','auto');
                });
            });
            content.on('mouseover',function (e) {
                    cover.unbind();
                })
                .on('mouseout',function (e) {
                    $(".enlarge").one('click',function (e) {
                        cover.addClass('hidden');
                        body.css('overflow','auto');
                    });
                });
            body.css('overflow','hidden');
            centerVertical();
        });
        function centerVertical() {
            var content = document.getElementById('cover-content');
            var heightVersion = document.documentElement.clientHeight;
            var heightLimit = heightVersion * 0.9;
            var heightBlock = parseInt(content.offsetHeight);
            var marginHeight = heightVersion - heightBlock;
            var halfMargin;
            if(heightBlock>heightLimit) {
                halfMargin = '5%';
            } else {
                halfMargin = Math.floor(marginHeight / 2) + 'px';
            }
            content.style.marginTop = halfMargin;
            content.style.marginBottom = halfMargin;
        }
        // $(function() {
        //     var bigImg = $(".bigImg");
        //     var popmask = $(".popmask");
        //     var popCnt = $(".popCnt");
        //     $('.magnifyImg').click(function(){
        //         var self = $(this);
        //         var src = self.attr("src");
        //         bigImg.attr("src", src);
        //         popmask.show(0);
        //         popCnt.fadeIn(200);
        //         $("body").addClass('hasPop');
        //
        //         bigImg.click(function() {
        //             $("body").removeClass('hasPop');
        //             popCnt.hide(0);
        //             popmask.hide(0);
        //         })
        //     })
        // });
   
    });
    // 列出所有的企业
    // var companyName = document.getElementById('companyName');
    // companyName.innerHTML = null;
    // methods.ajax({
    //     url : "/Government/Supervise/getCompanyname",
    //     callback:function(data){
    //         data =data.result;
    //         var str = "<option value='0' disabled='disabled' selected='selected'>--请选择名称--</option>";
    //         for(var i=0;i<data.length;i++){
    //             str += "<option value='"+data[i].companyid+"'>"+data[i].companyname+"</option>";
    //         }
    //         str +="<option value='1'>显示全部</option>";
    //         companyName.innerHTML = str;
    //     }
    // });
    // methods.addEvent(companyName,'change',function(){
    //     $("#Process tbody").remove();
    //     var str = "";
    //     var tr;
    //     var td =[];
    //     if(companyName.value === "1"){
    //         $.ajax({
    //             url : "/Government/Supervise/showProcess",
    //             method:"post",
    //             data:{page: "all"},
    //             dataType:"json",
    //             success:function(data){
    //                 if(data.code === "001"){
    //                     data = data.result;
    //                     for(var i = 0 ;i<data.length;i++){
    //                         var time = new Date(data[i].event_time+"000"-0);
    //                         var event_time = time.getFullYear() + "年" + ((time.getMonth() + 1) < 10 ? ('0' + (time.getMonth() + 1)) : (time.getMonth() + 1)) + "月" + (time.getDate() < 10 ? ('0' + time.getDate()) : time.getDate())+"日"+(time.getHours() < 10 ? ('0' + time.getHours()) : time.getHours())+":"+(time.getMinutes() < 10 ? ('0' + time.getMinutes()) : time.getMinutes())+":"+(time.getSeconds() < 10 ? ('0' + time.getSeconds()) : time.getSeconds());
    //                         var str = "";
    //                         for(var k = 0; k <data[i].image_path.length; k++) {
    //                             str += "<img class='magnifyImg' src='" + data[i].image_path[k] + "' />";
    //                         }
    //                         td[0] = "<td>"+data[i].companyname+"</td>";
    //                         td[1] = "<td><img src='"+data[i].productimage+"' /></td>";
    //                         td[2] = "<td>"+data[i].productname+"</td>";
    //                         td[3] = "<td>"+data[i].guige+"</td>";
    //                         td[4] = "<td>"+data[i].function_name+"</td>";
    //                         td[5] = "<td><img src='"+data[i].operator_image+"' /></td>";
    //                         td[6] = "<td>"+event_time+"</td>";
    //                         td[7] = "<td>"+data[i].userlocation+"</td>";
    //                         td[8] = "<td>"+str+"</td>";
    //                         td[9] = "<td>"+data[i].event_details+"</td>";
    //                         tr = $("<tr></tr>");
    //                         tr.append(td[0]).append(td[1]).append(td[2]).append(td[3]).append(td[4]).append(td[5]).append(td[6]).append(td[7]).append(td[8]).append(td[9]);
    //                         $("#Process").append(tr);
    //                     }
    //                 }
    //             },
    //             error:function(err){
    //                 console.log(err);
    //             }
    //         });
    //     }else{
    //         $.ajax({
    //             url : "/Government/Supervise/getComProcess",
    //             method:"post",
    //             data:{companyid:companyName.value},
    //             dataType:"json",
    //             success:function(data){
    //                 if(data.code === "001"){
    //                     data = data.result;
    //                     for(var i = 0 ;i<data.length;i++){
    //                         var time = new Date(data[i].event_time+"000"-0);
    //                         var event_time = time.getFullYear() + "年" + ((time.getMonth() + 1) < 10 ? ('0' + (time.getMonth() + 1)) : (time.getMonth() + 1)) + "月" + (time.getDate() < 10 ? ('0' + time.getDate()) : time.getDate())+"日"+(time.getHours() < 10 ? ('0' + time.getHours()) : time.getHours())+":"+(time.getMinutes() < 10 ? ('0' + time.getMinutes()) : time.getMinutes())+":"+(time.getSeconds() < 10 ? ('0' + time.getSeconds()) : time.getSeconds());
    //                         var str = "";
    //                         for(var k = 0; k <data[i].image_path.length; k++) {
    //                             str += "<img class='magnifyImg' src='" + data[i].image_path[k] + "' />";
    //                         }
    //                         td[0] = "<td>"+data[i].companyname+"</td>";
    //                         td[1] = "<td><img src='"+data[i].productimage+"' /></td>";
    //                         td[2] = "<td>"+data[i].productname+"</td>";
    //                         td[3] = "<td>"+data[i].guige+"</td>";
    //                         td[4] = "<td>"+data[i].function_name+"</td>";
    //                         td[5] = "<td><img src='"+data[i].operator_image+"' /></td>";
    //                         td[6] = "<td>"+event_time+"</td>";
    //                         td[7] = "<td>"+data[i].userlocation+"</td>";
    //                         td[8] = "<td>"+str+"</td>";
    //                         td[9] = "<td>"+data[i].event_details+"</td>";
    //                         tr = $("<tr></tr>");
    //                         tr.append(td[0]).append(td[1]).append(td[2]).append(td[3]).append(td[4]).append(td[5]).append(td[6]).append(td[7]).append(td[8]).append(td[9]);
    //                         $("#Process").append(tr);
    //                     }
    //                 }else{
    //                     for(var j=0;j<data.result.length;j++){
    //                         if(j=5){
    //                             td[5] = "<td>未查到数据</td>"
    //                         }
    //                         td[j]="<td></td>";
    //                     }
    //                     tr = $("<tr></tr>");
    //                     tr.append(td[j]);
    //                     $("#Process").append(tr);
    //                 }
    //             },
    //             error:function(err){
    //                 console.log(err);
    //             }
    //         });
    //     }
    // })
});
