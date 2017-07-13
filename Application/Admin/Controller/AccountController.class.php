<?php
namespace Admin\Controller;

use Common\Controller\BaseController;
use Admin\API\AccountAPI;

class AccountController extends BaseController
{
	public function fetch($companyId, $page = 1, $size = 10, $conditions = null, $sort = null)
	{
		$response = AccountAPI::fetchLogs($companyId, $page, $size, $conditions, $sort);
		echo json_encode($response);
	}

	public function charge($id, $fee)
	{
		$response = AccountAPI::chargeMoney($id, $fee);
		echo json_encode($response);
	}
}
