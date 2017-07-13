/**
 * Created by Liming on 2016/4/23.
 */
var mongoose = require('./connect.js');
var schema = mongoose.Schema;

var qrCodeModel = mongoose.model("zm_base_qrcode", new schema({
    c: {type: String, require: true},
    b: {type: Number, require: true},
    create_time: {type: Date, require: true, default: new Date()},
    update_time: {type: Date},
    status: {type: Number},
    hits: {type: Number}
}));

module.exports = qrCodeModel;
