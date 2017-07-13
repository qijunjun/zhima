<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/5/17
 * Time: 下午11:54
 */
namespace Produce\Controller;

use Common\Controller\BaseController;
use Produce\API\ProfileAPI;
use Product\API\Correlation;
class ConnectController extends BaseController
{
	public function select($productId)
	{
		$profile = MM("BaseProductprofile","zm_");
		$cond = array(
			"product_id" => $productId
		);
		$result = $profile -> where($cond) -> field("_id,name") -> select();
		echo json_encode(array(
			"code" => "001",
			"msg" => "Success",
			"result" => $result
		));
	}

	public function fetch()
	{
		$product = M("BaseProduct");
		$profile = MM("BaseProductprofile","zm_");
		$corr_profile = M("CorrQrcodeProductprofile");
		$cond = array(
			"companyid" => $_SESSION["user_info"]["companyid"]
		);
		$products = $product -> where($cond) -> getField("productid id, productname product_name, guige spec", true);
		$cond = array(
			"company_id" => $_SESSION["user_info"]["companyid"]
		);
		$data = $profile -> where($cond) -> field("_id,name") -> select();
		$profiles = array();
		foreach ($data as $item)
		{
			$profiles[$item["_id"]] = $item["name"];
		}
		$list = $corr_profile -> where($cond) -> field("id, product_id, productprofile_id profile_id, qrcode_range_s, qrcode_range_e, create_time") -> select();
		$result = array();
		foreach ($list as $entry)
		{
			$p = $products[$entry["product_id"]];
			unset($p["id"]);
			$n = $profiles[$entry["profile_id"]];
			$row = array_merge($entry, $p);
			$row["profile_name"] = $n;
			$result[] = $row;
		}
		echo json_encode(array(
			"code" => "001",
			"msg" => "Success",
			"result" => $result
		));
	}
/*
 * 质量码与生产信息关联
 * $productId:产品id
 * $profileId:生产信息id
 * $bStart, $bEnd:质量码起止范围
 */
	public function bind($productId, $profileId, $bStart, $bEnd)
	{
		$companyId = $_SESSION["user_info"]["companyid"];
		$time = time();
		if($productId==null||$profileId==null)
		{
			json("002", null, "请选择产品或生产信息");
			return;
		}
		if(!checkQCode($companyId,$bStart,$bEnd))
		{
			json("002", null, "不是当前公司的码段");
			return;
		}
		if ($bStart > $bEnd)
		{
			json("002", null, "起始码不可大于结束码");
			return;
		}
		$pid=Correlation::getProductbyRange($bStart,$bEnd);
		if($pid!=$productId)
		{
			json("002", null, "当前码段与关联产品不一致");
			return;
		}
		$idcollection = ProfileAPI::checkQrcodeRepeat($bStart, $bEnd);
		if (sizeof($idcollection) > 0)
		{
			json("009", $idcollection, "当前码段已关联了生产信息!");
			return;
		}
		$profile = M("CorrQrcodeProductprofile");
		$data = array(
			"company_id" => $companyId,
			"product_id" => $productId,
			"productprofile_id" => $profileId,
			"qrcode_range_s" => $bStart,
			"qrcode_range_e" => $bEnd,
			"create_time" => $time
		);
		$result = $profile -> data($data) -> add();
		echo json_encode(array(
			"code" => "001",
			"msg" => "Success",
			"result" => $result
		));
	}

	public function remove($id)
	{
		$profile = M("CorrQrcodeProductprofile");
		$cond = array(
			"id" => $id
		);
		$result = $profile -> where($cond) -> delete();
		if($result==0)
		{
			json('002',null,'删除失败');
			return;
		}
		else
		{
			json('001',null,'删除成功');
			return;
		}
	}
}
