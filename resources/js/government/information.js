/**
 * Created by 123 on 2016/8/19.
 */
$(document).ready(function(){
    $("#Information").bootgrid({
        ajax:true,
        ajaxSetting:{
            dataType:"json"
        },
        url:"/Government/Supervise/showCompanyInfo",
        identifier:"id",
        responseHandler:function(response){
            var rows;
            rows = {
                total:response.result.length,
                current:1,
                rows : response.result
            };
            response.result = rows;
            str = rows.length;
            return response;
        },
        formatters: {
            introduction: function (column, row) {
                return "<div>"+row.introduction+"</div>"+"<p class='bcontent' style='display: none'>"+row.introduction+"</p>";
            }
        }
    }).on("loaded.rs.jquery.bootgrid", function() {
        $('.text-left>div').dotdotdot({
            ellipsis: '...',
            after: $('<a href="javascript:void(0)" class="showcontent" style="color:#299be4">查看全部<a>'),
            wrap: 'letter',
            height: 180,
            watch: true,
            fallbackToLetter: true,
            lastCharacter: {

                /*	Remove these characters from the end of the truncated text. */
                remove: [' ', ',', ';', '.', '!', '?'],

                /*	Don't add an ellipsis if this array contains
                 the last character of the truncated text. */
                noEllipsis: []
            }
        });
        $(".showcontent").click(function(){
            var body = $(document.getElementsByTagName('body'));
            var cover = $(document.getElementById('cover'));
            var content = cover.children('#cover-content');
            var txt = $(this).parent().siblings().html();
            $(".pcontent").html(txt);
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