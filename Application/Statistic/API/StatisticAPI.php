<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/25
 * Time: 11:08
 */

namespace Statistic\API;


class GovernmentAPI
{
    /**
     * 查找政府管辖的企业2016825
     * @param $governmentid
     * @return mixed
     */
    public static function searchCorr($governmentid){
        $model = M("CorrGovernmentCompany");
        $map = [
            'government_id' => $governmentid
        ];
        $fields = 'company_id';
        $res = $model->where($map)->field($fields)->select();
        return $res;
    }

    /**
     * 政府管辖企业的统计数据2016825
     * @param $companyid
     * @return mixed
     */
    public static function allInfo($companyid){
        $model = M("BaseCompanyData");
        $map['company_id'] = $companyid;

        $res = $model->where($map)->find();
        return $res;
    }

    /**
     * 政府管辖企业的产品数量2016825
     * @param $companyid
     * @return mixed
     */
    public static function productCounts($companyid){
        $model = M("BaseCompanyData");
        $map['company_id'] = $companyid;

        $res = $model->where($map)->getField('product_count');
        return $res;
    }

    /**
     * 政府管辖企业的购买数量2016825
     * @param $companyid
     * @return mixed
     */
    public static function qrcodeBoughtCounts($companyid){
        $model = M("BaseCompanyData");
        $map['company_id'] = $companyid;

        $res = $model->where($map)->getField('qrcode_bought_count');
        return $res;
    }

    /**
     * 政府管辖企业的使用码数量2016825
     * @param $companyid
     * @return mixed
     */
    public static function qrcodeUsedCounts($companyid){
        $model = M("BaseCompanyData");
        $map['company_id'] = $companyid;

        $res = $model->where($map)->getField('qrcode_used_counts');
        return $res;
    }

    /**
     * 政府管辖企业的已关联码数量2016825
     * @param $companyid
     * @return mixed
     */
    public static function qrcodeJoinedCounts($companyid){
        $model = M("BaseCompanyData");
        $map['company_id'] = $companyid;

        $res = $model->where($map)->getField('qrcode_joined_counts');
        return $res;
    }

    /**
     * 政府管辖企业的入库码数量2016825
     * @param $companyid
     * @return mixed
     */
    public static function qrcodeCheckinCounts($companyid){
        $model = M("BaseCompanyData");
        $map['company_id'] = $companyid;

        $res = $model->where($map)->getField('qrcode_checkin_counts');
        return $res;
    }

    /**
     * 政府管辖企业的出库码数量2016825
     * @param $companyid
     * @return mixed
     */
    public static function qrcodeCheckoutCounts($companyid){
        $model = M("BaseCompanyData");
        $map['company_id'] = $companyid;

        $res = $model->where($map)->getField('qrcode_checkout_counts');
        return $res;
    }

    /**
     * 政府管辖企业的扫描数量2016825
     * @param $companyid
     * @return mixed
     */
    public static function qrcodeScanedCounts($companyid){
        $model = M("BaseCompanyData");
        $map['company_id'] = $companyid;

        $res = $model->where($map)->getField('qrcode_scaned_counts');
        return $res;
    }

    /**
     * 政府管辖企业的举报数量2016825
     * @param $companyid
     * @return mixed
     */
    public static function qrcodeTipoffCounts($companyid){
        $model = M("BaseCompanyData");
        $map['company_id'] = $companyid;

        $res = $model->where($map)->getField('qrcode_tipoff_counts');
        return $res;
    }

    /**
     * 每个企业的入库按天统计信息2016825
     * @param $companyid
     * @return int
     */
    public static function checkin($companyid){
        //  ini_set('mongo.long_as_object', 1);

        $model = MM('base_scanin',"zm_");
        if (!is_string($companyid)) $companyid = strval($companyid);
        $start_time = time()-1209600;
        $map = [
            'companyid' => $companyid,
            'create_time' => array('egt',$start_time)
        ];
        $fields = 'p,create_time';
        $data = $model->where($map)->field($fields)->order('create_time desc')->select();

        $temp=array();
        foreach ($data as $item)
        {
            //   if (is_object($item['p'])) $item['p'] = $item['p']->value;
            //    if (is_object($item['create_time'])) $item['create_time'] = $item['create_time']->value;
            $temp[]= date("Y-m-d",$item['create_time']);
            $result=array_count_values($temp);
        }
        //   ini_set('mongo.long_as_object', 0);
        if (empty($result)) return 0;
        return $result;

    }

    /**
     * 每个企业的出库按天统计信息2016825
     * @param $companyid
     * @return int
     */
    public static function checkout($companyid){
        // ini_set('mongo.long_as_object', 1);

        $model = MM('base_scanout',"zm_");
        if (!is_string($companyid)) $companyid = strval($companyid);
        $start_time = time()-1209600;
        $map = [
            'companyid' => $companyid,
            'create_time' => array('egt',$start_time)
        ];
        $fields = 'p,create_time';
        $data = $model->where($map)->field($fields)->order('create_time desc')->select();

        foreach ($data as $item)
        {

            //   if (is_object($item['p'])) $item['p'] = $item['p']->value;
            //   if (is_object($item['create_time'])) $item['create_time'] = $item['create_time']->value;
            $temp[]= date("Y-m-d",$item['create_time']);
            $result=array_count_values($temp);
        }
        //   ini_set('mongo.long_as_object', 0);
        if (empty($result)) return 0;

        return $result;
    }

    /**
     * 企业的统计信息2016826
     * @param $companyid
     * @return mixed
     */
    public static function companyInfo($companyid){
        $model = M("BaseCompanyData");
        $fields = 'qrcode_bought_count,qrcode_used_counts,qrcode_joined_counts';
        $map = [
            'company_id' => $companyid
        ];
        $res = $model->field($fields)->where($map)->find();
        return $res;
    }

    /**
     * 出库的经纬度信息2016826
     * @param $companyid
     * @return int
     */
    public static function checkoutArea($companyid){
//        ini_set('mongo.long_as_object', 1);

        $model = MM('base_scanout',"zm_");
        if (!is_string($companyid)) $companyid = strval($companyid);
        $map = [
            'companyid' => $companyid,
            'outtype' => 1
        ];
        $fields = 'destinationid';
        $data = $model->where($map)->field($fields)->select();

        foreach ($data as $item)
        {
//            if (is_object($item['p'])) $item['p'] = $item['p']->value;
            $result[] = $item['destinationid'];
            $result =  array_flip(array_flip($result));
            sort($result);
        }

//        ini_set('mongo.long_as_object', 0);

        if (empty($result)) return 0;
        $agent = M("BaseAgentArea");
        $where = [
            'agent_id' => array('in',$result),
            'companyid' => $companyid
        ];
        $fields = 'district';
        $res = $agent->field($fields)->where($where)->select();
        foreach($res as $value){
            $arr[] = $value['district'];
        }
        $region = M("DictRegion");
        $cond = [
            'region_id' => array('in',$arr),
        ];
        $fields = 'zm_dict_region.center_longitude,zm_dict_region.center_latitude';
        $re = $region->where($cond)->field($fields)->select();
        return $re;
    }

}