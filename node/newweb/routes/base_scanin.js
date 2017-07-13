/**
 * Created by Liming on 2016/4/23.
 */
var express = require('express');
var router = express.Router();
var scanInModel = require('../models/zm_base_scanin');
var common = require('../common');

//新增入库信息
router.post('/scanIn', function(req, res) {
    common.add(scanInModel, req, res);
});
//批量新增入库信息
router.post('/scanIns', function(req, res) {
    common.addAll(scanInModel, req, res);
});
//删除入库信息
router.delete('/scanIn/:_id', function(req, res) {
    common.del(scanInModel, req, res);
});
//修改入库信息
router.put('/scanIn/:_id', function(req, res) {
    common.edit(scanInModel, req, res);
});
//查询入库信息
router.get('/scanIn/:_id', function(req, res) {
    common.findById(scanInModel, req, res);
});
//查询入库信息
router.get('/scanIn', function(req, res) {
    common.find(scanInModel, req, res);
});

module.exports = router;
