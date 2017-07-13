var db;

function showResult(result) {
	console.log(result); //result为Array
}

// 判断是否第一次运行
function exitTables(result) {
	if (result[0].cnt == 0) {
		// alert('初始化表格');
		try {
			initTables();
		} catch (err) {
			alert(err.description);
		}
	}
}

// 初始化全部内容
function initTables() {
	deleteTables();
	createTables();
	// addData2Tables();
}

// 删除所有表
function deleteTables() {
	db.doQuery('DROP TABLE "companys";', showResult);
	db.doQuery('DROP TABLE "events";', showResult);
	db.doQuery('DROP TABLE "functions";', showResult);
	db.doQuery('DROP TABLE "images";', showResult);
	db.doQuery('DROP TABLE "tasks";', showResult);
	db.doQuery('DROP TABLE "users";', showResult);
	db.doQuery('DROP VIEW IF EXISTS "eventList";', showResult);
}

// 创建表结构
function createTables() {
	db.doQuery('CREATE TABLE IF NOT EXISTS "companys" ("companyID" INTEGER NOT NULL, "companyName" TEXT NOT NULL, "companyImage" TEXT, "companyLogo" TEXT, "synchTime" INTEGER, PRIMARY KEY ("companyID" ASC));', showResult);
	db.doQuery('CREATE TABLE IF NOT EXISTS "events" ("eventID" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "eventName" TEXT, "eventDetails" TEXT, "operator" TEXT, "eventTime" INTEGER NOT NULL, "taskID" INTEGER NOT NULL, "functionID" INTEGER NOT NULL, "companyID" INTEGER NOT NULL, "synchTime" INTEGER);', showResult);
	db.doQuery('CREATE TABLE IF NOT EXISTS "functions" ("functionID" INTEGER NOT NULL, "functionName" TEXT NOT NULL, "functionImage" TEXT, "taskID" INTEGER NOT NULL, "synchTime" INTEGER, PRIMARY KEY ("functionID" ASC));', showResult);
	db.doQuery('CREATE TABLE IF NOT EXISTS "images" ("imageID" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "imagePath" TEXT NOT NULL, "imageTitle" TEXT, "imageTime" INTEGER NOT NULL, "imageAttribute" TEXT, "imageFinger" TEXT, "longitude" TEXT, "latitude" TEXT, "location" TEXT, "eventID" INTEGER NOT NULL, "synchTime" INTEGER);', showResult);
	db.doQuery('CREATE TABLE IF NOT EXISTS "tasks" ("taskID" INTEGER NOT NULL, "taskName" TEXT NOT NULL, "taskImage" TEXT, "companyID" INTEGER NOT NULL, "synchTime" INTEGER, PRIMARY KEY ("taskID" ASC));', showResult);
	db.doQuery('CREATE TABLE IF NOT EXISTS "users" ("userID" INTEGER NOT NULL, "userName" TEXT NOT NULL, "password" TEXT NOT NULL, "companyID" INTEGER NOT NULL, "synchTime" INTEGER, PRIMARY KEY ("userID" ASC));', showResult);
	db.doQuery('CREATE VIEW IF NOT EXISTS "eventList" AS  SELECT companys.companyID, events.eventID, events.eventName, events.operator, events.eventTime, functions.functionName, tasks.taskName, tasks.taskImage FROM companys LEFT JOIN events ON companys.companyID = events.companyID INNER JOIN tasks ON events.taskID = tasks.taskID AND events.companyID = tasks.companyID AND companys.companyID = tasks.companyID INNER JOIN functions ON events.functionID = functions.functionID AND tasks.taskID = functions.taskID ORDER BY events.eventTime DESC;', showResult);
}

function InitCompanyDatas() {
	db.switchTable('companys').where({
		companyID : loginData.companyID
	}).getData(exitCompanyInfo);
}

function exitCompanyInfo(result) {
	if (result.length == 1) {
		if (result[0] == 0) {
			addCompanyDatas();
		}
	}
}

function addCompanyDatas() {
	// /* alert("添加Company信息"); */
	try {
		db.switchTable('companys').insertData([{
					companyID : loginData.companyID,
					companyName : loginData.companyName,
					companyImage : "images/bj.png",
					companyLogo : "images/head.png",
					synchTime : new Date().getTime()
				}
			]);
	} catch (err) {
		alert(err.description);
	}
	// /* alert("添加Task信息"); */
	if (loginData.tasks.length > 0) {
		for (i = 0; i < loginData.tasks.length; i++) {
			// var t = new Date();
			// t.setTime(loginData.tasks[i].synchTime*1000);
			try {
				db.switchTable('tasks').insertData([{
							taskID : loginData.tasks[i].taskID,
							taskName : loginData.tasks[i].taskName,
							taskImage : loginData.tasks[i].taskImage,
							companyID : loginData.companyID,
							synchTime : loginData.tasks[i].synchTime
						}
					]);
			} catch (err) {
				alert(err.description);
			}
		};
	}
	/* alert("添加Function信息"); */
	if (loginData.functions.length > 0) {
		for (i = 0; i < loginData.functions.length; i++) {
			// var t = new Date();
			// t.setTime(loginData.functions[i].synchTime*1000);
			try {
				db.switchTable('functions').insertData([{
							functionID : loginData.functions[i].functionID,
							functionName : loginData.functions[i].functionName,
							functionImage : loginData.functions[i].functionImage,
							taskID : loginData.functions[i].taskID,
							synchTime : loginData.functions[i].synchTime
						}
					]);
			} catch (err) {
				alert(err.description);
			}
		};
	}
	// db.doQuery('INSERT INTO "companys"("companyID","companyName","companyImage","companyLogo","synchTime") VALUES (' + loginData.companyID + ', "' + loginData.companyName + '", "images/bj.png", "images/head.png", null);', showResult);

	// if (loginData.tasks.length > 0) {
	// for (i = 0; i < loginData.tasks.length; i++) {
	// db.doQuery('INSERT INTO "tasks"("taskID","taskName","taskImage","companyID","synchTime") VALUES (' + loginData.tasks[i].taskID + ', "' + loginData.tasks[i].taskName + '", "' + loginData.tasks[i].taskImage + '", ' + loginData.companyID + ', null);', showResult);
	// };
	// }

	// if (loginData.functions.length > 0) {
	// for (i = 0; i < loginData.functions.length; i++) {
	// db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (' + loginData.functions[i].functionID + ', "' + loginData.functions[i].functionName + '", "' + loginData.functions[i].functionImage + '", null, ' + loginData.functions[i].taskID + ');', showResult);
	// }
	// }
}

// 添加初始化数据
function addData2Tables() {
	db.doQuery('INSERT INTO "companys"("companyID","companyName","companyImage","companyLogo","synchTime") VALUES (1, "陕西杜康酒业有限公司", "images/bj.png", "images/head.png", null);', showResult);
	db.doQuery('INSERT INTO "users"("userID","userName","password","companyID","synchTime") VALUES (1, "sx_dukang", "888888", 1, null);', showResult);
	db.doQuery('INSERT INTO "tasks"("taskID","taskName","taskImage","companyID","synchTime") VALUES (1, "胡萝卜", "images/huluobo.png", 1, null);', showResult);
	db.doQuery('INSERT INTO "tasks"("taskID","taskName","taskImage","companyID","synchTime") VALUES (2, "大葱", "images/dacong.png", 1, null);', showResult);
	db.doQuery('INSERT INTO "companys"("companyID","companyName","companyImage","companyLogo","synchTime") VALUES (13, "陕西高陵县耿镇胡萝卜生产基地", "images/bj.png", "images/head.png", null);', showResult);
	db.doQuery('INSERT INTO "users"("userID","userName","password","companyID","synchTime") VALUES (2, "sx.gaoling.genzheng", "888888", 13, null);', showResult);
	db.doQuery('INSERT INTO "tasks"("taskID","taskName","taskImage","companyID","synchTime") VALUES (3, "胡萝卜", "images/huluobo.png", 13, null);', showResult);
	db.doQuery('INSERT INTO "tasks"("taskID","taskName","taskImage","companyID","synchTime") VALUES (4, "大葱", "images/dacong.png", 13, null);', showResult);
	// db.doQuery('INSERT INTO "tasks"("taskID","taskName","taskImage","companyID","synchTime") VALUES (3, "大白菜", "images/dabaicai.png", 1, null);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (1, "施肥", "images/shifei.png", null, 1);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (2, "播种", "images/bozhong.png", null, 1);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (3, "防虫", "images/fangchong.png", null, 1);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (4, "浇水", "images/guangai.png", null, 1);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (5, "定苗", "images/duifei.png", null, 1);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (6, "收获", "images/shouhuo.png", null, 1);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (7, "检验", "images/jianyan.png", null, 1);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (8, "储存", "images/guangai.png", null, 1);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (9, "包装", "images/baozhuang.png", null, 1);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (10, "堆肥", "images/duifei.png", null, 2);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (11, "耕地", "images/gengdi.png", null, 2);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (12, "播种", "images/bozhong.png", null, 2);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (13, "施肥", "images/shifei.png", null, 2);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (14, "防虫", "images/fangchong.png", null, 2);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (15, "灌溉", "images/guangai.png", null, 2);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (16, "收获", "images/shouhuo.png", null, 2);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (17, "包装", "images/baozhuang.png", null, 2);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (18, "检验", "images/jianyan.png", null, 2);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (19, "施肥", "images/shifei.png", null, 3);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (20, "播种", "images/bozhong.png", null, 3);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (21, "防虫", "images/fangchong.png", null, 3);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (22, "浇水", "images/guangai.png", null, 3);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (23, "定苗", "images/duifei.png", null, 3);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (24, "收获", "images/shouhuo.png", null, 3);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (25, "检验", "images/jianyan.png", null, 3);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (26, "储存", "images/guangai.png", null, 3);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (27, "包装", "images/baozhuang.png", null, 3);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (28, "堆肥", "images/duifei.png", null, 4);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (29, "耕地", "images/gengdi.png", null, 4);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (30, "播种", "images/bozhong.png", null, 4);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (31, "施肥", "images/shifei.png", null, 4);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (32, "防虫", "images/fangchong.png", null, 4);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (33, "灌溉", "images/guangai.png", null, 4);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (34, "收获", "images/shouhuo.png", null, 4);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (35, "包装", "images/baozhuang.png", null, 4);', showResult);
	db.doQuery('INSERT INTO "functions"("functionID","functionName","functionImage","synchTime","taskID") VALUES (36, "检验", "images/jianyan.png", null, 4);', showResult);

	db.switchTable('events').insertData([{
				taskID : 1,
				functionID : 1,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 1,
				functionID : 2,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 1,
				functionID : 3,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 1,
				functionID : 4,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 1,
				functionID : 5,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 1,
				functionID : 6,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 1,
				functionID : 7,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 1,
				functionID : 8,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 1,
				functionID : 9,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 2,
				functionID : 10,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 2,
				functionID : 11,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 1,
				functionID : 1,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 2,
				functionID : 12,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 2,
				functionID : 13,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 2,
				functionID : 14,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 2,
				functionID : 15,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 2,
				functionID : 16,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 2,
				functionID : 17,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}, {
				taskID : 2,
				functionID : 18,
				operator : 'images/operator.png',
				eventTime : new Date().getTime(),
				companyID : 1
			}
		]);
	db.switchTable('images').insertData([{
				imagePath : 'images/shifei (1).jpg',
				imageTime : new Date().getTime(),
				eventID : 1
			}, {
				imagePath : 'images/shifei (2).jpg',
				imageTime : new Date().getTime(),
				eventID : 1
			}, {
				imagePath : 'images/shifei (3).jpg',
				imageTime : new Date().getTime(),
				eventID : 1
			}, {
				imagePath : 'images/bozhong (1).jpg',
				imageTime : new Date().getTime(),
				eventID : 12
			}, {
				imagePath : 'images/bozhong (2).jpg',
				imageTime : new Date().getTime(),
				eventID : 12
			}, {
				imagePath : 'images/bozhong (3).jpg',
				imageTime : new Date().getTime(),
				eventID : 12
			}, {
				imagePath : 'images/duifei (1).jpg',
				imageTime : new Date().getTime(),
				eventID : 10
			}, {
				imagePath : 'images/duifei (2).jpg',
				imageTime : new Date().getTime(),
				eventID : 10
			}, {
				imagePath : 'images/duifei (3).jpg',
				imageTime : new Date().getTime(),
				eventID : 10
			}, {
				imagePath : 'images/fangchong (1).jpg',
				imageTime : new Date().getTime(),
				eventID : 3
			}, {
				imagePath : 'images/fangchong (2).jpg',
				imageTime : new Date().getTime(),
				eventID : 3
			}, {
				imagePath : 'images/fangchong (3).jpg',
				imageTime : new Date().getTime(),
				eventID : 3
			}, {
				imagePath : 'images/gengdi (1).jpg',
				imageTime : new Date().getTime(),
				eventID : 11
			}, {
				imagePath : 'images/gengdi (2).jpg',
				imageTime : new Date().getTime(),
				eventID : 11
			}, {
				imagePath : 'images/gengdi (3).jpg',
				imageTime : new Date().getTime(),
				eventID : 11
			}, {
				imagePath : 'images/guangai (1).jpg',
				imageTime : new Date().getTime(),
				eventID : 16
			}, {
				imagePath : 'images/guangai (2).jpg',
				imageTime : new Date().getTime(),
				eventID : 16
			}, {
				imagePath : 'images/guangai (3).jpg',
				imageTime : new Date().getTime(),
				eventID : 16
			}
		]);
}
