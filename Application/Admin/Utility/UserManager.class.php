<?php
namespace Admin\Utility;

class UserManager
{
	public static function fetchUsers()
	{
		$authenticated = self::authenticatedUsers();
		$unauthenticated = self::unauthenticatedUsers();
		$result = array_merge($authenticated, $unauthenticated);
		return $result;
	}

	public static function authenticatedUsers()
	{
		$company = M("BaseCompany");
		$result = $company -> field("id, name, contact, phone, status") -> select();
		return $result;
	}

	public static function unauthenticatedUsers()
	{
		$company = M("BaseCompanyTemp");
		$result = $company -> field("id, name, contact, phone, status") -> select();
		return $result;
	}

	public static function authenticateUser($id)
	{
		$temp = M("BaseCompanyTemp");
		$company = M("BaseCompany");
		$user = M("BaseUser");
		$cond = array(
			"id" => $id
		);
		$data = $temp -> where($cond) -> find();
		$data["status"] = 1;
		unset($data["id"]);
		$company -> startTrans();
		$result = $company -> data($data) -> add();
		if ($result === false)
		{
			$company -> rollback();
			return false;
		}
		$company_id = $result;
		$result = $temp -> where($cond) -> delete();
		if ($result === false)
		{
			$company -> rollback();
			return false;
		}
		$cond = array(
			"id" => $data["uid"]
		);
		$data = array(
			"companyid" => $company_id
		);
		$result = $user -> where($cond) -> setField($data);
		if ($result === false)
		{
			$company -> rollback();
			return false;
		}
		$company -> commit();
		return $result;
	}

	public static function getUser($id)
	{
		$company = M("BaseCompanyTemp");
		$cond = array(
			"id" => $id
		);
		$result = $company -> where($cond) -> field("name, contact, phone") -> find();
		return $result;
	}

	public static function editUser($id, $name, $contact, $phone)
	{
		$company = M("BaseCompanyTemp");
		$cond = array(
			"id" => $id
		);
		$data = array(
			"name"    => $name,
			"contact" => $contact,
			"phone"   => $phone
		);
		$result = $company -> where($cond) -> setField($data);
		return $result;
	}

	public static function getAdminid($companyid)
	{
		$user = M("BaseCompany");
		$cond = array(
			"id" => $companyid
		);
		$uid = $user -> where($cond) -> getField("uid");
		return $uid;
	}

	public static function repassword($user_id, $new_password)
	{
		$user = M("BaseUser");
		$cond = array(
			"id" => $user_id
		);
		$reg_time = $user -> where($cond) -> getField("reg_time");
//		$old_password = sha1("EXCITED_HILL" . md5($old_password) . $reg_time);
		$new_password = sha1("EXCITED_HILL" . md5($new_password) . $reg_time);
//		$cond["password"] = $old_password;
		$data = array(
			"password" => $new_password
		);
		$result = $user -> where($cond) -> setField($data);
		return $result;
	}
}
