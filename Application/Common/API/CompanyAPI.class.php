<?php
namespace Common\API;

class CompanyAPI
{
	public static function fetchProducts()
	{
		$result = false;
		$user_info = $_SESSION["user_info"];
		if (is_array($user_info) && $user_info["type"] == 2)
		{
			$company_id = $user_info["companyid"];
			$product = M("base_product");
			$products = $product -> where("companyid = " . $company_id) -> select();
			$result = $products;
		}
		$code = $result === false ? "007" : "001";
		$msg = $result === false ? "Database error" : "Success";
		$results = $products;
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
	/* 注释-2016-07-23
	public static function fetchOperation($productId)
	{
		$operation = M("base_product_function");
		$fields = array(
			"functionid"    => "id",
			"function_name"  => "name",
			"function_image" => "image",
		);
		$result = $operation -> field($fields) -> where("productid = " . $productId) -> select();
		$code = $result === false ? "007" : "001";
		$msg = $result === false ? "Database error" : "Success";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
	*/
	//by david 2016-7-23
	public static function fetchOperation($productId)
	{
		$operation = M("base_product_function_operates");  //生产环节操作记录表
		$corrfunction=M("corr_product_function");    //生产环节和产品关联表
		$funtions=$corrfunction->field("functionid")->where("productid = ".$productId)->select(); //返回产品对应的生产环节id
		$cond="";
		foreach ($funtions as $key=>$value){
			$cond=$cond.$value["functionid"].',';
		}
		$cond=rtrim($cond, ",");
		$map['functionid']  = array('in',$cond);
		$fields = array(
			"functionid"    => "id",
			"function_name"  => "name",
			"function_image" => "image",
		);
		$result = $operation -> field($fields) -> where($map) -> select();
		$code = $result === false ? "007" : "001";
		$msg = $result === false ? "Database error" : "Success";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function fetchData($page)
	{
		$page_size = 10;
		$result = false;
		// Reset page index in case of invalid value.
		if (!is_numeric($page))
		{
			if ($page != "all") {
				$page = 1;
			}
		}
		else
		{
			$page = intval($page);
			$page = $page < 1 ? 1 : $page;
		}
		$user_info = $_SESSION["user_info"];
		// Get company id.
		if (is_array($user_info) && $user_info["type"] == 2)
		{
			$company_id = $user_info["companyid"];
			//$company_id=1019;
			// Prepare a prefetch model to gather data.
			$model = M();
			$sql = "
				SELECT
				zm_base_product.productid,
				zm_base_product.productname,
				zm_base_product.productimage,
				zm_base_product.guige,
				zm_base_product_event_data.operatorimage,
				zm_base_product_event_data.event_time,
				zm_base_product_event_data.userlocation,
				zm_base_product_event_data.event_details,
				zm_base_product_event_data.id,
				zm_base_product_event_data.function_operateid,
				zm_base_product_event_data.function_name
				FROM
				zm_base_product_event_data
				LEFT JOIN zm_base_product ON zm_base_product_event_data.productid = zm_base_product.productid
				WHERE
				zm_base_product_event_data.companyid =" .$company_id. " 
				ORDER BY
				zm_base_product_event_data.event_time DESC,
				zm_base_product_event_data.id DESC
				";
			if (is_numeric($page))
			{
				$sql .= "LIMIT " . ($page - 1) * $page_size . "," . $page_size;
			}
			$data = $model -> query($sql);
			if ($data !== false)
			{
				$result = array();
				$i = 0;
				$image = M("base_product_event_image");
				foreach ($data as $item)
				{
					$data_id = $item["id"];
					//unset($item["productid"]);
					//unset($item["functionid"]);
					$result[$i] = $item;
					$result[$i++]["image_path"] = $image -> where("eventid = " . $data_id) -> getField("image_path", true);
				}
			}
		}
		$code = $result === false ? "007" : "001";
		$msg = $result === false ? "Database error" : "Success";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
	public static function fetchDataByProduct($page,$productid)
	{
		$result = false;
		// Reset page index in case of invalid value.
		if (!is_numeric($page))
		{
			$page = 1;
		}
		else
		{
			$page = intval($page);
			$page = $page < 1 ? 1 : $page;
		}
		//$company_id = $user_info["companyid"];
		//$company_id=1019;
		// Prepare a prefetch model to gather data.
		$model = M();
		$sql = "
				SELECT
				zm_base_product.productid,
				zm_base_product.productname,
				zm_base_product.productimage,
				zm_base_product.guige,
				zm_base_product_event_data.operatorimage,
				zm_base_product_event_data.event_time,
				zm_base_product_event_data.userlocation,
				zm_base_product_event_data.event_details,
				zm_base_product_event_data.id,
				zm_base_product_event_data.function_operateid,
				zm_base_product_event_data.function_name
				FROM
				zm_base_product_event_data
				LEFT JOIN zm_base_product ON zm_base_product_event_data.productid = zm_base_product.productid
				WHERE
				zm_base_product_event_data.productid =" .$productid . " 
				ORDER BY
				zm_base_product_event_data.event_time DESC,
				zm_base_product_event_data.id DESC
				";
		$data = $model -> query($sql);
		if ($data !== false)
		{
			$result = array();
			$i = 0;
			$image = M("base_product_event_image");
			foreach ($data as $item)
			{
				$data_id = $item["id"];
				unset($item["productid"]);
				unset($item["functionid"]);
				$result[$i] = $item;
				$result[$i++]["image_path"] = $image -> where("eventid = " . $data_id) -> getField("image_path", true);
			}
		}
		$code = $result === false ? "007" : "001";
		$msg = $result === false ? "Database error" : "Success";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
	public static function fetchDataByEvent($page,$eventid){
		//$page_size = 3;
		$result = false;
		// Reset page index in case of invalid value.
		if (!is_numeric($page))
		{
			$page = 1;
		}
		else
		{
			$page = intval($page);
			$page = $page < 1 ? 1 : $page;
		}
		$user_info = $_SESSION["user_info"];
		// Get company id.
		if (is_array($user_info) && $user_info["type"] == 2)
		{
			$company_id = $user_info["companyid"];
			// Prepare a prefetch model to gather data.
			$model = M();
			$sql="
				SELECT
				zm_base_product.productid,
				zm_base_product.productname,
				zm_base_product.productimage,
				zm_base_product.guige,
				zm_base_product_event_data.operatorimage,
				zm_base_product_event_data.event_time,
				zm_base_product_event_data.userlocation,
				zm_base_product_event_data.event_details,
				zm_base_product_event_data.id,
				zm_base_product_event_data.function_operateid,
				zm_base_product_event_data.function_name
				FROM
				zm_base_product_event_data
				LEFT JOIN zm_base_product ON zm_base_product_event_data.productid = zm_base_product.productid
				WHERE
				zm_base_product_event_data.companyid =" .$company_id. " and zm_base_product_event_data.id =" .$eventid. "
				ORDER BY
				zm_base_product_event_data.event_time DESC,
				zm_base_product_event_data.id DESC
			  ";
			$data = $model -> query($sql);
			if ($data !== false)
			{
				$result = array();
				$i = 0;
				$image = M("base_product_event_image");
				foreach ($data as $item)
				{
					$data_id = $item["id"];
					$result[$i] = $item;
					$result[$i++]["image_path"] = $image -> where("eventid = " . $data_id) -> getField("image_path", true);
				}
			}
		}
		return $result;
	}
	/*
	 * 通过生产过程id获取生产过程的记录数据
	 * $event_range:生产记录id列表
	 */
	public static function fetchDataByCode($page,$event_range)
	{
		$result = false;
		// Reset page index in case of invalid value.
		if (!is_numeric($page))
		{
			$page = 1;
		}
		else
		{
			$page = intval($page);
			$page = $page < 1 ? 1 : $page;
		}
		// Prepare a prefetch model to gather data.
		$model = M();
		$sql = "
				SELECT
				zm_base_product.productname,
				zm_base_product.productimage,
				zm_base_product.guige,
				zm_base_product_event_data.operatorimage,
				zm_base_product_event_data.event_time,
				zm_base_product_event_data.userlocation,
				zm_base_product_event_data.event_details,
				zm_base_product_event_data.id,
				zm_base_product_event_data.function_operateid,
				zm_base_product_event_data.function_name,
				zm_base_product_event_data.productid
				FROM zm_base_product_event_data 
				left join zm_base_product
				ON zm_base_product.productid = zm_base_product_event_data.productid
                where zm_base_product_event_data.id in ($event_range)
				ORDER BY
				zm_base_product_event_data.event_time DESC,
				zm_base_product_event_data.id DESC
				";
		$data = $model -> query($sql);
		if ($data !== false)
		{
			$result = array();
			$i = 0;
			$image = M("base_product_event_image");
			foreach ($data as $item)
			{
				$data_id = $item["id"];
				unset($item["productid"]);
				unset($item["functionid"]);
				$result[$i] = $item;
				$result[$i++]["image_path"] = $image -> where("eventid = " . $data_id) -> getField("image_path", true);
			}
		}
		$code = $result === false ? "007" : "001";
		$msg = $result === false ? "Database error" : "Success";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
}
