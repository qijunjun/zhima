<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/8/22
 * Time: 下午5:42
 */
namespace Process\API;
class BindAPI
{
    public static function bindProcessRecord($data)
    {
        $model=M('corr_qrcode_product_event');
        if(!$model->create($data))
        {
            echo $model->getError();
            return false;
        }
        return $model->add();
    }

    /*
     * 检查是否为合法的码段
     * 合法返回对应的产品ID,名称,规格,否则返回NULL
     */
    public static function isValide($companyid,$start,$end)
    {
        $model=M();
        $modelProduct=M('base_product');
        $sql = 'select product_id from zm_corr_qrcode_product where(qrcode_range_s <='.$start.'  and  qrcode_range_e >='.$end.') and company_id='.$companyid;
        $result = $model->query($sql);
        $productid=$result[0]['product_id'];
        $cond=array(
            'productid'=>$productid
        );
        $result=$modelProduct->where($cond)->field("productid,productname,guige")->select();
        return $result;
    }

    /*
     * 检验输入的质量码段是否已关联相同的生产记录
     * 重复返回true,否则返回false
     */
    public static function checkRepeatQrcode($eventid,$start,$end ){
        if(empty($end)){
            $end = $start;
        }
        $model = M();
        $sql = 'select id from zm_corr_qrcode_product_event where((qrcode_range_s <='.$start.'  and  qrcode_range_e >='.$start.' ) or (qrcode_range_s <='.$end.' and qrcode_range_e >='.$end.' ) or (qrcode_range_s >='.$start.' and qrcode_range_s <='.$end.' ) or (qrcode_range_e >='.$start.' and qrcode_range_e <='.$end.' )) and event_id='.$eventid;
        $result = $model->query($sql);
        return $result;
    }

    public static function delBind($id)
    {
        $company_id= $_SESSION["user_info"]["companyid"];
        $cond=array(
            'id'=>$id,
            'company_id'=>$company_id
        );
        $model=M('corr_qrcode_product_event');
        $result=$model->where($cond)->delete();
        if($result===false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public static function editBind($data)
    {
        $model=M('corr_qrcode_product_event');
        $result=$model->save($data);
        if($result===false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public static function listEvents($productid,$function_operateid)
    {
        $model=M();
        $sql='SELECT zm_base_product_event_data.id, 
                zm_base_product_event_data.function_name, 
                zm_base_product_event_data.operatorimage, 
                zm_base_product_event_data.event_time, 
                zm_base_product_event_data.userlocation
                FROM zm_base_product_event_data 
                WHERE zm_base_product_event_data.productid='.$productid.' and zm_base_product_event_data.function_operateid='.$function_operateid .'
                ORDER BY
				zm_base_product_event_data.event_time DESC,
				zm_base_product_event_data.id DESC
				';
        $data= $model -> query($sql);
        if ($data !== false)
        {
            $result = array();
            $i = 0;
            $image = M("base_product_event_image");
            foreach ($data as $item)
            {
                $data_id = $item["id"];
                $result[$i] = $item;
                $result[$i++]["image_path"] = $image -> where("eventid = " . $data_id) -> getField("image_path", true);
            }
        }
        return $result;
    }

    public static function listCorrRecords()
    {
        $model=M();
        $company_id= $_SESSION["user_info"]["companyid"];
        $sql='SELECT zm_corr_qrcode_product_event.qrcode_range_s, 
                zm_corr_qrcode_product_event.qrcode_range_e, 
                zm_corr_qrcode_product_event.id, 
                zm_corr_qrcode_product_event.create_time, 
                zm_base_product.productname, 
                zm_base_product.guige, 
                zm_base_product_event_data.function_name
                FROM zm_base_product 
                INNER JOIN zm_corr_qrcode_product_event ON zm_base_product.productid = zm_corr_qrcode_product_event.product_id
	            INNER JOIN zm_base_product_event_data ON zm_corr_qrcode_product_event.event_id = zm_base_product_event_data.id
                WHERE company_id='.$company_id;
        $data=$model->query($sql);
        return $data;
    }
}