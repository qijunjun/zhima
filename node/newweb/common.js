/**
 * Created by Liming on 2016/4/23.
 */
/** 从req.body添加一条记录
 *
 * @param model 模型
 * @param req 请求对象
 * @param res 响应对象
 */
function add(model, req, res) {
    new model(req.body).save(function(err) {
        if(err) {
            res.send({code: -1, message: err});
        } else {
            res.send({code: 0});
        }
    });
}
/** 从req.body批量插入数据
 *
 * @param model 模型
 * @param req 请求对象
 * @param res 响应对象
 */
function addAll(model, req, res) {
    model.collection.insert(req.body.data, function(err, data) {
        if(err) {
            res.send({code: -1, message: err});
        } else {
            res.send({code: 0, result: data});
        }
    });
}
/** 删除记录
 *
 * @param model 模型
 * @param req 请求对象
 * @param res 响应对象
 */
function del(model, req, res) {
    model.remove(req.params, function(err) {
        if(err) {
            res.send({code: -1, message: err});
        } else {
            res.send({code: 0});
        }
    });
}
/** 修改记录
 *
 * @param model 模型
 * @param req 请求对象
 * @param res 响应对象
 */
function edit(model, req, res) {
    model.update(req.params, {$set: req.body}, function(err) {
        if(err) {
            res.send({code: -1, message: err});
        } else {
            res.send({code: 0});
        }
    });
}
/** 根据_id查询记录
 *
 * @param model 模型
 * @param req 请求对象
 * @param res 响应对象
 */
function findById(model, req, res) {
    model.find(req.params, function(err, data) {
        if(err) {
            console.log(err);
            res.send({code: -1, message: err});
        } else {
            res.send({code: 0, result: data});
        }
    });
}
/** 查询记录
 *
 * @param model 模型
 * @param req 请求对象
 * @param res 响应对象
 */
function find(model, req, res) {
    model.find(req.query, function(err, data) {
        if(err) {
            console.log(err);
            res.send({code: -1, message: err});
        } else {
            res.send({code: 0, result: data});
        }
    });
}

//导出函数
module.exports = {
    add: add,
    addAll: addAll,
    del: del,
    edit: edit,
    findById: findById,
    find: find
};
