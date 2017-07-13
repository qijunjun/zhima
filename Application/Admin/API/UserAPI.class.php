<?php
namespace Admin\API;

use Common\API\EntranceAPI;
use Admin\Utility\UserManager;

class UserAPI
{
	public static function fetchUsers()
	{
		$result = UserManager::fetchUsers();
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";

		return array(
			"code" => $code,
			"msg" => $msg,
			"result" => $result
		);
	}

	public static function authenticatedUsers()
	{
		$result = UserManager::authenticatedUsers();
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";

		return array(
			"code" => $code,
			"msg" => $msg,
			"result" => $result
		);
	}

	public static function unauthenticatedUsers()
	{
		$result = UserManager::unauthenticatedUsers();
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";

		return array(
			"code" => $code,
			"msg" => $msg,
			"result" => $result
		);
	}

	public static function authenticateUser($id)
	{
		$result = UserManager::authenticateUser($id);
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";

		return array(
			"code" => $code,
			"msg" => $msg,
			"result" => $result
		);
	}

	public static function getUser($id)
	{
		$result = UserManager::getUser($id);
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";

		return array(
			"code" => $code,
			"msg" => $msg,
			"result" => $result
		);
	}

	public static function editUser($id, $name, $contact, $phone)
	{
		$result = UserManager::editUser($id, $name, $contact, $phone);
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";

		return array(
			"code" => $code,
			"msg" => $msg,
			"result" => $result
		);
	}

	public static function repassword($companyid, $new_password)
	{
		$uid = UserManager::getAdminid($companyid);
		if (!is_numeric($uid) && $uid < 1)
		{
			return array(
				"code" => "002",
				"msg" => "not found user",
				"result" => $uid,
			);
		}
		$result = null;
		$flag = EntranceAPI::checkInput("repassword", null, strrev($new_password), null, null, $new_password, $msg);
		if ($flag)
		{
			$result = UserManager::repassword($uid, $new_password);
			$code = $result ? "001" : "007";
		}
		else
		{
			$code = "002";
		}

		return array(
			"code" => $code,
			"msg" => $msg,
			"result" => $result
		);
	}
}
