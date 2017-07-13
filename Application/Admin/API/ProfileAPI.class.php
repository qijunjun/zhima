<?php
namespace Admin\API;

use Admin\Utility\ProfileManager;

class ProfileAPI
{
	public static function fetchProfiles()
	{
		$result = ProfileManager::fetchProfiles();
		$flag = $result !== false;
		$code = $flag ? "001" : "006";
		$msg = $flag ? "Success" : "";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function addProfile($name)
	{
		$result = ProfileManager::addProfile($name);
		$flag = $result !== false;
		$code = $flag ? "001" : "006";
		$msg = $flag ? "Success" : "";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function editProfile($id, $name)
	{
		$result = ProfileManager::editProfile($id, $name);
		$flag = $result !== false;
		$code = $flag ? "001" : "006";
		$msg = $flag ? "Success" : "";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function removeProfile($id)
	{
		$result = ProfileManager::removeProfile($id);
		$flag = $result !== false;
		$code = $flag ? "001" : "006";
		$msg = $flag ? "Success" : "";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
}
