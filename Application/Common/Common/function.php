<?php
/**
 * 取IP地址所在地
 *
 * @param string $ipAddress IP地址，为空表示取当前访问IP
 *
 * @return array ['ip' => ip地址, 'beginip' => 起始段, 'endip' => 终止段, 'country' => 城市, 'area' => 地区]
 */
function getIpLocation($ipAddress = '') {
    $lib = new Org\Net\IpLocation(__DIR__.'/../Ip/QQWry.DAT');
    if($ipAddress == '') {
        $ipAddress = get_client_ip(0, true);
    }
    $result = $lib->getlocation($ipAddress);
    $result['country'] = iconv('GBK', 'UTF-8', $result['country']);
    $result['area'] = iconv('GBK', 'UTF-8', $result['area']);
    return $result;
}

/**
 * 直接输出Json信息，支持JsonP跨域
 * json响应信息输出，只给$code即可。
 * 可根据需要提供$result返回附加信息，如需自定义message信息，可以提供$message。
 * 调用本函数后，建议不要再使用任何输出语句，否直则会接跟在json消息后输出。
 *
 * 对于前端：如果需要跨域，只要在jQuery提交时将dataType设置为jsonP即可
 * 对于前端：如果采用iframe框架提交内容，需要在iframe框架外取得参数，需要在框架外定义一个js函数，然后将函数名作为get参数iframe_upload即可
 *
 * @param string $code    信息码
 * @param mixed  $result  [返回数据]
 * @param string $message [自定义信息文本]
 *
 * @return void
 *
 * Created by Notepad++;
 * User: 黎明;
 * Date: 2016/1/24
 * Last Modified: 2016/3/7
 * Last Change: 识别参数：@get_param String [$iframe_upload] 在子iframe中调用父窗口函数
 */
function json($code, $result = null, $message = null) {
    header('Author: Jin Liming, jinliming2@gmail.com');
    //消息信息
    if($message == null) {
        switch($code) {
            case "001":
                $message = "Success";  //成功
                break;
            case "002":
                $message = "Missing Parameter";  //缺少参数
                break;
            case "003":
                $message = "Invalid Token";  //无效Token
                break;
            case "004":
                $message = "Server Authentication Failed";  //服务器认证失败
                break;
            case "005":
                $message = "Inadequate Permissions";  //权限不够
                break;
            case "006":
                $message = "Unknown Reason";  //未知原因
                break;
            case "007":
                $message = "Database Error";  //数据库错误
                break;
            case "008":
                $message = "Server Error";  //服务器错误
                break;
            case "009":
                $message = "Parameter Error";  //参数错误
                break;
            default:
                $message = "程序猿君开小差了~";
                break;
        }
    }
    //返回信息拼接
    $ret = json_encode(array(
        "code"    => $code,
        "message" => $message,
        "result"  => $result
    ));
    if(isset($_GET['callback'])) {
        //跨域JsonP设置
        $ret = $_GET['callback'].'('.$ret.')';
        header('Content-type: application/javascript');
    } else if(isset($_GET['iframe_upload'])) {
        //iframe中调用父窗口函数
        $ret = "<script>parent.".$_GET['iframe_upload']."(".$ret.");</script>";
        header('Content-Type: text/html;charset=utf-8');
    } else {  //普通json
        header('Content-type: application/json');
    }
    echo $ret;
}

/**
 * 查询mongo表所对应的Node.JS链接地址
 *
 * @param string $table 表名
 *
 * @return bool|string 成功返回链接地址，失败返回false
 */
function mongoURL($table) {
    switch($table) {
        //生产信息
        case 'base_productprofiles':
            $m = 'productProfiles';
            $c = 'productProfile';
            break;
        //生产信息字段
        case 'base_productprofile_fields':
            $m = 'productProfiles';
            $c = 'field';
            break;
        //二维码
        case 'base_qrcode':
            $m = 'qrCode';
            $c = 'qrCode';
            break;
        //包装码
        case 'base_qrcode_pack':
            $m = 'qrCodePack';
            $c = 'qrCodePack';
            break;
        //入库
        case 'base_scanin':
            $m = 'scanIn';
            $c = 'scanIn';
            break;
        //出库
        case 'base_scanout':
            $m = 'scanOut';
            $c = 'scanOut';
            break;
        default:
            return false;
    }
    return "http://localhost:3000/{$m}/{$c}";
}

/**
 * 将多维数组转换为Get的Query查询字符串
 *
 * @param array $arr 多维数组
 * @param null  $key 字段名
 *
 * @return string GET Query 查询字符串
 */
function array2Query($arr, $key = null) {
    $a = "";
    foreach($arr as $k => $v) {
        $k = ($key !== null ? "{$key}[{$k}]" : $k);
        if(is_array($v)) {
            $a .= '&'.array2Query($v, $k);
        } else {
            $a .= '&'.$k.'='.$v;
        }
    }
    return substr($a, 1);
}

/**
 * 向mongo数据库插入若干记录
 * ##插入单条记录示例：
 * mongoInsert('base_productprofile_fields', ['field' => 'name']);
 * 第二个参数为一个关联数组，数组中的数据表示插入的数据字段
 *
 * ##批量插入多条记录示例：
 * mongoInsert('base_productprofile_fields', ['data' => [
 *  ['field' => 'name1'],
 *  ['field' => 'name2', 'type' => 'Number'],
 *  ['field' => 'name3'],
 * ]]);
 * 第二个参数为一个二维数组，外层只有一个data，内层为多条记录的索引数组
 *
 * @param string $table 表名
 * @param array  $query 数据，一维关联数组表示插入单条记录。如需插入多条数据，提供一个二维数组，第一维为data，第二维为一个关联数组的索引数组
 *
 * @return bool|mixed|string 处理结果的json数据，或者是错误信息
 */
function mongoInsert($table, $query) {
    //链接
    $url = mongoURL($table);
    if($url === false) {
        return false;
    }
    //初始化
    $ch = curl_init();
    //链接
    if(count($query) == 1 && isset($query['data']) && is_array($query['data'])) {
        $url .= "s";
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    //设置请求方式
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POST, true);
    //添加数据
    curl_setopt($ch, CURLOPT_POSTFIELDS, array2Query($query));
    //执行
    $output = curl_exec($ch);
    //错误处理
    if(curl_errno($ch)) {
        return curl_error($ch);
    }
    //关闭
    curl_close($ch);
    return json_decode($output, true);
}

/**
 * 从mongo数据库中删除一条记录
 * ##示例：
 * mongoDelete('base_productprofile_fields', '571ca3eed5052a3c1881dcd2');
 *
 * @param string $table 表名
 * @param string $_id   记录id
 *
 * @return bool|mixed|string 处理结果的json数据，或者是错误信息
 */
function mongoDelete($table, $_id) {
    //链接
    $url = mongoURL($table);
    if($url === false) {
        return false;
    }
    //初始化
    $ch = curl_init();
    //链接
    curl_setopt($ch, CURLOPT_URL, $url."/".$_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    //设置请求方式
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    //执行
    $output = curl_exec($ch);
    //错误处理
    if(curl_errno($ch)) {
        return curl_error($ch);
    }
    //关闭
    curl_close($ch);
    return json_decode($output, true);
}

/**
 * 修改mongo数据库中的一条记录
 * ##示例：
 * mongoUpdate('base_productprofile_fields', '571b47d9df2031a038ddc949', ['field' => '123']);
 *
 * @param string $table 表名
 * @param string $_id   记录id
 * @param array  $query 要修改的记录信息，一个关联数组
 *
 * @return bool|mixed|string 处理结果的json数据，或者是错误信息
 */
function mongoUpdate($table, $_id, $query) {
    //链接
    $url = mongoURL($table);
    if($url === false) {
        return false;
    }
    //初始化
    $ch = curl_init();
    //链接
    curl_setopt($ch, CURLOPT_URL, $url."/".$_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    //设置请求方式
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POST, true);
    //修改数据
    curl_setopt($ch, CURLOPT_POSTFIELDS, array2Query($query));
    //执行
    $output = curl_exec($ch);
    //错误处理
    if(curl_errno($ch)) {
        return curl_error($ch);
    }
    //关闭
    curl_close($ch);
    return json_decode($output, true);
}

/**
 * 查询mongo数据库中的记录
 * ##查询所有记录的示例：
 * mongoSelect('base_productprofile_fields');
 *
 * ##根据id查询记录的示例：
 * mongoSelect('base_productprofile_fields', '571b47d9df2031a038ddc949');
 *
 * ##根据where条件查询记录的示例：
 * mongoSelect('base_productprofile_fields', ['field' => '123']);
 *
 * @param string $table 表名
 * @param mixed  $query 查询信息，可以为字符串的_id，也可以为一个关联数组
 *
 * @return bool|mixed|string 查询到的json数据，或者是错误信息
 */
function mongoSelect($table, $query = null) {
    //链接
    $url = mongoURL($table);
    if($url === false) {
        return false;
    }
    //初始化
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    //设置请求方式
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    //筛选查询
    if(is_array($query) || $query === null) {
        if($query === null) {
            $query = [];
        }
        //查询参数
        $queryString = array2Query($query);
        if($queryString !== false) {
            $url .= '?'.$queryString;
        }
        //链接
        curl_setopt($ch, CURLOPT_URL, $url);
        //执行
        $output = curl_exec($ch);
    } else {
        //id查询链接
        curl_setopt($ch, CURLOPT_URL, $url."/".$query);
        //执行
        $output = curl_exec($ch);
    }
    //错误处理
    if(curl_errno($ch)) {
        return curl_error($ch);
    }
    //关闭
    curl_close($ch);
    return json_decode($output, true);
}
/**
 * 根据公司id得到公司的仓库列表
 * @param string $companyid 公司id
 * @return 公司所有的仓库,失败返回false
 */
function getWhList($companyid){
    if($companyid===''||$companyid==null){
        return false;
    }
    $condition=array(
        "companyid" => $companyid
    );
    $wlist=M('base_warehouse');
    $list=$wlist->where($condition)->field('id,warehouse_name')->select();
    return $list;
}
/**
 * 根据公司id得到公司的经销商列表
 * @param string $companyid 公司id
 * @return 公司所有的经销商,失败返回false
 */
function getAGList($companyid){
    if($companyid===''||$companyid==null){
        return false;
    }
    $condition=array(
        "companyid" => $companyid
    );
    $alist=M('base_agency');
    $list=$alist->where($condition)->field('id,agency_name')->select();
    return $list;
}
/*
 *质量码段输入检查
 * 一般用于质量码段与产品信息添加绑定之前的检查
 */
function checkQCode($companyid,$qrcode_s,$qrcode_e){
    $s = strval($qrcode_s);
    $e = strval($qrcode_e);
    $pattern = '/^' . $companyid . '1\d{9}$/';
    return preg_match($pattern,$s ) && preg_match($pattern,$e);
}
/*
 *箱码段输入检查
 *
 */
function checkXCode($companyid,$xrcode_s,$xrcode_e){
    $s = strval($xrcode_s);
    $e = strval($xrcode_e);
    $pattern = '/^' . $companyid . '0\d{9}$/';
    return preg_match($pattern,$s ) && preg_match($pattern,$e);
}
/*
 * 通过瓶码得到箱码信息
 * $code:瓶码
 */
function getPcodeByXcode($code)
{
    $qrcode_info = M("CorrQrcodePack");
    // code between start and end
    $cond = array(
        "qrcode_range_s" => array('elt', $code),
        "qrcode_range_e" => array('egt', $code)
    );
    $pack = $qrcode_info -> where($cond) ->find();
    if($pack){
        return $pack["qrcode_pack"];
    }
    else{
        return null;
    }
}
/*
 * 通过质量码得到产品id
 */
function getProductid($code)
{
    $qrcode_info = M("CorrQrcodeProduct");
    // code between start and end
    $cond = array(
        "qrcode_range_s" => array('elt', $code),
        "qrcode_range_e" => array('egt', $code)
    );
    $product_id = $qrcode_info -> where($cond) -> getField("product_id");
    if($product_id!=null){
        return $product_id;
    }else{
        return 0;
    }

}

/**
 * @param $b
 * void
 * 检测是否存在b
 */
function is_exist_b($b)
{

    $codeType = substr($b,4,1);

    $codeCompanyid = substr($b,0,4);

    if($_SESSION['user_info']['companyid'] == $codeCompanyid)
    {
        switch($codeType)
        {
            case '0':
                $model = new \Code\Model\ApiModel('xcode');
                $modelMap = array(
                    'b'=>new \MongoInt64($b),
                );
                $result = $model->where($modelMap)->select();
                if(empty($result))
                {
                    return false;
                }
                else
                {
                    return true;
                }
                exit;
                break;
            case '1':
                $model = new \Code\Model\ApiModel('zcode');
                $modelMap = array(
                    'b'=>new \MongoInt64($b),
                );
                $result = $model->where($modelMap)->select();
                if(empty($result))
                {
                    return false;
                }
                else
                {
                    return true;
                }
                exit;
                break;
            default:
                json('002',null,'is_exist_b_error!');
                exit;
        }
    }
    else
    {
        return false;
    }

}
/** 直接输出Json信息，支持JsonP跨域
 * json响应信息输出，只给$code即可。
 * 可根据需要提供$result返回附加信息，如需自定义message信息，可以提供$message。
 * 调用本函数后，建议不要再使用任何输出语句，否则会直接跟在json消息后输出。
 *
 * @param String $code 信息码
 * @param mixed [$result] 返回信息
 * @param String [$message] 自定义信息文本
 * @return void
 *
 * Created by Notepad++;
 * User: 黎明;
 * Date: 2016/1/24
 * Last Modified: 2016/3/7
 * Last Change: 识别参数：@param String [$iframe_upload] 在子iframe中调用父窗口函数
 */
function _echoJson($code, $result = null, $message = null) {
    header('Author: Jin Liming, jinliming2@gmail.com');
    //消息信息
    if($message == null) {
        switch($code) {
            case "001": $message = "Success"; break;  //成功
            case "002": $message = "Missing Parameter"; break;  //缺少参数
            case "003": $message = "Invalid Token"; break;  //无效Token
            case "004": $message = "Server Authentication Failed"; break;  //服务器认证失败
            case "005": $message = "Inadequate Permissions"; break;  //权限不够
            case "006": $message = "Unknown Reason"; break;  //未知原因
            case "007": $message = "Database Error"; break;  //数据库错误
            case "008": $message = "Server Error"; break;  //服务器错误
            case "009": $message = "Parameter Error"; break;  //参数错误
            default: $message = "程序猿君开小差了~"; break;
        }
    }
    //返回信息拼接
    $ret = json_encode(array(
        "code" => $code,
        "message" => $message,
        "result" => $result
    ));
    //跨域JsonP设置
    if(isset($_GET['callback'])) {
        $ret = $_GET['callback'] . '(' . $ret . ')';
        header('Content-type: application/javascript');
    } else if(isset($_GET['iframe_upload'])) {
        $ret = "<script>parent." . $_GET['iframe_upload'] . "(" . $ret . ");</script>";
        header('Content-Type: text/html;charset=utf-8');
    } else {
        header('Content-type: application/json');
    }
    echo $ret;
}

// Below are functions all defined by James "Carbon" leon Neo
/*
	A short-cut function to get a MongoDB model object.
	@param string $name    the name of the collection to operate on.
	@return object         the corresponding MongoDB model object.
 */
function MM($name, $tablePrefix)
{
    return new \Common\Model\MongoModel($name, $tablePrefix);
}
/*
	Update interval table for optimization in code-replication check.
	@param string $item          the type of interval to update.
	@param int    $company_id    identifier of the specified company.
	@param int    $code_start    start number of code interval.
	@param int    $code_end      end number of code interval.
	@return boolean              an indicator to show the result of operation.
 */
function updateInterval($item, $company_id, $code_start, $code_end)
{
    $model = M("CorrQrcode" . $item . "Interval");
    $sql = array();
    $sql[] = "SELECT count(id) AS flag FROM __TABLE__ WHERE qrcode_range_e = $code_start - 1 OR qrcode_range_s = $code_end + 1";
    $sql[] = "UPDATE __TABLE__ AS a, __TABLE__ AS b SET a.qrcode_range_e = b.qrcode_range_e WHERE a.qrcode_range_e = $code_start - 1 AND b.qrcode_range_s = $code_end + 1";
    $sql[] = "DELETE FROM __TABLE__ WHERE qrcode_range_s = $code_end + 1";
    $sql[] = "UPDATE __TABLE__ SET qrcode_range_s = $code_start WHERE company_id = $company_id AND qrcode_range_s = $code_end + 1";
    $sql[] = "UPDATE __TABLE__ SET qrcode_range_e = $code_end WHERE company_id = $company_id AND qrcode_range_e = $code_start - 1";
    $sql[] = "INSERT INTO __TABLE__(company_id, qrcode_range_s, qrcode_range_e) VALUES($company_id, $code_start, $code_end)";
    $result = $model -> query($sql[0]);
    $result = $result[0]["flag"];
    if ($result === false)
    {
        return false;
    }
    if ($result == 2)
    {
        $result = $model -> execute($sql[1]) && $model -> execute($sql[2]);
        if ($result === false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    for ($i = 3; $i < 6; $i++)
    {
        $result = $model -> execute($sql[$i]);
        if ($result === false)
        {
            return false;
        }
        if ($result != 0)
        {
            return true;
        }
    }
}

// Below are functions defined to get jQuery Bootgrid run.
// All by James "Carbon" leon Neo
// Revision 00036
// Last updated at 2016-02-22: Adjust for new conditions model;
/*
	Parse the conditions sent by jQuery Bootgrid.
	@param  mixed $conditions    the conditions sent by jQuery Bootgrid through AJAX.
	@return mixed $cond          the standard form of query criteria used for ThinkPHP models, typically an array.
	                             If the conditions sent is string or other types of empty criteria, the return-value is null.
 */
function parse_conditions($conditions)
{
    // Safe-guard for empty string "" or initial empty criteria array sent by jQuery Bootgrid during initialization.
    if (!is_array($conditions))
    {
        return array();
    }
    $relation = $conditions["relation"];
    $fields = array_keys($conditions);
    // Safe-guard ditto, description is above.
    if (count($fields) == 0)
    {
        return array();
    }
    $cond = array();
    foreach ($fields as $field)
    {
        if ($field == "relation")
        {
            continue;
        }
        $condition = $conditions[$field];
        if (!$condition["type"] && !$condition["criterion"])
        {
            $result = parse_conditions($condition);
            if (count($result))
            {
                array_push($cond, $result);
            }
        }
        else
        {
            switch ($condition["type"])
            {
                case "value":
                    $cond[$field] = $condition["criterion"];
                    break;
                case "pattern":
                    $cond[$field] = array("like", "%" . $condition["criterion"] . "%");
                    break;
                case "interval":
                case "array":
                    $criterion = $condition["criterion"];
                    $operator = count($criterion) == 2 ? "between" : "in";
                    $cond[$field] = array($operator, $criterion);
                    break;
            }
        }
    }
    if ($relation !== null)
    {
        $cond["_logic"] = $relation ? "and" : "or";
    }
    return $cond;
}

/*
	Retrieve the data for jQuery Bootgrid, designed for directly received parameters from jQuery Bootgrid.
	@param  Model $model         the model based on the table to fetch data.
	@param  mixed $conditions    the query conditions/criteria sent by jQuery Bootgrid through AJAX.
	@param  int   $page          the requested page index sent by jQuery Bootgrid through AJAX.
	@param  int   $size          the size of each page sent by jQuery Bootgrid through AJAX.
	@param  mixed $sort          the sort dictionary sent by jQuery Bootgrid through AJAX.
	@return mixed $result        the standard form of retrieved data for jQuery Bootgrid, typically an array.
	                             If any errors occurs during the process, the return-value is boolean false.
 */
function retrieve_data_basic($model, $conditions = null, $page = null, $size = null, $sort = null)
{
    return retrieve_data($model, null, $conditions, $page, $size, $sort);
}
/*
	Retrieve the data for jQuery Bootgrid, designed for directly received parameters from jQuery Bootgrid.
	@param  Model $model         the model based on the table to fetch data.
	@param  mixed $fields        the fields and their aliases to query in the table.
	@param  mixed $conditions    the query conditions/criteria sent by jQuery bootgrid through AJAX.
	@param  int   $page          the requested page index sent by jQuery bootgrid through AJAX.
	@param  int   $size          the size of each page sent by jQuery bootgrid through AJAX.
	@param  mixed $sort          the sort dictionary sent by jQuery bootgrid through AJAX.
	@return mixed $result        the standard form of retrieved data for jQuery bootgrid, typically an array.
	                             If any errors occurs during the process, the return-value is boolean false.
 */
function retrieve_data($model, $fields = null, $conditions = null, $page = null, $size = null, $sort = null)
{
    // Try parsing the conditions to ThinkPHP query criteria, if failed, ignore the conditions to get all the data.
    $criteria = parse_conditions($conditions);
    return retrieve_data_advanced($model, $fields, $criteria, $page, $size, $sort);
}
/*
	Retrieve the data for jQuery Bootgrid, the query parameters are assumed to be well preprocessed to use in this function.
	@param  Model $model       the model based on the table to fetch data.
	@param  mixed $fields      the fields and their aliases to query in the table.
	@param  mixed $criteria    the conditions/criteria to perform the query.
	@param  int   $page        the index of the page to get requested.
	@param  int   $size        the size of each page during the query.
	@param  mixed $order       the dictionary containing the fields and order to sort in the query.
	@return mixed $result      the standard form of retrieved data for jQuery Bootgrid, typically an array.
	                           If any errors occurs during the process, the return-value is boolean false.
 */
function retrieve_data_advanced($model, $fields = null, $criteria = null, $page = null, $size = null, $order = null)
{
    if (!is_array($criteria))
    {
        $criteria = array();
    }
    $model = $model -> field($fields) -> where($criteria);
    $duplication = clone $model;
    $count = $model -> count();
    $model = $duplication;
    // If the count of the rows get queried is larger than 100, the data is sent in pagination;
    if ($count > 100)
    {
        $result = $model -> order($order) -> page($page, $size) -> select();
    }
    // Otherwise, they would get sent once totally.
    else
    {
        $result = $model -> order($order) -> select();
        $page = 1;
    }
    // If the query is successfully done, pack the rows into a proper form to use in jQuery bootgrid.
    if ($result !== false)
    {
        $result = array(
            "current" => $page,
            "total" => $count,
            "rows" => $result
        );
    }
    return $result;
}

/**
 * 获取区间重叠部分
 * 满足max(A.start,B.start)<=min(A.end,B,end)，即可判断A，B重叠。
 * 重叠部分就是max(A.start,B.start)，min(A.end,B,end)
 *
 * @param $startA
 * @param $endA
 * @param $startB
 * @param $endB
 */
function overlapinterval($startA, $endA, $startB, $endB)
{
    $startN = max($startA, $startB);
    $endN = min($endA, $endB);
    if ($startN <= $endN)
    {
        return array("qrcode_range_s" => $startN, "qrcode_range_e" => $endN);
    }
    else
    {
        return false;
    }
}