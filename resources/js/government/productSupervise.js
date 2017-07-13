/**
 * Created by 123 on 2016/8/17.
 */
$(document).ready(function(){
    $("#product").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/Government/Supervise/showProduct",
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
    //列出所有的企业
    // var companyName = document.getElementById('companyName');
    //  companyName.innerHTML = null;
    //  methods.ajax({
    //      url : "/Government/Supervise/getCompanyname",
    //      callback:function(data){
    //          data =data.result;
    //          var str = "<option value='0' disabled='disabled' selected='selected'>--请选择名称--</option>";
    //          for(var i=0;i<data.length;i++){
    //              str += "<option value='"+data[i].companyid+"'>"+data[i].companyname+"</option>";
    //          }
    //          str +="<option value='1'>显示全部</option>";
    //          companyName.innerHTML = str;
    //      }
    //  });
    // methods.addEvent(companyName,'change',function(){
    //     $("#product tbody").remove();
    //     var str = "";
    //     var tr;
    //     var td =[];
    //     if(companyName.value === "1"){
    //         $.ajax({
    //             url : "/Government/Supervise/showProduct",
    //             method:"post",
    //             data:{},
    //             dataType:"json",
    //             success:function(data){
    //                 if(data.code === "001"){
    //                     data = data.result;
    //                     for(var i = 0 ;i<data.length;i++){
    //                         td[0] = "<td>"+data[i].companyname+"</td>";
    //                         td[1] = "<td><img src='"+data[i].productimage+"' /></td>";
    //                         td[2] = "<td>"+data[i].productname+"</td>";
    //                         td[3] = "<td>"+data[i].guige+"</td>";
    //                         td[4] = "<td>"+data[i].price+"</td>";
    //                         tr = $("<tr></tr>");
    //                         tr.append(td[0]).append(td[1]).append(td[2]).append(td[3]).append(td[4]);
    //                         $("#product").append(tr);
    //                     }
    //                 }
    //             },
    //             error:function(err){
    //                 console.log(err);
    //             }
    //         });
    //     }else{
    //         $.ajax({
    //             url : "/Government/Supervise/getComProduct",
    //             method:"post",
    //             data:{companyid:companyName.value},
    //             dataType:"json",
    //             success:function(data){
    //                 if(data.code === "001"){
    //                     data = data.result;
    //                     for(var i = 0 ;i<data.length;i++){
    //                         td[0] = "<td>"+data[i].companyname+"</td>";
    //                         td[1] = "<td><img src='"+data[i].productimage+"' /></td>";
    //                         td[2] = "<td>"+data[i].productname+"</td>";
    //                         td[3] = "<td>"+data[i].guige+"</td>";
    //                         td[4] = "<td>"+data[i].price+"</td>";
    //                         tr = $("<tr></tr>");
    //                         tr.append(td[0]).append(td[1]).append(td[2]).append(td[3]).append(td[4]);
    //                         $("#product").append(tr);
    //                     }
    //                 }else{
    //                     td[0] = "<td></td>";
    //                     td[1] = "<td></td>";
    //                     td[2] = "<td>未查到数据</td>";
    //                     td[3] = "<td></td>";
    //                     td[4] = "<td></td>";
    //                     tr = $("<tr></tr>");
    //                     tr.append(td[0]).append(td[1]).append(td[2]).append(td[3]).append(td[4]);
    //                     $("#product").append(tr);
    //                 }
    //             },
    //             error:function(err){
    //                 console.log(err);
    //             }
    //         });
    //     }
    // })
});