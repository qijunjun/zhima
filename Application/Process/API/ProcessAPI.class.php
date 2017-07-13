<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/5/24
 * Time: 上午12:30
 * 2016.08 dichun 修改
 */
namespace Process\API;
use Common\API\CompanyAPI;

class ProcessAPI {
    /*
     * 删除生产过程
     * $id:生产过程编号
     */
    public static function deleteProcess($id){
        $model=M('base_product_event_data');
        $map=array(
            'id'=>$id
        );
        $response=$model->where($map)->delete();
        return $response;
    }

    /* 2016.8.5 注释
    public static function getOperation($productid){
        $model=M('base_product_function');
        $map=array(
            'productid'=>$productid
        );
        $response=$model->where($map)->select();
        return $response;
    }
    */
    /*
   * 获取操作列表,给新增页面做接口
   * $productid:产品id
   * by dichun 2016.08.05
   */
    public static function getOperation($productid){
        $model = M("base_product_function_operates");
        $res = $model->where('productid='.$productid)->join('zm_corr_product_function ON zm_base_product_function_operates.functionid = zm_corr_product_function.functionid','LEFT')->select();
        return $res;
    }
    /*
  * 获取操作列表,给配置页面做接口
  * $productid:产品id
  * by david 2016.08.05
  */
    public static function getConfig($productid){
        $model = M("base_product_function_operates");
        $res = $model->where('productid='.$productid)->join('zm_corr_product_function ON zm_base_product_function_operates.functionid = zm_corr_product_function.functionid','LEFT')->field("zm_corr_product_function.functionid")->select();
        return $res;
    }
    public static function addProcess($data){
        $model=M('base_product_event_data');
        $response=$model->add($data);
        return $response;
    }
    public static function addProcessImage($data){
        $model=M('base_product_event_image');
        $result =$model->add($data);
        return $result;
    }
    public static function editProcess($id){
        $result=CompanyAPI::fetchDataByEvent(1,$id);
        return $result;
    }
    public static function upateProcess($data1,$data2,$images){
        $model1=M('base_product_event_data');
        $model2=M('base_product_event_image');

        $model1->startTrans(); //开启事务
        $result1=$model1->save($data1);
        $cond=[
            'eventid'=>$data1['id'] //对应product_event_image表中的eventid
        ];
        $imagesid=$model2->where($cond)->field('id,eventid')->select();
        $oldCount=count($imagesid); //原来eventid对应的图片个数
        $mark=0;                    //更新标识计数
        foreach ($images as $value){
            if($value!=null){
                if($mark<$oldCount)//在原来的基础上更新
                {
                    $dataT=[
                        "id"=>$imagesid[$mark]['id'],
                        "image_path" => $images[$mark],
                        "image_time" => $data2['image_time'],
                        "longitude" => $data2['longitude'],
                        "latitude" => $data2['latitude'],
                        "location" => $data2['location'],
                        "synch_time" => $data2['synch_time'],
                        "event_time" => $data2['event_time']
                    ];
                    $result2=$model2->save($dataT);
                }
                else
                {
                    $dataT=[
                        "eventid" => $data1['id'],
                        "image_path" => $images[$mark],
                        "image_time" => $data2['image_time'],
                        "longitude" => $data2['longitude'],
                        "latitude" => $data2['latitude'],
                        "location" => $data2['location'],
                        "synch_time" => $data2['synch_time'],
                        "event_time" => $data2['event_time']
                    ];
                    $result2=$model2->add($dataT);
                }
                $mark=$mark+1;
            }
        }
        if($mark<$oldCount-1) //当前更新的图片数量小于原来的,删除多余的数据
        {
            for($i=$mark+1;$i<$oldCount;$i++)
            {
                $map=[
                    'id'=> $imagesid[$i]['id']
                ];
                $result2=$model2->where($map)->delete();
            }
        }
        if(($result1!==false)&&($result2!==false)){
            $model1->commit();
            return true;
        }else{
            $model1->rollback();
            return false;
        }
    }
    /*
     * 添加生产过程环节
     */
    public static function addFunction($data){
        $model=M('base_product_function_operates');
        $result =$model->add($data);
        return $result;
    }
    public static function delFunction($map)
    {
        $model=M('base_product_function_operates');
        $result=$model->where($map)->delete();
        return $result;
    }
    public static function editFunction($map){
        $model=M('base_product_function_operates');
        $result=$model->where($map)->find();
        return $result;
    }
    public static function updateFunction($data)
    {
        $model=M('base_product_function_operates');
        $result =$model->save($data);
        return $result;
    }
    /*
     * 列出公司下所有的环节
     */
    public static function listFunction($map){
        $model=M('base_product_function_operates');
        $result=$model->where($map)->select();
        return $result;
    }
    /*
    public static function addCorrFunction($productid,$funtions){
        $model=M('corr_product_function');
        $dataList[]=null;
        foreach ($funtions as $value){
            $dataList[] = array('productid'=>$productid,'functionid'=>$value);
        }
        $result=$model->addall($dataList);
        return $result;
    }
    */
    public static function addCorrFunction($productid,$funtions){
        $model=M('corr_product_function');
        $result=0;
        foreach ($funtions as $value){
            $dataList[] = array('productid'=>$productid,'functionid'=>$value);
        }
        if (is_array($dataList)) {
            aSort($dataList);
            $model->startTrans();   //开启事务
            $result1=$model->where('productid='.$productid)->delete();//删除原有关联关系
            $result2=$model->addAll($dataList);  //$result为成功插入的第一条的id
            if(($result1!==false) && $result2){
                $model->commit();   //只有$result1 和  $result2  都执行成功是才真正执行上面的数据库操作
                return $result2;
            }else{
                $model->rollback();  //  条件不满足，回滚
                return false;
            }
        }
    }

    /**显示产品和绑定生产的环节201686
     * @return mixed
     */
    public static function showCorrFunction(){
        $model = M();
        $companyid = $_SESSION['user_info']['companyid'];
        $sql = "select `zm_corr_product_function`.`functionid`,`zm_corr_product_function`.`productid`,`zm_base_product`.`productname`,`zm_base_product_function_operates`.`function_name`,`zm_base_product_function_operates`.`function_image` from `zm_base_product` LEFT JOIN `zm_corr_product_function` ON `zm_corr_product_function`.`productid` = `zm_base_product`.`productid` LEFT JOIN `zm_base_product_function_operates` ON `zm_corr_product_function`.`functionid` = `zm_base_product_function_operates`.`functionid` where zm_base_product_function_operates.companyid=".$companyid;
        $rows = $model->query($sql);

        return $rows;
    }
    /**将接收到的字符串时间转换成日期格式
     * @param $time
     * @return bool|string
     */
    public static function strtime($time){
        $date = date("Y-m-d H:i:s",strtotime($time));
        return $date;
    }

    public static  function getProcessbyCode($qrcode)
    {
        $model=M('corr_qrcode_product_event');
        $cond['qrcode_range_s']=array('elt',$qrcode);
        $cond['qrcode_range_e']=array('egt',$qrcode);
        $fields='event_id';
        $result= $model->field($fields)->where($cond)->select();
        return $result;
    }

}