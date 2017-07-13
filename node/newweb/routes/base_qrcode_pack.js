/**
 * Created by Liming on 2016/4/23.
 */
var express = require('express');
var router = express.Router();
var QRPModel = require('../models/zm_base_qrcode_pack');
var common = require('../common');

//新增包装码
router.post('/qrCodePack', function(req, res) {
    common.add(QRPModel, req, res);
});
//批量新增包装码
router.post('/qrCodePacks', function(req, res) {
    common.addAll(QRPModel, req, res);
});
//删除包装码
router.delete('/qrCodePack/:_id', function(req, res) {
    common.del(QRPModel, req, res);
});
//修改包装码
router.put('/qrCodePack/:_id', function(req, res) {
    common.edit(QRPModel, req, res);
});
//查询包装码
router.get('/qrCodePack/:_id', function(req, res) {
    common.findById(QRPModel, req, res);
});
//查询包装码
router.get('/qrCodePack', function(req, res) {
    common.find(QRPModel, req, res);
});

module.exports = router;
