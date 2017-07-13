<?php
namespace Produce\Controller;

use Common\Controller\BaseController;
use Produce\API\ProfileAPI;

class ProfileController extends BaseController
{
	public function fetch()
	{
		$response = ProfileAPI::fetchProfile();
		echo json_encode($response);
	}

	public function add($productId, $list)
	{
		$response = ProfileAPI::addProfile($productId, $list);
		echo json_encode($response);
	}

	public function get($id)
	{
		$profile = MM("BaseProductprofile","zm_");
		$cond = array(
			"_id" => $id
		);
		$result = $profile -> where($cond) -> find();
		echo json_encode(array(
			"code" => "001",
			"msg"  => "Success",
			"result" => $result
		));
	}

	public function edit($id, $list)
	{
		$response = ProfileAPI::editProfile($id, $list);
		echo json_encode($response);
	}

	public function remove($id)
	{
		$response = ProfileAPI::removeProfile($id);
		echo json_encode($response);
	}
}
