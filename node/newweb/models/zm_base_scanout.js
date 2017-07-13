/**
 * Created by Liming on 2016/4/23.
 */
var mongoose = require('./connect.js');
var schema = mongoose.Schema;

var scanOutModel = mongoose.model("zm_base_scanout", new schema({
    warehouseid: {type: Number, require: true},
    agencyid: {type: Number, require: true},
    info: {type: String},
    b: {type: Number, require: true},
    p: {type: Number, require: true},
    time: {type: Date, require: true, default: new Date()},
    userid: {type: Number, require: true},
    companyid: {type: Number, require: true},
    synch_time: {type: Number},
    longitude: {type: String},
    latitude: {type: String}
}));

module.exports = scanOutModel;
