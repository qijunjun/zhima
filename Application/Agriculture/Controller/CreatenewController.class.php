<?php
namespace Agriculture\Controller;
use Think\Controller;
/** 新增农事采集事件
 *
 * Created by Notepad++;
 * User: 黎明;
 * Date: 2016/1/23
 * Last Modified: 2016/1/25
 * Last Change: 移除_initialize函数，使用_echoJson函数设置Content-type
 */
class CreatenewController extends AuthController {
  /** 事件提交
   * 将头像、图片移动至相应文件夹，并将事件录入数据库
   *
   * @return void
   *
   * @request_param Integer [$companyID] 公司ID
   * @post_param String [$operator] 操作者头像
   * @post_param Array [$images] 操作图片
   * @post_param Integer [$functionid] 操作ID
   * @post_param String [$function_name] 操作名称
   * @post_param Integer [$event_time] 事件发生时间戳（13位）
   * @post_param String [$event_details] 事件详情
   * @post_param Float [$longitude] 经度
   * @post_param Float [$latitude] 纬度
   * @post_param String [$userlocation] GPS/基站定位地点
   *
   * Created by Notepad++;
   * User: 黎明;
   * Date: 2016/1/23
   * Last Modified: 2016/1/24
   * Last Change: 使用_echoJson函数输出响应信息
   */
  public function addData() {
		//设置附件上传目录
		$ndate = date('Y-m-d',time());
		$cp = $_REQUEST['companyID'];
		$hdir = __PUBLIC__ . 'Uploads/AgricultureData/Head';
		if (!is_dir($hdir)) {
			mkdir($hdir, 0777);
		}
		$hdir .= '/' . $ndate . '-' . $cp . '-head';
		if (!is_dir($hdir)) {
			mkdir($hdir, 0777);
		}
		$hdir .= '/';
		$idir = __PUBLIC__ . 'Uploads/AgricultureData/Image';
		if (!is_dir($idir)) {
			mkdir($idir, 0777);
		}
		$idir .= '/' . $ndate . '-' . $cp . '-image';
		if (!is_dir($idir)) {
			mkdir($idir, 0777);
		}
		$idir .= '/';
    //补全路径移动文件

    $_POST['operator'] = __PUBLIC__ . preg_replace("/resources/", "", $_POST['operator']);
    rename($_POST['operator'], $hdir . basename($_POST['operator']));
    $_POST['operator'] = $hdir . basename($_POST['operator']);
    unset($V);
    foreach($_POST['images'] as $k => &$v) {
      if($v['imagePath'] == "") {
        unset($_POST['images'][$k]);
      }
      $v['imagePath'] = __PUBLIC__ . preg_replace("/resources/", "", $v['imagePath']);
      rename($v['imagePath'], $idir . basename($v['imagePath']));
      $v['imagePath'] = $idir . basename($v['imagePath']);
    }
    unset($v);

    //插入数据库
    $t = time();
    $dat = array(
       "companyid"=>$_POST['companyID'],  //公司id
       "productid"=>$_POST['productID'],  //产品id
      "functionid" => $_POST['operatorID'],  //操作 注释2016.8.5
      "function_operateid" => $_POST['operatorID'],  //操作
      "function_name" => $_POST['dataName'],  //操作名
      "operatorimage" => preg_replace('/[a-zA-Z\/\._]+\/resources/', '/resources', $_POST['operator']),  //操作者头像
      "event_time" =>  date('Y-m-d H:i:s',substr($_POST['eventTime'],0,10)),  //事件时间
      "synch_time" => date('Y-m-d H:i:s', $t),  //同步上传时间
      "event_details" => $_POST['dataDetails'],  //事件详情
      "longitude" => $_POST['longitude'],  //经度
      "latitude" => $_POST['latitude'],  //纬度
      "userlocation" => $_POST['userlocation']  //GPS/基站定位地点
    );
    $result = M('base_product_event_data') -> add($dat);
    if($result === false) {
      _echoJson(
        "007",
        array(
          "code" => 1,
          "message" => '事件插入失败！'
        )
      );
      //json('007',null,'事件插入失败！');
      return;
    }
    $dat = array();
    foreach($_POST['images'] as $v) {
      $dat[] = array(
        "image_path" => preg_replace('/[a-zA-Z\/\._]+\/resources/', '/resources', $v['imagePath']),
         //"image_path" =>$v['imagePath'],
        "image_time" => date('Y-m-d H:i:s',substr($v['imageTime'],0,10)),
        "longitude" => $v['longitude'],
        "latitude" => $v['latitude'],
        "location" => $v['location'],
        "eventid" => $result,
        "synch_time" => date('Y-m-d H:i:s', $t),
        "event_time" => date('Y-m-d H:i:s',substr($_POST['eventTime'],0,10))
      );
    }
    $result = M('base_product_event_image') -> addAll($dat);
    if($result === false) {
      _echoJson(
        "007",
        array(
          "code" => 2,
          "message" => '图片插入失败！'
        )
      );
      //json('007',null,'图片插入失败！');
      return;
    }

    _echoJson(
      "001",
      array(
        "code" => 0,
        "message" => 'success', "t" =>$t
      )
    );
    //json('001',$t);
  }
  /** 缓存上传
   * 上传头像、图片，并暂存在Temp目录，响应输出图片文件路径
   *
   * @param String [$type] 上传文件类型head/event
   * @return void
   *
   * @request_param Integer [$companyID] 公司ID
   * @post_param File $fileInfo 上传图片文件
   *
   * Created by Notepad++;
   * User: 黎明;
   * Date: 2016/1/23
   * Last Modified: 2016/3/6
   * Last Change: 重复文件会删除
   */
  public function uploadTemp($type = '') {
		//设置附件上传目录
		$ndate = date('Y-m-d',time());
		$cp = $_REQUEST['companyID'];
		$dir = __PUBLIC__ . 'Uploads/AgricultureData/Temp';
		if (!is_dir($dir)) {
			mkdir($dir, 0777);
		}
		$dir .= '/' . $ndate . '-' . $cp . '-' . $type . '-temp';
		if (!is_dir($dir)) {
			mkdir($dir, 0777);
		}
		$dir .= '/';
		//上传文件出错
		if ($_FILES['fileInfo']['error'] > 0) {
			switch($_FILES['fileInfo']['error']) {
				case 1: //_echoJson("008", array("code" => -1, "message" => '文件大小超出服务器限制！'));
                    json('008',null,'文件大小超出服务器限制！');
					break;
				case 2: //_echoJson("008", array("code" => -2, "message" => '文件大小超出客户端限制！'));
                    json('008',null,'文件大小超出客户端限制！');
					break;
				case 3: //_echoJson("008", array("code" => -3, "message" => '文件上传不完整！'));
                    json('008',null,'文件上传不完整！');
					break;
				case 4: //_echoJson("008", array("code" => -4, "message" => '没有上传文件！'));
                    json('008',null,'没有上传文件！');
					break;
				case 5: //_echoJson("008", array("code" => -5, "message" => '文件大小为0！'));
                    json('008',null,'文件大小为0！');
					break;
			}
			return;
		} else {
			//文件已存在
			if (file_exists($dir . $_FILES['fileInfo']['name'] . '.jpg') && !unlink($dir . $_FILES['fileInfo']['name'] . '.jpg')) {
        _echoJson(
          "008", array(
            "code" => 2,
            "message" => '文件head_' . $_FILES['fileInfo']['name'] . '.jpg已存在！'
          )
        );
               // json('008',null,'文件head_' . $_FILES['fileInfo']['name'] . '.jpg已存在！');
				return;
			} else {
				//转存
				if(move_uploaded_file($_FILES['fileInfo']['tmp_name'], $dir . $_FILES['fileInfo']['name'] . '.jpg') == false) {
					_echoJson(
            "008", array(
              "code" => 4,
              "message" => '文件转存失败！'
            )
          );
            //        json('008',null,'文件转存失败！');
					return;
				}
			}
		}

    _echoJson(
      "001", array(
        "code" => 0,
        // "message" => preg_replace("/[a-zA-Z\/_]+\/Public/", "Public", $dir . $_FILES['fileInfo']['name'] . '.jpg')
        "message" => preg_replace('/[a-zA-Z\/\._]+\/resources/', '/resources', $dir . $_FILES['fileInfo']['name'] . '.jpg')
      )
    );
  }
  /** 事件修改
   * 修改事件
   *
   * @return void
   *
   * @request_param Integer [$eventid] 事件ID
   * @request_param Integer [$companyID] 公司ID
   * @post_param String [$operator] 操作者头像
   * @post_param Array [$images] 操作图片
   * @post_param Array [$delImages] 待删除图片列表
   * @post_param Integer [$functionid] 操作ID
   * @post_param String [$function_name] 操作名称
   * @post_param Integer [$event_time] 事件发生时间戳（13位）
   * @post_param String [$event_details] 事件详情
   * @post_param Float [$longitude] 经度
   * @post_param Float [$latitude] 纬度
   * @post_param String [$userlocation] GPS/基站定位地点
   *
   * Created by Notepad++;
   * User: 黎明;
   * Date: 2016/1/23
   * Last Modified: 2016/1/24
   * Last Change: 使用_echoJson函数输出响应信息
   */
  public function editData() {
		//设置附件上传目录
		$ndate = date('Y-m-d',time());
		$cp = $_REQUEST['companyID'];
    //插入数据库
    $t = time();
    $dat = array(
      "functionid" => $_POST['functionid'],  //操作
      "function_name" => $_POST['function_name'],  //操作名
      "event_time" => $_POST['event_time'],  //事件时间
      "synch_time" => date('Y-m-d H:i:s', $t),  //同步上传时间
      "event_details" => $_POST['event_details'],  //事件详情
      "longitude" => $_POST['longitude'],  //经度
      "latitude" => $_POST['latitude'],  //纬度
      "userlocation" => $_POST['userlocation']  //GPS/基站定位地点
    );
    //头像
    if(isset($_POST['operator']) && $_POST['operator'] != "") {
      $hdir = __PUBLIC__ . 'Uploads/AgricultureData/Head';
      if (!is_dir($hdir)) {
        mkdir($hdir, 0777);
      }
      $hdir .= '/' . $ndate . '-' . $cp . '-head';
      if (!is_dir($hdir)) {
        mkdir($hdir, 0777);
      }
      $hdir .= '/';
      //补全路径移动文件
      $_POST['operator'] = __PUBLIC__ . preg_replace("/resources/", "", $_POST['operator']);
      rename($_POST['operator'], $hdir . basename($_POST['operator']));
      $_POST['operator'] = $hdir . basename($_POST['operator']);
      $dat['operator'] = preg_replace("/[a-zA-Z\/_]+\/resources/", "resources", $_POST['operator']);  //操作者头像
    }
    $result = M('base_product_event_data') -> where(array('eventid' => $_REQUEST['eventid'])) -> save($dat);
    if($result === false) {
      _echoJson(
        "007",
        array(
          "code" => 1,
          "message" => '事件插入失败！'
        )
      );
      //json('007',null,'事件插入失败！');
      return;
    }
    //操作
    if(isset($_POST['images']) && is_array($_POST['images']) && count($_POST['images']) > 0 && $_POST['images'][0]["image_path"] != "") {
      $dat = array();
      $idir = __PUBLIC__ . '/Uploads/AgricultureData/Image';
      if (!is_dir($idir)) {
        mkdir($idir, 0777);
      }
      $idir .= '/' . $ndate . '-' . $cp . '-image';
      if (!is_dir($idir)) {
        mkdir($idir, 0777);
      }
      $idir .= '/';
      //补全路径移动文件
      unset($V);
      foreach($_POST['images'] as $k => &$v) {
        if($v['image_path'] == "") {
          unset($_POST['images'][$k]);
        }
        $v['image_path'] = __PUBLIC__ . preg_replace("/\/Public/", "", $v['image_path']);
        rename($v['image_path'], $idir . basename($v['image_path']));
        $v['image_path'] = $idir . basename($v['image_path']);
      }
      unset($v);
      foreach($_POST['images'] as $v) {
        $dat[] = array(
          "image_path" => preg_replace("/[a-zA-Z\/_]+\/Public/", "/Public", $v['image_path']),
          "image_time" => $v['image_time'],
          "longitude" => $v['longitude'],
          "latitude" => $v['latitude'],
          "location" => $v['location'],
          "eventid" => $_REQUEST['eventid'],
          "synch_time" => date('Y-m-d H:i:s', $t),
          "event_time" => $_POST['event_time']
        );
      }
      $result = M('base_product_event_image') -> addAll($dat);
      if($result === false) {
        _echoJson(
          "007",
          array(
            "code" => 2,
            "message" => '图片插入失败！'
          )
        );
        return;
      }
    }
    $di = array();
    if(isset($_POST['delImages']) && is_array($_POST['delImages'])) {
      foreach($_POST['delImages'] as $k => $v) {
        if($v != "") {
          $di[] = intval($v);
        }
      }
    }
    if(count($di) > 0) {
      $result = M('base_product_event_image') -> delete(implode(',', $di));
      if($result === false) {
        _echoJson(
          "007",
          array(
            "code" => 3,
            "message" => '图片删除失败！'
          )
        );
        return;
      }
    }
    _echoJson(
      "001",
      array(
        "code" => 0,
        "message" => 'success', "t" =>$t
      )
    );
  }
}