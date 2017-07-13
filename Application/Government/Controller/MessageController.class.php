<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/30
 * Time: 15:31
 */

namespace Government\Controller;

use Common\Controller\BaseController;
use Government\API\MessageAPI;

class MessageController extends BaseController
{
    private $governmentid;
    private $_companyid;
    public function _initialize(){
        parent::_initialize();
        $this->governmentid = session('user_info.id');
        $this->_companyid = session('user_info.companyid');
//        $this->_companyid = 1019;
//        $this->governmentid = 1;
    }

    /**
     * 政府给企业发公告
     */
    public function sendAnnouncement(){
        $title = I('title');
        $content = I('content');
        if(empty($title) || empty($content)){
            json("002",null,"标题或内容不能为空");
            return;
        }
        $gov = self::getGovernmentInfo();
        $governmentname = $gov;
        $governmentid = $this->governmentid;
        $ids = self::getCompanyId();
        $result = MessageAPI::sendAnnouncement($title,$content,$governmentid,$governmentname,$ids);

        if($result){
            json("001",$result,'success');
        }else{
            json("002",null,'发送失败');
        }
    }

    /**
     * 当前登录政府的名称
     * @return mixed
     */
    public function getGovernmentInfo(){
        $governmentid = $this->governmentid;
        $model = M('BaseGovernment');
        $map = [
            'id' => $governmentid
        ];
        $governmentname =  $model->where($map)->getField('name');

        return $governmentname;
    }

    /**
     * 政府管辖的企业id2016830
     * @return mixed
     */
    public function getCompanyId(){
        $governmentid = $this->governmentid;
        $model = M("CorrGovernmentCompany");
        $map = [
            'government_id' => $governmentid
        ];
        $id = $model->where($map)->getField('company_id',true);
        return $id;
    }

    /**
     *政府已发的过往公告记录列表2016831
     */
    public function listInfo(){
        $governmentid = $this->governmentid;
        $result = MessageAPI::listInfo($governmentid);

        if($result == false){
            json("001",null,"没有公告记录");
        }else{
            json("001",$result,'success');
        }
    }

    /**
     * 政府已发的过往公告的详细信息2016831
     */
    public function checkGovInfo(){
        $id = I('id');
        $governmentid = $this->governmentid;
        $result = MessageAPI::checkGovInfo($id,$governmentid);

        if($result == false){
            json("001",null,"没有公告记录");
        }else{
            json("001",$result,'success');
        }
    }

    /**
     * 政府删除已发的过往公告记录2016831
     */
    public function delInfo(){
        $id = I('id');
        $result = MessageAPI::delInfo($id);
        if($result){
            json("001",$result,'success');
        }elseif($result == 0){
            json("009",null,"没有删除数据");
        }else{
            json("002");
        }
    }

    /**
     * 政府编辑已发的公告记录2016831
     */
    public function editInfo(){
        $id =   I('id');
        $result = MessageAPI::editInfo($id);

        if($result){
            json("001",$result,'success');
        }else{
            json("002");
        }
    }

    /**
     * 修改政府已发公告记录2016831
     */
    public function updateInfo(){
        $id = I('id');
        $data = [
            'title' => I('title'),
            'content' => I('content')
        ];
        $result = MessageAPI::updateInfo($data,$id);
        if($result){
            json("001",$result,'success');
        }else{
            json("002");
        }
    }

    /**
     * 企业公告信息显示列表2016831
     */
    public function listComInfo(){
        $companyid = $this->_companyid;
        $result = MessageAPI::listComInfo($companyid);

        if($result){
            json("001",$result,'success');
        }else{
            json("001",null);
        }
    }

    /**
     * 企业查看公告信息2016831
     */
    public function checkComInfo(){
        $id = I('postid');
        $companyid = $this->_companyid;
        $result = MessageAPI::checkComInfo($id,$companyid);
//        var_dump($result);

        if($result){
            json("001",$result,'success');
        }else{
            json("001",null);
        }
    }

    /**
     * 企业删除公告信息2016831
     */
    public function delComInfo(){
        $id = I('id');
        $result = MessageAPI::delComInfo($id);
        if($result){
            json("001",$result,'success');
        }elseif($result == 0){
            json("009",null,"没有删除数据");
        }else{
            json("002");
        }
    }

    /**
     * 企业留言2016831
     */
    public function leaveMessage(){
        $content = I('content');
        if(empty($content)){
            json("002",null,"内容不能为空");
            return;
        }
        $companyid = $this->_companyid;
        $model = M("BaseCompany");
        $map = [
            'id' => $companyid
        ];
        $companyname = $model->where($map)->getField('name as companyname');
        $modelCorr = M("CorrGovernmentCompany");
        $cond = [
            'company_id' => $companyid
        ];
        $governmentid = $modelCorr->where($cond)->getField('government_id');

        $result = MessageAPI::leaveMessage($companyid,$companyname,$governmentid,$content);
        if($result){
            json("001",$result,'success');
        }else{
            json("002");
        }
    }

    /**
     * 政府查看企业的留言2016831
     */
    public function listLeaveMessage(){
        $governmentid = $this->governmentid;
        $result = MessageAPI::listLeaveMessage($governmentid);

        if($result){
            json("001",$result,'success');
        }else{
            json("001",null);
        }
    }

    /**
     * 政府查看留言详情201693
     */
    public function checkLeaveMessage(){
        $id = I('id');
        $result = MessageAPI::checkLeaveMessage($id);

        if($result){
            json("001",$result,'success');
        }else{
            json("001",null);
        }
    }

}