<?php
namespace Produce\API;

use Produce\Utility\ProfileManager;

class ProfileAPI
{
	public static function fetchProfile()
	{
		$result = ProfileManager::fetchProfile();
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function addProfile($productId, $list)
	{
		$result = ProfileManager::addProfile($productId, $list);
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function editProfile($id, $list)
	{
		$result = ProfileManager::editProfile($id, $list);
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function removeProfile($id)
	{
		$result = ProfileManager::removeProfile($id);
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
	/*
	 * 检查质量码段是否已关联了生产信息,如已关联返回关联行的行号
	 */
	public static function checkQrcodeRepeat($start, $end=0)
	{
		if (empty($end))
		{
			$end = $start;
		}
		$model = M('CorrQrcodeProductprofile');
		$companyid = $_SESSION["user_info"]["companyid"];
		$sql = 'SELECT `id` FROM `zm_corr_qrcode_productprofile` WHERE ((`qrcode_range_s` <= ' . $start . ' AND `qrcode_range_e` >= ' . $start . ') OR (`qrcode_range_s` <= ' . $end . ' AND `qrcode_range_e` >= ' . $end . ') OR (`qrcode_range_s` >= ' . $start . ' AND `qrcode_range_s` <= ' . $end . ') OR (`qrcode_range_e` >= ' . $start . ' AND `qrcode_range_e` <= ' . $end . ')) AND `company_id` = ' . $companyid;
		$rows = $model->query($sql);
		return $rows;
	}

}
