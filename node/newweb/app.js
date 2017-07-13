var express = require('express');
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');

var routes = require('./routes/index');
//TODO: ======================================路由文件======================================
//生产信息
var base_productProfiles = require('./routes/base_productprofiles');
//二维码
var base_qrCode = require('./routes/base_qrcode');
//包装码
var base_qrCodePack = require('./routes/base_qrcode_pack');
//入库
var base_scanin = require('./routes/base_scanin');
//出库
var base_scanout = require('./routes/base_scanout');
//TODO: ======================================路由文件======================================

var app = express();

// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'ejs');

// uncomment after placing your favicon in /public
//app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({extended: true}));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

app.use('/', routes);
//TODO: ======================================路由设置======================================
//生产信息
app.use('/productProfiles', base_productProfiles);
//二维码
app.use('/qrCode', base_qrCode);
//包装码
app.use('/qrCodePack', base_qrCodePack);
//入库
app.use('/scanIn', base_scanin);
//出库
app.use('/scanOut', base_scanout);
//TODO: ======================================路由设置======================================

// catch 404 and forward to error handler
app.use(function(req, res, next) {
    var err = new Error('Not Found');
    err.status = 404;
    next(err);
});

// error handlers

// development error handler
// will print stacktrace
if(app.get('env') === 'development') {
    app.use(function(err, req, res, next) {
        res.status(err.status || 500);
        res.render('error', {
            message: err.message,
            error: err
        });
    });
}

// production error handler
// no stacktraces leaked to user
app.use(function(err, req, res, next) {
    res.status(err.status || 500);
    res.render('error', {
        message: err.message,
        error: {}
    });
});


module.exports = app;
