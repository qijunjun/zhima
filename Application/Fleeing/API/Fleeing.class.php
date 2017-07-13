<?php
/**
 * Created by PhpStorm.
 * User: Liming
 * Date: 2016/5/7
 * Time: 11:43
 */

namespace Fleeing\API;

use CheckIO\API\CheckioAPI;

/**
 * 窜货API
 * Class Fleeing
 * @package Fleeing\API
 */
class Fleeing {
    /**
     * 窜货处理
     *
     * @param int    $b         瓶码
     * @param float  $longitude [经度]
     * @param float  $latitude  [纬度]
     * @param string $location  [地理位置]
     */
    public static function check($b, $longitude = null, $latitude = null, $location = null) {

        $ip = getIpLocation();
        //得到箱码信息
        $p=getPcodeByXcode($b);
        if($p==null){
            //瓶码与箱码未关联
            self::save($b, $ip, $longitude, $latitude, $location, 4);
            json('006','瓶码与箱码未关联');
            return;
        }
        //根据瓶码得到出库信息
        $data=CheckioAPI::listCheckoutbyPack(['p'=>floatval($p)]);
        if(count($data) <= 0) {
            //没有出库信息
            self::save($b, $ip, $longitude, $latitude, $location, 1);
            json('006',null,'没有出库信息');
            return;
        }
        $data = $data[count($data) - 1];
        if(intval($data['outtype']) != 1) {
            //没有出库到经销商
            self::save($b, $ip, $longitude, $latitude, $location, 2);
            json('006',null,'没有出库到经销商');
            return;
        }
        $model = M('base_agent');
        $agent = $model->where(['id' => $data['destinationid']])->find(); //得到经销商的信息
        if($agent === false) {
            //TODO: 经销商查找失败
            json('002',null,'经销商查找失败');
            return;
        }
        $agentAreas=$agent['agent_area'];
        if($agentAreas==null){
            return;
        }
        $agentAreas=rtrim($agentAreas, ',');
        $areas=explode(",",$agentAreas);
        if($location === null) {
            $location = $ip['country'];
        }
        $hit=0;  //如果与经销商代理区域命中则加1
        foreach ($areas as $value){
            $province=self::getProvince($value);
            if(mb_substr($province, 0, 2) == mb_substr($location, 0, 2)) {
                $hit=$hit+1;
            }
        }
        if(!$hit) {
            //省份不相同
            self::save($b, $ip, $longitude, $latitude, $location, 3);
            json('006','省份不相同');
            return;
        }
    }

    /**
     * 窜货数据保存
     *
     * @param int    $b         瓶码
     * @param array  $ipAddress ip所在地
     * @param float  $longitude 经度
     * @param float  $latitude  纬度
     * @param string $location  地理位置
     * @param int    $type      窜货类型
     */
    private static function save($b, $ipAddress, $longitude, $latitude, $location, $type) {
        $model = M('base_beyond_areas');
        $result = $model->create([
            'b'              => $b,
            'ip'             => get_client_ip(0, true),
            'ipaddr'         => "{$ipAddress['country']} {$ipAddress['area']}",
            'create_time'    => time(),
            'lon'            => $longitude,
            'lat'            => $latitude,
            'address'        => $location,
            'beyondareas_id' => $type,
            'company_id'=>(int)substr($b,0,4)
        ]);
        if($result !== false) {
            $model->add();
        }
    }
    private static function getProvince($agentid)
    {
        $model=M('base_agent_area');
        $model1 = M('dict_region');
        $cond=array(
            'agent_id'=>$agentid
        );
        $province=$model->where($cond)->field("province")->find();
        $province=$province['province'];
        $provincename = $model1->where(['region_id' => $province])->find();
        return $provincename['region_name'];
    }
}
