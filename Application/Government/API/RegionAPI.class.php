<?php
namespace Government\API;

use Government\Utility\DataProvider;

class RegionAPI
{
	public static function regionInfo()
	{
		$result = DataProvider::regionInfo();
		$flag = $result !== false;
		$code = $flag ? "001" : "007";
		$msg = $flag ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
}
