<?php
namespace Trace\API;

use Trace\Utility\TraceUtility;

class TraceAPI
{
	public static function prepareInfo($code, $check)
	{
		TraceUtility::prepareInfo($code, $check);
	}

	public static function scanInfo()
	{
		$code = $_SESSION["code_info"]["item_code"];
		$result = TraceUtility::scanInfo($code);

		return array(
			"code" => "001",
			"message" => "Success",
			"result" => $result
		);
	}

	public static function companyInfo()
	{
		$company_id = $_SESSION["code_info"]["company_id"];
		$result = TraceUtility::companyInfo($company_id);

		return array(
			"code" => "001",
			"message" => "Success",
			"result" => $result
		);
	}

	public static function productInfo()
	{
		$product_id = $_SESSION["code_info"]["product_id"];
		$result = TraceUtility::productInfo($product_id);

		return array(
			"code" => "001",
			"message" => "Success",
			"result" => $result
		);
	}

	public static function profileInfo()
	{
		$profile_id = $_SESSION["code_info"]["profile_id"];
		$result = TraceUtility::profileInfo($profile_id);

		return array(
			"code" => "001",
			"message" => "Success",
			"result" => $result
		);
	}

	public static function warehouseInfo()
	{
		$item_code = $_SESSION["code_info"]["item_code"];
		$package_code = $_SESSION["code_info"]["package_code"];
		$company_id = $_SESSION["code_info"]["company_id"];
		$result = TraceUtility::warehouseInfo($item_code, $package_code, $company_id);

		return array(
			"code" => "001",
			"message" => "Success",
			"result" => $result
		);
	}

	public static function recallInfo()
	{
		$company_id = $_SESSION["code_info"]["company_id"];
		$qrcode = $_SESSION["code_info"]["b"];
		$result = TraceUtility::recallInfo($company_id, $qrcode);

		return array(
			"code" => "001",
			"message" => "Success",
			"result" => $result
		);
	}
	public static function searchCodeInfo(){
		$company_id = $_SESSION["code_info"]["company_id"];
		$code = $_SESSION["code_info"]["package_code"];
		$result = TraceUtility::searchCodeInfo($code,$company_id);
		return array(
			"code" => "001",
			"message" => "Success",
			"result" => $result
		);

	}
	/**根据质量码查询箱码2016.8.5
	 * @return array
	 */
	public static function findCodeInfo()
	{
		$company_id = $_SESSION["code_info"]["company_id"];
		$code = $_SESSION["code_info"]["item_code"];
		$result = TraceUtility::findCodeInfo($code, $company_id);
		if ($result) {
			return array(
				"code" => "001",
				"message" => "Success",
				"result" => $result
			);
		} else {
			return array(
				"code" => "001",
				"message" => "没有对应箱码",
				"result" => array()
			);
		}

	}

	/**箱码对应物流信息
	 * @return array
	 */
	public static function logisticspackInfo(){
		$company_id = $_SESSION['code_info']['company_id'];
		$code = $_SESSION['code_info']['package_code'];
		$result = TraceUtility::logisticspackInfo($code,$company_id);

		return array(
			"code" => "001",
			"message" => "Success",
			"result" => $result
		);
	}

	/**质量码对应物流信息2016.8.5
	 * @return array
	 */
	public static function logisticsInfo(){
		$company_id = $_SESSION['code_info']['company_id'];
		$code = $_SESSION['code_info']['item_code'];
		$result = TraceUtility::logisticsInfo($code,$company_id);

		return array(
			"code" => "001",
			"message" => "Success",
			"result" => $result
		);
	}



}

function cmp($a, $b)
{
	return $a["time"] > $b["time"];
}
