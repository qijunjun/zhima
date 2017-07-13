/**
 * Created by Liming on 2016/4/23.
 */
var express = require('express');
var router = express.Router();
var scanOutModel = require('../models/zm_base_scanout');
var common = require('../common');

//新增入库信息
router.post('/scanOut', function(req, res) {
    common.add(scanOutModel, req, res);
});
//批量新增入库信息
router.post('/scanOuts', function(req, res) {
    common.addAll(scanOutModel, req, res);
});
//删除入库信息
router.delete('/scanOut/:_id', function(req, res) {
    common.del(scanOutModel, req, res);
});
//修改入库信息
router.put('/scanOut/:_id', function(req, res) {
    common.edit(scanOutModel, req, res);
});
//查询入库信息
router.get('/scanOut/:_id', function(req, res) {
    common.findById(scanOutModel, req, res);
});
//查询入库信息
router.get('/scanOut', function(req, res) {
    common.find(scanOutModel, req, res);
});

module.exports = router;
