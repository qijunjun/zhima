var inheritObject = function (o) {
    function F() {}
    F.prototype = o;
    return new F();
};
var inheritPrototype = function (superClass, subClass) {
    var p = inheritObject(superClass.prototype);
    p.constructor = subClass;
    subClass.prototype = p;
};

/**
 * 绑定弹窗事件类
 * @param text 内容对象{title: xxx, content: xxx}
 * @constructor 指向自己
 */
var EventBind = function (text) {
    if (this instanceof EventBind) {
        this.constructor = this;
        this.text = text;
        //让构造器指向自己
        //创建弹窗消息
        this.popMsg = document.createElement("DIV");
        this.popMsg.className = 'popMsg';
        this.section = document.createElement("SECTION");
        this.top = document.createElement("DIV");
        this.top.className = 'top';
        this.middle = document.createElement("DIV");
        this.middle.className = 'middle';
        this.down = document.createElement("DIV");
        this.down.className = 'down';
    } else {
        return new EventBind(text);
    }
};
/**
 * 事件绑定类的原型方法
 * @type {{
  * initTip: EventBind.initTip,     初始化消息弹窗
  * delete: EventBind.delete,       删除事件类
  * addEvent: EventBind.addEvent,   绑定事件重写,增强兼容性
  * getEvent: EventBind.getEvent,   获取事件重写,增强兼容性
  * getTarget: EventBind.getTarget, 获取目标事件重写,增强兼容性
  * insert: EventBind.insert,       appendChild重写,实现联动
  * showMsg: EventBind.showMsg      绑定弹窗到指定位置
  * }}
 */
EventBind.prototype = {
    init: function () {
        //添加top位置的文字以及关闭按钮
        var span = document.createElement('SPAN');
        span.innerHTML = this.text.title;
        var img = document.createElement('DIV');
        img.className = 'img';
        img.setAttribute('name', 'popOut');
        this.top.innerHTML = null;
        this.insert(this.top, span).insert(this.top, img);

        //添加down位置的文字以及关闭文字
        var p = document.createElement('P');
        p.innerHTML = "确认";
        p.setAttribute('name', 'confirm');
        var pClose = document.createElement('P');
        pClose.setAttribute('name', 'popOut');
        pClose.innerHTML = "取消";
        this.down.innerHTML = null;
        this.insert(this.down, p).insert(this.down, pClose);
    },
    addEvent: methods.addEvent,
    removeEvent: methods.removeEvent,
    getEvent: methods.getEvent,
    getTarget: methods.getTarget,
    insert: function (superClass, subClass) {
        superClass.appendChild(subClass);
        return this;
    },
    showMsg: function (target) {
        try {
            this.insert(this.popMsg, this.section)
                .insert(this.section, this.top)
                .insert(this.section, this.middle)
                .insert(this.section, this.down);
            this.insert(target, this.popMsg);
        } catch (e){console.log(e);}
    }
};

/**
 * 删除事件绑定
 * @param text 内容对象{title: xxx, content: xxx}
 * @constructor EventBind
 */
var Delete = function (text) {
    this.constructor = EventBind;
    EventBind.call(this, text);
    this.section.className = 'tip';
    this.init(this.text);
};
inheritPrototype(EventBind, Delete);
Delete.prototype.alert = function (id, url) {
    //缓存id与url
    this.id = id;
    this.url = url;

    /**
     * 给middle显示提示文字
     * @span 为填充的文字内容的dom类型
     * 需要给this.middle的html清空
     * 然后再调用insert方法把@span填充到this.middle里
     */
    var span = document.createElement("SPAN");
    span.innerHTML = this.text.content;
    this.middle.innerHTML = null;
    this.insert(this.middle, span);

    /**
     * 调用showMsg方法
     * @target 要把message弹窗在body里显示
     */
    this.showMsg(document.getElementsByTagName('body')[0]);

    var that = this;
    //冒泡事件绑定,判断点击的目标是否name为confirm,如果是则调用删除方法。
    this.addEvent(this.popMsg, 'click', function (event) {
        event = that.getEvent(event);
        var target = that.getTarget(event);
        try {
            switch (target.getAttribute('name')) {
                case 'popOut':
                    that.popMsg.parentNode.removeChild(that.popMsg);
                    break;
                case 'confirm':
                    that.popMsg.parentNode.removeChild(that.popMsg);
                    that.removeList();
                    break;
            }
            that.removeEvent(that.popMsg, 'click');
        } catch (e) {console.log(e);}
    });
};
Delete.prototype.removeList = function () {
    console.log(this.id);
    /**
     * ajax是是用的jQuery提供的ajax方法
     * 这里data: {id: this.id}是错误的写法,这里的this是指向了$也就是jQuery所以不包含id和url,所以这里应该先用that来缓存起来Delete对象的this,然后用that来负责传递
     */
    var delete_this = this;
    $.ajax({
        url: delete_this.url,
        method: "post",
        dataType: "json",
        data: {
            id: delete_this.id
        },
        success: function (data) {
            if (data.code === "001") {
                $("#" + delete_this.id).remove();
                window.location.reload();
            } else {
                new Inform({title: '错误', content: data.msg}).alert();
                console.error(data.code, data.msg);
            }
        },
        error: function (err) {
            new Inform({title: '错误', content: '网络错误<br />请稍后重试'}).alert();
            console.error(err);
        }
    });
};

/**
 * 弹窗事件绑定
 * @param text 对象类型{title:xxx, content:xxx}
 * @constructor 指向EventBind
 */
var Inform = function (text) {
    this.constructor = EventBind;
    EventBind.call(this, text);
    this.section.className = 'tip';
    this.init(this.text);
};
inheritPrototype(EventBind, Inform);
Inform.prototype.alert = function (callback) {
    //缓存callback
    this.callback = callback || function () {};

    /**
     * 给middle显示提示文字
     * @span 为填充的文字内容的dom类型
     * 需要给this.middle的html清空
     * 然后再调用insert方法把@span填充到this.middle里
     */
    var span = document.createElement("SPAN");
    span.innerHTML = this.text.content;
    this.middle.innerHTML = null;
    this.insert(this.middle, span);

    /**
     * 调用showMsg方法
     * @target 要把message弹窗在body里显示
     */
    this.showMsg(document.getElementsByTagName('body')[0]);

    var enterPress = function (event) {
        event.preventDefault();
        if (event.keyCode == 13) {
            that.popMsg.parentNode.removeChild(that.popMsg);
            $('body').unbind('keypress', enterPress);
            that.callback();
        }
    };
    // this.addEvent(document.getElementsByTagName('body')[0], 'keypress', enterPress);
    $('body').keypress(enterPress);

    var that = this;
    //冒泡事件绑定,判断点击的目标是否name为confirm,如果是则调用删除方法。
    this.addEvent(this.popMsg, 'click', function (event) {
        event = that.getEvent(event);
        var target = that.getTarget(event);
        var body = $('body');
        try {
            switch (target.getAttribute('name')) {
                case 'popOut':
                    that.popMsg.parentNode.removeChild(that.popMsg);
                    body.unbind('keypress', enterPress);
                    break;
                case 'confirm':
                    that.popMsg.parentNode.removeChild(that.popMsg);
                    body.unbind('keypress', enterPress);
                    that.callback();
                    break;
            }
            that.removeEvent(that.popMsg, 'click');
        } catch (e) {console.log(e);}
    });
};

/**
 * 图片上传事件绑定
 * @param text 对象类型{title:xxx, name:xxx}
 * @param imgSrc 图片的src
 * @constructor 指向父类EventBind
 */
var ImgUpload = function (text, imgSrc) {
    this.constructor = EventBind;
    this.imgSrc = imgSrc || null;
    EventBind.call(this, text);
    this.section.className = 'operation';
    this.init(this.text);
};
inheritPrototype(EventBind, ImgUpload);
ImgUpload.prototype.alert = function () {
    //缓存this
    var that = this;

    //form提交图片
    var form = document.createElement('FORM');
    form.id = 'imgUpload';
    form.setAttribute('action', '/Home/File/upload?iframe_upload=getImgUpload');
    form.setAttribute('method', 'post');
    form.setAttribute('target', 'imgIframe');
    form.setAttribute('enctype', 'multipart/form-data');

    //input[type=file]加载本地图片
    var input = document.createElement('INPUT');
    input.id = 'fileUpload';
    input.setAttribute('name', this.text.name);
    input.setAttribute('type', 'file');

    //input绑定change事件,发生change时调用canvasDisplay方法
    this.addEvent(input, 'change', function (event) {
        that.canvasDisplay(this);
    });

    //通过iframe执行callback=>getImgUpload
    var iframe = document.createElement('IFRAME');
    iframe.id = 'imgIframe';
    iframe.setAttribute('name', 'imgIframe');
    iframe.className = 'hidden';

    //canvas显示图片
    var canvas = document.createElement('CANVAS');
    canvas.id = 'canvasDisplay';
    canvas.setAttribute('width', '600');
    canvas.setAttribute('height', '280');

    /**
     * 给middle显示图片
     * 需要给this.middle的html清空
     * 然后再调用insert方法把图片填充到this.middle里
     */
    this.middle.className += " middleUpload";
    this.middle.innerHTML = null;
    this.insert(form, input).insert(form, iframe).insert(form, canvas);
    this.insert(this.middle, form);

    /**
     * 调用showMsg方法
     * @target 要把message弹窗在body里显示
     */
    this.showMsg(document.getElementsByTagName('body')[0]);

    /**
     * 如果imgSrc没有的话则不执行显示图片的方法,反之则执行显示图片
     */
    if (this.imgSrc) {
        this.canvasShow(this.imgSrc);
    }

    //冒泡事件绑定,判断点击的目标是否name为confirm,如果是则调用删除方法。
    this.addEvent(this.popMsg, 'click', function (event) {
        event = that.getEvent(event);
        var target = that.getTarget(event);
        try {
            switch (target.getAttribute('name')) {
                case 'popOut':
                    that.popMsg.parentNode.removeChild(that.popMsg);
                    break;
                case 'confirm':
                    document.getElementById('imgUpload').submit();
            }
            that.removeEvent(that.popMsg, 'click');
        } catch (e) {console.log(e);}
    });
};
ImgUpload.prototype.canvasShow = function (imgSrc) {
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
        console.log(w+"-"+h+"-"+ratio.image);
    };
};
ImgUpload.prototype.canvasDisplay = function (file) {
    //缓存this
    var that = this;

    var type = /.*\.(.*)$/.exec(file.value)[1].toLowerCase();
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
        if (file.files[0].size > 2097152) {
            new Inform({title: '警告', content: '<span class="red">图片大小不能超过2M!</span>'}).alert(function () {
                $('.popMsg').remove();
            });
            return ;
        }
        var bili = image.width / image.height;
        if(  bili != 16/9){
            new Inform({title: '警告', content: '上传图片宽高比必须为16:9!<br/><span class="red">请检查！</span>'}).alert(function () {
                $('.popMsg').remove();
            });
            return ;
        }
        image.src = window.URL.createObjectURL(file.files[0]);
        console.log(file.files[0]);
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
            that.clipImage(ctx);
            console.log(w+"-"+h+"-"+ratio.image);
        };
    } else {
        console.error("文件读取失败!");
    }
};
ImgUpload.prototype.clipImage = function (ctx) {

};