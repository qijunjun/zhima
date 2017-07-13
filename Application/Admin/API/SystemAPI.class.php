<?php
/*
	Module SystemAPI
	Interface functions to manage system configurations.
	Revision 00003
	By James "Carbon" leon Neo
*/
namespace Admin\API;

use Admin\Utility\SystemManager;

class SystemAPI
{
	public static function clearTemp()
	{
		$result = SystemManager::clearTemp();
		$code = $result ? "001" : "007";
		$msg = $result ? "操作成功！" : "出现错误，请联系维护人员！";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function updateCache()
	{
		$result = SystemManager::updateCache();
		$code = $result ? "001" : "007";
		$msg = $result ? "操作成功！" : "出现错误，请联系维护人员！";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
}
