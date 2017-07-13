<?php
/**
 * Created by PhpStorm.
 * User: Liming
 * Date: 2016/4/22
 * Time: 14:48
 */

namespace Faker\API;

/** 产品真伪信息
 * Class Authenticity
 *
 * @package Faker\API
 */
class Authenticity
{
    /**
     * 真伪检查
     *
     * @param int    $b 质量码
     * @param string $c 二维码校验
     *
     * @return mixed 真伪信息
     */
    private static function check($b, $c)
    {
        $result = [];
        //TODO: 检查真伪
        $fake = false;  //TODO: 是否为假货
        $company = null;  //TODO: 公司ID
        $scanCount = 0;  //TODO: 扫描次数
        //检查黑名单
        $black = self::blackList($b);
        //返回
        $result['code'] = ($fake ? 1 : 0) + ($black ? 2 : 0);
        //是否为假货
        $result['fake'] = $fake;
        //是否在黑名单中
        $result['blackList'] = $black;
        //加载假货提示
        if ($result['code'] > 0)
        {
            if ($company !== null)
            {
                //匹配到公司
                $model = M('base_company');
                $config = $model->where(['id' => $company])->find();
                //警告信息
                $result['message'] = [
                    'scan_tip' => $config['scan_tip'],
                    'scan_tel' => $config['scan_tel']
                ];
                if ($scanCount > $config['scan_counts2'])
                {
                    //二级扫描警告
                    $result['code'] += 8;
                }
                else if ($scanCount > $config['scan_counts'])
                {
                    //一级扫描警告
                    $result['code'] += 4;
                }
            }
            else
            {
                //默认提示
                $result['message'] = [
                    'scan_tip' => '假货！',
                    'scan_tel' => ''
                ];
            }
        }

        return $result;
    }

    /**
     * 黑名单判断
     *
     * @param int $b 质量码
     *
     * @return bool 是否为黑名单码
     */
    public static function blackList($b)
    {
        $model = M('base_qrcode_spam');
        $count = $model->where(['b' => $b, 'status' => 1])->count();

        return $count > 0;
    }

    /**
     * 码召回判断
     * @param $b
     *
     * @return bool
     */
    public static function recallList($b)
    {
        $model = M('base_recall');
        $count = $model->where("qrcode_range_s<=" . $b . " and qrcode_range_e>=" . $b)->count();

        return $count > 0;
    }

    /**
     * 码召回判断
     * @param $b
     *
     * @return bool
     */
    public static function recallReason($b)
    {
        $model = M('base_recall');
        $reason = $model->where("qrcode_range_s<=" . $b . " and qrcode_range_e>=" . $b)->getField("reason");
        return $reason;
    }
}
