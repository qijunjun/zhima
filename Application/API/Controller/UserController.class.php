<?php
namespace API\Controller;

use Think\Controller;
use Think\Verify;
use Common\API\EntranceAPI;

class UserController extends Controller
{
	protected function _initialize()
	{
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/x-javascript");
	}/*

	public function register($username, $password, $phone, $company)
	{
		$response = UserAPI::register($username, $password, $phone, $company);
		echo json_encode($response);
	}*/

	public function login($username, $password)
	{
		$response = EntranceAPI::login($username, $password);
		echo json_encode($response);
	}/*

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
	}*/
}
