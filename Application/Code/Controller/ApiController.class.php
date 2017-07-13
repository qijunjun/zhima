<?php
/**
 * Created by Yangbin.
 * User: Yangbin
 * Date: 2016/4/13
 * Time: 15:45
 */
namespace Code\Controller;
use Common\Controller\BaseController;
use Code\Model\ApiModel;
use Think\Controller;
use Faker\API\Authenticity;
use Trace\API\TraceAPI;
use Common\API\MatchtimeAPI;
class ApiController extends Controller{

    public function __construct()
    {
        parent::__construct();//没有则display无法使用
    }

    public function __destruct()
    {

    }

    public function info()
    {
        $model = new ApiModel();

        $model->show();
    }

    public function add()
    {
        $model = new ApiModel();

        $codeNum = I('codenum','0','htmlspecialchars');
        $codeNum2 = I('codenumtwo','0','htmlspecialchars');

        $codeHead = $model->getCodeHead();

        $codeType = $model->getCodeType(I('codetype','0','htmlspecialchars'));

        $latestCode = $model->getLatestCode($codeType);

        $model->inputNumLimit($codeNum, $codeNum2);

        $model->generate($codeNum, $codeHead, $codeType, $latestCode, $codeNum2);
    }

    public function exp()
    {
        $id = I('id','0','htmlspecialchars');
        $max = I('num','0','htmlspecialchars');

        $model = new ApiModel();

        $qrcodeCounts = $model->getGenerateLog($id);

        $model->getCsv($id,$max);

        if($max >= $qrcodeCounts)
        {
//            json('002',null,'More than limit!');
//            exit;
            $max = $qrcodeCounts;
        }
//        else
//        {
//            $model->getCsv($id,$max);
//        }
        $model->getCsv($id,$max);
    }

    public function createLog()
    {
        $companyId = I('companyid','0','htmlspecialchars');
        $userId = I('userid','0','htmlspecialchars');
        $counts = I('num','0','htmlspecialchars');

        $model = D('base_qrcode_created_log');

        $modelMap = array(
            'companyid'=>$companyId,
        );

        $list = $model->where($modelMap)->order('operate_time desc')->select();

        if(!empty($list))
        {
            $modelData = array(
                'companyid'=>$companyId,
                'operatorid'=>$userId,
                'operate_time'=>NOW_TIME,
                'qrcode_counts'=>$counts,
                'qrcode_all'=>$counts+$list[0]['qrcode_all'],
                'qrcode_left'=>$counts+$list[0]['qrcode_left']
            );

            $model->where($modelMap)->order('operate_time desc')->setField('qrcode_left',0);
        }
        else
        {
            $modelData = array(
                'companyid'=>$companyId,
                'operatorid'=>$userId,
                'operate_time'=>NOW_TIME,
                'qrcode_counts'=>$counts,
                'qrcode_all'=>$counts,
                'qrcode_left'=>$counts
            );
        }

        $result = $model->add($modelData);

        if(!empty($result))
        {
            json('001',$result);
        }
        else
        {
            json('002',$result);
        }
    }

    public function log()
    {
        $companyId = I('companyid','0','htmlspecialchars');
        $userId = I('userid','0','htmlspecialchars');
        $counts = I('num','0','htmlspecialchars');
        $operation = I('operation','0','htmlspecialchars');

        switch($operation)
        {
            case 'add':
                $model = D('base_qrcode_created_log');

                $modelMap = array(
                    'companyid'=>$companyId,
                );

                $list = $model->where($modelMap)->order('operate_time desc')->select();

                if(!empty($list))
                {
                    $modelData = array(
                        'companyid'=>$companyId,
                        'operatorid'=>$userId,
                        'operate_time'=>NOW_TIME,
                        'qrcode_counts'=>$counts,
                        'qrcode_all'=>$counts+$list[0]['qrcode_all'],
                        'qrcode_left'=>$counts+$list[0]['qrcode_left']
                    );

                    $model->where($modelMap)->order('operate_time desc')->setField('qrcode_left',0);
                }
                else
                {
                    $modelData = array(
                        'companyid'=>$companyId,
                        'operatorid'=>$userId,
                        'operate_time'=>NOW_TIME,
                        'qrcode_counts'=>$counts,
                        'qrcode_all'=>$counts,
                        'qrcode_left'=>$counts
                    );
                }

                $result = $model->add($modelData);

                if(!empty($result))
                {
                    json('001',$result);
                }
                else
                {
                    json('002',$result);
                }
                break;

            case 'list':
                $model = D('base_qrcode_created_log');

                $modelMap = array(
                    '_string'=>'companyid='. $companyId .' OR operatorid='. $userId,
                );
                $list = $model->where($modelMap)->order('operate_time desc')->select();
                if(!empty($list))
                {
                    json('001',$list);
                }
                else
                {
                    json('002',$list);
                }
                break;
            default;
                break;
        }
    }

    public function scanSetting()
    {
        $model = new ApiModel();
        $setting = $model->getSetting();
        json('001',$setting,'防伪配置信息!');
    }

    public function updateSetting()
    {
        $model = new ApiModel();
        $setting = $model->settingUpdate();
        if(!empty($setting))
        {
            json('001',$setting,'防伪配置信息更新成功!');
        }
        else
        {
            json('002',null,'防伪配置信息更新失败！');
        }

    }

    public function check()
    {
        $b = $_SESSION["code_info"]["b"];
        $c = $_SESSION["code_info"]["c"];

        $cmpID = substr($b,0,4);

        $model = new ApiModel();
        $codeType = $model->getScanCodeType($b);
        //---step--1--- : 码真伪
        $codeTf = $model->getCodeStatus($codeType,$c,$b);
        $modelMysql = D('base_scaninfo');
        $modelMysqlMap = array(
            'b'=>$b,
        );
        $modelMysqlResult = $modelMysql->where($modelMysqlMap)->select();


        $codeIsLimit = Authenticity::blackList($b);

        $setting = $model->getSetting($cmpID);

        if($setting['max_scan_count'] == 0)
        {
            $mysqlSettingMaxScanCout = 99999;
        }
        else
        {
            $mysqlSettingMaxScanCout = $setting['max_scan_count'];
        }

        if($codeIsLimit & $modelMysqlResult[0]['scan_count'] >= $mysqlSettingMaxScanCout)
        {
            //既是黑名单又是扫描过多假码
            $scanErrorType = '2';
            $codeTf = false;
        }
        else if($codeIsLimit)
        {
            //黑名单假码
            $scanErrorType = '3';
            $codeTf = false;
        }
        else if($modelMysqlResult[0]['scan_count'] >= $mysqlSettingMaxScanCout)
        {
            //扫描次数过多假码
            $scanErrorType = '4';
            $codeTf = false;
        }
        else if($codeTf & !$modelMysqlResult[0]['istf'])
        {
            $codeTf = true;
            $scanErrorType = '1';
        }
        else
        {
            $codeTf = false;
            $scanErrorType = '1';
        }

        switch($scanErrorType)
        {
            case '1':
                $errorInfo = '曾经被扫描为假码或出现在黑名单中！';
                break;
            case '2':
                $errorInfo = $setting['scan_tips_text'] . '<br/>' . $setting['fake_tips_text'];
                $message = '黑名单假码';
                break;
            case '3':
                $errorInfo = $setting['fake_tips_text'];
                $message = '黑名单扫码';
                break;
            case '4':
                $errorInfo = $setting['scan_tips_text'];
                $message = '扫码次数过多假码';
                break;
            default:
                $errorInfo = '';
                break;
        }

        if($scanErrorType > 1){
            $address = $modelMysqlResult[0]['recent_address'];
            $time = date("Y-m-d",$modelMysqlResult[0]['recent_time']);
            $productid = $modelMysqlResult[0]['productid'];
            $product = M("BaseProduct");
            $map = ['productid' => $productid];
            $fields = 'productname,guige';
            $info = $product->where($map)->field($fields)->find();
            $msg = $info['productname'].'('.$info['guige'].')'.'在'.$address.'发现'.$message.'  时间:'.$time;
            $agent_id = 2;
            $touser = self::getUserId($b);
//            $touser = 64;
//            $data = array('agentid' => $agent_id, 'content' => $msg, 'to_user' => $touser);
//            R('Weixin/Message/sendText',$data);
            $mdata = array('agentid' => $agent_id, 'msgtype' => "text", 'text' => array('content' => $msg), 'touser' => $touser);
            R('Weixin/QYWeixin/sendMessage', array('data' => $mdata, 'mpid' => ''), 'Api');
        }
        if(empty($modelMysqlResult))
        {
            $jsonArray = array(
                'scancount'=>1,
                'recent_time'=>NOW_TIME,
                'first_time'=>NOW_TIME,
                'codestatus'=>$codeTf,
                'codetype'=>substr($b,4,1),
            );
        }
        else
        {
            $jsonArray = array(
                'scancount'=>$modelMysqlResult[0]['scan_count']+1,
                'recent_time'=>$modelMysqlResult[0]['recent_time'],
                'first_time'=>$modelMysqlResult[0]['first_time'],
                'codestatus'=>$codeTf,
                'codetype'=>substr($b,4,1),
                'scan_tips_text'=>$errorInfo,
            );
        }

        json('001',$jsonArray);

//        if($codeTf)
//        {
//            json('001',$jsonArray);
//        }
//        else
//        {
//            $jsonArray = array(
//                'b'=>$b,
//                'c'=>$c,
//            );
//            json('002',$jsonArray,'code error!');
//        }
    }
    /*
     * 检查码真伪,给小飞用
     */
    public function checkCode($b,$c){
        $model = new ApiModel();
        $codeType = $model->getScanCodeType($b);
        //---step--1--- : 码真伪,true真码,false假码
        $codeTf = $model->getCodeStatus($codeType,$c,$b);
        //return $codeTf;
        if($codeTf){
            echo '1';
        }else{
            echo '0';
        }

    }

    public function fake()
    {
        $modelMongo = new ApiModel();
        $modelMysql = D('base_scaninfo');

        $companyid = $modelMongo->getCompanyId();

        $modelMysqlMap = array(
            'companyid'=>$companyid,
            'istf'=>1,
        );
        $list = $modelMysql->where($modelMysqlMap)->select();

        if(!empty($list))
        {
            json('001',$list);
        }
        else
        {
            json('001',array());//没有假货数据返回空
        }
    }

    public function scanlog($c, $b, $flag, $longitude, $latitude, $location)
    {
        /*
        $c = I('c','0','htmlspecialchars');
        $b = I('b','0','htmlspecialchars');
        */
        $modelMongo = new ApiModel();
        $modelMysql = D('base_scan_log');
        $ipInfo = $modelMongo->getIpLocation();

        if(!empty($longitude) & !empty($latitude))
        {
            $dataLog['log'] = $longitude;
            $dataLog['lat'] = $latitude;
            $dataLog['ipaddr'] = $location;
            $dataLog['address'] = $location;
        }
        else
        {
            $dataLog['log'] = $ipInfo['log'];
            $dataLog['lat'] = $ipInfo['lat'];
            $dataLog['ipaddr'] = $ipInfo['address'];
            $dataLog['address'] = $ipInfo['address'];
        }

        $dataLog['b'] = $b;
        $dataLog['c'] = $c;
        $dataLog['ip'] = $ipInfo['ip'];
        $dataLog['companyid'] = substr($b,0,4);
        $dataLog['productid'] = getProductid($b);
        $dataLog['mac'] = null;
        $dataLog['quality_goods'] = null;
        $dataLog['create_time'] = NOW_TIME;
        $dataLog['istf'] = $flag;
        $modelMysql->add($dataLog);
        //json('001');

    }

    public function scaninfo()
    {

//        $c = I('c','0','htmlspecialchars');
//        $b = I('b','0','htmlspecialchars');

        $b = $_SESSION["code_info"]["b"];
        $c = $_SESSION["code_info"]["c"];
        $longitude = I('longitude','0','htmlspecialchars');
        $latitude = I('latitude','0','htmlspecialchars');
        $location = I('location','0','htmlspecialchars');

        $modelMongo = new ApiModel();
        $modelMysql = D('base_scaninfo');
        $modelMysqlMap = array(
            'b'=>$b,
        );
        $modelMysqlResult = $modelMysql->where($modelMysqlMap)->select();
        if(!empty($modelMysqlResult))
        {
            $codeType = $modelMongo->getScanCodeType($b);
            //---step--1--- : 码真伪
            $codeTf = $modelMongo->getCodeStatus($codeType,$c,$b);
            if($codeTf & !$modelMysqlResult[0]['istf'])
            {
                $istf = 0;
            }
            else
            {
                $istf = 1;
            }
            self::scanlog($c, $b, $istf, $longitude, $latitude, $location);
            $ipInfo = $modelMongo->getIpLocation();
            $dataInfo['recent_time'] = NOW_TIME;
            $dataInfo['recent_ip'] = $ipInfo['ip'];
            $dataInfo['recent_ipaddr'] = $ipInfo['address'];
            $dataInfo['recent_address'] = $ipInfo['address'];
            $dataInfo['recent_mac'] = null;
            $dataInfo['scan_count'] = $modelMysqlResult[0]['scan_count']+1;
            $dataInfo['istf'] = $istf;
            $modelMysql->where($modelMysqlMap)->field('recent_time,recent_ip,recent_ipaddr,recent_address,recent_mac,scan_count,istf')->save($dataInfo);
            json('001');
        }
        else
        {
            $codeType = $modelMongo->getScanCodeType($b);
            //---step--1--- : 码真伪
            $codeTf = $modelMongo->getCodeStatus($codeType,$c,$b);
            if($codeTf)
            {
                $istf = 0;
            }
            else
            {
                $istf = 1;
            }
            self::scanlog($c, $b, $istf, $longitude, $latitude, $location);
            $ipInfo = $modelMongo->getIpLocation();
            $dataInfo['b'] = $b;
            $dataInfo['c'] = $c;
            $dataInfo['first_time'] = NOW_TIME;
            $dataInfo['first_ip'] = $ipInfo['ip'];
            $dataInfo['first_ipaddr'] = $ipInfo['address'];
            $dataInfo['first_address'] = $location;
            $dataInfo['first_mac'] = null;
            $dataInfo['scan_count'] = 1;
            $dataInfo['companyid'] = substr($b,0,4);
            $dataInfo['productid'] = getProductid($b);
            $dataInfo['istf'] = $istf;
            $dataInfo['productid']=getProductid($b);
            $dataInfo['recent_time'] = NOW_TIME;
            $dataInfo['recent_ip'] = $ipInfo['ip'];
            $dataInfo['recent_ipaddr'] = $ipInfo['address'];
            $dataInfo['recent_address'] = $location;
            $dataInfo['recent_mac'] = null;
            //$modelMysql->where($modelMysqlMap)->add($dataInfo);
            $modelMysql->add($dataInfo);
            json('001');
        }
    }

    public function scan()
    {
        $model = new ApiModel();

        $mysqlModel = D('base_scaninfo');
        $modelMap = array(
            'zm_base_scaninfo.companyid'=>$model->getCompanyId(),
        );
        //$result = $mysqlModel->where($modelMap)->select();
        $result = $mysqlModel->join([
            'LEFT JOIN __BASE_PRODUCT__ ON __BASE_SCANINFO__.productid=__BASE_PRODUCT__.productid'])->where($modelMap)->select();
        if($result){
            json('001',$result);
        }
        else{
            json('001',array());
        }
    }

    public function verify()
    {
        $c = I('c','0','htmlspecialchars');
        $b = I('b','0','htmlspecialchars');

        //---step--3--- : session处理
        TraceAPI::prepareInfo($b,$c);
        if($_SESSION["code_info"]["scan_template"]=="2"){
            $this->display('template/rpmy.html');
        }
        else
        {
            $this->display('App/ScanCode/index.html');
        }
//
//
//        $model = new ApiModel();
//        $codeType = $model->getScanCodeType($b);
//
//        //---step--1--- : 码真伪
//        $codeTf = $model->getCodeStatus($codeType,$c,$b);
//        //---step--2--- ：黑名单
//        $codeIsLimit = Authenticity::blackList($b);
//
//        if($codeTf & $codeIsLimit)
//        {
//            //json('001',$codeTf);
//            $this->display('App/ScanCode/index.html');
//        }
//        else
//        {
//            //json('002',$codeTf,'code error!');
//            $this->display('App/ScanCode/index.html');
//        }
//        //---step--2--- ：黑名单
//        $codeIsLimit = Authenticity::blackList($b);
//

//
//        //---step--4--- : 获取溯源信息
//
//        //---step--5--- : 插入扫码记录信息
//
//        //---step--6--- : json前台

    }
    /**
     * 查询IP地址对应的地理位置信息
     * @param $ip
     *
     * @return null
     */
    public static function iplookup($ip)
    {
        $ch = curl_init();
        $url = 'http://apis.baidu.com/apistore/iplookupservice/iplookup?ip='.$ip;
        $header = array(
            'apikey: 154f3d2491a4b0ddff9dea326977b9d7',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch, CURLOPT_URL, $url);
        $res = json_decode(curl_exec($ch),true);

        if ($res->errNum==0)
        {
            return $res["retData"];
        }
        else
        {
            return null;
        }
    }


    /**
     * 按时间查询假货数据2016822
     */
    public function SearchFake(){
        $starttime = I('startdate',null);
        $endtime = I('enddate',null);

        $re = MatchtimeAPI::checkDate($starttime,$endtime);
        if($re === false){
            return;
        }

        $modelMongo = new ApiModel();
        $modelMysql = D('base_scaninfo');

        $companyid = $modelMongo->getCompanyId();

        if($starttime && $endtime){
            $modelMysqlMap = array(
                'companyid'=>$companyid,
                'istf'=>1,
                'recent_time' => array('between',array($re[1][0],$re[1][1]))
            );
        }elseif($starttime && empty($endtime)){
            $modelMysqlMap = array(
                'companyid'=>$companyid,
                'istf'=>1,
                'recent_time' => array('egt',$re[1])
            );
        }elseif(empty($starttime) && $endtime){
            $modelMysqlMap = array(
                'companyid'=>$companyid,
                'istf'=>1,
                'recent_time' => array('elt',$re[1])
            );
        }elseif($starttime == null && $endtime == null){
            $modelMysqlMap = array(
                'companyid'=>$companyid,
                'istf'=>1,
            );
        }

        $list = $modelMysql->where($modelMysqlMap)->select();

        if(!empty($list))
        {
            json('001',$list);
        }
        else
        {
            json('001',array());//没有假货数据返回空
        }
    }

    /**
     * 按时间查询扫码记录2016822
     */
    public function SearchScan(){
        $starttime = I('startdate',null);
        $endtime = I('enddate',null);
        $re = MatchtimeAPI::checkDate($starttime,$endtime);

        if($re === false){
            return;
        }

        $model = new ApiModel();
        $mysqlModel = D('base_scaninfo');
        if($starttime && $endtime){
            $modelMap = array(
                'zm_base_scaninfo.companyid'=>$model->getCompanyId(),
                'first_time' => array('between',array($re[1][0],$re[1][1]))
            );
        }elseif($starttime && empty($endtime)){
            $modelMap = array(
                'zm_base_scaninfo.companyid'=>$model->getCompanyId(),
                'first_time' => array('egt',$re[1])
            );
        }elseif(empty($starttime) && $endtime){
            $modelMap = array(
                'zm_base_scaninfo.companyid'=>$model->getCompanyId(),
                'first_time' => array('elt',$re[1])
            );
        }elseif($starttime == null && $endtime == null){
            $modelMap = array(
                'zm_base_scaninfo.companyid'=>$model->getCompanyId(),
            );
        }

        $result = $mysqlModel->join([
            'LEFT JOIN __BASE_PRODUCT__ ON __BASE_SCANINFO__.productid=__BASE_PRODUCT__.productid'])->where($modelMap)->select();
        if($result){
            json('001',$result);
        }
        else{
            json('001',array());
        }
    }

    /**
     * 查找企业id2016910
     * @param $b
     */
    static function getUserId($b){
        $model = M("BaseScaninfo");
        $map = [
            'zm_base_scaninfo.b' => $b
        ];
        $id = $model->where($map)->join('zm_base_user ON zm_base_scaninfo.companyid = zm_base_user.companyid')->getField('zm_base_user.id');

        return $id;
    }
}