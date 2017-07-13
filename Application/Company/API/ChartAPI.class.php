<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/9/5
 * Time: 10:18
 */

namespace Company\API;


class ChartAPI
{
    /**
     * 企业购码数量按天统计201695
     * @param $companyid
     * @return array
     */
    public static function boughtCounts($companyid)
    {
        $basedata = M("BaseQrcodeCreatedLog");
        $con = [
            'companyid' => $companyid
        ];
        $fields = "sum(qrcode_counts) as qrcode_bought_count,FROM_UNIXTIME(operate_time,'%Y-%m-%d') as bought_time";
        $nums = $basedata->where($con)->field($fields)->group('bought_time')->select();

        if ($nums == null) {
            $result['qrcode_bought_count'] = strval(0);
        }
        foreach($nums as $value){
            $re['qrcode_bought_count'] = $value['qrcode_bought_count'];
            $re['bought_time'] = $value['bought_time'];
            $result[] = $re;
        }

        return $result;
    }


    /**
     * 企业生码数量按天统计201695
     * @param $companyid
     * @return array
     */
    public static function usedCounts($companyid){
        $basedata = M("BaseQrcodeUsedLog");
        $con = [
            'companyid' => $companyid
        ];
        $fields = "sum(qrcode_counts) as qrcode_used_counts,FROM_UNIXTIME(operate_time,'%Y-%m-%d') as used_time";
        $nums = $basedata->where($con)->field($fields)->group('used_time')->select();

        if($nums == null){
            $result['qrcode_used_counts'] = strval(0);
        }
        foreach($nums as $value){
            $re['qrcode_used_counts'] = $value['qrcode_used_counts'];
            $re['used_time'] = $value['used_time'];
            $result[] = $re;
        }
        return $result;
    }

    /**
     * 企业已关联码数量按天统计201695
     * @param $companyid
     * @return array
     */
    public static function joinedCounts($companyid){
        $basedata = M("CorrQrcodeProduct");
        $con = [
            'company_id' => $companyid
        ];
        $fields = "from_unixtime(create_time,'%Y-%m-%d') as joined_time,sum(qrcode_range_e-qrcode_range_s+1) as qrcode_joined_counts";
        $nums = $basedata->where($con)->field($fields)->group('joined_time')->select();
        if($nums == null){
            $result['qrcode_joined_counts'] = strval(0);
        }
        foreach($nums as $value){
            $re['qrcode_joined_counts'] = $value['qrcode_joined_counts'];
            $re['joined_time'] = $value['joined_time'];
            $result[] = $re;
        }
        return $result;
    }

    /**
     * 产品用码量按天统计201698
     * @param $companyid
     * @param $productid
     * @return array
     */
    public static function projoinedCounts($companyid,$productid){
        $basedata = M("CorrQrcodeProduct");
        if($productid == ''){
            $con = [
                'company_id' => $companyid
            ];
        }
        if($productid != ''){
            $con = [
                'company_id' => $companyid,
                'product_id' => $productid
            ];
        }

        $fields = "from_unixtime(create_time,'%Y-%m-%d') as joined_time,sum(qrcode_range_e-qrcode_range_s+1) as qrcode_joined_counts";
        $nums = $basedata->where($con)->field($fields)->group('joined_time')->select();
        if($nums == null){
            $result['qrcode_joined_counts'] = strval(0);
        }
        foreach($nums as $value){
            $re['qrcode_joined_counts'] = $value['qrcode_joined_counts'];
            $re['joined_time'] = $value['joined_time'];
            $result[] = $re;
        }
        return $result;
    }

    /**
     * 企业举报数据按天统计201695
     * @param $companyid
     * @param $productid
     * @return array
     */
    public static function tipoffCountsBar($companyid,$productid){
        $model = M();
        $sql = "select b,substring(from_unixtime(zm_base_scanreport.create_time,'%Y-%m-%d'),6,2) as tipofftime from zm_base_scanreport where left(b,4) =".$companyid.self::isProduct($productid);
        $nums = $model->query($sql);
        foreach($nums as $value){
            $re[] = $value['tipofftime'];
            $result = array_count_values($re);
        }
        foreach($result as $key => $val){
            $num['tipofftime'] = $key;
            $num['counts'] = $val;
            $res[] = $num;
        }
        return $res;
    }


    /**
     * 产品扫码统计饼图201696
     * @param $companyid
     * @param $productid
     * @param $startdate
     * @param $enddate
     * @return mixed
     */
    public static function scanedCountsPie($companyid,$productid,$startdate,$enddate){
        $model = M();
        $sql = "select left(first_address,3) as address,sum(scan_count) as counts from zm_base_scaninfo where companyid = ".$companyid.self::isProduct($productid). self::scanScop($startdate,$enddate) ." group by address";
        $res = $model->query($sql);
        return $res;
    }

    /**
     * 产品扫码统计柱图201696
     * @param $companyid
     * @param $productid
     * @param $startdate
     * @param $enddate
     * @return mixed
     */
    public static function scanedCountsBar($companyid,$productid,$startdate,$enddate){
        $model = M();
        $sql = "select substring(from_unixtime(first_time,'%Y-%m-%d'),6,2) as scantime,sum(scan_count) as counts from zm_base_scaninfo where companyid = ".$companyid.self::isProduct($productid).self::scanScop($startdate,$enddate) ." group by scantime";

        $res = $model->query($sql);
        return $res;
    }


    /**
     * 按照时间列出扫码的前n个地域 统计柱图201699
     * @param $companyid
     * @param $productid
     * @param $startdate
     * @param $enddate
     * @return mixed
     */
    public static function scanedCountsBars($companyid,$productid,$startdate,$enddate){
        $model = M();
        $sql = "select from_unixtime(first_time,'%Y-%m-%d') as scantime,left(first_address,3) as address,sum(scan_count) as counts from zm_base_scaninfo where companyid=".$companyid.self::isProduct($productid).self::scanScop($startdate,$enddate)." group by scantime,address order by scantime,counts desc";
        $res = $model->query($sql);
        return $res;
    }

    public static function scanedCountsTop($companyid,$productid,$startdate,$enddate)
    {
        $model = M();
        $sql = "select from_unixtime(first_time,'%Y-%m-%d') as scantime,left(first_address,3) as address,sum(scan_count) as counts
              from zm_base_scaninfo where companyid=".$companyid.self::isProduct($productid).self::scanScop($startdate,$enddate).
            " group by scantime,address having address!='' order by counts desc";
        $res = $model->query($sql);

        $list=array_column($res, 'scantime');
        $map = [];
        foreach ($res as $key)
        {
            $scantime=$key['scantime'];
            if ($map[$scantime])
            {
                //按照扫码次数选择前三个地域
                if(count($map[$scantime])>=2)
                {
                    continue;
                }
                else
                {
                    $map[$scantime][]=array
                    (
                        "address" =>$key['address'],
                        "counts" =>$key['counts']
                    );
                }
            }
            else
            {
                $map[$scantime] = [];
                $map[$scantime][] = array
                (
                    "address" =>$key['address'],
                    "counts" =>$key['counts']
                );
            }
        }
        return $map;
    }


    /**
     * 产品扫码统计时间范围
     * @param $startdate
     * @param $enddate
     * @return string
     */
    static function scanScop($startdate,$enddate){
        $where = "";
        if (strlen($startdate) > 0)
        {
            $starttime = strtotime($startdate);
        }
        if (strlen($enddate) > 0)
        {
            $endtime = strtotime($enddate);
        }

        if ($starttime > 0 || $endtime > 0)
        {
            if (($starttime > 0 && $endtime > 0) && $starttime > $endtime)//如果起始时间大于结束时间，互换
            {
                $tmptime = $starttime;
                $starttime = $endtime;
                $endtime = $tmptime;
            }

            if ($starttime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_base_scaninfo`.first_time>" . strtotime(date('Y-m-d', $starttime));
            }
            if ($endtime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_base_scaninfo`.first_time<" . (strtotime(date('Y-m-d', $endtime)) + 86400);
            }
        }
        if ($where != "")
        {
            $where = " AND " . $where;
        }

        return $where;
    }

    /**
     * 可选参数：产品201698
     * @param $productid
     * @return string
     */
    static function isProduct($productid){
        $map = '';
        if($productid !== ''){
            $map = ' AND productid = '.$productid;
        }
        return $map;
    }

    /**
     * 获取企业基本信息201699
     * @param $companyid
     * @return mixed
     */
    static function getCompanyInfo($companyid){
        $model = M("BaseCompany");
        $fields = 'longitude,latitude,name';
        $map = [
            'id' => $companyid
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