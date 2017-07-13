<?php
namespace API\Controller;

use Think\Controller;
use Think\Verify;
use User\API\UserAPI;

class PublicController extends Controller
{
	protected function _initialize()
	{
		header("content-type: application/x-javascript");
	}

	public function register($username, $password, $phone, $company)
	{
		$response = UserAPI::register($username, $password, $phone, $company);
		echo json_encode($response);
	}

	public function login($username, $password, $verify = null)
	{
		$flag = $_SERVER["HTTP_REFERER"] == "http://nongye.zmade.cn/login.php";
		$response = UserAPI::login($username, $password, $verify, $flag);
		echo json_encode($response);
	}

	public function logout()
	{
		UserAPI::logout();
	}

	public function validate($token = null)
	{
		$response = UserAPI::validate($token);
		echo json_encode($response);
	}

	public function validation($token = null)
	{
		$response = UserAPI::validate($token);
		echo json_encode($response);
	}

	public function verify()
	{
		$verify = new Verify();
		$verify -> entry("user_login");
	}
}
