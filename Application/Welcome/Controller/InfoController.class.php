<?php
namespace Welcome\Controller;

use Think\Controller;

class InfoController extends Controller
{
	public function scan()
	{
		$productid = I("productid");


		$company_id = $_SESSION["user_info"]["companyid"];
		$product = M("BaseProduct");
		$scan_log = M("BaseScanLog");
		$agent = M("BaseAgent");
		$cond = array(
			"companyid" => $company_id
		);

		$agents = $agent->where($cond)->getField("id, agent_name agentname, agent_phone agentphone", true);

		if (!empty($productid) && is_numeric($productid))
		{
			$cond['productid'] = $productid;
		}
		$products = $product->where($cond)->getField("productid id, productname name, guige spec, productimage image", true);

		$cond["log"] = array("exp", "IS NOT NULL");
		$cond["lat"] = array("exp", "IS NOT NULL");

		$startdate = I("startdate");
		if (!empty($startdate))
		{
			$startdate = strtotime($startdate);
		}
		$enddate = I("enddate");
		if (!empty($enddate))
		{
			$enddate = strtotime($enddate);
		}

		if (!empty($startdate) && !empty($enddate))
		{
			$cond['create_time'] = array('between', array($startdate, $enddate));
		}
		elseif (!empty($startdate))
		{
			$cond['create_time'] = array('EGT', $startdate);
		}
		elseif (!empty($enddate))
		{
			$cond['create_time'] = array('ELT', $enddate);
		}

		$scan_logs = $scan_log->where($cond)->field("productid product_id, b code, log longitude, lat latitude, create_time time, istf flag, agentid")->select();


		$result = array();
		foreach ($scan_logs as $entry)
		{
			$data = $products[$entry["product_id"]];
			unset($data["id"]);
			$item = array_merge($entry, $data);
			unset($item["product_id"]);
			$data = $agents[$entry["agentid"]];
			if ($data !== null)
			{
				unset($data["id"]);
			}
			else
			{
				$data = array("agentname" => '', "agentphone" => '');
			}
			$item = array_merge($item, $data);
			unset($item["agentid"]);
			$result[] = $item;
		}

		json("001", $result);
	}
}
