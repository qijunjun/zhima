/**
 * Created by apple on 16/3/12.
 */
var isImgChange = false;

var EventBind = function () {
    if(this instanceof EventBind) {
        this.operation = $(".operation");
        this.deleteData = $(".delete");
        this.auditData = $(".audit");
        this.portrait = $(".portrait");
        this.cout = $(".cout");
    } else {
        return new EventBind();
    }
};
// EventBind.prototype = {
//     check: function (_object) {
//         if (_object == "undefined") {
//             throw expertSystem Error("并没有此操作");
//         }
//     },
//     opera: function () {
//         this.check(this.operation);
//         this.operation.click(function () {
//             var src = "../../" + e.currentTarget.dataset.list + "/" + e.currentTarget.dataset.operation + ".html?id=" + e.currentTarget.dataset.id;
//             $(window.parent.document.getElementById('article')).attr('src', src);
//         });
//     },
//     delete: function () {
//         this.check(this.deleteData);
//         return this.deleteData;
//     },
//     audit: function () {
//         this.check(this.auditData);
//         this.auditData.click(function () {
//             customPop("认证确认", "tip");
//         });
//     },
//     port: function () {
//         this.check(this.portrait);
//         this.portrait.click(function () {
//             customPop("图片上传", "operation", "middleUpload");
//         });
//     },
//     out: function () {
//         this.check(this.cout);
//         this.cout.click(function () {
//             customPop("导出", "tip");
//         });
//     },
//     all: function () {
//         this.opera();
//         this.delete();
//         this.audit();
//         this.port();
//         this.out();
//     }
// };

var customPop = function(title, sectionClass, middleClass, id, imgSrc, customWord) {
    isImgChange = false;
    var object = {
        section: $("<section></section>"),
        divTop: $("<div></div>").addClass("top"),
        divMiddle: $("<div></div>").addClass("middle"),
        divDown: $("<div></div>").addClass("down")
    };
    var popMsg = $("<div></div>").addClass("popMsg");
    var span, div,body;
    middleClass = middleClass || "";
    object.section.addClass(sectionClass);
    span = $("<span></span>").html(title);
    div = $("<div></div>").addClass("img").attr("name", "popOut");
    body=$("body");
    object.divTop.append(span).append(div);
    object.divMiddle.addClass(middleClass);
    middleDownSetting(object.divMiddle, object.divDown, title, id, imgSrc, customWord);
    body.append(popMsg.append(object.section.append(object.divTop).append(object.divMiddle).append(object.divDown)));
    if (imgSrc != undefined) {
        canvasShow(imgSrc);
    }
    closeMsg();
    var enterPress = function (event) {
        event.preventDefault();
        if (event.keyCode == 13) {
            var popMsg = $(".popMsg");
            popMsg.remove();
            $('body').unbind('keypress', enterPress);
        }
    };
};

var middleDownSetting = function (middle, down, title, id, imgSrc, customWord) {
    var i, j, attrs, number = 0;
    var object = [];
    switch (title) {
        case "提示" :
            middle.append($("<span></span>").html("操作已完成<br />").append($("<span></span>").addClass("red").html(" ")));
            down.append($("<p></p>").html("朕知道了").attr("onclick", "gotMsg();")).append($("<p></p>"));
            break;
        case "错误" :
            middle.append($("<span></span>").html("" + customWord + "<br />").append($("<span></span>").addClass("red").html(" ")));
            down.append($("<p></p>").html("朕知道了").attr("onclick", "gotErrorMsg();")).append($("<p></p>"));
            break;
        case "删除提示":
            middle.append($("<span></span>").html("是否确认要删除?<br />").append($("<span></span>").addClass("red").html("一旦删除将不可恢复")));
            down.append($("<p></p>").html("朕确认").attr("onclick", "removeList("+id+")")).append($("<p></p>").attr("name", "popOut").html("容我三思"));
            break;
        case "认证确认":
            middle.append($("<span></span>").html("是否确认要通过?<br />"));
            down.append($("<p></p>").html("朕确认")).append($("<p></p>").attr("name", "popOut").html("容我三思"));
            break;
        case "导出":
            middle.append($("<span></span>").html("请输入一个文件存码量:")).append($("<input />").attr("type", "text").attr("name", "coutNumber").attr("id", "coutNumber"));
            down.append($("<p></p>").html("朕确认").attr("onclick", "coutCode("+id+")")).append($("<p></p>").attr("name", "popOut").html("容我三思"));
            break;
        case "图片上传":
            //此处排序谨记...<A><B></B></A>,则需要先写A后写B
            attrs = {
                form: {
                    id: "imgUpload",
                    action: "/Home/File/upload?iframe_upload=getImgUpload",
                    method: "post",
                    target: "imgIframe",
                    enctype: "multipart/form-data"
                },
                input: {
                    id: "fileUpload",
                    name: id,
                    type: "file",
                    onchange: "canvasDisplay(this);"
                },
                iframe: {
                    id: "imgIframe",
                    name: "imgIframe",
                    class: "hidden"
                },
                canvas: {
                    id: "canvasDisplay",
                    width: 600,
                    height: 280
                }
            };
            var temp_jq, temp_i;
            for (i in attrs) {
                temp_i = i;
                i = i.replace(/\d+/g,'');
                object[number] = $("<" + i + "></" + i + ">");
                i = temp_i;
                for (j in attrs[i]) {
                    object[number].attr(j, attrs[i][j]);
                }
                if (i == "form") {
                    middle.append(object[number]);
                    temp_jq = object[number];
                } else {
                    temp_jq.append(object[number]);
                }
                number ++;
            }
            imgSrc == undefined ? down.append($("<p></p>").html("朕确认").attr("id", "confirm").attr("onclick", "imageUpload(0);")).append($("<p></p>").attr("name", "popOut").html("容我三思")) : down.append($("<p></p>").html("朕确认").attr("id", "confirm").attr("onclick", "imageUpload(1);")).append($("<p></p>").attr("name", "popOut").html("容我三思"));
            break;
        default:
            console.error("弹窗类型错误");
    }
};

var closeMsg = function() {
    var popMsg = $(".popMsg");

    var popOut = $(document.getElementsByName("popOut"));
    popOut.click(function () {
        popMsg.remove();
    });
};

var imageUpload = function (isImgExist) {
    $("#confirm").html("上传中");
    var imgUpload = $("#imgUpload");
    console.log(isImgChange, imgUpload);

    if (!isImgChange) {
        if (isImgExist != 0) {
            $(".popMsg").remove();
        } else {
            imgUpload.submit();
        }
    } else {
        imgUpload.submit();
    }
};

var canvasShow = function (imgSrc) {
    var canvas = document.getElementById("canvasDisplay");
    var ctx = canvas.getContext("2d");
    if (!canvas || !canvas.getContext) {
        alert("您的浏览器不支持本功能!请更换浏览器");
        window.location.reload();
        return ;
    }
    var size = {
        width: canvas.width,
        height: canvas.height
    };
    ctx.clearRect(0, 0, size.width, size.height);

    var image = new Image();

    image.src = imgSrc;
    image.onload = function () {
        var w, h, ratio;
        ratio = {
            image: image.width / image.height,
            canvas: size.width / size.height,
            width: size.width / image.width,
            height: size.height / image.height
        };
        if (ratio.image > ratio.canvas) {
            w = size.width;
            h = image.height * ratio.width;
            ctx.drawImage(image, 0, (size.height - h) * 0.5, w, h);
        } else {
            w = image.width * ratio.height;
            h = size.height;
            ctx.drawImage(image, (size.width - w) * 0.5, 0, w, h);
        }
    };
};
var canvasDisplay = function(file) {
    var type = /.*\.(.*)$/.exec(file.value)[1];
    if (type != "png" && type != "jpg" && type != "jpeg") {
        alert("您上传的图片格式不为png、jpg、jpeg!请重新上传");
        return ;
    }
    var canvas = document.getElementById("canvasDisplay");
    var ctx = canvas.getContext("2d");
    if (!canvas || !canvas.getContext) {
        alert("您的浏览器不支持本功能!请更换浏览器");
        window.location.reload();
        return ;
    }
    var size = {
        width: canvas.width,
        height: canvas.height
    };
    ctx.clearRect(0, 0, size.width, size.height);

    var image = new Image();

    if (file.files && file.files[0]) {
        image.src = window.URL.createObjectURL(file.files[0]);
        image.onload = function () {
            var w, h, ratio;
            ratio = {
                image: image.width / image.height,
                canvas: size.width / size.height,
                width: size.width / image.width,
                height: size.height / image.height
            };
            if (ratio.image > ratio.canvas) {
                w = size.width;
                h = image.height * ratio.width;
                ctx.drawImage(image, 0, (size.height - h) * 0.5, w, h);
            } else {
                w = image.width * ratio.height;
                h = size.height;
                ctx.drawImage(image, (size.width - w) * 0.5, 0, w, h);
            }
            isImgChange = true;
            clipImage(ctx);
        };
    } else {
        console.error("文件读取失败!");
    }
};

var clipImage = function(ctx) {
    
};

var getSvg = function () {
    return "<svg width=\"14px\" height=\"14px\" viewBox=\"0 0 18 18\"><g stroke=\"none\" stroke-width=\"1\"><g transform=\"translate(-1376.000000, -678.000000)\" fill=\"#000000\"><g transform=\"translate(334.000000, 486.000000)\"><g transform=\"translate(0.000000, 86.000000)\"><g transform=\"translate(1.000000, 64.000000)\"><g transform=\"translate(1050.056349, 51.056349) rotate(-315.000000) translate(-1050.056349, -51.056349) translate(1039.556349, 40.556349)\"><rect x=\"0\" y=\"8.59090909\" width=\"21\" height=\"3.81818182\"></rect><rect transform=\"translate(10.500000, 10.500000) rotate(-270.000000) translate(-10.500000, -10.500000) \" x=\"0\" y=\"8.59090909\" width=\"21\" height=\"3.81818182\"></rect></g></g></g></g></g></g></svg>"
};