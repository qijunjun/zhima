<?php
/**
 * Created by PhpStorm.
 * User: Liming
 * Date: 2016/4/22
 * Time: 14:48
 */


namespace Faker\Controller;

use Common\Controller\BaseController;
use CheckIO\API\CheckioAPI;

/**
 * 产品真伪判断
 * Class AuthenticityController
 * @package Faker\Controller
 */
class AuthenticityController extends BaseController {
    /**
     * 新增黑名单
     *
     * @param float $bStart 起始码段
     * @param float $bEnd   [终止码段]
     */
    public function addBlacklist($bStart, $bEnd = null) {
        $time = time();
        $data = [];
        $companyid=session('user_info')['companyid'];
        if(!is_numeric($bStart)) {
            json("009");
        }
        $bStart = floatval($bStart);
        if(is_numeric($bEnd)) {
            $bEnd = floatval($bEnd);
        } else {
            $bEnd = $bStart;
        }
        if($bStart > $bEnd) {
            $t = $bStart;
            $bStart = $bEnd;
            $bEnd = $t;
        }
        //检查质量码是否有效
        if(!checkQCode($companyid,$bStart,$bEnd)){
            json('009');
            return;
        }
        for(; $bStart <= $bEnd; $bStart++) {
            //TODO: 检查质量码是否有效
            $data[] = [
                'b' => $bStart,
                'create_time' => $time,
                'status' => 1,
                'company_id'=>$companyid
            ];
        }
        $model = M('base_qrcode_spam');
        if($model->addAll($data)) {
            json("001");
        } else {
            json("007");
        }
    }

    /**
     * 删除黑名单
     *
     * @param float $id ID
     */
    public function removeBlacklist($id) {
        if(!is_numeric($id)) {
            json("009");
        }
        $id = intval($id);
        $model = M('base_qrcode_spam');
        if($model->where(['id' => $id])->delete() === false) {
            json("007");
        } else {
            json("001");
        }
    }

    /**
     * 获取黑名单
     */
    public function getBlacklist() {
        $model = M('base_qrcode_spam');
        $companyid=session('user_info')['companyid'];
        $condition=array(
            "company_id" => $companyid
        );
        $result = $model->where($condition)->select();
        if($result === false) {
            json("007");
        } else {
            $c = count($result);
            for($i = 0; $i < $c; $i++) {
                $info = CheckioAPI::productInfo($result[$i]['b']);
                if(count($info) > 0) {
                    $result[$i] = array_merge($result[$i], $info);
                }
            }
            json("001", $result);
        }
    }

    /**
     * 黑名单检查
     *
     * @param int    $b 质量码
     * @param string $c 校验码
     */
    public function checkAuthenticity($b, $c) {
        if(true) {
            return;  //TODO:不给前端开放
        }
        //json("001", Authenticity::check($b, $c));
    }
}
