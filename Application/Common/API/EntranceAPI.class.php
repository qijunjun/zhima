<?php
/*
    Module EntranceAPI
    Some basic user-related API functions
    Revision 00039
    By James "Carbon" leon Neo
*/
namespace Common\API;

use Think\Verify;
use Common\Utility\EntranceUtility;

class EntranceAPI
{
	public static function checkInput($operation, $username = null, $password = null, $phone = null, $company = null, $new_password = null, &$msg)
	{
		switch ($operation)
		{
			case "register":
				if (trim($phone) == "" || trim($company) == "")
				{
					$msg = "请输入合法参数！";

					return false;
				}
			case "login":
				if (trim($username) == "" || trim($password) == "")
				{
					$msg = "请输入合法参数！";

					return false;
				}
				if ($operation == "register")
				{
					$user = M("BaseUser");
					// Check property existence to avoid duplicated username or phone-number.
					$cond = array(
						"username" => $username,
						"phone" => $phone,
						"_logic" => "or"
					);
					$count = $user->where($cond)->count();
					if ($count)
					{
						$msg = "您的用户名或手机号已被注册！";

						return false;
					}
					break;
				}
				break;
			case "repassword":
				if (trim($password) == "" || trim($new_password) == "")
				{
					$msg = "请输入合法参数！";

					return false;
				}
				if ($password == $new_password)
				{
					$msg = "请输入不同的密码！";

					return false;
				}
		}

		return true;
	}

	/**检验政府用户是否重复
	 * @param $government
	 * @param $msg
	 * @return mixed
	 */
	public static function checkGovName($government,&$msg){
		$gov = M("BaseGovernment");
		$map = [
			'name' => $government,
		];
		$num = $gov->where($map)->count();
		if($num){
			$msg = "您的政府账号已经注册";
			return false;
		}
		return true;
	}

	public static function register($username, $password,$phone, $company)
	{
		$result = null;
		$flag = EntranceAPI::checkInput("register", $username, $password, $phone,$company, null, $msg);
		if ($flag)
		{
			$result = EntranceUtility::register($username, $password, $phone, $company, $msg);
			$code = $result !== false ? "001" : "007";
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

	/**新建政府用户
	 * @return array
	 */
	public static function registerGov($username, $password, $phone, $government, $type, $province, $city, $district, $regioncode,$contact)
	{
		$result = null;
		$flag = self::checkInput("register", $username, $password, $phone, $government, null, $msg);
		if($flag){
			$flag = self::checkGovName($government,$msg);
		}
		if ($flag)
		{
			$result = EntranceUtility::registerGov($username, $password, $phone, $government, $msg, $type, $province, $city, $district, $regioncode,$contact);
			$code = $result !== false ? "001" : "007";
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

	public static function login($username, $password)
	{
		$result = null;
		$flag = EntranceAPI::checkInput("login", $username, $password,null, null, null, $msg);
		if ($flag)
		{
			$result = EntranceUtility::login($username, $password, $msg);
			$code = $result !== false ? "001" : "007";
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

	public static function logout()
	{
		EntranceUtility::logout();
	}

	public static function repassword($user_id, $old_password, $new_password)
	{
		$result = null;
		$flag = EntranceAPI::checkInput("repassword", null, $old_password, null,null, $new_password, $msg);
		if ($flag)
		{
			$result = EntranceUtility::repassword($user_id, $old_password, $new_password);
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
