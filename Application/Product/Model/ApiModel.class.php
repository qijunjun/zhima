<?php
/**
 * Created by Yangbin.
 * User: Yangbin
 * Date: 2016/4/13
 * Time: 11:27
 */
namespace Product\Model;
use Common\API\MatchtimeAPI;
use Product\Controller\ApiController;
use Think\Model;
class ApiModel extends Model {

    public function __construct()
    {

        if(empty($_SESSION['user_info']['id']))
        {
            json('002',null,'no login!');
            exit;
        }

        $this->companyid=$_SESSION['user_info']['companyid'];

        $this->userid=$_SESSION['user_info']['id'];
    }

    public function __destruct()
    {

    }

    protected $tableName = 'base_product';

    public function getCompanyId()
    {
        $companyid = $this->companyid;

        return $companyid;
    }

    public function getUserId()
    {
        $userid = $this->userid;

        return $userid;
    }

    public function setJson($status,$msg,$data)
    {
        $jsonData = json_encode(array(

            'status'=> $status,
            'msg'=> $msg,
            'data'=> $data,

        ));

        if($_GET['callback']){
            header('Content-Type: application/json; charset=utf8');
            echo $_GET['callback'].'('.$jsonData.')';
            exit;
        }else{
            echo $jsonData;
        }
    }

    public function show($productId)
    {
        if(!empty($productId))
        {
            $condition = array(
                'productid' => $productId,
            );

            $productMysql = D($this->getTableName());
            $productAllInfo = $productMysql->where($condition)->select();
            json('001',$productAllInfo);
            exit;
        }
        else
        {
            $condition = array(
                'companyid' => $this->companyid,
            );
            $productMysql = D($this->getTableName());
            $productAllInfo = $productMysql->where($condition)->select();

            json('001',$productAllInfo);
            exit;
        }
        json('002');
    }

    public function getFieldsList($companyId)
    {
        if(!empty($companyId))
        {
            $condition = array(
                'companyid' => $companyId,
            );

            $productMysql = D($this->getTableName());
            $productAllInfo = $productMysql->where($condition)->field('productid,productname,guige,config')->select();
            json('001',$productAllInfo);
            exit;
        }
        else
        {
            json('002');
            exit;
        }
    }

    public function upload()
    {
        $config = array(
            'maxSize'    =>    3145728,
            'rootPath'   =>    './resources/',
            'savePath'   =>    'imgages/product/',
            'saveName'   =>    array('uniqid',''),
            'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub'    =>    true,
            'subName'    =>    array('date','Ymd'),
        );
        $upload = new \Think\Upload($config);
        $info = $upload->upload();
        if(!$info)
        {
            json('002',null,$this->error($upload->getError()));
            exit;
        }
        else
        {
            $data['productimage'] = $info[0]['savename'];
        }
    }

    public function insert()
    {

        //$this->upload();

        $data['productname'] = I('productname','0','htmlspecialchars');
        $data['productinfo'] = I('productinfo','0','htmlspecialchars');
        $data['wdadr'] = I('wdadr','0','htmlspecialchars');
        //$data['wdadr'] = $_REQUEST['wdadr'];
        //$data['companyid'] = I('companyid','0','htmlspecialchars');
        $data['companyid'] = $this->getCompanyId();
        $data['productimage'] = I('productimage','0','htmlspecialchars');
        $data['productimage1'] = I('productimage1','0','htmlspecialchars');
        $data['productimage2'] = I('productimage2','0','htmlspecialchars');
        $data['productimage3'] = I('productimage3','0','htmlspecialchars');
        $data['productimage4'] = I('productimage4','0','htmlspecialchars');
        $data['price'] = I('price','0','htmlspecialchars');
        $data['guige'] = I('guige','0','htmlspecialchars');
        $data['create_time'] = NOW_TIME;
        $data['order'] = I('order','0','htmlspecialchars');
        $data['score'] = I('score','0','htmlspecialchars');
        $data['spec'] = I('spec','0','htmlspecialchars');
        $data['config']=I('config','0','htmlspecialchars');
        $model = D($this->getTableName());
        if (false === $model->create($data))
        {
            json('002',null,$this->error($model->getError()));
            exit;
        }

        $list = $model->add();
        if ($list !== false)
        {
            json('001');
            exit;
        }
        else
        {
            json('002');
            exit;
        }
    }

    public function edit($productId)
    {
        if(empty($productId))
        {
            json('002');
            exit;
        }

        $model = D($this->getTableName());

        $condition = array(
            'productid' => $productId,
        );

        $list = $model->where($condition)->select();

        json('001',$list,null);

    }

    public function update($productId)
    {
        //$this->upload();

        if(empty($productId))
        {
            json('002');
            exit;
        }

        $data['productname'] = I('productname','0','htmlspecialchars');
        //$data['productinfo'] = I('productinfo','0','htmlspecialchars');
        $data['productinfo'] = addslashes($_POST['productinfo']);
        $data['wdadr'] = I('wdadr','0','htmlspecialchars');
        //$data['companyid'] = I('companyid','0','htmlspecialchars');
        $data['companyid'] = $this->getCompanyId();
        $data['productimage'] = I('productimage','0','htmlspecialchars');
        $data['productimage1'] = I('productimage1','0','htmlspecialchars');
        $data['productimage2'] = I('productimage2','0','htmlspecialchars');
        $data['productimage3'] = I('productimage3','0','htmlspecialchars');
        $data['productimage4'] = I('productimage4','0','htmlspecialchars');
        $data['price'] = I('price','0','htmlspecialchars');
        $data['guige'] = I('guige','0','htmlspecialchars');
        $data['create_time'] = NOW_TIME;
        $data['order'] = I('order','0','htmlspecialchars');
        $data['score'] = I('score','0','htmlspecialchars');
        $data['spec'] = I('spec','0','htmlspecialchars');
        $data['config']=I('config','0','htmlspecialchars');
        $model = D($this->getTableName());

        $condition = array(
            'productid' => $productId,
        );

        $list = $model->where($condition)->save($data);

        if (false !== $list) {
            json('001');
            exit;
        } else {
            json('002');
            exit;
        }
    }

    public function delete($productId)
    {
        if(empty($productId))
        {
            json('002');
            exit;
        }

        $model = D($this->getTableName());

        $condition = array(
            'productid' => $productId,
        );

        $list = $model->where($condition)->delete();

        if (false !== $list) {
            json('001');
            exit;
        } else {
            json('002');
            exit;
        }
    }

    /**
     * 按时间查询产品信息2016822
     * @param $starttime
     * @param $endtime
     */
    public function timeSearchInfo($starttime,$endtime){
        $re = MatchtimeAPI::checkDate($starttime,$endtime);
        if($re === false){
            return;
        }
        if($starttime >0 && $endtime >0){
            $condition = array(
                'companyid' => $this->companyid,
                'create_time' => array('between', array($re[1][0], $re[1][1]))
            );
        }elseif($starttime == null && $endtime == null){
            $condition = array(
                'companyid' => $this->companyid,
            );
        }elseif($starttime >0 && $endtime == null){
            $condition = array(
                'companyid' => $this->companyid,
                'create_time' => array('egt', $re[1])
            );
        }
        elseif($starttime == null && $endtime >0 ){
            $condition = array(
                'companyid' => $this->companyid,
                'create_time' => array('elt', $re[1])
            );
        }

        $productMysql = D($this->getTableName());
        $productAllInfo = $productMysql->where($condition)->select();
        if($productAllInfo){
            json('001',$productAllInfo);
        }else{
            json("002");
        }
    }
}