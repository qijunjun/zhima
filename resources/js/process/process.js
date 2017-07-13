/**
 * Created by GUOQH on 2016/5/22.
 * Edited By James "Carbon" leon Neo on 2016/07/22.
 */
$(document).ready(function () {
	$("#Process").bootgrid({
		ajax: true,
		ajaxSettings: {
			dataType: "json"
		},
		post: {
			page: "all"
		},
		url: "/API/Company/fetchdata",
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
			product_image: function (column, row) {
				return "<img class='magnifyImg' src='" + row.productimage + "' />";
			},
			productName_Spec: function (column, row) {
				return "<span>"+row.productname+"</span>"+"-"+"<span>"+row.guige+"</span>";
			},
			operator_image: function (column, row) {
				return "<img class='magnifyImg' src='" + row.operatorimage + "' />";
			},
			content_image: function (column, row) {
				var str = "";
		if (!(row.image_path instanceof Array))
		{
			return "";
		}
				for(var k = 0; k < row.image_path.length; k++) {
					str += "<img class='magnifyImg' src='" + row.image_path[k] + "' />";
				}
				return str;
			},
			operation: function (column, row)
			{
				return "<a href='/Application/Process/View/Edit/editProcess.html?id=" + row.id + "'><p class='operation'>修改</p></a>";
			},
			delete: function (column, row)
			{
				return "<div class=\"delete\" data-id='" + row.id + "'>" + methods.getSvg() + "</div>"
			}
		}
	}).on("loaded.rs.jquery.bootgrid", function()
	{
		$('.delete').click(function(){
			var id = $(this).data("id");
			$(this).parent().parent().attr("id",id);
			new Delete({title:'删除提示',content:'是否确认删除?<br /><span class="red">一旦删除将不可恢复</span>'}).alert(id,'/Process/Index/delete');
		});
		$(".magnifyImg").click(function(){
			var body = $(document.getElementsByTagName('body'));
			var cover = $(document.getElementById('cover'));
			var content = cover.children('#cover-content');
			var src = $(this).attr("src");
			$(".content").attr("src",src);
			cover.removeClass('hidden');
			cover.one('mouseover',function(e) {
				$(".enlarge").one('click',function (e) {
					cover.addClass('hidden');
					body.css('overflow','auto');
				});
			});
			content.on('mouseover',function (e) {
					cover.unbind();
				})
				.on('mouseout',function (e) {
					$(".enlarge").one('click',function (e) {
						cover.addClass('hidden');
						body.css('overflow','auto');
					});
				});
			body.css('overflow','hidden');
			centerVertical();
		});
		function centerVertical() {
			var content = document.getElementById('cover-content');
			var heightVersion = document.documentElement.clientHeight;
			var heightLimit = heightVersion * 0.9;
			var heightBlock = parseInt(content.offsetHeight);
			var marginHeight = heightVersion - heightBlock;
			var halfMargin;
			if(heightBlock>heightLimit) {
				halfMargin = '5%';
			} else {
				halfMargin = Math.floor(marginHeight / 2) + 'px';
			}
			content.style.marginTop = halfMargin;
			content.style.marginBottom = halfMargin;
		}
	});
	$("#searchResult").click(function(){
		var startDate = $("#startDate").val();
		var endDate = $("#endDate").val();
		var start = parseInt(new Date(startDate).getTime() / 1000);
		var end = parseInt(new Date(endDate).getTime() / 1000);
		if(start > end){
			new Inform({title:'通知',content:'起始时间不能大于终止时间！'}).alert();
			return ;
		}
		$("#switchProcess").css('display','table');
		$("#Process-footer").css('display','none');
		$("#Process").css('display','none');
		$("#switchProcess").bootgrid({
			ajax: true,
			ajaxSettings: {
				dataType: "json"
			},
			post: {
				page: "all",
				startdate:startDate,
				enddate:endDate
			},
			url: "#按时间查询",
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
				product_image: function (column, row) {
					return "<img class='magnifyImg' src='" + row.productimage + "' />";
				},
				productName_Spec: function (column, row) {
					return "<span>"+row.productname+"</span>"+"-"+"<span>"+row.guige+"</span>";
				},
				operator_image: function (column, row) {
					return "<img class='magnifyImg' src='" + row.operatorimage + "' />";
				},
				content_image: function (column, row) {
					var str = "";
					if (!(row.image_path instanceof Array))
					{
						return "";
					}
					for(var k = 0; k < row.image_path.length; k++) {
						str += "<img class='magnifyImg' src='" + row.image_path[k] + "' />";
					}
					return str;
				},
				operation: function (column, row)
				{
					return "<a href='/Application/Process/View/Edit/editProcess.html?id=" + row.id + "'><p class='operation'>修改</p></a>";
				},
				delete: function (column, row)
				{
					return "<div class=\"delete\" data-id='" + row.id + "'>" + methods.getSvg() + "</div>"
				}
			}
		}).on("loaded.rs.jquery.bootgrid", function()
		{
			$('.delete').click(function(){
				var id = $(this).data("id");
				$(this).parent().parent().attr("id",id);
				new Delete({title:'删除提示',content:'是否确认删除?<br /><span class="red">一旦删除将不可恢复</span>'}).alert(id,'/Process/Index/delete');
			});
			$(".magnifyImg").click(function(){
				var body = $(document.getElementsByTagName('body'));
				var cover = $(document.getElementById('cover'));
				var content = cover.children('#cover-content');
				var src = $(this).attr("src");
				$(".content").attr("src",src);
				cover.removeClass('hidden');
				cover.one('mouseover',function(e) {
					$(".enlarge").one('click',function (e) {
						cover.addClass('hidden');
						body.css('overflow','auto');
					});
				});
				content.on('mouseover',function (e) {
						cover.unbind();
					})
					.on('mouseout',function (e) {
						$(".enlarge").one('click',function (e) {
							cover.addClass('hidden');
							body.css('overflow','auto');
						});
					});
				body.css('overflow','hidden');
				centerVertical();
			});
			function centerVertical() {
				var content = document.getElementById('cover-content');
				var heightVersion = document.documentElement.clientHeight;
				var heightLimit = heightVersion * 0.9;
				var heightBlock = parseInt(content.offsetHeight);
				var marginHeight = heightVersion - heightBlock;
				var halfMargin;
				if(heightBlock>heightLimit) {
					halfMargin = '5%';
				} else {
					halfMargin = Math.floor(marginHeight / 2) + 'px';
				}
				content.style.marginTop = halfMargin;
				content.style.marginBottom = halfMargin;
			}
		});
	});
});

