/**
 * Created by 123 on 2016/7/15.
 */
$(document).ready(function () {
    var img = "<img style='width: 20px; height:20px;' src='/resources/images/star-on-big.png'>";
    var arr=[img,img+img,img+img+img,img+img+img+img,img+img+img+img+img];
    $("#evaluateManage").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/Code/Api/fetchReviews",
        identifier: "id",
        responseHandler: function (response) {
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
            productImage: function(column, row) {
                return "<img class='magnifyImg' src='" + row.productimage + "' />";
            },
            quality_goods : function(column,row){
                return arr[row.quality_goods-1];
            },
            delete: function (column, row)
            {
                return "<div class=\"delete\" data-id='" + row.id + "'>" + methods.getSvg() + "</div>"
            }
        }
    }).on("loaded.rs.jquery.bootgrid", function()
    {
        $(".delete").click(function () {
            var id = $(this).data("id");
            $(this).parent().parent().attr("id", id);
            new Delete({title:'删除提示',content:'是否确认删除?<br /><span class="red">一旦删除将不可恢复</span>'}).alert(id,'/Reviews/ReviewsManager/clearReviews');
        });
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