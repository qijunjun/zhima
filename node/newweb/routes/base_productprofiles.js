/**
 * Created by Liming on 2016/4/23.
 */
var express = require('express');
var router = express.Router();
var mongoose = require('../models/connect');
var common = require('../common');
var schema = mongoose.Schema;
var fieldsModel = mongoose.model("zm_base_productprofile_fields", new schema({
    field: {type: String, required: true},
    type: {type: String, required: true, default: "String"}
}));
var productProfileModel;
var fields = {
    companyID: {type: Number, required: true},
    profileID: {type: Number, required: true},
    productID: {type: Number, required: true}
};
//生产信息所有字段列表，生成生产信息对象
var getFields = function(req, res, next) {
    fieldsModel.find({}, 'field type', function(err, data) {
        if(err) {
            console.log(err);
        }
        if(data) {
            for(var i = 0; i < data.length; i++) {
                fields[data[i]['field']] = {
                    type: data[i]['type']
                };
            }
        }
        productProfileModel = mongoose.model("zm_base_productprofiles", new schema(fields));
        next();
    });
};

//添加字段
router.post('/field', function(req, res) {
    common.add(fieldsModel, req, res);
});
//批量添加字段
router.post('/fields', function(req, res) {
    common.addAll(fieldsModel, req, res);
});
//删除字段
router.delete('/field/:_id', function(req, res) {
    common.del(fieldsModel, req, res);
});
//修改字段
router.put('/field/:_id', function(req, res) {
    common.edit(fieldsModel, req, res);
});
//查询字段
router.get('/field/:_id', function(req, res) {
    common.findById(fieldsModel, req, res);
});
//查询字段
router.get('/field', function(req, res) {
    common.find(fieldsModel, req, res);
});
//生产信息对象生成
router.post('/productProfile*', getFields);
router.delete('/productProfile*', getFields);
router.put('/productProfile*', getFields);
router.get('/productProfile*', getFields);

//新增生产信息
router.post('/productProfile', function(req, res) {
    common.add(productProfileModel, req, res);
});
//批量新增生产信息
router.post('/productProfiles', function(req, res) {
    common.addAll(productProfileModel, req, res);
});
//删除生产信息
router.delete('/productProfile/:_id', function(req, res) {
    common.del(productProfileModel, req, res);
});
//修改生产信息
router.put('/productProfile/:_id', function(req, res) {
    common.edit(productProfileModel, req, res);
});
//查询生产信息
router.get('/productProfile/:_id', function(req, res) {
    common.findById(productProfileModel, req, res);
});
//查询生产信息
router.get('/productProfile', function(req, res) {
    common.find(productProfileModel, req, res);
});

module.exports = router;
