<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/15
 * Time: 15:01
 */

namespace Company\Controller;

use Common\Controller\BaseController;
use Company\API\AptitudeAPI;
class AptitudeController extends BaseController
{
    /**
     * 添加企业资质信息2016815
     */
    public function add(){
        $companyid = $_SESSION['user_info']['companyid'];
        $aptitudename = I('aptitudeName');
        $aptitudecode = I('aptitudeCode');
//        $aptitudetype = I('aptitudeType');
        $authorizer = I('license');
        $create_time = I('releaseTime');
        $validity_time = I('indate');
        $range = I('permissionScope');
        $img1 = I('certificate',null);
        $img2 = I('certificate2',null);
        $img3 = I('certificate3',null);
        $img4 = I('certificate4',null);

        $data = [
            'companyid' => $companyid,
            'aptitudename' => $aptitudename,
            'aptitudecode' => $aptitudecode,
//            'aptitudetype' => $aptitudetype,
            'authorizer' => $authorizer,
            'create_time' => $create_time,
            'validity_time' => $validity_time,
            'range' => $range,
            'aptitudeimage1' => $img1,
            'aptitudeimage2' => $img2,
            'aptitudeimage3' => $img3,
            'aptitudeimage4' => $img4
        ];

        $name = $data['aptitudename'];
        $res = AptitudeAPI::searchRepeat($name);
        if($res > 0){
            json("009",null,"您的资质已提交");
            return false;
        }
        $result = AptitudeAPI::add($data);
        if($result){
            json("001",$result,"添加资质成功");
        }else{
            json("002");
        }
    }

    /**
     * 更新企业资质信息2016815
     */
    public function update(){
        $id = I('id');
        $data = [
            'aptitudename' => I('aptitudeName'),
            'aptitudecode' => I('aptitudeCode'),
//            'aptitudetype' => I('aptitudeType'),
            'authorizer' => I('license'),
            'create_time' => I('releaseTime'),
            'validity_time' => I('indate'),
            'range' => I('permissionScope'),
            'aptitudeimage1' => I('certificate',null),
            'aptitudeimage2' => I('certificate2',null),
            'aptitudeimage3' => I('certificate3',null),
            'aptitudeimage4' => I('certificate4',null)
        ];
        $result = AptitudeAPI::update($id,$data);
        if($result){
            json("001",$result,"更新成功");
        }else{
            json("002");
        }
    }

    /**
     * 编辑企业资质信息2016815
     */
    public function edit(){
        $id=I('id');
        if(($id!=='')&&($id!==null)){
            $data=AptitudeAPI::edit($id);
            if($data){
                json("001",$data);
            }else{
                json("002");
                return;
            }
        }else{
            json("002");
            return;
        }
    }

    /**
     * 资质信息显示列表2016817
     */
    public function showAll(){
        $result = AptitudeAPI::showAll();
        if($result){
            json("001",$result,"success");
        }
    }

    public function showAllByCompany($companyid = null,$page = 1, $size = 1000){
            $result = AptitudeAPI::showAllByCompany($companyid);
            if($result){
                json("001",$result,"success");
            }
        }

    /**
     * 删除资质信息2016817
     */
    public function del(){
        $id = I('id');
        $result = AptitudeAPI::del($id);
        if($result > 0){
            json("001",$result,"success");
        }elseif($result == 0){
            json("009",null,"删除的数据不存在");
        }else{
            json("002");
        }
    }
}