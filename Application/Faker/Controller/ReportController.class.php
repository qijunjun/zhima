<?php
/**
 * Created by PhpStorm.
 * User: Liming
 * Date: 2016/4/15
 * Time: 13:41
 */

namespace Faker\Controller;

use Common\Controller\BaseController;
use Faker\API\Report;
use Common\API\MatchtimeAPI;
class ReportController extends BaseController {
    /**
     * 添加举报信息
     *
     * @post_param String $content 举报内容
     * @post_param String $tel [举报人联系电话]
     * @post_param String $pic [图片地址]
     * @post_param Number $productId [产品ID]
     * @post_param Number $b [产品码]
     * @post_param String $c [二维码校验]
     * @post_param String $address [定位地点]
     * @return void
     *
     * Created by PhpStorm;
     * User: Liming;
     * Date: 2016/4/15
     */
    public function addReport() {
        $data = [
            'content'   => I('post.content', '123'),
            'tel'       => I('post.tel', 123123),
            'pic'       => I('post.pic', 123123123),
            'productId' => I('post.productId', 72),
            'b'         => I('post.b', '10191000001973'),
            'c'         => I('post.c', 'C7EE9658F8395AF8'),
            'ip'        => get_client_ip(0, true),
            'ipaddr'    => getIpLocation(),
            //'address'   => I('post.address', null)
        ];
        if($data['content'] == null ||
            $data['content'] === ''
        ) {
            json("002");
        }

        if(Report::add($data)) {
            $companyid = substr($data['b'],0,4);
            $productid = $data['productId'];
            $product = M("BaseProduct");
            $cond = [
                'productid' => $productid,
                'companyid' => $companyid
            ];
            $fields = 'productname,guige';
            $info = $product->where($cond)->field($fields)->find();
            $productName = $info['productname'];
            $guige = $info['guige'];
            $msg = $productName.($guige).'发现举报信息';

            $model = M("BaseUser");
            $map = [
                'companyid' => $companyid
            ];
            $touser = $model->where($map)->getField('id');
//            $touser = 64;
            $agent_id = 2;
//            $mdata = array('agentid' => $agent_id, 'content' => $msg, 'to_user' => $touser);
//            R('Weixin/Message/sendText',$mdata);
            $mdata = array('agentid' => $agent_id, 'msgtype' => "text", 'text' => array('content' => $msg), 'touser' => $touser);
            R('Weixin/QYWeixin/sendMessage', array('data' => $mdata, 'mpid' => ''), 'Api');
            json("001");
        } else {
            json("006");
        }
    }

    /** 获取举报信息列表
     *
     * @param int [$start] 起始条数
     * @param int [$limit] 取出条数
     *
     * @post_param int [$productId] 产品ID
     * @post_param int [$bStart] 起始码段
     * @post_param int [$bEnd] 终止码段
     * @post_param int [$timeStart] 记录起始时间（10位时间戳）
     * @post_param int [$timeEnd] 记录终止时间（10位时间戳）
     */
    public function getReport($start = 0, $limit = 100) {
        $filter = [];
        $companyid=session('user_info')['companyid'];
        $value = I('post.productId', null);
        if($value !== null) {
            $filter['productId'] = $value;
        }
        $filter['b'] = [
            [
                ['egt', intval($companyid . "0000000001")],
                ['elt', intval($companyid . "1999999999")],
                'and'
            ],
            ['exp', 'IS NULL'],
            'or'
        ];
        $value = I('post.bStart', null);
        if($value !== null && preg_match("/^$companyid/", $value)) {
            $filter['b'][0][0][1] = floatval($value);
            $filter['b'][1][1] = 'IS NOT NULL';
            $filter['b'][2] = 'and';
        }
        $value = I('post.bEnd', null);
        if($value !== null && preg_match("/^$companyid/", $value)) {
            $filter['b'][0][1][1] = floatval($value);
            $filter['b'][1][1] = 'IS NOT NULL';
            $filter['b'][2] = 'and';
        }
        $filter['create_time'] = [
            [
                ['egt', 0],
                ['elt', time()],
                'and'
            ],
            ['exp', 'IS NULL'],
            'or'
        ];
        $value = I('post.timeStart', null);
        if($value !== null) {
            $filter['create_time'][0][0][1] = $value;
            $filter['create_time'][1][1] = 'IS NOT NULL';
            $filter['create_time'][2] = 'and';
        }
        $value = I('post.timeEnd', null);
        if($value !== null) {
            $filter['create_time'][0][1][1] = $value;
            $filter['create_time'][1][1] = 'IS NOT NULL';
            $filter['create_time'][2] = 'and';
        }
        $result = Report::lists($filter, $start, $limit);
        if($result === false) {
            json("006");
        } else {
            json("001", $result);
        }
    }
    /*
     * 根据id删除举报数据
     */
    public function deleteReport($id)
    {
        if ($id === '' || $id == null) {
            json("002");
        }
        $filter=array(
            'id'=>$id
        );
        $response=Report::delete($filter);
        if($response){
            json("001",$response);
        }else{
            json('002');
            return;
        }
    }

    /**
     * 按时间检索举报数据2016822
     */
    public function SearchReport(){
        $companyid = $_SESSION['user_info']['companyid'];
        $starttime = I('startdate',null);
        $endtime = I('enddate',null);
        $re = MatchtimeAPI::checkDate($starttime,$endtime);

        if($re === false){
            return;
        }
        $model = M('base_scanreport');
        $fields = 'ip,ipaddr,create_time,tel,content,address,b';

        if($starttime && $endtime){
            $map = array(
                'b'=>array('like',"$companyid%"),
                'create_time' => array('between',array($re[1][0],$re[1][1]))
            );
        }elseif($starttime && empty($endtime)){
            $map = array(
                'b'=>array('like',"$companyid%"),
                'create_time' => array('egt',$re[1])
            );
        }elseif(empty($starttime) && $endtime){
            $map = array(
                'b'=>array('like',"$companyid%"),
                'create_time' => array('elt',$re[1])
            );
        }elseif($starttime == null && $endtime == null){
            $map = array(
                'b'=>array('like',"$companyid%"),
            );
        }
        $result = $model->where($map)->field($fields)->select();
        if($result){
            json('001',$result);
        }
        else{
            json('001',array());
        }
    }
}
