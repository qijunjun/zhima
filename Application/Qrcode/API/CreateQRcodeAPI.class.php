<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/8
 * Time: 16:46
 */

namespace Qrcode\API;


class CreateQRcodeAPI
{
    /**
     * 功能：生成二维码201688
     * @param string $qr_data   手机扫描后要跳转的网址
     * @param string $qr_level  默认纠错比例 分为L、M、Q、H四个等级，H代表最高纠错能力
     * @param string $qr_size   二维码图大小，1－10可选，数字越大图片尺寸越大
     * @param string $save_path 图片存储路径
     * @param string $save_prefix 图片名称前缀
     */
    function createQRcode($save_path,$qr_data='PHP QR Code :)',$qr_level='L',$qr_size=4,$save_prefix='zhima',$logo_image=''){
        if(!isset($save_path)) return '';
        //设置生成png图片的路径
        $PNG_TEMP_DIR =& $save_path;
        //导入二维码核心程序
        vendor('PHPQRcode.class#phpqrcode');  //注意这里的大小写哦，不然会出现找不到类，PHPQRcode是文件夹名字，class#phpqrcode就代表class.phpqrcode.php文件名
        $QRcode = new \QRcode();
        //检测并创建生成文件夹
        if (!file_exists($PNG_TEMP_DIR)){
            mkdir($PNG_TEMP_DIR,0777);
        }
        $filename = $PNG_TEMP_DIR.'test.png';
        $errorCorrectionLevel = 'L';
        if (isset($qr_level) && in_array($qr_level, array('L','M','Q','H'))){
            $errorCorrectionLevel = & $qr_level;
        }
        $matrixPointSize = 4;
        if (isset($qr_size)){
            $matrixPointSize = & min(max((int)$qr_size, 1), 10);
        }
        if (isset($qr_data)) {
            if (trim($qr_data) == ''){
                die('data cannot be empty!');
            }
            //生成文件名 文件路径+图片名字前缀+md5(名称)+.png
            $filename = $PNG_TEMP_DIR.$save_prefix.md5($qr_data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';

            //开始生成
            $QRcode::png($qr_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2,true);
        } else {
            //默认生成
            $QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2,true);
        }
        if($logo_image == ''){
            if(file_exists($PNG_TEMP_DIR.basename($filename))) {
                return basename($filename);
            }
            else{
                return FALSE;
            }
        }else{
            //以下是生成带logo的二维码2016811
            $logo = __ROOT__."Public/qrcode/".$logo_image;//准备好的logo图片
            $QR = __ROOT__."Public/qrcode/".basename($filename);//已经生成的原始二维码图
            if ($logo !== FALSE) {
                $QR = imagecreatefromstring(file_get_contents($QR));
                $logo = imagecreatefromstring(file_get_contents($logo));
                $QR_width = imagesx($QR);//二维码图片宽度
                $QR_height = imagesy($QR);//二维码图片高度
                $logo_width = imagesx($logo);//logo图片宽度
                $logo_height = imagesy($logo);//logo图片高度
                $logo_qr_width = $QR_width / 5;
                $scale = $logo_width/$logo_qr_width;
                $logo_qr_height = $logo_height/$scale;
                $from_width = ($QR_width - $logo_qr_width) / 2;
                //重新组合图片并调整大小
                imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                    $logo_qr_height, $logo_width, $logo_height);
            }
            //输出图片
            $re = imagepng($QR,__ROOT__."Public/qrcode/".basename($filename));
            if($re){
                return basename($filename);
            }else{
                return false;
            }
        }
    }

}