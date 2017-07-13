<?php
namespace Agriculture\Controller;
use Think\Controller;
/**
 */
class IndexController extends AuthController {
  /**
   */
  public function agriculture() {
    $this->display();
  }
  /**
   */
  public function add() {
    $this->display();
  }
  /**
   */
  public function edit($dataId = null) {
    if($dataId === null) {
      die('Access Deny!');
    }
    $m_data = M('agriculturedata');
    $data = $m_data->where(array('dataID' => $dataId))->find();
    if($data === false) {
      die('Data ID Error');
    }
    $this->assign('data', $data);
    $m_image = M('agricultureimage');
    $image = $m_image->where(array('dataID' => $dataId))->select();
    if($image === false) {
      die('Data ID Error');
    }
    $this->assign('image', $image);
    $m_operate = M('agricultureoperate');
    $operate = $m_operate->where(array('operatorID' => $data['operatorid']))->find();
    if($operate === false) {
      die('Unknown Error');
    }
    $this->assign('operate', $operate);
    $operates = $m_operate->where(array('productID' => $operate['productid']))->select();
    if($operates === false) {
      die('Unknown Error');
    }
    $this->assign('operates', $operates);
    $this->assign('dataId', $dataId);
    $this->display();
  }
}