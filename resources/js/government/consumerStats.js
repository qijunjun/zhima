/**
 * Created by Tianye on 2016/8/30.
 */
window.onload = function () {
    //これわ変数の声明
    var scanCode = echarts.init(document.getElementById('scan-code'));
    var tipOff = echarts.init(document.getElementById('tip-off'));

    //
    var scanAttr = ['companyname','qrcode_scaned_counts'];
    var tipAttr = ['companyname','qrcode_tipoff_counts'];

    //ここわxAxisの声明
    var scanXAxis = [];
    var tipXAxis = [];

    //ここわデタの声明
    var scanData = [];
    var tipData = [];

    //
    var arr = [];
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
    var scanOption = {
        title: {
            text: '各企业扫码量统计对比'
        },
        tooltip : tooltipSet,
        legend: {
            data:['扫码量'],
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
                name:'扫码量',
                type:'bar',
                data:[],
                markLine: extraLine
            }
        ]
    };
    var tipOption = {
        title: {
            text: '各企业举报量统计对比'
        },
        tooltip : tooltipSet,
        legend: {
            data:['举报量'],
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
                name:'举报量',
                type:'bar',
                data:[],
                itemStyle:{
                    normal:{
                        color: color[1]
                    }
                },
                markLine: extraLine
            }
        ]
    };

    $.ajax({
        url:'/Statistic/Government/qrcodeScanedCounts',
        method:'get',
        dataType:'json',
        success: function(data) {
            console.log(data);
            if(data.code=='001'&&(!!data.result)) {
                initBar(scanOption, data.result, scanAttr, scanData, scanAttr.length - 1, scanXAxis);
            }
            scanCode.setOption(scanOption);
        },
        error:function(err){
            console.log(err);
            scanCode.setOption(scanOption);
        }
    });

    $.ajax({
        url:'/Statistic/Government/qrcodeTipoffCounts',
        method:'get',
        dataType:'json',
        success: function(data) {
            console.log(data);
            if(data.code=='001'&&(!!data.result)) {
                initBar(tipOption, data.result, tipAttr, tipData, tipAttr.length - 1, tipXAxis);
            }
            tipOff.setOption(tipOption);
        },
        error:function(err){
            console.log(err);
            tipOff.setOption(tipOption);
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
