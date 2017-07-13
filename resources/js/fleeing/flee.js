/**
 * Created by apple on 16/5/9.
 */
$(document).ready(function () {
    $("#flee").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/Fleeing/Fleeing/get",
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
            product_image: function (column, row) {
                return "<img src='" + row.product_image + "' />";
            }
            // delete: function (column, row)
            // {
            //     return "<div class=\"delete\" data-id='" + row.id + "'>" + getSvg() + "</div>"
            // }
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
        $("#searchResult").click(function(){
            var startDate = $("#startDate").val();
            var endDate = $("#endDate").val();
            var start = parseInt(new Date(startDate).getTime() / 1000);
            var end = parseInt(new Date(endDate).getTime() / 1000);
            if(start > end){
                new Inform({title:'通知',content:'起始时间不能大于终止时间！'}).alert();
                return ;
            }
            $("#switchFlee").css('display','table');
            $("#flee-footer").css('display','none');
            $("#flee").css('display','none');
            $("#switchFlee").bootgrid({
                ajax: true,
                ajaxSettings: {
                    dataType: "json"
                },
                url: "/Fleeing/Fleeing/Searchget",
                post:{
                    startdate:startDate,
                    enddate:endDate
                },
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
                        return "<img src='" + row.productimage + "' />";
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
            });
        });
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
        //     window.location.href="/Exportexcel/Exportexcel/index/name/fleeinginfo/startdate/"+startDate+ "/enddate/" + endDate;
        // }
        window.location.href="/Exportexcel/Exportexcel/index/name/fleeinginfo";
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
    //         $("#flee tbody").remove();
    //         methods.ajax({
    //             url: "/Exportexcel/Exportexcel/index/name/fleeinginfo/startdate/"+startDate+ "/enddate/" + endDate+"/sign/"+1,
    //             callback: function(data){
    //                 data = data.result;
    //                 var flee = methods.getId('flee');
    //                 for(var i=0;i<data.length;i++){
    //                     var time = new Date(data[i].create_time+"000"-0);
    //                     var creat_time = time.getFullYear() + "年" + ((time.getMonth() + 1) < 10 ? ('0' + (time.getMonth() + 1)) : (time.getMonth() + 1)) + "月" + (time.getDate() < 10 ? ('0' + time.getDate()) : time.getDate())+"日"+(time.getHours() < 10 ? ('0' + time.getHours()) : time.getHours())+":"+(time.getMinutes() < 10 ? ('0' + time.getMinutes()) : time.getMinutes())+":"+(time.getSeconds() < 10 ? ('0' + time.getSeconds()) : time.getSeconds());
    //                     flee.innerHTML += "<tr data-row-id='"+i+"'><td class='text-left'><img src='"+data[i].product_image+"'></td><td class='text-left'>"+data[i].product_name+"</td><td class='text-left'>"+data[i].b+"</td><td class='text-left'>"+data[i].agent+"</td><td class='text-left'>"+data[i].ipaddr+"</td><td class='text-left'>"+creat_time+"</td><td class='text-left'>"+data[i].beyondareas_id+"</td></tr>";
    //                 }
    //             }
    //         })
    //     }
    // });
});
// var removeList = function (id) {
//     $.ajax({
//         url : "#"+id,
//         method : "post",
//         dataType : "json",
//         data: {
//             id: id
//         },
//         success : function(data){
//             if (data.code === "001"){
//                 $("#" + id).remove();
//                 $(".popMsg").remove();
//             }else{
//                 console.error(data.code,data.message);
//             }
//         },
//         error: function(err){
//             console.error(err);
//         }
//     });
// };