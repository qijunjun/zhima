<?php
namespace API\Controller;

use Think\Controller;
use Common\API\CompanyAPI;

class CompanyController extends Controller
{
	protected function _initialize()
	{
		header("content-type: application/x-javascript");
	}

	public function fetchProducts()
	{
		$response = CompanyAPI::fetchProducts();
		echo json_encode($response);
	}

	public function fetchOperation($productId)
	{
		$response = CompanyAPI::fetchOperation($productId);
		echo json_encode($response);
	}

	public function fetchData($page = 1)
	{
		$response = CompanyAPI::fetchData($page);
		echo json_encode($response);
	}
	public function fetchDataByProduct($page = 1,$productid)
	{
		$response = CompanyAPI::fetchDataByProduct($page,$productid);
		echo json_encode($response);
	}
	
}
