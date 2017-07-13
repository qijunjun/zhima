<?php
namespace Process\Controller;

use Common\Controller\BaseController;
use Process\API\ProcessAPI;
use Common\API\CompanyAPI;
class IndexController extends BaseController
{
    public function delete($id=null)
    {
        if ($id === '' || $id == null) {
            json("002",null,'删除失败');
            return;
        }
        $response=ProcessAPI::deleteProcess($id);
        if($response){
            json("001",$response);
        }else{
            json("002",null,'删除失败');
            return;
        }
    }
    /*
     * 获得产品对应的操作
     * $productid 产品id
     */
    public function getOperation($productid){
        if ($productid === '' || $productid == null) {
            json("001",array());
            return;
        }
        $response=ProcessAPI::getOperation($productid);
        if($response){
            json("001",$response);
        }else{
            json("001",null);
            return;
        }
    }
    public function getConfig($productid){
        if ($productid === '' || $productid == null) {
            json("001",array());
            return;
        }
        $response=ProcessAPI::getConfig($productid);
        if($response){
            //数组存放产品对应的环节id
            $config=[];
            foreach ($response as $value)
            {
                $config[]=$value['functionid'];
            }
            json("001",$config);
        }else{
            json("001",null);
            return;
        }
    }
    /*
     * 后台上传生产过程
     */
    public function add(){

        //接收字符串时间转换为日期格式
        $time = I('request.event_time',null);
        $event_time = ProcessAPI::strtime($time);
        $data = [
            'companyid' => $_SESSION["user_info"]["companyid"],
            'productid'=>I('request.productid', null),
            'function_operateid'=>I('request.functionid', null),
            'function_name'=>I('request.function_name', null),
            'operatorimage' => I('request.operatorimage', null),
            'event_time' =>  $event_time,
            'event_details'=>I('request.event_details', null),
            'userlocation' => I('request.userlocation', null),
            'longitude' => I('request.longitude', null),
            'latitude' => I('request.longitude', null),
        ];

        if(($data['productid']==null)||$data['function_name']==null){
            json('002',null,'请检查输入是否正确');
            return;
        }

        $result=ProcessAPI::addProcess($data);
        if($result){
            //后台最多上传5张图片
            $images[0]=I('request.content_image', null);
            $images[1]=I('request.content_image1', null);
            $images[2]=I('request.content_image2', null);
            $images[3]=I('request.content_image3', null);
            $images[4]=I('request.content_image4', null);
            for($i=0;$i<5;$i++)
            {
                $imagepath=$images[$i];
                if($imagepath!=null)
                {

                    $data1 = [
                        "image_path" => $imagepath,
                        "image_time" => date('Y-m-d H:i:s',date('Y-m-d H:i:s',time())),
//                        "image_time" => $image_time,
                        "longitude" => I('request.longitude', null),
                        "latitude" => I('request.longitude', null),
                        "location" => I('request.userlocation', null),
                        "eventid" => $result,
                        "synch_time" => date('Y-m-d H:i:s', time()),
//                        "event_time" => date('Y-m-d H:i:s',time())
//                        "synch_time" => $synch_time,
                        "event_time" => $event_time
                    ];
                    $result1=ProcessAPI::addProcessImage($data1);
                    if(!$result1) //如果某一条插入失败则跳出循环
                    {
                        break;
                    }
                }
            }
            if($result1){
                json("001",$result1,'添加生产过程成功!');
            }else{
                json('002',$result,'添加图片失败!');
                return;
            }

        }else{
            json('002',$result,'添加生产过程失败!');
            return;
        }

    }
    /*
     * 修改生产过程
     */
    public function edit($id){
        if(($id==null)||!(is_numeric($id))){
            json('002',null,'参数错误');
            return;
        }
        $result=ProcessAPI::editProcess($id);
        if($result){
            json("001",$result[0]);
        }else{
            json('002',$result,'修改失败!');
            return;
        }
    }
    /*
     * 更新生产过程
     */
    public function update(){
        $id=I('request.id',null);
        if(!is_numeric($id)){
            json('002',null,'参数错误');
            return;
        }
        $time = I('request.event_time',null);
        $event_time = ProcessAPI::strtime($time);
        $data1 = [
            'id'=>$id,
            'function_operateid'=>I('request.functionid', null),
            'function_name'=>I('request.function_name', null),
            'operatorimage' => I('request.operatorimage', null),
            'event_time' =>  $event_time,
            'event_details'=>I('request.event_details', null),
            'userlocation' => I('request.userlocation', null),
            'longitude' => I('request.longitude', null),
            'latitude' => I('request.longitude', null),
        ];
        //后台编辑最多上传5张图片
        $images[0]=I('request.content_image', null);
        $images[1]=I('request.content_image1', null);
        $images[2]=I('request.content_image2', null);
        $images[3]=I('request.content_image3', null);
        $images[4]=I('request.content_image4', null);

        $time1 = I('request.image_time',null);
        $image_time = ProcessAPI::strtime($time1);
        $time2 = I('request.synch_time',null);
        $synch_time = ProcessAPI::strtime($time2);
        $time3 = I('request.event_time',null);
        $event_time = ProcessAPI::strtime($time3);

        //编辑后的图片基本信息(不含图片地址)
        $data2 = [
            //"image_path" => I('request.content_image', null),
            "image_time" => $image_time,
            "longitude" => I('request.longitude', null),
            "latitude" => I('request.longitude', null),
            "location" => I('request.userlocation', null),
            "synch_time" => $synch_time,
            "event_time" => $event_time
        ];
        $response=ProcessAPI::upateProcess($data1,$data2,$images);
        if($response){
            json('001',$response);
        }else{
            json('002',null,'更新失败!');
        }

    }

    /*
     * 根据质量码获取对应的生产过程
     */
    public function getProcessbyCode($page = 1,$qrcode=10461000030332)
    {
        $events=ProcessAPI::getProcessbyCode($qrcode); //根据质量码获取生产记录id列表
        if($events==null)
        {
            json('002',null,'无生产环节数据!');
            return;
        }
        $event_range="";
        foreach ($events as $item)
        {
            $event_range=$event_range.$item['event_id'].',';
        }
        $event_range=trim($event_range,',');
        $response = CompanyAPI::fetchDataByCode($page,$event_range);
        echo json_encode($response);
    }

}
