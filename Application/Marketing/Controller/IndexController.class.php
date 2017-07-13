<?php
namespace Marketing\Controller;

use Common\Controller\BaseController;
use Marketing\API\MarketingAPI;

class IndexController extends BaseController
{
    public function wxhongbao(){
        $this->display('wxhongbao');
    }
    /*
     * 添加红包配置信息
     */
    public function add(){
        $companyid=$_SESSION["user_info"]["companyid"];
        if((floatval($_POST['min_value'])>floatval($_POST['max_value']))||(floatval($_POST['max_value'])>200))
        {
            json("002",null,'随机范围错误!');
            return;
        }
        $qrcode_range_s=I('qrcode_range_s','','htmlspecialchars');
        $qrcode_range_e=I('qrcode_range_e','','htmlspecialchars');
        $start_time=I('request.start_time','','htmlspecialchars');
        $end_time=I('request.end_time','','htmlspecialchars');
        //检查起止结束码范围
        if($qrcode_range_s>$qrcode_range_e)
        {
            json("002",null,'请检查码段范围!');
            return;
        }
        if($start_time>$end_time)
        {
            json("002",null,'请检查时间范围!');
            return;
        }
        if(!checkQCode($companyid,$qrcode_range_s,$qrcode_range_e)){
            json("002",null,'请检查输入的码段!');
            return;
        }
        $data=array(
            "is_random"=>1,
            "companyid"=>$companyid,  //公司id
            "promotion_name"=>I('request.promotion_name','','htmlspecialchars'),  //活动名称
            "promotion_wishing" => I('request.promotion_wishing','','htmlspecialchars'),  //活动红包祝福语
            "promotion_remark" => I('request.promotion_remark','','htmlspecialchars'),  //备注信息
            "min_value" => floatval(I('request.min_value',0,'htmlspecialchars'))*100,  //红包最小金额
            "max_value" => floatval(I('request.max_value',0,'htmlspecialchars'))*100,  //红包最大金额
            "send_name" => I('request.send_name','','htmlspecialchars'),  //红包发送方名称
            "nick_name"=>I('request.send_name','','htmlspecialchars'),
            "support_name" => I('request.support_name','','htmlspecialchars'),  //红包提供方名称
            "start_time"=>I('request.start_time','','htmlspecialchars'),  //红包活动有效期开始
            "end_time"=>I('request.end_time','','htmlspecialchars'),      //红包活动有效期结束
        );
        $response=MarketingAPI::addConfig($data);
        //红包活动关联表数据
        $data1=array(
            "companyid"=>$companyid,  //公司id
            "promotionid"=>$response,
            "qrcode_range_s"=>$qrcode_range_s,
            "qrcode_range_e"=>$qrcode_range_e,
            "create_time"=>time(),
        );
        if($response){
            $result=MarketingAPI::addCorrData($data1);
            if($result){
                json("001",$result);
            }else{
                json("002",null,$result);
                return;
            }
        }else{
            unset($data1);
            json("002",null,$response);
            return;
        }
    }
    /*
     * 列出公司下的营销活动
     */
    public function listPromotion(){
        $companyid=$_SESSION["user_info"]["companyid"];
        if($companyid==null){
            json('002',null,'找不到数据!');
            return;
        }
        $result=MarketingAPI::listPromotion($companyid);
        if($result){
            foreach ($result as &$item){
                $item['min_value']=floatval($item['min_value'])/100;
                $item['max_value']=floatval($item['max_value'])/100;
            }
            json('001',$result);
        }else{
            json('001',array());
        }

    }
    public function listRecordByPromotion($promotionid)
    {
        $companyid=$_SESSION["user_info"]["companyid"];
        if($companyid==null){
            json('002',null,'找不到数据!');
            return;
        }
        $filter=array(
            "companyid" => $companyid,
            "promotionid" => $promotionid,
        );
        $result=MarketingAPI::listRecordByPromotion($filter);
        if($result){
            json('001',$result);
        }else{
            json('001',array());
        }

    }
    /*
     * 更新红包配置信息
     */
    public function update(){
        if((floatval($_POST['min_value'])>floatval($_POST['max_value']))||(floatval($_POST['max_value'])>200))
        {
            json("002",null,'随机范围错误!');
            return;
        }
        //红包活动数据
        $data1=[
            "is_random"=>1,
            "promotionid"=>I('request.promotionid','','htmlspecialchars'), //当前或动的id
            "promotion_name"=>I('request.promotion_name','','htmlspecialchars'),  //活动名称
            "promotion_wishing" => I('request.promotion_wishing','','htmlspecialchars'),  //活动红包祝福语
            "promotion_remark" => I('request.promotion_remark','','htmlspecialchars'),  //备注信息
            "min_value" => floatval(I('request.min_value',0,'htmlspecialchars'))*100,  //红包最小金额
            "max_value" => floatval(I('request.max_value',0,'htmlspecialchars'))*100,  //红包最大金额
            "send_name" => I('request.send_name','','htmlspecialchars'),  //红包发送方名称
            "nick_name"=>I('request.send_name','','htmlspecialchars'),
            "start_time"=>I('request.start_time','','htmlspecialchars'),  //红包活动有效期开始
            "end_time"=>I('request.end_time','','htmlspecialchars'),      //红包活动有效期结束
        ];
        //红包活动关联表数据
        $data2=[
            "id"=>I('request.id','','htmlspecialchars'),  //公司id
            "qrcode_range_s"=>I('request.qrcode_range_s','','htmlspecialchars'),
            "qrcode_range_e"=>I('request.qrcode_range_e','','htmlspecialchars'),
            "update_time"=>time(),
        ];
        $result=MarketingAPI::updateConfig($data1,$data2);
        if($result){
            json('001',null,'保存成功!');
        }else{
            json('002',null,'保存失败');
        }
    }
    public function edit(){
        $id=I('id','','htmlspecialchars');
        $result=MarketingAPI::listPromotionbyid($id);
        if($result){
            foreach ($result as &$item){
                $item['min_value']=floatval($item['min_value'])/100;
                $item['max_value']=floatval($item['max_value'])/100;
            }
            json('001',$result);
        }else{
            json('002',array());
        }
    }
    public function delete(){
        $id=I('id','','htmlspecialchars');
        $result=MarketingAPI::deletePromotionbyid($id);
        if(is_numeric($result)){
            json('001',$result);
        }else{
            json('002',null,$result);
        }
    }
    /*
     * 从微信端口接收数据
     */
    public function getWXInfo(){

        $qrcode = @addslashes(trim($_POST["qrcode"]));
        $openid = @addslashes(trim($_POST["openid"]));
        $nickname = @addslashes(trim($_POST['nickname']));//用户名
        $sex = @addslashes(trim($_POST['sex']));//用户性别
        $country = @addslashes(trim($_POST['country']));//国家
        $province = @addslashes(trim($_POST['province']));//省
        $city = @addslashes(trim($_POST['city']));//市
        $headimgurl = @addslashes(trim($_POST['headimgurl']));//用户头像
        $longitude=@addslashes(trim($_POST['longitude']));//经度
        $latitude=@addslashes(trim($_POST['latitude']));//纬度

        $this->assign('qrcode',$qrcode);
        $this->assign('openid',$openid);
        $this->assign('nickname',$nickname);
        $this->assign('sex',$sex);
        $this->assign('country',$country);
        $this->assign('province',$province);
        $this->assign('city',$city);
        $this->assign('headimgurl',$headimgurl);
        $this->assign('longitude',$longitude);
        $this->assign('latitude',$latitude);
        $this->display();
    }
    public function getAccount(){
        $companyid=$_SESSION["user_info"]["companyid"];
        $model=M('base_company_account');
        $cond=array(
            'companyid'=>$companyid
        );
        $result=$model->where($cond)->getField('account');
        json('001',$result,'当前余额');
    }

    /***获得红包活动名称201687
     * @param int $promotionid
     */
    public function actname($promotionid){
        $companyid=$_SESSION["user_info"]["companyid"];
        if($companyid==null){
            json('002',null,'找不到数据!');
            return;
        }
        $filter=array(
            "companyid" => $companyid,
            "promotionid" => $promotionid,
        );
        $result=MarketingAPI::actname($filter);
        if($result){
            json('001',$result);
        }else{
            json('002',null,'不存在该活动');
        }
    }
}