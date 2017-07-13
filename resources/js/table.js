/**
 * Created by 123 on 2016/8/22.
 */
function Table() {
    "use strict";

    var paginate = function paginate(obj) {
        var _obj = {};
        _obj.id = obj.id || 'tableContent';
        _obj.listLength = obj.listLength || 10;
        _obj.operation = obj.operation || null;
        _obj.article = obj.article || {};
        _obj.checkbox = obj.checkbox;
        _obj.search = obj.search || true;
        _obj.callback = obj.callback || function () {
                if (!_obj.operation) return ;
                _obj.operation.evaluate && $('.dusEvaluate').click(function () {
                    window.location.hash = "{link: '" + obj.evaluate.link + "?id=" + $(this).parent().parent().parent().find("input:checkbox")[0].id + "', asideNumber: " + obj.evaluate.asideNumber + ", navNumber: " + obj.evaluate.navNumber + "}";
                });

                _obj.operation.edit && $('.dusEdit').click(function () {
                    window.location.hash = "{link:'" + obj.edit.link + "?id=" + $(this).parent().parent().parent().find("input:checkbox")[0].id + "', asideNumber: " + obj.edit.asideNumber + ", navNumber: " + obj.edit.navNumber + "}";
                });

                _obj.operation.deleted && $('.dusDelete').click(function () {
                    var $li = $(this).parent().parent().parent();
                    var id = $li.find("input:checkbox")[0].id;
                    new Inform({title: '通知', content: '是否确认删除?'}).alert(function () {
                        $.ajax({
                            url: obj.deleted.link,
                            method: 'post',
                            dataType: 'json',
                            data: {
                                id: id
                            },
                            success: function (data) {
                                if (data.code !== '001') {
                                    new Inform({title: '通知', content: '删除失败!'}).alert(function () {
                                        $('.popMsg').remove();
                                    });
                                    return ;
                                }
                                obj.deleted.callback && obj.deleted.callback(id);
                            },
                            error: function (err) {
                                console.log(err);
                                new Inform({title: '通知', content: '操作失败!'}).alert(function () {
                                    $('.popMsg').remove();
                                });
                            }
                        });
                    });
                });
            };
        return new Paginate(_obj);
    };

    var ajaxData = function ajax(url, useful, callback, data) {
        $.ajax({
            url: url,
            method: 'post',
            dataType: 'json',
            data: data || null,
            success: function (data) {
                if (data.code !== "001") {
                    new Inform({title: '通知', content: '操作失败!'}).alert(function () {
                        $('.popMsg').remove();
                    });
                    return ;
                }
                data = data.result;

                /**
                 * 设置总数据长度
                 */
                $('.listLength').html(data.length);

                /**
                 * 设置要展示的数据表相
                 * id不显示, 保存在checkbox中
                 * @type {Array}
                 */
                callback(useful, data);
            },
            error: function (err) {
                new Inform({title: '通知', content: '操作失败!'}).alert(function () {
                    $('.popMsg').remove();
                });
                console.log(err);
            }
        })
    };

    return {
        paginate: paginate,
        ajaxData: ajaxData
    }
}