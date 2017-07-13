<?php
/*
	Class GroupController
	Controller class to manage user group and its priviledge.
	Revision 00012
	By James "Carbon" leon Neo
*/
namespace Admin\Controller;

use Common\Controller\BaseController;
use Admin\API\GroupAPI;

class GroupController extends BaseController
{
	protected function _initialize()
	{
		parent::_initialize();
		if (ACTION_NAME != "index")
		{
			header("content-type: application/x-javascript");
		}
	}

	public function rules()
	{
		$response = GroupAPI::fetchRules();
		echo json_encode($response);
	}

	public function fetch()
	{
		$response = GroupAPI::fetchGroups();
		echo json_encode($response);
	}

	public function add($title, $description, $rules)
	{
		$response = GroupAPI::addGroup($title, $description, $rules);
		echo json_encode($response);
	}

	public function edit($id, $title, $description, $status, $rules)
	{
		$response = GroupAPI::editGroup($id, $title, $description, $status, $rules);
		echo json_encode($response);
	}
}
