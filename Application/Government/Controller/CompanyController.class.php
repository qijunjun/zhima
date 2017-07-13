<?php
namespace Government\Controller;

use Common\Controller\BaseController;
use Government\API\CompanyAPI;

class CompanyController extends BaseController
{
	public function fetch($page = 1, $size = 10, $conditions = null, $sort = null)
	{
		$result = CompanyAPI::fetchCompanies($page, $size, $conditions, $sort);
		echo json_encode($result);
	}

	public function fetchCompany($page = 1, $size = 10, $conditions = null, $sort = null)
	{
		$page = I('page');
		$size = I('size');
		$result = CompanyAPI::fetchCompanies($page, $size, $conditions, $sort);
		echo json_encode($result);
	}

	public function inCompass($companyid)
	{
		$res= CompanyAPI::inCompass($companyid);
		if($res)
		{
			json('001');
			return;
		}
		else
		{
			json('002',null,'该码所在公司不在管辖范围');
		}
	}
	
	public function info($companyId)
	{
		$result = CompanyAPI::companyInfo($companyId);
		echo json_encode($result);
	}

	public function products($companyId, $page = 1, $size = 10, $conditions = null, $sort = null)
	{
		$result = CompanyAPI::fetchProducts($companyId, $page, $size, $conditions, $sort);
		echo json_encode($result);
	}

	public function processes($companyId)
	{
		$_SESSION["user_info"]["companyid"] = $companyId;
		$response = \Common\API\CompanyAPI::fetchData("all");
		echo json_encode($response);
	}
}
