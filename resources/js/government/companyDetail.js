/**
 * Created by Lenovo on 2016/6/17.
 * Edited by James "Carbon" leon Neo on 2016/07/24.
 */
$(document).ready(function ()
{
	var companyId;
	companyId = /(\d+)\/{0,1}$/.exec(location.href)[1];
	var imgSrc = {};
	methods.ajax({
		url: '/government/company/info',
		data: {
			companyId: companyId
		},
		callback: function (data)
		{
			data = data.result;
			id = data.id;
			setData(data);
			if (data.businesslicense_img)
			{
				$("#businesslicense_img").addClass("getImg").html("点击查看");
				imgSrc.businesslicense_img = data.businesslicense_img;
			}
			$(".portrait").click(function ()
			{
				new ImgUpload({title: '查看图片'+($(this).data('spec')==undefined?"":$(this).data('spec')), name: $(this).attr('name')}, $(this).val()).alert();
			});
		}
	});
	$("#product").bootgrid({
		ajax: true,
		ajaxSettings: {
			dataType: "json"
		},
		url: "/government/company/products",
		post: {
			companyId: companyId
		},
		identifier: "productid",
		formatters: {
			productimage: function (column, row)
			{
				return "<img src='" + row.productimage + "' />";
			}
		}
	});
	$("#Process").bootgrid({
		ajax: true,
		ajaxSettings: {
			dataType: "json"
		},
		url: "/government/company/processes",
		post: {
			companyId: companyId
		},
		identifier: "id",
		responseHandler: function (response)
		{
			var rows;
			rows = {
				total: response.result.length,
				current: 1,
				rows: response.result
			};
			response.result = rows;
			return response;
		},
		formatters: {
			product_image: function (column, row)
			{
				return "<img src='" + row.productimage + "' />";
			},
			productName_Spec: function (column, row)
			{
				return "<span>"+row.productname+"</span>"+"-"+"<span>"+row.guige+"</span>";
			},
			operator_image: function (column, row)
			{
				return "<img src='" + row.operatorimage + "' />";
			},
			content_image: function (column, row)
			{
				var str = "";
				if (!(row.image_path instanceof Array))
				{
					return "";
				}
				for(var k = 0; k < row.image_path.length; k++)
				{
					str += "<img src='" + row.image_path[k] + "' />";
				}
				return str;
			}
		}
	})
});
