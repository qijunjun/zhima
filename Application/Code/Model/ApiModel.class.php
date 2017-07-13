<?php
/**
 * Created by Yangbin.
 * User: Yangbin
 * Date: 2016/4/13
 * Time: 15:45
 * ver:1.3
 */
namespace Code\Model;

use Think\Model\MongoModel;
use Faker\API\Authenticity;

class ApiModel extends MongoModel
{

    public function __construct($name, $tablePrefix, $connection)
    {

        parent::__construct($name, $tablePrefix, $connection);

//
//        if(empty($_SESSION['user_info']['id']))
//        {
//            json('002',null,'no login!');
//            exit;
//        }

        $this->trueTableName = $name;

        $this->tablePrefix = $tablePrefix;

        $this->connection = $connection;

        $this->companyid = $_SESSION['user_info']['companyid'];

        $this->userid = $_SESSION['user_info']['id'];

    }

    public function __destruct()
    {

    }

    protected $connection = array(
        'db_type' => 'mongo',
        'db_host' => 'localhost',
        'db_port' => '27017',
        'db_user' => 'zhima',
        'db_pwd' => 'zhima',
        'db_name' => 'zhima',
        'db_charset' => 'utf8',
    );

    protected $dbName = 'zhima';

    Protected $_idType = self::TYPE_INT;

    protected $_autoinc = true;

    public function show()
    {

        $model = D('base_qrcode_used_log');
        $modelMapXz = array(
            '_string' => 'companyid=' . $this->getCompanyId() . ' AND flag <> 2',

        );
        $modelMapRelation = array(
            '_string' => 'companyid=' . $this->getCompanyId() . ' AND flag=2',
        );
        $listXz = $model->where($modelMapXz)->order('operate_time desc')->select();

        $listRelation = $model->where($modelMapRelation)->order('operate_time desc')->select();

        $result = array(
            'listxz' => $listXz,
            'listrelation' => $listRelation,
            'coderemainder' => $this->setCodeAvailableNumber(),
        );

        json('001', $result);

    }

    public function getScanCodeType($b)
    {
        $codeType = substr($b, 4, 1);

        return $codeType;
    }

    public function getCodeStatus($codeType, $c, $b)
    {
        import("Code.Lib.Mongo.MongoInt64");
        switch ($codeType) {
            case '0' :

                $model = new ApiModel('xcode');
                $modelMap = array(
                    'b' => new \MongoInt64($b),
                );
                $result = $model->where($modelMap)->select();
                $codeIsLimit = Authenticity::blackList($b);//验证黑名单,true表示为在黑名单里
                if (!empty($result) & $result[0]['c'] == $c & !$codeIsLimit) {
                    return true;
                } else {
                    return false;
                }

                break;
            case '1' :

                $model = new ApiModel('zcode');
                $modelMap = array(
                    'b' => new \MongoInt64($b),
                );
                $result = $model->where($modelMap)->select();
                $codeIsLimit = Authenticity::blackList($b); //验证黑名单
                if (!empty($result) & $result[0]['c'] == $c & !$codeIsLimit) {
                    return true;
                } else {
                    return false;
                }

                break;
            default:
                json('002', null, 'code error!');
                break;
        }
    }

    public function getCsv($id, $max)
    {
        $model = D('base_qrcode_used_log');

        $modelMap = array(
            'id' => $id,
        );

        $result = $model->where($modelMap)->select();

        if (empty($result)) {
            json('002', null, 'Missing primary key!');
            exit;
        }

        if ($result[0]['flag'] == 0) {
            $documentName = 'xcode-' . '' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'];
            $documentNameTarGz = 'xcode-' . '' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'] . '.tar.gz';
            //$path =__PUBLIC__ . 'code/';
            $pathPre = __PUBLIC__ . 'code/';
            $path = '/zhimaCode/';
            if (is_file($pathPre . $documentNameTarGz)) {

                $downloadLink = array(
                    'link' => '/resources/code/' . $documentNameTarGz,
                );

                json('001', $downloadLink, 'This is the download address. ');

                exit;
            } else {
                mkdir($path);
                $cmdMkdir = 'mkdir ' . $path . $documentName;
                exec($cmdMkdir, $out, $status);
                if (!$status) {
                    //$path =__PUBLIC__ . 'code/' . $documentName . '/';
                    $path = '/zhimaCode/' . $documentName . '/';
                    $pathPre = __PUBLIC__ . 'code/';
                    $field = 'b,c';
                    $condition = 'b';
                    $collection = 'xcode';
                    $outMongoName = 'xcode-' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'] . ".dat";
                    $outFinalName = 'xcode-' . 'final-' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'] . ".dat";
                    $type = $result[0]['flag'];
                    $qrcode_s = $result[0]['qrcode_s'];
                    $qrcode_e = $result[0]['qrcode_e'];
                    $cmdResult = $this->getMongoExport($path, $collection, $outMongoName, $qrcode_s, $qrcode_e, $field, $condition);

                    if (!$cmdResult) {
                        $cmdResult = $this->parseCsv($path, $type, $path . $outMongoName, null, $outFinalName);

                        $cmdRm = 'rm -rf ' . $path . $outMongoName;

                        exec($cmdRm, $out, $status);

                        $cmdResult = $this->splitCsv($path . $outFinalName, $max, $path);

                        $cmdRm = 'rm -rf ' . $path . $outFinalName;

                        exec($cmdRm, $out, $status);

                        $cmdDatToTxt = 'rename .dat .txt ' . $path . '*.dat';

                        exec($cmdDatToTxt, $out, $status);

                        //$cmdTar = 'tar -zcvf ' . $pathPre . $documentName . '.tar.gz ' . $pathPre . $documentName;
                        $cmdTar = 'tar -zcvf ' . $pathPre . $documentName . '.tar.gz ' . $path . '*';

                        exec($cmdTar, $out, $status);//123

                        $downloadLink = array(
                            'link' => '/resources/code/' . $documentName . '.tar.gz',
                        );

                        //json('001',$downloadLink,'This is the download address. ');

                        //$this->downloadFile($path.$outFinalName,$outFinalName);

                        //$cmdRm = 'rm -rf ' . $path . $outMongoName;

                        //exec($cmdRm,$out,$status);
                    } else {
                        json('002', $cmdResult, 'mongoexp error!');
                    }
                } else {
                    json('002', $status, 'mkdir error!');
                }
            }
        } else if ($result[0]['flag'] == 1)//
        {
            $documentName = 'zcode-' . '' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'];
            $documentNameTarGz = 'zcode-' . '' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'] . '.tar.gz';
            //$path =__PUBLIC__ . 'code/';
            $pathPre = __PUBLIC__ . 'code/';
            $path = '/zhimaCode/';
            if (is_file($pathPre . $documentNameTarGz)) {

                $downloadLink = array(
                    'link' => '/resources/code/' . $documentNameTarGz,
                );

                json('001', $downloadLink, 'This is the download address. ');

                exit;

            } else {
                mkdir($path);
                $cmdMkdir = 'mkdir ' . $path . $documentName;
                exec($cmdMkdir, $out, $status);
                if (!$status) {
                    //$path =__PUBLIC__ . 'code/' . $documentName . '/';
                    $path = '/zhimaCode/' . $documentName . '/';
                    $pathPre = __PUBLIC__ . 'code/';
                    $field = 'b,c';
                    $condition = 'b';
                    $collection = 'zcode';
                    $outMongoName = 'zcode-' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'] . ".dat";
                    $outFinalName = 'zcode-' . 'final-' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'] . ".dat";
                    $sh = 'zcode-' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'] . ".sh";
                    $type = $result[0]['flag'];
                    $qrcode_s = $result[0]['qrcode_s'];
                    $qrcode_e = $result[0]['qrcode_e'];
                    $cmdResult = $this->getMongoExport($path, $collection, $outMongoName, $qrcode_s, $qrcode_e, $field, $condition);

                    if (!$cmdResult) {
                        $cmdResult = $this->parseCsv($path, $type, $path . $outMongoName, null, $outFinalName);

                        //$this->downloadFile($path.$outFinalName,$outFinalName);

                        $cmdRm = 'rm -rf ' . $path . $outMongoName;

                        exec($cmdRm, $out, $status);

                        $cmdResult = $this->splitCsv($path . $outFinalName, $max, $path);

                        $cmdRm = 'rm -rf ' . $path . $outFinalName;

                        exec($cmdRm, $out, $status);

                        $cmdDatToTxt = 'rename .dat .txt ' . $path . '*.dat';

                        exec($cmdDatToTxt, $out, $status);

                        $cmdTar = 'tar -zcvf ' . $pathPre . $documentName . '.tar.gz ' . $path . '*';

                        exec($cmdTar, $out, $status);

                        $downloadLink = array(
                            'link' => '/resources/code/' . $documentName . '.tar.gz',
                        );

                        //json('001',$downloadLink,'This is the download address. ');
                    } else {
                        json('002', $cmdResult);
                    }
                } else {
                    json('002', $status, 'mkdir error!');
                }
            }

        } else if ($result[0]['flag'] == 2 & !empty($result[0]['relation'])) {
            $zcodeMap = array(
                'id' => $result[0]['relation'],
            );
            $zcodeResult = $model->where($zcodeMap)->select();

            if (empty($zcodeResult)) {
                json('002', null, 'no coderelation data!');
                exit;
            }

            $documentName = 'coderelation-' . '' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'];
            $documentNameTarGz = 'coderelation-' . '' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'] . '.tar.gz';
            //$path =__PUBLIC__ . 'code/';
            $pathPre = __PUBLIC__ . 'code/';
            $path = '/zhimaCode/';
            if (is_file($pathPre . $documentNameTarGz)) {

                $downloadLink = array(
                    'link' => '/resources/code/' . $documentName . '.tar.gz',
                );

                json('001', $downloadLink, 'This is the download address. ');

                exit;
            } else {
                mkdir($path);
                $cmdMkdir = 'mkdir ' . $path . $documentName;
                exec($cmdMkdir, $out, $status);
                if (!$status) {
                    //---step-1---
                    //$path =__PUBLIC__ . 'code/' . $documentName . '/';
                    $path = '/zhimaCode/' . $documentName . '/';
                    $pathPre = __PUBLIC__ . 'code/';
                    $field = 'b,c';
                    $condition = 'b';
                    $collection = 'zcode';
                    $outZcodeMongoName = 'zcoderelation-' . $zcodeResult[0]['qrcode_s'] . '-' . $zcodeResult[0]['qrcode_e'] . ".dat";
                    $type = $zcodeResult[0]['flag'];
                    $qrcode_s = $zcodeResult[0]['qrcode_s'];
                    $qrcode_e = $zcodeResult[0]['qrcode_e'];
                    $cmdResult = $this->getMongoExport($path, $collection, $outZcodeMongoName, $qrcode_s, $qrcode_e, $field, $condition);

                    //---step-2---
                    $field = 'qrcode_pack';
                    $condition = 'qrcode_pack';
                    $collection = 'coderelation';
                    $outCodeRelationMongoName = 'coderelation-' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'] . ".dat";
                    $type = $result[0]['flag'];
                    $qrcode_s = $result[0]['qrcode_s'];
                    $qrcode_e = $result[0]['qrcode_e'];
                    $cmdResult = $this->getMongoExport($path, $collection, $outCodeRelationMongoName, $qrcode_s, $qrcode_e, $field, $condition);

                    //---step-3---
                    $outFinalName = 'coderelation-' . 'final-' . $result[0]['qrcode_s'] . '-' . $result[0]['qrcode_e'] . ".dat";
                    $cmdResult = $this->parseCsv($path, $type, $path . $outCodeRelationMongoName, $path . $outZcodeMongoName, $outFinalName);
                    //$this->downloadFile($path.$outFinalName,$outFinalName);

                    $cmdRm = 'rm -rf ' . $path . $outZcodeMongoName;
                    exec($cmdRm, $out, $status);
                    $cmdRm = 'rm -rf ' . $path . $outCodeRelationMongoName;
                    exec($cmdRm, $out, $status);

                    $cmdResult = $this->splitCsv($path . $outFinalName, $max, $path);

                    $cmdRm = 'rm -rf ' . $path . $outFinalName;

                    exec($cmdRm, $out, $status);//

                    $cmdDatToTxt = 'rename .dat .txt ' . $path . '*.dat';

                    exec($cmdDatToTxt, $out, $status);

                    $cmdTar = 'tar -zcvf ' . $pathPre . $documentName . '.tar.gz ' . $path . '*';

                    exec($cmdTar, $out, $status);

                    $downloadLink = array(
                        'link' => '/resources/code/' . $documentName . '.tar.gz',
                    );

                    //json('001',$downloadLink,'This is the download address. ');
                } else {
                    json('002', $status, 'mkdir error!');
                }
            }
        } else if ($result[0]['flag'] == 2 & empty($result[0]['relation'])) {
            json('002', null, 'This can not be exported!');
        }

    }

    //3.0以上写法
    public function getMongoExport($path, $collection, $outName, $qrcode_s, $qrcode_e, $field, $condition)
    {
        $command = '/usr/bin/mongoexport -h localhost -u zhima -p zhima -c ' . $collection . ' -d zhima -f ' . $field . ' -o ' . $path . $outName . ' --type=csv -q ' . "'" . '{' . $condition . ':{$gte:' . $qrcode_s . ',' . '$lte:' . $qrcode_e . '}}' . "'";
        exec($command, $out, $status);
        return $status;
    }

//    //3.0以下写法
//    public function getMongoExport($path, $collection, $outName, $qrcode_s, $qrcode_e, $field, $condition)
//    {
//      $command = '/usr/bin/mongoexport -h localhost -c ' . $collection . ' -d zhima -f ' . $field . ' -o ' . $path . $outName . ' --type=csv -q ' . "'" . '{' . $condition . ':{$gte:' . $qrcode_s . '},' . $condition . ':{$lte:' . $qrcode_e . '}}' . "'";
//	$command = '/usr/bin/mongoexport -h localhost -c ' . $collection . ' -d zhima -f ' . $field . ' -o ' . $path . $outName . ' --type=csv -q ' . "'" . '{' . $condition . ':{$gte:' . $qrcode_s . ',' . '$lte:' . $qrcode_e . '}}' . "'";
//        $command = '/usr/bin/mongoexport -h localhost -c ' . $collection . ' -d zhima -f ' . $field . ' -o ' . $path . $outName . ' --csv -q ' . "'" . '{' . $condition . ':{$gte:' . $qrcode_s . '},' . $condition . ':{$lte:' . $qrcode_e . '}}' . "'";
//        exec($command,$out,$status);
//        return $status;
//    }

    public function parseCsv($path, $type, $csvDat_1, $csvDat_2, $outName)
    {
        /*help :
        cmd_pro_zmade [2] [coderelationout.dat] [zcoderelationout.dat] [outName.dat]
        cmd_pro_zmade [1] [zcodeout.dat] [outName.dat]
        cmd_pro_zmade [0] [xcodeout.dat] [outName.dat]*/
        if ($type == 1 || $type == 0) {
            $command = '/usr/bin/cmd_pro_zmade ' . $type . ' ' . $csvDat_1 . ' ' . $path . $outName;
        } else {
            $command = '/usr/bin/cmd_pro_zmade ' . $type . ' ' . $csvDat_1 . ' ' . $csvDat_2 . ' ' . $path . $outName;
        }

        exec($command, $out, $status);
        return $status;
    }

    public function parseCsvOther($path, $type, $csvDat_1, $csvDat_2, $outName)
    {
        /*help :
        cmd_pro_zmade [2] [coderelationout.dat] [zcoderelationout.dat] [outName.dat]
        cmd_pro_zmade [1] [zcodeout.dat] [outName.dat]
        cmd_pro_zmade [0] [xcodeout.dat] [outName.dat]*/
        if ($type == 1 || $type == 0) {
            $command = '/usr/bin/cmd_pro_zmade_x ' . $type . ' ' . $csvDat_1 . ' ' . $path . $outName;
        } else {
            //$command = '/usr/bin/cmd_pro_zmade ' . $type . ' ' . $csvDat_1 . ' ' . $csvDat_2 . ' ' . $path . $outName;
        }

        exec($command, $out, $status);
        return $status;
    }

    public function splitCsv($csvDat_1, $max, $path)
    {
        $type = '0';
        $command = '/usr/bin/cmd_pro_zmade_split ' . $type . ' ' . $csvDat_1 . ' ' . $max . ' ' . $path;
        exec($command, $out, $status);
        return $status;
    }

    public function downloadFile($path, $saveName)
    {
        $filename = $path;
        $file = fopen($filename, "rb");
        Header("Content-type:  application/octet-stream ");
        Header("Accept-Ranges:  bytes ");
        Header("Content-Disposition:  attachment;  filename=" . $saveName);
        $contents = "";
        while (!feof($file)) {
            $contents .= fread($file, 819200);
        }
        echo $contents;
        fclose($file);
    }

    /**
     * 进行生码数量限制
     * 首先判断剩余码数量
     * 再判断单次生码数量不能超过10000
     * @param $codeNum      每箱质量码数量
     * @param $codeNum2     箱码数量
     *
     * @return null
     *
     */
    public function inputNumLimit($codeNum, $codeNum2)
    {
        $currentCodeRemainder = $this->setCodeAvailableNumber();


        if (!empty($codeNum2)) {
            $codeInputNum = $codeNum * $codeNum2 + $codeNum;
        } else {
            $codeInputNum = $codeNum;
        }

        if ($currentCodeRemainder > 0 & $codeInputNum <= $currentCodeRemainder) {
            if ($currentCodeRemainder >= 10000) {
                $max = 10000;
            } else {
                $max = $currentCodeRemainder;
            }

        } else {
            $max = 0;
            json('002', null, "please buy more code!");
            exit;
        }

        //如果生成箱码-质量码，去掉箱码零头
        if (!empty($codeNum2)) {
            $codeInputNum = $codeNum * $codeNum2;
        }

        if ($codeInputNum <= $max) {
            return null;
        } else {
            json('002', null, "exceed the upper limit!");
            exit;
        }


    }

    public function getCodeHead()
    {
        //verify user longin status

        //get user companyid
        $companyid = $this->companyid;

        return $companyid;
    }

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

    public function checkCodeAvailability($num)
    {
        $currentCodeRemainder = $this->setCodeAvailableNumber();
        if ($num >= $currentCodeRemainder) {
            json('002', null, 'please buy more code!');
            exit;
        } else {
            return null;
        }
    }

    public function setCodeAvailableNumber($num)
    {

        $model = D('base_qrcode_created_log');
        $modelMap = array(
            'companyid' => $this->getCompanyId(),
        );

        if (empty($num)) {
            $model = D('base_qrcode_created_log');
            $modelMap = array(
                'companyid' => $this->getCompanyId(),
            );
            $list = $model->where($modelMap)->order('operate_time desc')->select();

            if (!empty($list)) {
                return $list[0]['qrcode_left'];
            } else {
                json('002', null, 'base_qrcode_created_log no record!');
                exit;
                return null;
            }
        } else {
            $modelMap = array(
                'companyid' => $this->getCompanyId(),
                'qrcode_left' => array('gt', 0)
            );

            $resutlt = $model->where($modelMap)->order('operate_time desc')->setDec('qrcode_left', $num);

            return $resutlt;

        }

    }

    public function calculateCode($codeNum, $codeNum2, $codeHead)
    {
        //codeNum equal x, codeNum2 equal z/x
        $xCodeNum = $codeNum;
        $zCodeNum = $codeNum * $codeNum2;

        $latestXcode = $this->getLatestCode(0);
        $latestZcode = $this->getLatestCode(1);


        if (!empty($latestXcode)) {
            $latestXcode = $latestXcode + 1;
        } else {
            $latestXcode = $codeHead . '0' . "000000001";
        }

        if (!empty($latestZcode)) {

            $latestZcode = $latestZcode;
        } else {
            $latestZcode = $codeHead . '1' . "000000000";
        }

        for ($i = 0; $i < $xCodeNum; $i++) {
            $codeRelation[$i]['qrcode_pack'] = $latestXcode + $i;
            $codeRelation[$i]['qrcode_range_s'] = $latestZcode + 1;
            $codeRelation[$i]['qrcode_range_e'] = $codeRelation[$i]['qrcode_range_s'] + $codeNum2 - 1;
            $latestZcode = $codeRelation[$i]['qrcode_range_e'];
            $codeRelation[$i]['ratio'] = $codeNum2;
            $codeRelation[$i]['create_time'] = NOW_TIME;
            $codeRelation[$i]['update_time'] = NOW_TIME;
            $codeRelation[$i]['status'] = 0;
            $codeRelation[$i]['company_id'] = 1001;
        }

        return $codeRelation;


    }

    public function getCodeType($codeType)
    {
        return $codeType;
    }

    public function getLatestCode($codeType)
    {
        $model = D('base_qrcode_used_log');
        $modelMap = array(
            'companyid' => $this->getCompanyId(),
            'qrcodetypeid' => $codeType

        );
        $list = $model->where($modelMap)->order('operate_time desc')->select();

        if (!empty($list)) {
            return $list[0]['qrcode_e'];
        } else {
            return null;
        }

    }

    public function getGenerateLog($id)
    {
        $model = D('base_qrcode_used_log');
        $modelMap = array(
            'id' => $id,

        );
        $list = $model->where($modelMap)->select();

        if (!empty($list)) {
            return $list[0]['qrcode_counts'];
        } else {
            return null;
        }
    }

    public function std_class_object_to_array($stdclassobject)
    {
        $_array = is_object($stdclassobject) ? get_object_vars($stdclassobject) : $stdclassobject;

        foreach ($_array as $key => $value) {
            $value = (is_array($value) || is_object($value)) ? std_class_object_to_array($value) : $value;
            $array[$key] = $value;
        }

        return $array;
    }

    public function checkData($latestCode, $codeNum, $codeType)
    {

    }

    public function coreCode($latestCode, $codeHead, $codeNum, $codeType, $codeTypeFlag)
    {
        if (!empty($latestCode)) {
            $latestCode = $latestCode + 1;
            $this->setCodeAvailableNumber($codeNum);
            $codeOriginAvailableNum = $this->setCodeAvailableNumber();
        } else {
            $latestCode = $codeHead . $codeType . "000000001";
            $codeOriginAvailableNum = $this->setCodeAvailableNumber();
            $codeOriginAvailableNum = $codeOriginAvailableNum - $codeNum;
            $this->setCodeAvailableNumber($codeNum);
        }
        //===step 1===
        $t1 = microtime(true);
        $secretKey = '';
        set_time_limit(0);
        for ($i = 0; $i < $codeNum; $i++) {
            $secretKey = $secretKey . microtime(true);
            $secretKey = $secretKey . $latestCode + $i;
            $codeInfo[$i]['b'] = new \MongoInt64($latestCode + $i);
            $codeInfo[$i]['c'] = strtoupper(substr(md5($secretKey), 8, 16));
            $codeInfo[$i]['create_time'] = NOW_TIME;
            $codeInfo[$i]['update_time'] = NOW_TIME;
            $codeInfo[$i]['status'] = 0;
            $codeInfo[$i]['hits'] = 0;
        }
        $t2 = microtime(true);
        $realCodeCount = count($codeInfo);
        $gtime = floatval(round($t2 - $t1, 3));

        unset($t1);
        unset($t2);
        unset($i);
        //===step 2===
        import("Code.Lib.Mongo.MongoInt64");
        switch ($codeType) {
            case '0' :
                $model = new ApiModel('xcode');
                break;
            case '1' :
                $model = new ApiModel('zcode');
                break;
            default:
                json('002', null, 'code type erroe!');
                exit;
                break;
        }
        $t1 = microtime(true);
        $mongoResult = $model->addAll($codeInfo);
        $t2 = microtime(true);
        $mtime = floatval(round($t2 - $t1, 3));

        unset($t1);
        unset($t2);
        unset($i);
        //===step 3===
        $qrcode_s = $this->std_class_object_to_array($codeInfo[0]['b']);
        $qrcode_e = $this->std_class_object_to_array($codeInfo[$realCodeCount - 1]['b']);
        $logMoel = D('base_qrcode_used_log');
        $log['companyid'] = $this->getCompanyId();
        $log['operatorid'] = $this->getUserId();
        $log['qrcodetypeid'] = $codeType;
        $log['operate_time'] = NOW_TIME;
        $log['qrcode_s'] = $qrcode_s['value'];
        $log['qrcode_e'] = $qrcode_e['value'];
        $log['qrcode_counts'] = $realCodeCount;
        $log['qrcode_left'] = $codeOriginAvailableNum;
        $log['flag'] = $codeTypeFlag;
        $logResult = $logMoel->add($log);

        $result = array(
            'info' => 'Congratulations,successful completion!',
            'qrcodetype' => $codeType,
            'num' => $realCodeCount,
            'gtime' => $gtime,
            'mtime' => $mtime,
            'mysql' => $logResult,
            'mongo' => $mongoResult,
            //'code'=>$codeInfo
        );

        return $result;
    }

    public function setCodeRelation($xcodeid, $zcodeid)
    {
        $model = D('base_qrcode_used_log');
        $modeMap = array(
            'id' => $xcodeid,
        );
        $result = $model->where($modeMap)->setField('relation', $zcodeid);
        return $result;
    }

    public function getLoopRealValue($codeNum, $codeNum2)
    {
        $maxLimit = 400000;

        $codeNum2 = $codeNum * $codeNum2 + $codeNum;

        if ($codeNum > $maxLimit || $codeNum2 > $maxLimit) {
            $loopRealValue = $codeNum / $maxLimit;
            return $loopRealValue;
        } else {
            return false;
        }

    }

    public function getLoopModulus($codeNum, $codeNum2)
    {
        $maxLimit = 400000;

        $codeNum2 = $codeNum * $codeNum2 + $codeNum;

        if ($codeNum > $maxLimit || $codeNum2 > $maxLimit) {
            $modulus = floor($this->getLoopRealValue($codeNum, $codeNum2));
            return $modulus;
        } else {
            return false;
        }
    }

    public function getMongoInsertLimit()
    {
        $maxLimit = 400000;

        return $maxLimit;
    }

    public function generate($codeNum, $codeHead, $codeType, $latestCode, $codeNum2)
    {

        switch ($codeType) {
            //xcode
            case '0' :
                //批量插入，但有点bug需要调试
                /*if($loopModulus = $this->getLoopModulus($codeNum, $codeNum2))
                {
                    $maxLimit = $this->getMongoInsertLimit();
                    $loopRealValue = $this->getLoopRealValue($codeNum, $codeNum2);
                    for($i=0; $i<$loopRealValue; $i++)
                    {
                        if($i == $loopModulus)
                        {
                            //$val1 = ($loopRealValue-$i)*$maxLimit; //this is error calulate!
                            $codeNum = $codeNum - $i*$maxLimit;
                            $latestCode = $this->getLatestCode($codeType);
                            $result[$i] =$this->coreCode($latestCode, $codeHead, $codeNum, $codeType, $codeType);
                        }
                        else
                        {
                            $latestCode = $this->getLatestCode($codeType);
                            $result[$i] =$this->coreCode($latestCode, $codeHead, $maxLimit, $codeType, $codeType);
                        }
                    }
                }
                else
                {
                    $latestCode = $this->getLatestCode($codeType);
                    $result =$this->coreCode($latestCode, $codeHead, $codeNum, $codeType, $codeType);
                }*/

                $result = $this->coreCode($latestCode, $codeHead, $codeNum, $codeType, $codeType);

                json('001', $result);

                break;
            //zcode
            case '1' :

                $result = $this->coreCode($latestCode, $codeHead, $codeNum, $codeType, $codeType);

                json('001', $result);

                break;
            //xcode & zcode
            case '2' :
                //codeNum equal xcode
                $codeRelation = $this->calculateCode($codeNum, $codeNum2, $codeHead);

                $codeRelationMongoModel = new ApiModel('coderelation');
                ini_set('mongo.native_long', 1);
                $codeRelationMongoResult = $codeRelationMongoModel->addAll($codeRelation);
                ini_set('mongo.native_long', 0);

                $logRelationMoel = D('corr_qrcode_pack');
                $logRelationResult = $logRelationMoel->addAll($codeRelation);

                $latestXcode = $this->getLatestCode(0);
                $latestZcode = $this->getLatestCode(1);

                //xcode
                $xcodeResult = $this->coreCode($latestXcode, $codeHead, $codeNum, 0, 2);

                //zcode
                $zcodeResult = $this->coreCode($latestZcode, $codeHead, $codeNum * $codeNum2, 1, 2);

                //update relation
                $setRelationResult = $this->setCodeRelation($xcodeResult['mysql'], $zcodeResult['mysql']);

                $result = array(
                    'xcode' => $xcodeResult,
                    'zcode' => $zcodeResult,
                    'coderelationmongo' => $codeRelationMongoResult,
                    'coderelation' => $logRelationResult,
                    'setcoderelation' => $setRelationResult,

                );

                json('001', $result);
                break;

            default :
                json('002', null, 'code type erroe!');
                exit;
                break;
        }
    }

    public function getIp()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = "unknown";
        return ($ip);
    }

    public function getIpLocation()
    {
        $getIp = $this->getIp();

        $content = file_get_contents("http://api.map.baidu.com/location/ip?ak=7IZ6fgGEGohCrRKUE9Rj4TSQ&ip={$getIp}&coor=bd09ll");

        $json = json_decode($content);

        $array = array(
            'ip' => $getIp,
            'log' => $json->{'content'}->{'point'}->{'x'},
            'lat' => $json->{'content'}->{'point'}->{'y'},
            'address' => $json->{'content'}->{'address'}
        );

        return $array;
    }

    /**
     * 查询IP地址对应的地理位置信息
     * @param $ip
     *
     * @return null
     */
    public static function ipLookup($ip)
    {
        $ch = curl_init();
        $url = 'http://apis.baidu.com/apistore/iplookupservice/iplookup?ip=' . $ip;
        $header = array(
            'apikey: 154f3d2491a4b0ddff9dea326977b9d7',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch, CURLOPT_URL, $url);
        $res = json_decode(curl_exec($ch), true);

        if ($res->errNum == 0) {
            if ($res["retData"]["country"] == "中国") {
                return $res["retData"]["province"] . "," . $res["retData"]["city"] . "," . $res["retData"]["district"];
            } else {
                return $res["retData"]["country"];
            }
        } else {
            return null;
        }
    }

    /**
     * 获取码对应的经销商id
     * @param $qcode
     * @return bool
     */
    public static function getAgentbyQCode($qcode)
    {
        $agent = A("CheckIO/Checkout");

        $con = $agent->getAgentbyQCodeData($qcode) ;

        if(empty($con))
        {
            return null;
        }
        else{
            return $con["id"];
        }
    }

    public function getSetting($cmpID)
    {
        $model = D('base_anti_fake_setting');

        $modelMap = array(
            'cmp_id' => $cmpID,
        );

        if (empty($cmpID))//若没有赋值，则说明是后台管理获取配置信息
        {
            $modelMap = array(
                'cmp_id' => $this->companyid,
            );
        }

        $result = $model->where($modelMap)->find();

        return $result;
    }

    public function settingUpdate()
    {
        $model = D('base_anti_fake_setting');

        $modelMap = array(
            'cmp_id' => $this->companyid,
        );

        $data = array(
            'max_scan_count' => I('max_scan_count', '0', 'htmlspecialchars'),
            'scan_tips_text' => I('scan_tips_text', '0', 'htmlspecialchars'),
            'fake_tips_text' => I('fake_tips_text', '0', 'htmlspecialchars'),
            'cmp_id' => $this->companyid,
        );

        $result = $model->where($modelMap)->find();

        if (!empty($result)) {
            $result = $model->where($modelMap)->save($data);
        } else {
            $result = $model->add($data);
        }

        if (!empty($result)) {
            return '001';
        } else {
            return '002';
        }
    }

    public function settingUpdateInit($companyId)
    {
        $model = D('base_anti_fake_setting');

        $modelMap = array(
            'cmp_id' => $companyId,
        );

        $data = array(
            'max_scan_count' => 99999,
            'scan_tips_text' => "亲，扫描次数过多！",
            'fake_tips_text' => "亲，这是黑名单中的码！",
            'cmp_id' => $companyId,
        );

        $result = $model->where($modelMap)->find();

        if (!empty($result)) {
            $result = $model->where($modelMap)->save($data);
        } else {
            $result = $model->add($data);
        }

        if (!empty($result)) {
            return '001';
        } else {
            return '002';
        }
    }

}
