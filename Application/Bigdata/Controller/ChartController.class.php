<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 16/4/12
 * Time: 下午11:08
 */
namespace  Bigdata\Controller;
use Common\Controller\BaseController;
class ChartController extends BaseController{

    protected function _initialize(){

    }
    public function  index(){
        $this->display('index');
    }
}