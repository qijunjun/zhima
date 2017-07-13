<?php
namespace Admin\Controller;

use Common\Controller\BaseController;
use Admin\API\UserAPI;

class UserController extends BaseController
{
	public function fetch()
	{
		$response = UserAPI::fetchUsers();
		echo json_encode($response);
	}

	public function authenticated()
	{
		$response = UserAPI::authenticatedUsers();
		echo json_encode($response);
	}

	public function authenticate($id)
	{
		$response = UserAPI::authenticateUser($id);
		echo json_encode($response);
	}

	public function unauthenticated()
	{
		$response = UserAPI::unauthenticatedUsers();
		echo json_encode($response);
	}

	public function get($id)
	{
		$response = UserAPI::getUser($id);
		echo json_encode($response);
	}

	public function edit($id, $name, $contact, $phone)
	{
		$response = UserAPI::editUser($id, $name, $contact, $phone);
		echo json_encode($response);
	}

	public function repassword()
	{
		$companyid = I("companyid");
		$newPassword = I("newPassword");
		$result = UserAPI::repassword($companyid, $newPassword);
		echo json_encode($result);
	}
}
