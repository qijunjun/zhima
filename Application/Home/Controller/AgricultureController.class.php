<?php
namespace Home\Controller;
use Think\Controller;
/** 农事数据采集 */
class AgricultureController extends Controller {
  /** 首页 */
  public function index() {
    //header("Location: " . U('MobileApp/My/login'), true, 301);
    $this->display();
  }
  /** 登录 */
  public function login() {
    //header("Location: " . U('MobileApp/My/login'), true, 301);
    $this->display();
  }
  /** 事件列表 */
  public function eventlite() {
    $this->display();
  }
  /** 产品 */
  public function task() {
    $this->display();
  }
  /** 操作 */
  public function functions() {
    $this->display();
  }
  /** 拍照 */
  public function photo() {
    $this->display();
  }
  /** 列表 */
  public function table() {
    $this->display();
  }
}