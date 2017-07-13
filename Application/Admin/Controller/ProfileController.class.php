<?php
namespace Admin\Controller;

use Common\Controller\BaseController;
use Admin\API\ProfileAPI;

class ProfileController extends BaseController
{
	public function fetch()
	{
		$response = ProfileAPI::fetchProfiles();
		echo json_encode($response);
	}

	public function add($name)
	{
		$response = ProfileAPI::addProfile($name);
		echo json_encode($response);
	}

	public function edit($id, $name)
	{
		$response = ProfileAPI::editProfile($id, $name);
		echo json_encode($response);
	}

	public function remove($id)
	{
		$response = ProfileAPI::removeProfile($id);
		echo json_encode($response);
	}
}
