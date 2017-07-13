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
     * 查找政府管辖的企业2016827
     * @return mixed
     */
    public static function searchCorr(){
        $governmentid = $_SESSION['user_info']['id'];
//        $governmentid = 1;
        $model = M("CorrGovernmentCompany");
        $map = [
            'government_id' => $governmentid
        ];
        $fields = 'company_id';
        $res = $model->where($map)->field($fields)->select();
        return $res;
    }

    /**
     * 政府管辖企业的产品数量2016827
     * @return array
     */
    public static function productCounts(){
        $ids = self::searchCorr();
        for($i=0;$i<count($ids);$i++){
//            $companyid = $ids[$i]['company_id'];
//            $model = M("BaseCompanyData");
//            $map['company_id'] = $companyid;
//            $fields = 'name as companyname,product_count';
//            $res = $model->where($map)->field($fields)->join('zm_base_company ON zm_base_company.id = zm_base_company_data.company_id')->find();
//            if(!empty($res)){
//                $result[]=$res;
//            }
            $companyid = $ids[$i]['company_id'];
            $base = M("BaseCompany");
            $map = [
                'id' => $companyid
            ];
            $re = $base->where($map)->getField('name as companyname');
            $basedata = M("BaseCompanyData");
            $con = [
                'company_id' => $companyid
            ];
            $nums = $basedata->where($con)->getField('product_count');
            if($nums == null){
                $nums = strval(0);
            }
            $result[$i]['companyname'] = $re;
            $result[$i]['product_count'] = $nums;
        }
        return $result;
    }

    /**
     * 政府管辖企业的购买数量2016827
     * @return array
     */
    public static function qrcodeBoughtCounts(){
        $ids = self::searchCorr();
        for($i=0;$i<count($ids);$i++){
            $companyid = $ids[$i]['company_id'];
            $base = M("BaseCompany");
            $map = [
                'id' => $companyid
            ];
            $re = $base->where($map)->getField('name as companyname');
            $basedata = M("BaseCompanyData");
            $con = [
                'company_id' => $companyid
            ];
            $nums = $basedata->where($con)->getField('qrcode_bought_count');
            if($nums == null){
                $nums = strval(0);
            }
            $result[$i]['companyname'] = $re;
            $result[$i]['qrcode_bought_count'] = $nums;
            }
        return $result;
    }

    /**
     * 政府管辖企业的使用码数量2016827
     * @return array
     */
    public static function qrcodeUsedCounts(){
        $ids = self::searchCorr();
        $result = self::qrcodeBoughtCounts();
        for($i=0;$i<count($ids);$i++){
            $companyid = $ids[$i]['company_id'];
            $basedata = M("BaseCompanyData");
            $con = [
                'company_id' => $companyid
            ];
            $nums = $basedata->where($con)->getField('qrcode_used_counts');
            if($nums == null){
                $nums = strval(0);
            }
            $result[$i]['qrcode_used_counts'] = $nums;
        }
        return $result;
    }

    /**
     * 政府管辖企业的已关联码数量2016827
     * @return array
     */
    public static function qrcodeJoinedCounts(){
        $ids = self::searchCorr();
        $result = self::qrcodeUsedCounts();
        for($i=0;$i<count($ids);$i++){
            $companyid = $ids[$i]['company_id'];
            $basedata = M("BaseCompanyData");
            $con = [
                'company_id' => $companyid
            ];
            $nums = $basedata->where($con)->getField('qrcode_joined_counts');
            if($nums == null){
                $nums = strval(0);
            }
            $result[$i]['qrcode_joined_counts'] = $nums;
        }
        return $result;
    }

    /**
     * 政府管辖企业的用码统计（购码/已生成/关联）2016827
     * @return array
     */
    public static function qrcodeCounts(){
        $result = self::qrcodeJoinedCounts();
        return $result;
    }

    /**
     * 政府管辖企业的扫描数量2016827
     * @return array
     */
    public static function qrcodeScanedCounts(){
        $ids = self::searchCorr();
        for($i=0;$i<count($ids);$i++){
            $companyid = $ids[$i]['company_id'];
            $base = M("BaseCompany");
            $map = [
                'id' => $companyid
            ];
            $re = $base->where($map)->getField('name as companyname');
            $basedata = M("BaseCompanyData");
            $con = [
                'company_id' => $companyid
            ];
            $nums = $basedata->where($con)->getField('qrcode_scaned_counts');
            if($nums == null){
                $nums = strval(0);
            }
            $result[$i]['companyname'] = $re;
            $result[$i]['qrcode_scaned_counts'] = $nums;
        }
        return $result;
    }

    /**
     * 政府管辖企业的举报数量2016827
     * @return array
     */
    public static function qrcodeTipoffCounts(){
        $ids = self::searchCorr();
        for($i=0;$i<count($ids);$i++){
            $companyid = $ids[$i]['company_id'];
            $base = M("BaseCompany");
            $map = [
                'id' => $companyid
            ];
            $re = $base->where($map)->getField('name as companyname');
            $basedata = M("BaseCompanyData");
            $con = [
                'company_id' => $companyid
            ];
            $nums = $basedata->where($con)->getField('qrcode_tipoff_counts');
            if($nums == null){
                $nums = strval(0);
            }
            $result[$i]['companyname'] = $re;
            $result[$i]['qrcode_tipoff_counts'] = $nums;
        }
        return $result;
    }

    /**
     * 政府管辖企业的入库码数量2016827
     * @return array
     */
    public static function qrcodeCheckinCounts(){
        $ids = self::searchCorr();
        for($i=0;$i<count($ids);$i++){
            $companyid = $ids[$i]['company_id'];
            $base = M("BaseCompany");
            $map = [
                'id' => $companyid
            ];
            $re = $base->where($map)->getField('name as companyname');
            $basedata = M("BaseCompanyData");
            $con = [
                'company_id' => $companyid
            ];
            $nums = $basedata->where($con)->getField('qrcode_checkin_counts');
            if($nums == null){
                $nums = strval(0);
            }
            $result[$i]['companyname'] = $re;
            $result[$i]['qrcode_checkin_counts'] = $nums;
        }
        return $result;
    }

    /**
     * 政府管辖企业的出库码数量2016827
     * @return array
     */
    public static function qrcodeCheckoutCounts(){
        $ids = self::searchCorr();
        $result = self::qrcodeCheckinCounts();
        for($i=0;$i<count($ids);$i++){
            $companyid = $ids[$i]['company_id'];
            $basedata = M("BaseCompanyData");
            $con = [
                'company_id' => $companyid
            ];
            $nums = $basedata->where($con)->getField('qrcode_checkout_counts');
            if($nums == null){
                $nums = strval(0);
            }
            $result[$i]['qrcode_checkout_counts'] = $nums;
        }
        return $result;
    }

    /**
     * 政府管辖企业出入库统计2016827
     * @return array
     */
    public static function qrcodeCheck(){
        $result = self::qrcodeCheckoutCounts();
        return $result;
    }

    /**
     * 每个企业的入库按天统计信息2016825
     * @param $companyid
     * @return int
     */
    public static function checkin($companyid){
        ini_set('mongo.long_as_object', 1);

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
            if (is_object($item['p'])) $item['p'] = $item['p']->value;
            if (is_object($item['create_time'])) $item['create_time'] = $item['create_time']->value;
            $temp[]= date("Y-m-d",$item['create_time']);
            $result=array_count_values($temp);
        }

        ini_set('mongo.long_as_object', 0);

        if (empty($result)) return 0;
        return $result;

    }

    /**
     * 每个企业的出库按天统计信息2016825
     * @param $companyid
     * @return int
     */
    public static function checkout($companyid){
        ini_set('mongo.long_as_object', 1);

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

            if (is_object($item['p'])) $item['p'] = $item['p']->value;
            if (is_object($item['create_time'])) $item['create_time'] = $item['create_time']->value;
            $temp[]= date("Y-m-d",$item['create_time']);
            $result=array_count_values($temp);
        }
        ini_set('mongo.long_as_object', 0);

        if (empty($result)) return 0;
        return $result;
    }

    /**
     * 企业出入库每天统计2016827
     * @param $companyid
     * @return mixed
     */
    public static function check($companyid){
        $checkin = self::checkin($companyid);
        $timein = array_keys($checkin);
        $checkout = self::checkout($companyid);
        $timeout = array_keys($checkout);
        if($timein == null && $timeout != null){
            $time = $timeout;
            $new =  array_unique($time);
            sort($new);
            for($i=0;$i<count($new);$i++) {
                if($checkin == 0){
                    $checkindata = 0 ;
                }
                $res[$i]['time'] = $new[$i];
                $res[$i]['checkin'] = $checkindata;
                $res[$i]['checkout'] = $checkout[$new[$i]];
            }
            return $res;
        }
        if($timeout == null && $timein != null){
            $time = $timein;
            $new =  array_unique($time);
            sort($new);
            for($i=0;$i<count($new);$i++) {
                if($checkout == 0){
                    $checkoutdata = 0 ;
                }
                $res[$i]['time'] = $new[$i];
                $res[$i]['checkin'] = $checkin[$new[$i]];
                $res[$i]['checkout'] = $checkoutdata;
            }
            return $res;
        }
        if($timein != null && $timeout != null){
            $time = array_merge($timein,$timeout);
            $new =  array_unique($time);
            sort($new);
            for($i=0;$i<count($new);$i++) {
                if($checkin[$new[$i]] == null){
                    $checkin[$new[$i]] = 0 ;
                }
                if($checkout[$new[$i]] == null){
                    $checkout[$new[$i]] =0 ;
                }
                $res[$i]['time'] = $new[$i];
                $res[$i]['checkin'] = $checkin[$new[$i]];
                $res[$i]['checkout'] = $checkout[$new[$i]];
            }
            return $res;
        }
        if($timein == null && $timeout == null){
            $res['time'] = 0;
            $res['checkin'] = 0;
            $res['checkout'] = 0;
            return $res;
        }
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
        $model = MM('base_scanout',"zm_");
        if (!is_string($companyid)) $companyid = strval($companyid);
        $map = [
            'companyid' => $companyid,
            'outtype' => 1
        ];
        $fields = 'destinationid';
        $data = $model->where($map)->field($fields)->select();
        $temp=array();
        foreach ($data as $item)
        {
            $temp[]=$item['destinationid'];
            $result=array_count_values($temp);
        }

        if (empty($result))
            return 0;
        $condAgent=array_keys($result);
        $agent = M("BaseAgentArea");
        $where = [
            'agent_id' => array('in',$condAgent),
            'companyid' => $companyid
        ];
        $fields = 'agent_id,district';
        $res = $agent->field($fields)->where($where)->select();

        foreach($res as $value){
            $arr[] = $value['district'];
            $id[] = $value['agent_id'];
        }

        $region = M("DictRegion");
        $cond = [
            'region_id' => array('in',$arr),
        ];
        $fields = 'zm_dict_region.center_longitude,zm_dict_region.center_latitude';
        $re = $region->where($cond)->field($fields)->select();
        $agentmodel = M("BaseAgent");
        $condition = [
            'id' => array("in",$id)
        ];
        $fields = 'agent_name,id';
        $rr = $agentmodel->where($condition)->field($fields)->select();
        for($i=0;$i<count($rr);$i++){
            $re[$i]['agent_name'] = $rr[$i]['agent_name'];
            $re[$i]['agent_id']=$rr[$i]['id'];
            $re[$i]['count']=$result[$rr[$i]['id']];
        }
        return $re;
    }


}