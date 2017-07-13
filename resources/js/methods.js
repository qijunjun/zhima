var methods = {
    /**
     * 获取生产信息全部数据
     * @param url
     * @param target
     * @return 得到结果为给target中添加checkbox类型的生产信息
     */
    getProduce: function (url, target) {
        $.ajax({
            url: url,
            method: "post",
            dataType: "json",
            async: false,
            success: function (data) {
                console.log(data);
                if (data.code !== "001") {
                    new Inform({title: '错误', content: data.msg});
                    throw new Error(data.code + "," + data.msg);
                }
                var str = "";
                for (var i in data.result) {
                    str += "<div>" +
                        "<label class='checkboxLabel' for='produceInfo" + i + "'>" +
                        "<input type='checkbox' id='produceInfo" + i + "' name='produceInfo" + i + "' value='" + i + "' />" +
                        data.result[i] +
                        "</label>" +
                        "</div>"
                }
                target.innerHTML = str;
            },
            error: function (err) {
                new Inform({title: '错误', content: '网络错误'});
                console.error(err);
            }
        });
    },
    /**
     * 获取生产环节全部数据
     * @param url
     * @param target
     * @return 得到结果为给target中添加checkbox类型的生产环节
     */
    getProcess: function (url, target) {
        $.ajax({
            url: url,
            method: "post",
            dataType: "json",
            async: false,
            success: function (data) {
                console.log(data);
                if (data.code !== "001") {
                    new Inform({title: '错误', content: data.msg});
                    throw new Error(data.code + "," + data.msg);
                }
                var str = "";
                for (var i=0;i<data.result.length;i++) {
                    str += "<div>" +
                        "<label class='checkboxLabel' for='processInfo" + data.result[i].functionid + "'>" +
                        "<input type='checkbox' id='processInfo" + data.result[i].functionid + "' name='processInfo" + data.result[i].functionid + "' value='" + data.result[i].functionid + "' />" +
                        data.result[i].function_name +
                        "</label>" +
                        "</div>"
                }
                target.innerHTML = str;
            },
            error: function (err) {
                new Inform({title: '错误', content: '网络错误'});
                console.error(err);
            }
        });
    },

    /**
     * 通过id获取dom对象
     * @param id dom的id名
     */
    getId: function (id) {
        return document.getElementById(id);
    },

    /**
     * ajax数据
     * @param object{url,data,callback}
     */
    ajax: function (object) {
        $.ajax({
            url: object.url,
            method: 'post',
            dataType: 'json',
            data: object.data || {},
            success: function (data) {
                if (data.code !== '001') {
                    new Inform({title: '通知', content: data.message || data.msg || "错误"}).alert();
                    throw new Error(data.code + "," + data.message || data.msg || "未知错误");
                }
                object.callback(data);
            },
            error: function (err) {
                new Inform({title: '通知', content: '网络错误'}).alert();
                throw new Error(err || "未知错误");
            }
        });
    },

    /**
     * 添加事件监听的兼容模式
     * @param dom dom级对象
     * @param type 事件类型eg.click,change...
     * @param fn 回调函数
     */
    addEvent: function (dom, type, fn) {
        fn = fn || function () {};
        if (dom.addEventListener) {
            dom.addEventListener(type, fn, false);
        } else if (dom.attachEvent) {
            dom.attachEvent('on' + type, fn);
        } else {
            dom['on' + type] = fn;
        }
    },

    /**
     * 删除事件监听的兼容模式
     * !!!!!谨记!!!!!删除不可以删除用匿名函数创建的事件监听
     * @param dom dom级对象
     * @param type 事件类型eg.click,change...
     * @param fn 回调函数
     */
    removeEvent: function (dom, type, fn) {
        fn = fn || function () {};
        if (dom.removeEventListener) {
            dom.removeEventListener(type, fn, false);
        } else if (dom.detachEvent) {
            dom.detachEvent('on' + type, fn);
        } else {
            dom['on' + type] = null;
        }
    },

    /**
     * 获取event事件的兼容模式
     * @param event
     * @returns {*|Event}
     */
    getEvent: function (event) {
        return event || window.event;
    },

    /**
     * 获取target的兼容模式
     * @param event
     * @returns {EventTarget|Object}
     */
    getTarget: function (event) {
        return this.getEvent(event).target || this.getEvent(event).srcElement;
    },

    /**
     * 获取删除svg图标,用于列表页面
     * @returns {string}
     */
    getSvg: function () {
        return "<svg width=\"14px\" height=\"14px\" viewBox=\"0 0 18 18\"><g stroke=\"none\" stroke-width=\"1\"><g transform=\"translate(-1376.000000, -678.000000)\" fill=\"#000000\"><g transform=\"translate(334.000000, 486.000000)\"><g transform=\"translate(0.000000, 86.000000)\"><g transform=\"translate(1.000000, 64.000000)\"><g transform=\"translate(1050.056349, 51.056349) rotate(-315.000000) translate(-1050.056349, -51.056349) translate(1039.556349, 40.556349)\"><rect x=\"0\" y=\"8.59090909\" width=\"21\" height=\"3.81818182\"></rect><rect transform=\"translate(10.500000, 10.500000) rotate(-270.000000) translate(-10.500000, -10.500000) \" x=\"0\" y=\"8.59090909\" width=\"21\" height=\"3.81818182\"></rect></g></g></g></g></g></g></svg>";
    }
};