<?php
/*
	Module EntranceUtility
	Some basic user-related utility functions
	Revision 00039
	By James "Carbon" leon Neo
*/
namespace Common\Utility;

class EntranceUtility
{
	public static function register($username, $password, $phone, $company, &$msg)
	{
		$result = null;
		$user = M("BaseUser");
		$reg_time = time();
		$password = sha1("EXCITED_HILL" . md5($password) . $reg_time);
		$data = array(
			"username" => $username,
			"password" => $password,
			"phone"    => $phone,
			"type"     => 2,
			"nickname" => $username,
			"reg_time" => $reg_time
		);
		// Begin transaction to create a member.
		$user -> startTrans();
		$result = $user -> data($data) -> add();
		if ($result !== false)
		{
			$user_id = $result;
			$name = $company;
			$company = M("BaseCompanyTemp");
			$data = array(
				"uid"         => $user_id,
				"name"        => $name,
				"create_time" => $reg_time,
				"update_time" => $reg_time
			);
			$result = $company -> data($data) -> add();
		}
		if ($result !== false)
		{
			$user -> commit();
			$msg = "Success";
			$result = array(
				"id" => $user_id
			);
		}
		else
		{
			$user -> rollback();
			$msg = "数据库错误";
		}
		return $result;
	}

	public static function login($username, $password, &$msg)
	{
		$user = M("BaseUser");
		$cond = array(
			array(
				"username" => $username,
				"phone"    => $username,
				"_logic"   => "or"
			),
			"status"   => 1
		);
		$common_info = $user -> where($cond) -> find();
		if (is_array($common_info))
		{
			if (sha1("EXCITED_HILL" . md5($password) . $common_info["reg_time"]) == $common_info["password"])
			{
				$type = $common_info["type"];
				unset($common_info["password"]);
				switch ($type)
				{
					case 1:
						$other_info["groups"] = array(1);
						break;
					case 2:
						$company = is_numeric($common_info["companyid"]) ? M("BaseCompany") : M("BaseCompanyTemp");
						$other_info = $company -> where("uid = " . $common_info["id"]) -> find();
						$other_info["companyid"] = $other_info["id"];
						$other_info["groups"] = array(2);
						unset($other_info["id"]);
						unset($other_info["uid"]);
						break;
					case 4:
						$government = M("BaseGovernment");
						$other_info = $government -> where("user_id = " . $common_info["id"]) -> find();
						$other_info["groups"] = array(4);
						unset($other_info["user_id"]);
				}
				// unset($common_info["type"]);
				if ($other_info !== null)
				{
					$user_info = array_merge($common_info, $other_info);
				}
				else
				{
					$user_info = $common_info;
				}
				$_SESSION["user_info"] = $user_info;
				if ($type == 2)
				{
					if (Auth::check("Unauthenticated"))
					{
						$_SESSION["user_info"]["menu_entries"] = array(7);
						$_SESSION["user_info"]["auth_list"] = array(
							"Company/Basic/basic" => "",
							"Unauthenticated"     => "{status}<1"
						);
					}
				}
				$msg = "Success";
				$result = $user_info;
			}
			else
			{
				$msg = "密码输入错误！";
				$result = false;
			}
		}
		else
		{
			$msg = "请输入合法有效的用户名！";
			$result = false;
		}
		return $result;
	}

	public static function logout()
	{
		$_SESSION["user_info"] = null;
	}

	public static function repassword($user_id, $old_password, $new_password)
	{
		$user = M("BaseUser");
		$cond = array(
			"id" => $user_id
		);
		$reg_time = $user -> where($cond) -> getField("reg_time");
		$old_password = sha1("EXCITED_HILL" . md5($old_password) . $reg_time);
		$new_password = sha1("EXCITED_HILL" . md5($new_password) . $reg_time);
		$cond["password"] = $old_password;
		$data = array(
			"password" => $new_password
		);
		$result = $user -> where($cond) -> setField($data);
		return $result;
	}

	/**
	 * 新建政府用户
	 * @param $username
	 * @param $password
	 * @param $phone
	 * @param $company
	 * @param $msg
	 * @param $type
	 * @param $province
	 * @param $city
	 * @param $district
	 * @param $regioncode
	 *
	 * @return array|null
	 */
	public static function registerGov($username, $password, $phone, $company, &$msg, $type, $province, $city, $district, $regioncode,$contact)
	{
		$result = null;
		$user = M("BaseUser");
		$reg_time = time();
		$password = sha1("EXCITED_HILL" . md5($password) . $reg_time);
		$data = array(
			"username" => $username,
			"password" => $password,
			"phone" => $phone,
			"type" => 4,
			"nickname" => $username,
			"reg_time" => $reg_time
		);
		// Begin transaction to create a member.
		$user->startTrans();
		$result = $user->data($data)->add();

		if ($result !== false)
		{
			$user_id = $result;
			$name = $company;
			$companytemp = M("BaseGovernment");
			$data = array(
				"user_id" => $user_id,
				"name" => $name,
				"type" => $type,
				"province" => $province,
				"city" => $city,
				"district" => $district,
				"region_code" => $regioncode,
				"contact" => $contact
			);
			$result = $companytemp->data($data)->add();
		}
		if ($result !== false)
		{
			$user->commit();
			$msg = "Success";
			$result = array(
				"id" => $user_id
			);
		}
		else
		{
			$user->rollback();
			$msg = "数据库错误";
		}

		return $result;
	}

}
