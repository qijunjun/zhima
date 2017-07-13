<?php
namespace Admin\Utility;

class AccountManager
{
	public static function fetchLogs($companyId, $page, $size, $conditions, $sort)
	{
		$log = M("BaseCompanyAccountLog");
		$criteria = array(
			"companyid" => $companyId
		);
		$order = array(
			"id" => "asc"
		);
		$cond = parse_conditions($conditions);
		if (count($cond))
		{
			$criteria["_logic"] = "and";
			$criteria["_complex"] = $cond;
		}
		if ($sort !== null)
		{
			$order = array_merge($sort, $order);
		}
		$result = retrieve_data_advanced($log, null, $criteria, $page, $size, $order);
		return $result;
	}

	public static function chargeMoney($id, $fee)
	{
		$log = M("BaseCompanyAccountLog");
		$account = M("BaseCompanyAccount");
		$cond = array(
			"companyid" => $id
		);
		$account_id = $account -> where($cond) -> getField("accountid");
		$time = time();
		$data = array(
			"companyid"      => $id,
			"accountid"      => $account_id,
			"recharge_time"  => $time,
			"recharge_count" => $fee
		);
		$log -> startTrans();
		$result = $log -> data($data) -> add();
		if ($result !== false)
		{
			$log -> commit();
		}
		else
		{
			$log -> rollback();
		}
		return $result;
	}
}
