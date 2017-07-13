<?php
/**
 * Created by PhpStorm.
 * User: Liming
 * Date: 2016/4/16
 * Time: 10:59
 */

namespace Home\Controller;

use Think\Controller;
use Think\Upload;

class FileController extends Controller {
    /**
     * 对图片进行隔行扫描处理
     *
     * @return void
     *
     * Created by PhpStorm;
     * User: Liming;
     * Date: 2016/4/16
     */
    private function isImage() {
        foreach($_FILES as $k => $v) {
            list($width, $height, $type) = getimagesize($v["tmp_name"]);
            if($width === null || $height === null || $type === null) {
                unset($_FILES[$k]);
            } else {
                $type = image_type_to_extension($type, false);
                $func_read = "imagecreatefrom$type";
                $func_write = "image$type";
                $type = str_ireplace("jpeg", "jpg", $type);
                $img = $func_read($v["tmp_name"]);
                imageinterlace($img, true);
                $func_write($img, $v["tmp_name"]);
                imagedestroy($img);
            }
        }
    }

    /**
     * 上传图片统一接口
     *
     * @param null $m 模块
     * @param null $c 控制器
     * @return void
     *
     * Created by PhpStorm;
     * User: Liming;
     * Date: 2016/4/16
     */
    public function upload($m = null, $c = null) {
        if(!is_dir(__UPLOAD__)) {
            mkdir(__UPLOAD__, 777);
        }
        $this->isImage();
        $path = '';
        if($m !== null && $m != '') {
            $path .= $m . '/';
        }
        if($c !== null && $c != '') {
            $path .= $c . '/';
        }
        $config = [
            'rootPath' => __UPLOAD__,
            'savePath' => $path,
            'saveName' => ['uniqid'],
            'exts'     => ['jpg', 'gif', 'png', 'jpeg', 'bmp'],
            'autoSub'  => true,
            'subName'  => ['date', 'Y/m/d'],
            'hash'     => false
        ];
        $upload = new Upload($config);
        $result = $upload->upload();
        if(!$result) {
            json("008", $upload->getError());
        } else {
            unset($value);
            foreach($result as &$value) {
                $value['savepath'] = C('TMPL_PARSE_STRING')['__UPLOAD__'] . $value['savepath'];
            }
            unset($value);
            json("001", $result);
        }
    }
}
