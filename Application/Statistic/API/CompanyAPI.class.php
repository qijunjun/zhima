<?php
/**
 * Created by PhpStorm.
 * User: Yu
 * Date: 2016/8/24
 * Time: 18:00
 */

namespace Statistic\API;


class CompanyAPI
{
    /**
     * 获取企业的统计数据
     * @param $companyid
     * @return mixed
     */
    public static function fetchInfo($companyid)
    {
        $model = M("BaseCompanyData");
        $map["company_id"] = $companyid;
        return $model->where($map)->find();
    }

    /**
     * 获取产品数量
     * @param $companyid
     * @return mixed
     */
    public static function getProductCounts($companyid)
    {
        $model = M("BaseProduct");
        $map["companyid"] = $companyid;
        return $model->where($map)->count();
    }

    /**
     * 获取购码量
     * @param $companyid
     * @return mixed
     */
    public static function getQrcodeBoughtCounts($companyid)
    {
        $model = M("BaseQrcodeCreatedLog");
        $map["companyid"] = $companyid;
        return $model->where($map)->sum("qrcode_counts");
    }

    /**
     * 获取用码量
     * @param $companyid
     * @return mixed
     */
    public static function getQrcodeUsedCounts($companyid)
    {
        $model = M("BaseQrcodeUsedLog");
        $map["companyid"] = $companyid;
        return $model->where($map)->sum("qrcode_counts");
    }

    /**
     * 获取已关联码量
     * @param $companyid
     * @return mixed
     */
    public static function getQrcodeJoinedCounts($companyid)
    {
        $model = M("CorrQrcodeProduct");
        $map["company_id"] = $companyid;
        return $model->where($map)->sum("qrcode_range_e-qrcode_range_s+1");
    }

    /**
     * 获取入库码量
     * @param $companyid
     * @return int
     */
    public static function getQrcodeCheckinCounts($companyid)
    {
//        ini_set('mongo.long_as_object', 1);

        $model = MM('base_scanin');
        if (!is_string($companyid)) $companyid = strval($companyid);
        $map["companyid"] = $companyid;
        $data = $model->where($map)->getField("p", true);
        foreach ($data as $item)
        {
//            if (is_object($item))$item = $item->value;
            $result[] = $item;
        }
//        ini_set('mongo.long_as_object', 0);
        if (empty($result)) return 0;
        $corrqrcode = M("CorrQrcodePack");
        $filter['qrcode_pack'] = array("in", $result);
        $counts = $corrqrcode->where($filter)->sum("ratio");
        return $counts;

    }

    /**
     * 获取出库码量
     * @param $companyid
     * @return int
     */
    public static function getQrcodeCheckoutCounts($companyid)
    {
//        ini_set('mongo.long_as_object', 1);

        $model = MM('base_scanout');
        if (!is_string($companyid)) $companyid = strval($companyid);
        $map["companyid"] = $companyid;
        $data = $model->where($map)->getField("p", true);
        foreach ($data as $item)
        {
//            if (is_object($item)) $item = $item->value;
            $result[] = $item;
        }
//        ini_set('mongo.long_as_object', 0);
        if (empty($result)) return 0;
        $corrqrcode = M("CorrQrcodePack");
        $filter['qrcode_pack'] = array("in", $result);
        $counts = $corrqrcode->where($filter)->sum("ratio");
        return $counts;

    }

    /**
     * 获取已扫码量
     * @param $companyid
     * @return mixed
     */
    public static function getQrcodeScanedCounts($companyid)
    {
        $model = M("BaseScaninfo");
        $map["companyid"] = $companyid;
        return $model->where($map)->sum("scan_count");
    }

    /**
     * 获取举报数量
     * @param $companyid
     * @return mixed
     */
    public static function getQrcodeTipoffCounts($companyid)
    {
        $model = M("BaseScanreport");
        return $model->where("left(`b`,4)=" . $companyid)->count();
    }

    public static function saveData($data)
    {
        $model = M("BaseCompanyData");
        $map["company_id"] = $data["company_id"];
        if ($model->where($map)->count())
        {
            $model->save($data);
        }
        else
        {
            $model->add($data);
        }
    }
}