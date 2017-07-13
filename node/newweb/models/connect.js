if(!global.mongooseConnect) {
  global.mongooseConnect = require('mongoose');
  global.mongooseConnect.connect('mongodb://127.0.0.1/Zmade');
}

module.exports = global.mongooseConnect;
