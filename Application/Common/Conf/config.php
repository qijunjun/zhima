<?php
return array(
	// '配置项' => '配置值'
	'DB_TYPE'              => 'mysql',                   // 数据库类型
	'DB_HOST'              => 'localhost',               // 服务器地址
	'DB_PORT'              => '3306',                    // 端口
	'DB_USER'              => 'zhima',                   // 用户名
	'DB_PWD'               => 'sanshi@123',              // 密码
	'DB_NAME'              => 'zhima',                   // 数据库名
	'DB_PREFIX'            => 'zm_',                     // 数据库表前缀
	'DB_CHARSET'           => 'utf8',                    // 数据库编码
	'DB_DEBUG'             => TRUE,                      // 数据库调试模式开启后可以记录SQL日志
	'URL_CASE_INSENSITIVE' => TRUE,                      // 开启URL大小写不敏感
	'URL_MODEL'            => 2,                         // URL模式为Rewrite模式
	'TMPL_PARSE_STRING'    => array(                     // 模板替换规则
		'__PUBLIC__' => __ROOT__ . '/resources/',
		'__UPLOAD__' => __ROOT__ . '/resources/Uploads/',
		'__CODE__'   => __ROOT__ . '/resources/code/'
	),
	'URL_PARAMS_FILTER'    => TRUE,                      // 绑定参数过滤机制
	'DEFAULT_FILTER'       => 'htmlspecialchars,trim',   // 参数过滤方法
	'MONGODB_CONNECTION'   => array(                     // MongoDB连接
		'DB_TYPE'    => 'mongo',
		'DB_HOST'    => 'localhost',
		'DB_PORT'    => '27017',
		'DB_USER'    => 'zhima',
		'DB_PWD'     => 'zhima',
		'DB_NAME'    => 'zhima',
		'DB_CHARSET' => 'utf8',
    )
);
