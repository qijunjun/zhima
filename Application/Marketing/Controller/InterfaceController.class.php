<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/5/27
 * Time: 下午1:48
 */
namespace Marketing\Controller;

use Think\Controller;

class InterfaceController extends Controller
{
    public  function getProductid($code){
        if($code=null){
            echo -1;
        }
        $result=getProductid($code);
        echo $result;
    }
}