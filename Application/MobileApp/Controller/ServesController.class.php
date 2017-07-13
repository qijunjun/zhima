<?php

namespace MobileApp\Controller;

use Think\Controller;

/** ���� */

class ServesController extends Controller {

  /** �̳�ģ�� */

  public function display($template = null) {

    $template || $template = ACTION_NAME;

    parent::display('Serves/' . CONTROLLER_NAME . '/' . $template);

  }

}