/**
 * Created by Tianye on 2016/8/29.
 */
window.onload = function () {
    //これわ変数の声明
    var productAll = echarts.init(document.getElementById('product-total-all'));
    var codeUse = echarts.init(document.getElementById('code-use-all'));
    var ioTotal = echarts.init(document.getElementById('io-total-all'));

    //
    var productAttr = ['companyname','product_count'];
    var codeAttr = ['companyname','qrcode_bought_count','qrcode_used_counts','qrcode_joined_counts'];
    var ioAttr = ['companyname','qrcode_checkin_counts','qrcode_checkin_counts'];

    //ここわxAxisの声明
    var productXAxis = [];
    var codeXAxis = [];
    var ioXAxis = [];

    //ここわデタの声明
    var productData = [];
    var codeData = [];
    var ioData = [];

    //
    var allCompanies = [];
    var color = ['#c23531','#2f4554', '#61a0a8', '#d48265', '#91c7ae','#749f83',  '#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3'];
    var gridFix = ['8%','10%','15%'];
    var tooltipSet = {
        trigger: 'axis',
        axisPointer : {
            type : 'shadow'
        },
        formatter:function (data) {
            if (data != null && data != undefined) {
                if (data[0] != null && data[0] != undefined) {
                    arr = [];
                    arr[0] = allCompanies[data[0].name];
                    for (var i = 0; i < data.length; i++) {
                        arr[i + 1] = [['<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background-color:', data[i].color, '"></span>'].join(''), data[i].seriesName, data[i].value].join('：');
                    }
                    return arr.join('<br/>');
                } else {
                    return [['<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background-color:', data.color, '"></span>', data.seriesName].join(''), [data.name, '：', data.value].join('')].join('<br />');
                }

            }
        }
    };
    var extraLine = {
        data: [
            {type: 'average', name: '平均值'},
            [{
                symbol: 'none',
                x: '90%',
                yAxis: 'max'
            }, {
                symbol: 'circle',
                label: {
                    normal: {
                        position: 'start',
                        formatter: '最大值'
                    }
                },
                type: 'max',
                name: '最大值'
            }]
        ]
    };

    //ここわオプションの声明
    var productOption = {
        title: {
            text: '各企业产品数量统计对比'
        },
        tooltip : tooltipSet,
        legend: {
            data:['产品数量'],
            left: '50%',
            top: '5px'
        },
        grid: {
            left: gridFix[0],
            right: gridFix[1],
            bottom: gridFix[2],
            containLabel: true
        },
        xAxis : [
            {
                type : 'category',
                axisLabel:{
                    interval: 0,
                    rotate: 30
                },
                data : []
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
            {
                name:'产品数量',
                type:'bar',
                data:[],
                itemStyle:{
                    normal:{
                        color: color[2]
                    }
                },
                markLine: extraLine
            }
        ]
    };
    var codeOption = {
        title: {
            text: '各企业用码量统计对比'
        },
        tooltip : tooltipSet,
        legend: {
            data:['购码量','生码量','已关联码量'],
            left: '50%',
            top: '5px'
        },
        grid: {
            left: gridFix[0],
            right: gridFix[1],
            bottom: gridFix[2],
            containLabel: true
        },
        xAxis : [
            {
                type : 'category',
                position: {
                    top:'-20px'
                },
                axisLabel:{
                    interval: 0,
                    rotate: 30
                },
                data : []
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
            {
                name:'购码量',
                type:'bar',
                data:[],
                itemStyle:{
                    normal:{
                        color: color[3]
                    }
                },
                markLine: extraLine
            },
            {
                name:'生码量',
                type:'bar',
                data:[],
                itemStyle:{
                    normal:{
                        color: color[4]
                    }
                },
                markLine: extraLine
            },
            {
                name:'已关联码量',
                type:'bar',
                data:[],
                itemStyle:{
                    normal:{
                        color: color[5]
                    }
                },
                markLine: extraLine
            }
        ]
    };
    var ioOption = {
        title: {
            text: '各企业出入库统计对比'
        },
        tooltip : tooltipSet,
        legend: {
            data:['出库量','入库量'],
            left: '50%',
            top: '5px'
        },
        grid: {
            left: gridFix[0],
            right: gridFix[1],
            bottom: gridFix[2],
            containLabel: true
        },
        xAxis : [
            {
                type : 'category',
                axisLabel:{
                    interval: 0,
                    rotate: 30
                },
                data : []
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
            {
                name:'出库量',
                type:'bar',
                data:[],
                itemStyle:{
                    normal:{
                        color: color[6]
                    }
                },
                markLine: extraLine
            },
            {
                name:'入库量',
                type:'bar',
                data:[],
                itemStyle:{
                    normal:{
                        color: color[7]
                    }
                },
                markLine: extraLine
            }
        ]
    };

    $.ajax({
        url:'/Statistic/Government/productCounts',
        method:'get',
        dataType:'json',
        success: function(data) {
            console.log(data);
            if(data.code=='001'&&(!!data.result)) {
                initBar(productOption,data.result,productAttr,productData,productAttr.length-1,productXAxis);
            }
            productAll.setOption(productOption);
        },
        error: function(err){
            console.log(err);
            productAll.setOption(productOption);
        }
    });

    $.ajax({
        url:'/Statistic/Government/qrcodeCounts',
        method:'get',
        dataType:'json',
        success: function(data) {
            console.log(data);
            if(data.code=='001'&&(!!data.result)) {
                initBar(codeOption,data.result,codeAttr,codeData,codeAttr.length-1,codeXAxis);
            }
            codeUse.setOption(codeOption);
        },
        error: function (err) {
            console.log(err);
            codeUse.setOption(codeOption);

        }
    });

    $.ajax({
        url:'/Statistic/Government/qrcodeCheck',
        method:'get',
        dataType:'json',
        success: function(data) {
            console.log(data);
            if(data.code=='001'&&(!!data.result)) {
                initBar(ioOption,data.result,ioAttr,ioData,ioAttr.length-1,ioXAxis);
            }
            ioTotal.setOption(ioOption);
        },
        error: function(err){
            console.log(err);
            ioTotal.setOption(ioOption);
        }
    });

    function initBar(option,data,dataAttr,seriesData,seriesAmount,xAxis) {
        var length;
        var i,j;
        var dataPicker;
        var cutSentence;

        length = data.length;

        for(j=0;j<seriesAmount;j++) {
            seriesData[j]=[];
        }
        for(i=0;i<length;i++) {
            if(data[i]!=null) {
                cutSentence = whiteSpace(data[i][dataAttr[0]],10);
                allCompanies[cutSentence] = data[i][dataAttr[0]];
                xAxis.push(cutSentence);
                for (j = 0; j < seriesAmount; j++) {
                    dataPicker = data[i][dataAttr[j + 1]];
                    seriesData[j].push(dataPicker === null ? 0 : dataPicker);
                }
            }
        }
        option.xAxis[0].data = xAxis;
        for(i=0;i<seriesAmount;i++) {
            option.series[i].data = seriesData[i];
        }
    }

    function whiteSpace(value,num) {
        return value == null ? "" : value.length > num ? value.substr(0, num) + "..." : value;
    }
};
