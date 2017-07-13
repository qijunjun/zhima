<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/8/22
 * Time: 下午5:38
 */
namespace Process\Controller;
use Common\Controller\BaseController;
use Process\API\BindAPI;

class BindController extends BaseController
{
    /*
     * 如果当前码段属于一个产品返回产品id
     * 否则返回false
     */
    public function isValid()
    {
        $company_id= $_SESSION["user_info"]["companyid"];
        $qrcode_range_s=I('qrcode_range_s');
        $qrcode_range_e=I('qrcode_range_e');
        if($qrcode_range_s > $qrcode_range_e){
            json("002",null,"起始质量码不能大于结束质量码");
            return false;
        }
        if(!checkQCode($company_id,$qrcode_range_s,$qrcode_range_e))
        {
            json('002',null,'请检查输入的码段');
            return false;
        }
        $result=BindAPI::isValide($company_id,$qrcode_range_s,$qrcode_range_e);
        if($result)
        {
            json('001',$result,'ok');
            return;
        }
        else
        {
            json('002',null,'请检查输入的码段');
            return;
        }
    }
    /*
     * 绑定生产过程记录
     */
    public function bindProcessRecord()
    {
        $company_id= $_SESSION["user_info"]["companyid"];
        $product_id=I('product_id');
        $event_id=I('event_id');
        $qrcode_range_s=I('qrcode_range_s');
        $qrcode_range_e=I('qrcode_range_e');
        if(BindAPI::checkRepeatQrcode($event_id,$qrcode_range_s,$qrcode_range_e))
        {
            json('002',null,'当前质量码段已关联该生产记录');
            return;
        }
        $data=array(
            'company_id'=>$company_id,
            'product_id'=>$product_id,
            'event_id'=>$event_id,
            'qrcode_range_s'=>$qrcode_range_s,
            'qrcode_range_e'=>$qrcode_range_e,
            'create_time'=>time()
        );
        $result=BindAPI::bindProcessRecord($data);
        if($result)
        {
            json('001',null,'绑定生产记录成功');
        }
        else
        {
            json('002',null,'绑定生产记录失败');
            return;
        }
    }
    /*
     * 删除绑定记录
     */
    public function delBind()
    {
        $id=I('id');
        if(!is_numeric($id))
        {
            json('002',null,'删除生产记录失败');
            return;
        }
        if(BindAPI::delBind($id))
        {
            json('001',null,'删除绑定生产记录成功');
            return;
        }
        else
        {
            json('002',null,'删除绑定生产记录失败');
            return;
        }
    }

    public function editBind()
    {
        $id=I('id',1);
        if(!is_numeric($id))
        {
            json('002',null,'编辑绑定记录失败');
            return;
        }
        $event_id=I('event_id');
        $qrcode_range_s=I('qrcode_range_s');
        $qrcode_range_e=I('qrcode_range_e');
        if(BindAPI::checkRepeatQrcode($event_id,$qrcode_range_s,$qrcode_range_e))
        {
            json('002',null,'当前质量码段已关联该生产记录');
            return;
        }
        $data=array(
            'id'=>$id,
            'event_id'=>$event_id,
            'update_time'=>time()
        );
        $result=BindAPI::editBind($data);
        if($result)
        {
            json('001',null,'修改生产记录成功');
        }
        else
        {
            json('002',null,'修改生产记录失败');
            return;
        }

    }

    public function listEvents()
    {
        $function_operateid=I('function_operateid');
        $productid=I('product_id');
        $result=BindAPI::listEvents($productid,$function_operateid);
        if($result)
        {
            json('001',$result,'ok');
            return;
        }
        else
        {
            json('001',null);
            return;
        }
    }

    /*
     * 列出码段和生产记录绑定的列表
     */
    public function listCorrRecords()
    {
        $result=BindAPI::listCorrRecords();
        if($result)
        {
            json('001',$result,'ok');
            return;
        }
        else
        {
            json('001',null);
            return;
        }
    }

}