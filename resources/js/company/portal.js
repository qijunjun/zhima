/**
 * Created by Tianye on 2016/8/23.
 */


var geoCoordMap = {};
var geoIdMap = {};
var companyData = [];

function whiteSpace(value,num) {
    return value == null ? "" : value.length > num ? value.substr(0, num) + "..." : value;
}

function getLocalTime(nS) {
    return new Date(parseInt(nS) * 1000).toLocaleDateString();
}

function showAllContent() {
    var body = $(document.getElementsByTagName('body'));
    var cover = $(document.getElementById('cover'));
    var content = cover.children('#cover-content');
    var pcontent = $(".pcontent");
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
}


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


window.onload = function () {
    var companyInfo = {};
    var inside = $('#imgs-inside');
    var leftArrow = $('#img-leftArrow');
    var rightArrow = $('#img-rightArrow');
    var realWidth = 0;
    var imgs = '';
    var delta;

    var code = echarts.init(document.getElementById('code-use'));
    var ioTotal = echarts.init(document.getElementById('io-total'));
    var ioMap = echarts.init(document.getElementById('io-map'));
    var ioData = {};

    //var companyId;
    var imgSrc = {};

    var companyid = /(\d+)\/{0,1}$/.exec(location.href)[1];

    var snipper;
    var tempArr = [];
    var max;

    $.ajax({
        url: '/government/company/info',
        method: 'get',
        dataType: 'json',
        data: {
            companyId: companyid
        },
        success: function (data)
        {
            console.log(data);
            if(data.code=='001'&&data.result!=null) {
                var imgs = '';
                var inside = $('#imgs-inside');

                data = data.result;

                if(data.introimage != null && data.introimage != '') {
                    imgs += '<img src="'+data.introimage + '" />';
                }
                if(data.introimage1 != null && data.introimage1 != '') {
                    imgs += '<img src="'+data.introimage1+'" />';
                }
                if(data.introimage2 != null && data.introimage2 != '') {
                    imgs += '<img src="'+data.introimage2+'" />';
                }
                if(data.introimage3 != null && data.introimage3 != '') {
                    imgs += '<img src="'+data.introimage3+'" />';
                }
                if(data.introimage4 != null && data.introimage4 != '') {
                    imgs += '<img src="'+data.introimage4+'" />';
                }
                if(data.logo != "" && data.logo != null && data.logo != undefined) {
                    $('.logo').children('img').attr('src', data.logo).attr('alt',data.name).removeClass('hidden');
                }
                else {
                    $('.logo').html('<p>暂无LOGO</p>');
                }
                $('.name').children('div').text(data.name);
                $('.address').children('span')[1].innerHTML = data.address;
                $('.corporation').children('span')[1].innerHTML = data.legalperson;
                $('.contact').children('span')[1].innerHTML = data.contact;
                $('.phone').children('span')[1].innerHTML = data.phone;
                if(data.introduction != "" && data.introduction != null && data.introduction != undefined) {
                    $('.introduction').children('div')[1].innerHTML = '<p>' + data.introduction + '</p>';
                    $(".pcontent").html(data.introduction);
                }
                else {
                    $('.introduction').children('div')[1].innerHTML = '<p>目前该企业没有填写介绍哟！</p>';
                }
                if(imgs == "") imgs = "<p>该企业没有上传展示图片！</p>"
                else {
                    snipper = setInterval(function(){
                        setupImgs(inside);
                    },200)
                }
                inside.html(imgs);
                $('.address>span:nth-child(2)').dotdotdot({
                    ellipsis: '...',
                    height:72
                });
                $('.introduction>div>p').dotdotdot({
                    ellipsis: '...',
                    after: $('<a href="javascript:void(0)" onclick="showAllContent()">查看全部<a>'),
                    wrap: 'letter',
                    height:180,
                    watch: true,
                    fallbackToLetter: true,
                    lastCharacter: {

                        /*	Remove these characters from the end of the truncated text. */
                        remove: [ ' ', ',', ';', '.', '!', '?' ],

                        /*	Don't add an ellipsis if this array contains
                         the last character of the truncated text. */
                        noEllipsis: []
                    }
                });
            }
        },
        error: function (err) {
            console.log(err);
        }
    });

    function setupImgs(imgBox) {
        var imgs = $(imgBox).children('img');
        realWidth = 0;
        for(var i = 0; i<imgs.length;i++) {
            realWidth += imgs[i].width;
        }
        if(realWidth>inside.width()) {
            delta = Math.floor(realWidth / imgs.length + 11);
            leftArrow.on('click',function (e) {
                var inside = $('#imgs-inside');
                var left = parseInt(inside.css('marginLeft'));
                if(!inside.is(':animated')&&left<0)
                    inside.animate({marginLeft:left + delta});
            });
            rightArrow.on('click',function (e) {
                var inside = $('#imgs-inside');
                var left = parseInt(inside.css('marginLeft'));
                if(!inside.is(':animated')&&left>((-delta)*(imgs.length-1)))
                    inside.animate({marginLeft:left - delta});
            });
            leftArrow.css('display','block');
            rightArrow.css('display','block');
        }
        if(realWidth>0){
            clearInterval(snipper);
        }
        console.log(realWidth);
    }


    var placeHolderStyle = {
        tooltip: { show: false },
        normal : {
            color: 'rgba(0,0,0,0)',
            label: {show:false},
            labelLine: {show:false}
        },
        emphasis : {
            color: 'rgba(0,0,0,0)'
        }
    };

    var codeOption = {
        title: {
            text: '企业用码统计'
        },
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b}: {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',
            x: 'left',
            top: '40px',
            data:['购码量','生码量','已关联码量']
        },
        series: [
            {
                name:'',
                type:'pie',
                center: ['50%','55%'],
                radius: [ '55%', '70%'],
                avoidLabelOverlap: false,
                label: {
                    normal: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        show: true,
                        textStyle: {
                            fontSize: '18',
                            fontWeight: 'bold'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:[
                    {value:750, name:'购码量'},
                    {value:250, name:'填充数据',itemStyle: placeHolderStyle}
                ]
            },
            {
                name:'',
                type:'pie',
                center: ['50%','55%'],
                radius: [ '40%', '55%'
                ],
                avoidLabelOverlap: false,
                label: {
                    normal: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        show: true,
                        textStyle: {
                            fontSize: '18',
                            fontWeight: 'bold'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:[
                    {value:440, name:'生码量'},
                    {value:370, name:'填充数据',itemStyle: placeHolderStyle}
                ]
            },
            {
                name:'',
                type:'pie',
                center: ['50%','55%'],
                radius: ['25%', '40%'],
                avoidLabelOverlap: false,
                label: {
                    normal: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        show: true,
                        textStyle: {
                            fontSize: '18',
                            fontWeight: 'bold'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:[
                    {value:335, name:'已关联码量'},
                    {value:370, name:'填充数据',itemStyle: placeHolderStyle}
                ]
            }
        ]
    };

    var ioOption = {
        title: { text: '企业出入库量统计' },
        tooltip : {
            trigger: 'axis'
        },
        legend: {
            data:['出库量','入库量'],
            top: '30px'
        },
        toolbox: {
            show : true,
            top: '30px',
            feature : {
                mark : {show: true},
                dataView : {
                    show: true,
                    title: '出入库统计数据',
                    readOnly: true,
                    optionToContent: function(){
                    },
                    contentToOption: function () {
                    },
                    lang: ['出入库统计数据','关闭','刷新']
                },
                magicType : {show: true, type: ['line', 'bar']},
                restore : {show: true},
                saveAsImage : {show: true}
            }
        },
        grid: {
            top: '90px'
        },
        calculable : true,
        xAxis : [
            {
                type : 'category',
                boundaryGap : false,
                data : []
            }
        ],
        yAxis : [
            {
                type : 'value',
                axisLabel : {
                    formatter: '{value}'
                }
            }
        ],
        series : [
            {
                name:'出库量',
                type:'line',
                data:[],
                markPoint : {
                    data : [
                        {type : 'max', name: '最大值'},
                        {type : 'min', name: '最小值'}
                    ]
                },
                markLine : {
                    data : [
                        {type : 'average', name: '平均值'}
                    ]
                }
            },
            {
                name:'入库量',
                type:'line',
                data:[],
                markPoint : {
                    data : [
                        {type : 'max', name: '最大值'},
                        {type : 'min', name: '最小值'}
                    ]
                },
                markLine : {
                    data : [
                        {type : 'average', name : '平均值'}
                    ]
                }
            }
        ]
    };


    var planePath = 'path://M1705.06,1318.313v-89.254l-319.9-221.799l0.073-208.063c0.521-84.662-26.629-121.796-63.961-121.491c-37.332-0.305-64.482,36.829-63.961,121.491l0.073,208.063l-319.9,221.799v89.254l330.343-157.288l12.238,241.308l-134.449,92.931l0.531,42.034l175.125-42.917l175.125,42.917l0.531-42.034l-134.449-92.931l12.238-241.308L1705.06,1318.313z';

    var convertData = function (data) {
        var res = [];
        for (var i = 0; i < data.length; i++) {
            var dataItem = data[i];
            var fromCoord = geoCoordMap[dataItem[0].name];
            var toCoord = geoCoordMap[dataItem[1].name];
            if (fromCoord && toCoord) {
                res.push({
                    fromName: dataItem[0].name,
                    toName: dataItem[1].name,
                    coords: [fromCoord, toCoord],
                    value: dataItem[1].value
                });
            }
        }
        return res;
    };

    var color = ['#a6c84c', '#ffa022', '#46bee9'];
    var series = [];
    var mapOption = {
        title : {
            text: '出库去向统计',
            left:'33px'
        },
        tooltip : {
            trigger: 'item'
        },
        legend: {
            show: true,
            orient: 'vertical',
            y: '10%',
            left:'33px',
            data:[companyInfo.name],
            selectedMode: 'single'
        },
        visualMap: {
            min : 0,
            max : 100,
            left: '33px',
            calculable : true,
            color: ['#ff3333', 'orange', 'yellow','lime','aqua']
        },
        geo: {
            map: 'china',
            label: {
                emphasis: {
                    show: false
                }
            },
            roam: true,
            itemStyle: {
                normal: {
                    areaColor: 'rgba(47,69,84,1)',
                    borderColor:'rgba(100,149,237,1)'
                },
                emphasis: {
                    areaColor: '#2a333d'
                }
            }
        },
        series: {}
    };
    ioMap.showLoading();

    //ECHARTS地图迁徙获取数据
    $.ajax({
        url: '/government/company/info',
        method: 'get',
        dataType: 'json',
        data: {
            companyId: companyid
        },
        success: function (data)
        {
            if(data.code=='001') {
                companyInfo = data.result;
                $.ajax({
                    url: '/Statistic/Government/checkoutArea',
                    method: 'get',
                    dataType: 'json',
                    data: {
                        id: companyid
                    },
                    success: function(data){
                        var i = 0;
                        if(data.code=='001'&&data.result!=null) {
                            data = data.result;
                            geoCoordMap[companyInfo.name] = [companyInfo.longitude, companyInfo.latitude];
                            for (i = 0; i < data.length; i++) {
                                geoCoordMap[data[i].agent_name] = [];
                                geoCoordMap[data[i].agent_name] = [data[i].center_longitude, data[i].center_latitude];
                                geoIdMap[data[i].agent_name] = data[i].agent_id;
                                companyData[i] = [{name: companyInfo.name}, {
                                    name: data[i].agent_name,
                                    value: data[i].count,
                                    id: data[i].agent_id
                                }];
                                tempArr[i] = data[i].count;
                            }
                            mapOption.visualMap.max = parseInt(Math.max.apply(null,tempArr)*2);
                        }
                    },
                    complete: function(data) {
                        //[[companyInfo.name, companyData]].forEach(mapSeriesSet);
                        mapSeriesSet([companyInfo.name, companyData],0);
                        mapOption.series = series;
                        ioMap.hideLoading();
                        ioMap.setOption(mapOption);
                    }
                });
            }
        },
        error: function (err) {
            console.log(err);
        }
    });


    function mapSeriesSet(item, i) {
        series.push({
                name: item[0],
                type: 'lines',
                zlevel: 1,
                effect: {
                    show: true,
                    period: 6,
                    trailLength: 0.7,
                    color: '#fff',
                    symbolSize: 3
                },
                lineStyle: {
                    normal: {
                        color: color[i],
                        width: 0,
                        curveness: 0.2
                    }
                },
                data: convertData(item[1])
            },
            {
                name: item[0],
                type: 'lines',
                zlevel: 2,
                effect: {
                    show: true,
                    period: 6,
                    trailLength: 0,
                    symbol: planePath,
                    symbolSize: 15
                },
                label: {
                    normal: {
                        show: true,
                        position: 'right',
                        formatter: '{b}'
                    }
                },
                tooltip: {
                    formatter: function (v) {
                        return v.data.fromName + '>' + v.data.toName + '  共 ' + v.data.value;
                    }
                },
                lineStyle: {
                    normal: {
                        color: color[i],
                        width: 2,
                        opacity: 0.4,
                        curveness: 0.2
                    }
                },
                data: convertData(item[1])
            },
            {
                name: '',
                type: 'effectScatter',
                coordinateSystem: 'geo',
                zlevel: 2,
                rippleEffect: {
                    brushType: 'stroke'
                },
                label: {
                    normal: {
                        show: true,
                        position: 'right',
                        formatter: '{b}'
                    }
                },
                tooltip: {

                },
                symbolSize: function (val) {
                    console.log(val);
                    return val[2];
                },
                itemStyle: {
                    normal: {
                        color: color[i]
                    }
                },
                data: item[1].map(function (dataItem) {
                    return {
                        name: dataItem[1].name,
                        value: geoCoordMap[dataItem[1].name].concat([dataItem[1].value])
                    };
                })
            },
            {
                name: '出库公司',
                type: 'effectScatter',
                coordinateSystem: 'geo',
                zlevel: 2,
                rippleEffect: {
                    brushType: 'stroke'
                },
                label: {
                    normal: {
                        show: true,
                        position: 'right',
                        formatter: '{b}'
                    }
                },
                tooltip: {

                },
                symbolSize: 6,
                itemStyle: {
                    normal: {
                        color: color[i]
                    }
                },
                data: [
                    {
                        name:companyInfo.name,
                        value:geoCoordMap[companyInfo.name]
                    }
                ]
            }
        );
    }




    code.showLoading();
    $.ajax({
        url: '/Statistic/Government/companyInfo',
        method: 'get',
        dataType: 'json',
        data: {
            id: companyid
        },
        success: function(data){
            if(data.code === '001'&&data.result!=null) {
                var name = ['购码量','生码量','已关联码量'];
                var result = [data.result.qrcode_bought_count,data.result.qrcode_used_counts,data.result.qrcode_joined_counts];
                // var fillNum = result[0] * 0.34 * 4;
                var fillNum = result[0];
                for(var i=0; i<3;i++) {
                    codeOption.series[i].data = [{value:parseInt(result[i]), name:name[i]},
                    {value:fillNum - result[i]>0?fillNum-result[i]:0, name:'',itemStyle: placeHolderStyle}];
                }
                code.setOption(codeOption);
            }
        },
        error: function(err) {
            console.log(err);
        },
        complete:function(){
            code.setOption(codeOption);
            code.hideLoading();
        }
    });

    var inDate = [];
    var outDate = [];
    var ioDate = [];
    var inData = [];
    var outData = [];

    ioTotal.showLoading();
    $.ajax({
        url: '/Statistic/Government/check',
        method: 'get',
        dataType: 'json',
        data: {
            id: companyid
        },
        success: function(data){
            if(data.code === '001'&&data.result!=null) {
                data = data.result;
                for(var i=0;i<data.length;i++){
                    ioDate[i]=data[i].time;
                    inData[i]=data[i].checkin;
                    outData[i]=data[i].checkout;
                }
                ioOption.xAxis[0].data = ioDate;
                ioOption.series[0].data = outData;
                ioOption.series[1].data = inData;
                ioOption.toolbox.feature.dataView.optionToContent = function (opt) {
                    var axisData = opt.xAxis[0].data;
                    var series = opt.series;
                    var table = '<section class="table re-table"><table class="bottom" style="width:100%;text-align:center"><tbody><tr>'
                        + '<th>时间</th>'
                        + '<th>' + series[0].name + '</th>'
                        + '<th>' + series[1].name + '</th>'
                        + '</tr>';
                    for (var i = 0, l = axisData.length; i < l; i++) {
                        table += '<tr>'
                            + '<td>' + axisData[i] + '</td>'
                            + '<td>' + series[0].data[i] + '</td>'
                            + '<td>' + series[1].data[i] + '</td>'
                            + '</tr>';
                    }
                    table += '</tbody></table></section>';
                    return table;
                };
                ioTotal.setOption(ioOption);
            }
        },
        error: function(err) {
            console.log(err);
        },
        complete: function(){
            ioTotal.setOption(ioOption);
            ioTotal.hideLoading();
        }
    });

    function delRepeat(arr1,arr2){
        arr1.sort();
        arr2.sort();
        var arr = [];
        var length1 = arr1.length;
        var length2 = arr2.length;
        for(var i=0,j=0,k=0;i<length1&&j<length2;) {
            if(arr1[i]<arr2[j]){
                arr[k++] = arr1[i];
                i++;
            } else if(arr1[i]==arr2[j]) {
                arr[k++] = arr2[j];
                i++;
                j++;
            } else {
                arr[k++] = arr2[j];
                i++;
            }
            if(i==length1) {
                for(var m=j;m<length2;m++) {
                    arr[k++] = arr2[m];
                }
            } else if(j==length2) {
                for(var n=i;n<length1;n++) {
                    arr[k++] = arr2[n];
                }
            }
        }
        return arr;
    }


    $("#aptitude").bootgrid({
        ajax:true,
        ajaxSettings:{
            method: 'get',
            dataType:"json"
        },
        url:"/Company/Aptitude/showAllByCompany/companyid/"+companyid,
        identifier:"aptitudeid",
        responseHandler:function(response){
            console.log(response);
            var rows;
            rows = {
                total:response.result.length,
                current:1,
                rows:response.result
            };
            response.result = rows;
            return response;
        },
        formatters:{
            certificate:function(column,row){
                return "<img class='magnifyImg' src='"+row.aptitudeimage1+"'>";
            },
            operation:function(column,row){
                return "<a href='/Application/Company/View/Aptitude/editAptitude.html?id="+row.aptitudeid+"'><p class='operation'>修改</p></a>"
            },
            delete:function(column,row){
                return "<div class='delete' data-id='"+row.aptitudeid+"'>"+methods.getSvg()+"</div>"
            }
        }
    }).on("loaded.rs.jquery.bootgrid", function()
    {
        $(".magnifyImg").click(function(){
            var body = $(document.getElementsByTagName('body'));
            var cover = $(document.getElementById('cover'));
            var content = cover.children('#cover-content');
            var BigImg = $("<img class='content'>");
            var src = $(this).attr("src");
            BigImg.attr("src",src);
            content.append(BigImg);
            $(".pcontent").remove();
            cover.removeClass('hidden');
            cover.one('mouseover',function(e) {
                $(".enlarge").one('click',function (e) {
                    BigImg.remove();
                    cover.addClass('hidden');
                    body.css('overflow','auto');
                });
            });
            content.on('mouseover',function (e) {
                    cover.unbind();
                })
                .on('mouseout',function (e) {
                    $(".enlarge").one('click',function (e) {
                        BigImg.remove();
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

};




