<?php
namespace Admin\API;

use Admin\Utility\AccountManager;

class AccountAPI
{
	public static function fetchLogs($companyId, $page, $size, $conditions, $sort)
	{
		$result = AccountManager::fetchLogs($companyId, $page, $size, $conditions, $sort);
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function chargeMoney($id, $fee)
	{
		$result = AccountManager::chargeMoney($id, $fee);
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
}
