/**
 * Created by asus on 2016/7/11.
 */
// window.onload = function() {
//     $(".stars").children("span").on("click",function(e) {
//         console.log($(this).index());
//         var value = $(this).index();
//         var $stars = $(this).parent().children();
//         $stars.removeClass("star-on").removeClass("star-off").addClass("star-off");
//         for(var i = 1; i <= value ;i++){
//             $($stars[i]).removeClass("star-off").addClass("star-on");
//         }
//         $(this).siblings().first().attr("value",value);
//     });
//     wordLimit("comment-content","words-count",150);
//     $("#comment-submit").on("click",submitComment);
//     $(".backArrow").on("click",function(e) {
//         window.location.href = localStorage.getItem("lastPage");
//         localStorage.removeItem("lastPage");
//     });
// };
//
// var json = [];
//
// function submitComment() {
//     json = [];
//     var input = document.getElementsByTagName("input");
//     var text = document.getElementsByTagName("textarea");
//     var msg = "";
//     for(var i = 0; i < input.length; i++) {
//         if(input[i].value != undefined && input[i].value != "")
//             json[input[i].name] = input[i].value;
//         else {
//             msg += $(input[i]).parent().siblings().text();
//
//         }
//     }
//     for(var i = 0; i < text.length; i++) {
//         if(text[i].value != undefined && text[i].value != "")
//         json[text[i].name] = text[i].value;
//     }
//     console.log(json);
//     if(json.length == 0) {
//         alert("至少评论或点一下星呗~");
//         return;
//     }
//     if(json != undefined && json != "")
//             postComment(json);
// }
//
// function wordLimit(area,count,limit) {
//     var content = document.getElementById(area);
//     var word = document.getElementById(count);
//     $(content).on("input change",function(){
//         var wordAmount = this.value.length;
//         $(word).children().last().text(wordAmount + "/" + limit);
//         if(wordAmount >= limit){
//             $(word).addClass("overLimit");
//             $(word).children().last().text(limit + "/" + limit);
//             this.value = this.value.slice(0,limit);
//         }
//         else
//             $(word).removeClass("overLimit");
//     })
// }
//
// function postComment(data) {
//     methods.ajax({
//         url:"",
//         data: data,
//         callback: function(result) {
//         }
//     })
// }
$(document).ready(function(){
    $(".stars").children("span").on("click",function(e) {
        console.log($(this).index());
        var value = $(this).index();
        var stars = $(this).parent().children();
        stars.removeClass("star-on").removeClass("star-off").addClass("star-off");
        for(var i = 1; i <= value ;i++){
            $(stars[i]).removeClass("star-off").addClass("star-on");
        }
        $(this).siblings().first().attr("value",value);
    });
    wordLimit("comment-content","words-count",150);
    function wordLimit(area,count,limit) {
        var content = document.getElementById(area);
        var word = document.getElementById(count);
        $(content).on("input change",function(){
            var wordAmount = this.value.length;
            $(word).children().last().text(wordAmount + "/" + limit);
            if(wordAmount >= limit){
                $(word).addClass("overLimit");
                $(word).children().last().text(limit + "/" + limit);
                this.value = this.value.slice(0,limit);
            }
            else
                $(word).removeClass("overLimit");
        })
    }
    $("#comment-submit").click(function () {
        var score = $("#score-1").val();
        var content = $("#comment-content").val();
        $.ajax({
            url: "/Reviews/Reviews/updateReviews",
            method: "post",
            dataType: "json",
            data: {
                reviews: content,
                qualitygoods: score
            },
            success: function (data) {
                if (data.code === "001") {
                    new Inform({title:'通知',content:'评论成功，感谢您对我们工作的支持！'}).alert(function(){
                        window.history.back();
                    });
                } else {
                    new Inform({title:'通知',content:data.message}).alert();
                    console.error(data.code, data.message);
                }
            },
            error: function (err) {
                console.error(err);
            }
        });
    });
});
var getParameterByName = function (name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};