<?php
namespace Trace\Controller;

use Think\Controller;
use Trace\API\TraceAPI;

class InfoController extends Controller
{
	public function prepareInfo($code)
	{
		$company_id = $_SESSION["user_info"]["companyid"];
		if(strlen($code)!=14)
		{
			json("002",null,"码格式错误");
			return;
		}
		if($_SESSION["user_info"]["groups"][0]==4)//政府用户 type=4
		{
			$flag=true;
			TraceAPI::prepareInfo($code, "");
		}
		else    //普通公司用户
		{
			$flag = preg_match('/^' . $company_id . '[01]\d{9}/', $code);
			if ($flag)
			{
				TraceAPI::prepareInfo($code, "");
			}
		}
		$code = $flag ? "001" : "002";
		$msg = $flag ? "Success" : "码格式错误";
		echo json_encode(array(
			"code" => $code,
			"message"  => $msg
		));
	}

	public function scanInfo()
	{
		echo json_encode(TraceAPI::scanInfo());
	}

	public function companyInfo()
	{
		echo json_encode(TraceAPI::companyInfo());
	}

	public function productInfo()
	{
		echo json_encode(TraceAPI::productInfo());
	}
	
	public function profileInfo()
	{
		echo json_encode(TraceAPI::profileInfo());
	}

	public function warehouseInfo()
	{
		echo json_encode(TraceAPI::warehouseInfo());
	}
   /*
    * 显示召回信息 by ymf
    */
	public function recallInfo()
	{
		echo json_encode(TraceAPI::recallInfo());
	}
	/**
	 * 根据箱码查询对应的质量码 2016.8.1 by gaodc
	 */
	public function searchCodeInfo()
	{
		echo json_encode(TraceAPI::searchCodeInfo());
	}
	/**
	 * 箱码对应物流
	 */
	public function logisticspackInfo(){
		echo json_encode(TraceAPI::logisticspackInfo());
	}
	/**
	 * 根据质量码查询对应的箱码 2016.8.5
	 */
	public function findCodeInfo(){
		echo json_encode(TraceAPI::findCodeInfo());
	}
	/**
	 * 质码对应物流
	 */
	public function logisticsInfo(){
		echo json_encode(TraceAPI::logisticsInfo());
	}
}

