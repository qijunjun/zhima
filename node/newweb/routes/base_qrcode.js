/**
 * Created by Liming on 2016/4/23.
 */
var express = require('express');
var router = express.Router();
var QRModel = require('../models/zm_base_qrcode');
var common = require('../common');

//新增二维码
router.post('/qrCode', function(req, res) {
    common.add(QRModel, req, res);
});
//批量新增二维码
router.post('/qrCodes', function(req, res) {
    common.addAll(QRModel, req, res);
});
//删除二维码
router.delete('/qrCode/:_id', function(req, res) {
    common.del(QRModel, req, res);
});
//修改二维码
router.put('/qrCode/:_id', function(req, res) {
    common.edit(QRModel, req, res);
});
//查询二维码
router.get('/qrCode/:_id', function(req, res) {
    common.findById(QRModel, req, res);
});
//查询二维码
router.get('/qrCode', function(req, res) {
    common.find(QRModel, req, res);
});

module.exports = router;
