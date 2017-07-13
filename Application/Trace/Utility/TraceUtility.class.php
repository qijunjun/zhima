<?php
namespace Trace\Utility;

class TraceUtility
{
	public static function prepareInfo($code, $check)
	{
		$qrcode_info = M("CorrQrcodeProduct");
		$qrcode_pack = M("CorrQrcodePack");
		$qrcode_profile = M("CorrQrcodeProductprofile");
		$company = M("BaseCompany");
		$company_id = substr($code, 0, 4);
		if ($code[4] == "1")
		{
			$cond = array(
				"qrcode_range_s" => array('elt', $code),
				"qrcode_range_e" => array('egt', $code)
			);
			$item_code = $code;
			$package_code = $qrcode_pack->where($cond)->getField("qrcode_pack");
		}
		else
		{
			$cond = array(
				"qrcode_pack" => $code
			);
			$item_code = $qrcode_pack->where($cond)->getField("qrcode_range_s");
			$package_code = $code;
			$cond = array(
				"qrcode_range_s" => array('elt', $item_code),
				"qrcode_range_e" => array('egt', $item_code)
			);
		}
		$product_id = $qrcode_info->where($cond)->getField("product_id");
		$profile_id = $qrcode_profile->where($cond)->getField("productprofile_id");
		$template_id = $company->where("id = " . $company_id)->getField("scan_template");
		$_SESSION["code_info"] = array(
			"b" => $code,
			"c" => $check,
			"item_code" => floatval($item_code),
			"package_code" => floatval($package_code),
			"company_id" => $company_id,
			"product_id" => $product_id,
			"profile_id" => $profile_id,
			"scan_template" => $template_id
		);
	}

	public static function scanInfo($code)
	{
		$scan_info = M("BaseScaninfo");
		$fields = "first_time, scan_count";
		$cond = array(
			"b" => $code
		);
		$result = $scan_info->field($fields)->where($cond)->find();

		return $result;
	}

	public static function companyInfo($company_id)
	{
		$company = M("BaseCompany");
		$fields = "name, address, introduction, contact, logo, phone, email, introimage intro_image0, introimage1 intro_image1, introimage2 intro_image2, introimage3 intro_image3, introimage4 intro_image4";
		$cond = array(
			"id" => $company_id
		);
		$result = $company->field($fields)->where($cond)->find();

		return $result;
	}

	public static function productInfo($product_id)
	{
		$product = M("BaseProduct");
		$fields = "productid id, productname name, guige spec, price, productinfo info, productimage image0, productimage1 image1, productimage2 image2, productimage3 image3, productimage4 image4, wdadr netshop";
		$cond = array(
			"productid" => $product_id
		);
		$result = $product->field($fields)->where($cond)->find();

		return $result;
	}

	public static function profileInfo($profile_id)
	{
		$profile = MM("BaseProductprofile","zm_");
		$cond = array(
			"_id" => $profile_id
		);
		$result = $profile->where($cond)->find();

		return $result;
	}

	public static function warehouseInfo($item_code, $package_code, $company_id)
	{
		$warehouse = M("BaseWarehouse");
		$agent = M("BaseAgent");
		$scan_in = MM("base_scanin","zm_");
		$scan_out = MM("base_scanout","zm_");
		$cond = array(
			"companyid" => $company_id
		);
		$warehouses = $warehouse->where($cond)->getField("id, warehouse_name name", true);
		$agents = $agent->where($cond)->getField("id, agent_name name", true);
		$cond = array(
			"p" => $package_code
		);
		$ins = $scan_in->where($cond)->field("create_time,warehouseid")->select();
		$outs = $scan_out->where($cond)->field("create_time,outtype,destinationid")->select();
		$result = array();
		foreach ($ins as $in)
		{
			$item = array(
				"name" => $warehouses[$in["warehouseid"]],
				"time" => $in["create_time"],
				"type" => "1"
			);
			array_push($result, $item);
		}
		foreach ($outs as $out)
		{
			$item = array(
				"name" => $out["outtype"] == "0" ? $warehouses[$out["destinationid"]] : $agents[$out["destinationid"]],
				"time" => $out["create_time"],
				"type" => "0"
			);
			array_push($result, $item);
		}

		return $result;
	}
	/**
	 * 根据箱码查询对应的质量码 2016.8.1
	 */
	public static function searchCodeInfo($code,$company_id){
		$qrcode_pack = M("CorrQrcodePack");
		$fields = "qrcode_range_s,qrcode_range_e";
		$where = array(
			"qrcode_pack" => $code,
			"company_id" => $company_id
		);
		$recode = $qrcode_pack->field($fields)->where($where)->find();
		return $recode;
	}
	/**
	 * 根据质量码查询对应的箱码 2016.8.5
	 */
	public static function findCodeInfo($code,$company_id){
		$qrcode_pack = M("CorrQrcodePack");
		$map['company_id'] = $company_id;
		$map['qrcode_range_s'] = array('elt',$code);
		$map['qrcode_range_e'] = array('egt',$code);
		$recode = $qrcode_pack->field('qrcode_pack')->where($map)->find();
		return $recode;
	}
	/**
	 * 获取召回信息
	 * @param $companyid
	 * @param $qrcode
	 *
	 * @return mixed
	 */
	public static function recallInfo($companyid, $qrcode)
	{
		$recall = M("BaseRecall");
		$map["company_id"] = $companyid;
		$map["qrcode_range_s"] = array('ELT', $qrcode);
		$map["qrcode_range_e"] = array('EGT', $qrcode);
		$result = $recall->field('product_id,reason,create_time,update_time')->where($map)->find();
		return $result;
	}
	/**箱码对应物流
	 * @param $code
	 * @param $company_id
	 * @return mixed
	 */
	public static function logisticspackInfo($code,$company_id){
		$model = M("BaseLogistics");
		$map['company_id'] = $company_id;
		$map['qrcode_pack_s'] = array('elt',$code);
		$map['qrcode_pack_e'] = array('egt',$code);
		$result = $model->field('logistics,expresslist,logistics_time')->where($map)->find();
		return $result;
	}

	/**
	 * 质量码对应的物流2016.8.5
	 */
	public static function logisticsInfo($code,$company_id){
		$model = M("BaseLogistics");
		//根据质量码先查到对应的箱码
		$qrcode = M("CorrQrcodePack");
		$map['company_id'] = $company_id;
		$map['qrcode_range_s'] = array('elt',$code);
		$map['qrcode_range_e'] = array('egt',$code);
		$qrcode_pack = $qrcode->where($map)->getField('qrcode_pack');

		//根据箱码查到对应的物流信息
		$where['company_id'] = $company_id;
		$where['qrcode_pack_s'] = array('elt',$qrcode_pack);
		$where['qrcode_pack_e'] = array('egt',$qrcode_pack);
		$result = $model->field('logistics,expresslist,logistics_time')->where($where)->find();
		array_push($result,$qrcode_pack);

		return $result;

	}

}
