<?php
/**
 * Created by PhpStorm.
 * User: Liming
 * Date: 2016/5/7
 * Time: 16:20
 */

namespace Fleeing\Controller;
use Common\Controller\BaseController;
use CheckIO\API\CheckioAPI;
use Faker\API\Report;
use Fleeing\API\Fleeing;
use Common\API\MatchtimeAPI;
/**
 * 窜货数据
 * Class FleeingController
 * @package Fleeing\Controller
 */
class FleeingController extends BaseController{
    /**
     * 获取窜货数据
     */
    public function get() {
        $model = M('base_beyond_areas');
        $result = $model->where(['company_id' => session('user_info')['companyid']])->select();
        if($result === false) {
            json("007");
            return;
        } else {
            $records = array();
            $model = M('base_agent');
            $modelFlee=M('dict_beyond_areas');
            $fleeAddress=$modelFlee->where()->select();
            foreach ($result as $key=>$item){
                $productinfo=CheckioAPI::productInfo($item['b']);
                //获取箱码信息
                $p=getPcodeByXcode($item['b']);
                //根据箱码得到出库信息
                $data=CheckioAPI::listCheckoutbyPack(['p'=>floatval($p)]);
                $data = $data[count($data) - 1];
                //获得经销商信息
                $agent = $model->where(['id' => $data['destinationid']])->find();
                if($productinfo!=null){
                    $record = array(
                        'id'=>$item['id'],
                        'b'=>$item["b"],
                        'ip'  => $item['ip'],
                        "product_name" => $productinfo['name'],
                        'spec'=>$productinfo['spec'],
                        'product_image'=>$productinfo['image'],
                        'ipaddr'=> $item['ipaddr'],
                        'create_time'=> $item['create_time'],
                        'lon' => $item['lon'],
                        'lat'=>$item['lat'],
                        'address'=>$item['address'],
                        'beyondareas_id' => $fleeAddress[$item['beyondareas_id']-1]['beyond_areas_type'],
                        'agent'=>$agent['agent_name']
                    );
                    array_push($records, $record);
                }
            }
            unset($record);

            json("001", $records);
        }
    }
    /*
     * 分析窜货数据
     */
    public function analyze(){

        $b=I('post.b', null);
        $longitude=I('post.longitude', null);
        $latitude=I('post.latitude', null);
        $location=I('post.location', null);
        $response=Fleeing::check($b, $longitude, $latitude, $location);
    }

    /**
     * 按时间查询串货数据2016822
     */
    public function Searchget(){
        $starttime = I('startdate',null);
        $endtime = I('enddate',null);

        $re = MatchtimeAPI::checkDate($starttime,$endtime);
        if($re === false){
            return;
        }

        $companyid = session('user_info')['companyid'];

        if($starttime && $endtime){
            $map = array(
                'company_id'=>$companyid,
                'create_time' => array('between',array($re[1][0],$re[1][1]))
            );
        }elseif($starttime && empty($endtime)){
            $map = array(
                'company_id'=>$companyid,
                'create_time' => array('egt',$re[1])
            );
        }elseif(empty($starttime) && $endtime){
            $map = array(
                'company_id'=>$companyid,
                'create_time' => array('elt',$re[1])
            );
        }elseif($starttime == null && $endtime == null){
            $map = array(
                'company_id'=>$companyid,
            );
        }

        $model = M('base_beyond_areas');
        $result = $model->where($map)->select();

        if($result === false) {
            json("007");
            return;
        } else {
            $records = array();
            $model = M('base_agent');
            $modelFlee=M('dict_beyond_areas');
            $fleeAddress=$modelFlee->where()->select();
            foreach ($result as $key=>$item){
                $productinfo=CheckioAPI::productInfo($item['b']);
                //获取箱码信息
                $p=getPcodeByXcode($item['b']);
                //根据箱码得到出库信息
                $data=CheckioAPI::listCheckoutbyPack(['p'=>floatval($p)]);
                $data = $data[count($data) - 1];
                //获得经销商信息
                $agent = $model->where(['id' => $data['destinationid']])->find();
                if($productinfo!=null){
                    $record = array(
                        'id'=>$item['id'],
                        'b'=>$item["b"],
                        'ip'  => $item['ip'],
                        "product_name" => $productinfo['name'],
                        'spec'=>$productinfo['spec'],
                        'product_image'=>$productinfo['image'],
                        'ipaddr'=> $item['ipaddr'],
                        'create_time'=> $item['create_time'],
                        'lon' => $item['lon'],
                        'lat'=>$item['lat'],
                        'address'=>$item['address'],
                        'beyondareas_id' => $fleeAddress[$item['beyondareas_id']-1]['beyond_areas_type'],
                        'agent'=>$agent['agent_name']
                    );
                    array_push($records, $record);
                }
            }
            unset($record);

            json("001", $records);
        }
    }
}
