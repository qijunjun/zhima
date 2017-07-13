<?php
/**
 * Created by PhpStorm.
 * User: Liming
 * Date: 2016/5/6
 * Time: 13:14
 */


namespace Product\Controller;

use Common\Controller\BaseController;
use CheckIO\API;
use Product\API\Correlation;
use Product\API\CorrelationPack;

/**
 * 包装码与质量码关联
 * Class CorrelationPackController
 * @package Product\Controller
 */
class CorrelationPackController extends BaseController {
    /**
     * 将码段关联结果插入数据库
     * 必须先进行view预览，得到确认结果后才能使用这个操作，将结果存入数据库
     *
     * @param Integer $t view操作输出结果中的_t参数，直接传入即可
     *
     * @return void
     */
    public function insert($t) {
        if(!session('?AssociateCodeSegment')) {  //SESSION中没有数据
            json("002", ['code' => -1, 'message' => '请先预览结果！']);
            return;
        }
        if(!session('?user_info')) {
            json("002");
            return;
        }
        $data = session('AssociateCodeSegment');
        if($data["_t"] != $t) {  //参数与所存_t不一致
            json("009", ['code' => -2, 'message' => '预览结果不匹配，请尝试重新预览！']);
            return;
        }
        //数据操作
        $map = [];
        $companyId = session('user_info')['companyid'];
        $time = time();

        //循环检测箱码与质量码是否有重复201689
        foreach($data['data'] as $v) {
            //检验箱码是不否合法，是否重复
            $qrcode_pack    =$v['x'];
            //判断是否合法
            $res_pack = CorrelationPack::checkCompany($qrcode_pack);
            if($res_pack == false){
                json("002",null,"箱码不合法，请检查后重新输入！");
                return;
            }
            //判断是否有未生成箱码
            $re = CorrelationPack::checkQrcodeUsed($qrcode_pack);
            if($re == false){
                json("002",null,"箱码还未生成，请生成后再操作");
                return;
            }
            //判断是否已有关联码段
            $result_pack = CorrelationPack::checkRepeat($qrcode_pack);
            if($result_pack){
                json("009",null,"箱码已关联质量码");
                return;
            }

            //检验起始质量码和结束质量码是否合法，是否重复
            $qrcode_range_s = $v['p'][0];
            $qrcode_range_e = $v['p'][count($v['p'])-1];
            //判断是否合法
            $res = checkQCode($companyId,$qrcode_range_s,$qrcode_range_e);
            if($res == false){
                json( "002",null,"质量码不合法，请检查后重新输入！");
                return;
            }
            //判断起始码是否大于结束码
            if($qrcode_range_s > $qrcode_range_e){
                json("002",null,"起始质量码不能大于结束质量码");
                return;
            }
            //判断是否有未生成质量码
            $re = CorrelationPack::checkQrcodeUseds($qrcode_range_s,$qrcode_range_e);
            if($re == false){
                json("002",null,"部分质量码未生成，请生成后操作");
                return;
            }
            //判断是否已有关联码段
            $result_range = CorrelationPack::checkRepeatQrcode($qrcode_range_s,$qrcode_range_e);
            if($result_range){
                json("009",null,"质量码段已关联箱码");
                return;
            }

            $map[] = [
                'company_id'     => $companyId,
                'qrcode_pack'    => $qrcode_pack,
                'qrcode_range_s' => $qrcode_range_s,
                'qrcode_range_e' => $qrcode_range_e,
                'ratio'          => $data['number'],
                'create_time'    => $time,
                'status'         => 1
            ];
        }

        $result = M('corr_qrcode_pack')->addAll($map);
        if($result === false) {
            json("007");
        } else {
            json("001", $result);
            //处理完成，删除已存数据
            session('AssociateCodeSegment', null);
        }
    }

    /**
     * 预览关联结果
     * 将所输入的参数进行关联计算，得到关联结果以便预览。
     * 输出的结果中包含的_t在insert操作时需要用到！
     *
     * @param array   $ping     瓶码段 ["100000000000-200000000000", "300000000000-400000000000"]
     * @param array   $xiang    箱码段 ["100000000000-200000000000", "300000000000-400000000000"]
     * @param Integer $number   [一箱对应瓶数]
     * @param array   $pingFei  [瓶码废标] ["100000550055", "100000000777", "300000000055", "400000000000"]
     * @param array   $xiangFei [箱码废标] ["100000550055", "100000000777", "300000000055", "400000000000"]
     *
     * @return void
     */
    public function view($ping, $xiang, $number = 6, $pingFei = null, $xiangFei = null) {
        if(!session('?user_info')) {
            json("002");
            return;
        }
        if(!is_array($ping) || !is_array($xiang)) {
            json("009");
            return;
        }
        if(session('?AssociateCodeSegment')) {  //检测是否相同参数重复预览
            if(
                $_SESSION['AssociateCodeSegment']['ping'] == $ping &&
                $_SESSION['AssociateCodeSegment']['xiang'] == $xiang &&
                $_SESSION['AssociateCodeSegment']['number'] == $number &&
                $_SESSION['AssociateCodeSegment']['pingFei'] == $pingFei &&
                $_SESSION['AssociateCodeSegment']['xiangFei'] == $xiangFei
            ) {
                //直接返回已计算结果
                json("001", [
                    "data" => $_SESSION['AssociateCodeSegment']['data'],
                    "_t"   => $_SESSION['AssociateCodeSegment']['_t']
                ]);
                return;
            }
        }
        $result = [];
        //数组计数器
        $ip = 0;
        $ix = 0;
        //第一个瓶码段
        $pn = explode('-', $ping[$ip]);
        if(count($pn) !== 2) {  //瓶码段错误
            json("009", [
                "code"    => -1,
                "line"    => $ip + 1,
                "string"  => $ping[$ip],
                "message" => "第".($ip + 1)."个瓶码段错误"
            ]);
            return;
        }
        $ps = floatval($pn[0]);  //瓶码起始
        $pe = floatval($pn[1]);  //瓶码终止
        if($pe < $ps) {  //如果尾小于头
            $temp = $pe;
            $pe = $ps;
            $ps = $temp;
        }
        //第一个箱码段
        $xn = explode('-', $xiang[$ix]);
        if(count($xn) !== 2) {  //箱码段错误
            json("009", [
                "code"    => -2,
                "line"    => $ix + 1,
                "string"  => $xiang[$ix],
                "message" => "第".($ix + 1)."个箱码段错误"
            ]);
            return;
        }
        $xs = floatval($xn[0]);  //箱码起始
        $xe = floatval($xn[1]);  //箱码终止
        if($xe < $xs) {  //如果尾小于头
            $temp = $xe;
            $xe = $xs;
            $xs = $temp;
        }
        $countp = count($ping);
        $countx = count($xiang);
        //瓶箱对应
        while(true) {
            //箱码结束
            if($ix >= $countx) {
                break;
            }
            //寻找箱码废标
            $isFei = true;
            foreach($xiangFei as $xfv) {
                if($xfv == $xs) {
                    $isFei = false;
                    break;
                }
            }
            if($xs <= $xe && $isFei) {
                $newresult = [];
                $newresult['x'] = $xs;
                $newresult['p'] = [];
                for($pxh = 0; $pxh < $number; $pxh++) {
                    //瓶码结束
                    if($ip >= $countp) {
                        break;
                    }
                    //寻找瓶码废标
                    $isF = true;
                    foreach($pingFei as $pfv) {
                        if($pfv == $ps) {
                            $isF = false;
                            break;
                        }
                    }
                    if($ps <= $pe && $isF) {
                        //瓶入箱
                        $newresult['p'][] = $ps;
                    } else {
                        //跳过
                        $pxh--;
                    }
                    if($ps >= $pe) {
                        //当前瓶段结束
                        $ip++;
                        //瓶码结束
                        if($ip >= $countp) {
                            break;
                        }
                        //下一个瓶码段
                        $pn = explode('-', $ping[$ip]);
                        if(count($pn) !== 2) {  //瓶码段错误
                            json("009", [
                                "code"    => -1,
                                "line"    => $ip + 1,
                                "string"  => $ping[$ip],
                                "message" => "第".($ip + 1)."个瓶码段错误"
                            ]);
                            return;
                        }
                        $ps = floatval($pn[0]);
                        $pe = floatval($pn[1]);
                        if($pe < $ps) {  //如果尾小于头
                            $temp = $pe;
                            $pe = $ps;
                            $ps = $temp;
                        }
                    } else {
                        //下一个瓶码
                        $ps++;
                    }
                }
                $result[] = $newresult;
            }
            if($xs >= $xe) {
                //当前箱码结束
                $ix++;
                //箱码结束
                if($ix >= $countx) {
                    break;
                }
                //下一个箱码段
                $xn = explode('-', $xiang[$ix]);
                if(count($xn) !== 2) {  //箱码段错误
                    json("009", [
                        "code"    => -2,
                        "line"    => $ix + 1,
                        "string"  => $xiang[$ix],
                        "message" => "第".($ix + 1)."个箱码段错误"
                    ]);
                    return;
                }
                $xs = floatval($xn[0]);
                $xe = floatval($xn[1]);
                if($xe < $xs) {  //如果尾小于头
                    $xtt = $xe;
                    $xe = $xs;
                    $xs = $xtt;
                }
            } else {
                //下一个箱码
                $xs++;
            }
        }
        $data = [
            "data" => $result,
            "_t"   => time()
        ];
        json("001", $data);
        session('AssociateCodeSegment', null);
        $data['ping'] = $ping;
        $data['xiang'] = $xiang;
        $data['number'] = $number;
        $data['pingFei'] = $pingFei;
        $data['xiangFei'] = $xiangFei;
        session('AssociateCodeSegment', $data);
    }

    /**
     * 获取关联数据
     */
    public function getList() {
        $model = M('corr_qrcode_pack');
        //$result = $model->select();
        $result = $model->where(['company_id' => session('user_info')['companyid']])->select();//byxu
        if($result === false) {
            json("007");
            return;
        } else {
            $records = array();
            foreach ($result as $key=>$item){
                $productinfo=API\CheckioAPI::productInfo($item['qrcode_range_s']);
                if($productinfo!=null){
                    $record = array(
                        'id'=>$item['id'],
                        'qrcode_pack'=>$item["qrcode_pack"],
                        "product_name" => $productinfo['name'],
                        'spec'=>$productinfo['spec'],
                        'qrcode_range_s'=>$item['qrcode_range_s'],
                        'qrcode_range_e'=>$item["qrcode_range_e"],
                        'time'=>$item['create_time']
                    );
                    array_push($records, $record);
                }else{
                    $record = array(
                        'id'=>$item['id'],
                        'qrcode_pack'=>$item["qrcode_pack"],
                        "product_name" => '无信息',
                        'spec'=>'无信息',
                        'qrcode_range_s'=>$item['qrcode_range_s'],
                        'qrcode_range_e'=>$item["qrcode_range_e"],
                        'time'=>$item['create_time']
                    );
                    array_push($records, $record);
                }
            }
            unset($record);
            json("001", $records);
        }
    }

    /**
     * 删除关联数据
     * @param integer $id ID
     */
    public function remove($id) {
        $model = M('corr_qrcode_pack');
        $companyId = session('user_info')['companyid'];
        $result = $model->where(['id' => $id, 'company_id' => $companyId])->delete();
        if($result === false) {
            json("009");
        } else {
            json("001", $result);
        }
    }
}
