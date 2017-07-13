<?php

/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/8
 * Time: 16:37
 */
namespace Qrcode\Controller;

use Think\Controller;
use Qrcode\API\CreateQRcodeAPI;

class QrcodeController extends Controller
{

    /**
     * 生成二维码201688
     */
    public function qrcode(){
        $qr_data = I('qr_data','http://zhima.zmade.cn','trim');
        if(strlen($qr_data)> 150){
            json("009",null,"您输入的网址过长");

            return;
        }
        $logo_image = I('logo_image','');
        $save_path = isset($_REQUEST['save_path'])?$_REQUEST['save_path']:__ROOT__.'Public/qrcode/';  //图片存储的绝对路径
        $web_path = isset($_REQUEST['save_path'])?$_REQUEST['web_path']:'/Public/qrcode/';        //图片在网页上显示的路径
        $qr_level = isset($_REQUEST['qr_level'])?$_REQUEST['qr_level']:'L';
        $qr_size = isset($_REQUEST['qr_size'])?$_REQUEST['qr_size']:'4';
        $save_prefix = isset($_REQUEST['save_prefix'])?$_REQUEST['save_prefix']:'zhima';

        if($filename = CreateQRcodeAPI::createQRcode($save_path,$qr_data,$qr_level,$qr_size,$save_prefix,$logo_image)){
            $picpath = $web_path.$filename;
        }
//        echo "<img src='".$picpath."'>";

        json("001",$picpath);
    }

    public function bqrcode(){

        $qr = I('qr_data');
        $qr_data = R('Company/Basic/getCodeUrl/',array($qr));

        if($qr_data==null)
        {
            json('002',null,'生成失败');
            return;
        }
        $logo_image = I('logo_image','');
        $save_path = isset($_REQUEST['save_path'])?$_REQUEST['save_path']:__ROOT__.'Public/qrcode/';  //图片存储的绝对路径
        $web_path = isset($_REQUEST['save_path'])?$_REQUEST['web_path']:'/Public/qrcode/';        //图片在网页上显示的路径
        $qr_level = isset($_REQUEST['qr_level'])?$_REQUEST['qr_level']:'L';
        $qr_size = isset($_REQUEST['qr_size'])?$_REQUEST['qr_size']:'4';
        $save_prefix = isset($_REQUEST['save_prefix'])?$_REQUEST['save_prefix']:'zhima';

        if($filename = CreateQRcodeAPI::createQRcode($save_path,$qr_data,$qr_level,$qr_size,$save_prefix,$logo_image)){
            $picpath = $web_path.$filename;
        }

        json("001",$picpath);
    }
}