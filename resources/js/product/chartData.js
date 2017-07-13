/**
 * Created by apple on 16/4/2.
 */
$(document).ready(function () {
    //data
    var data = {
        labels : ["7日","8日","9日","10日","11日","12日","13日","14日","15日","16日","17日"],
        datasets : [
            {
                fillColor : "rgba(254,111,77,0)",
                strokeColor : "#FE6F4D",
                pointColor : "#FE6F4D",
                pointStrokeColor : "#fff",
                data : [5,12,10,8,3,4,1,7,3,9,10]
            },
            {
                fillColor : "rgba(254,178,89,0)",
                strokeColor : "#FEB259",
                pointColor : "#FEB259",
                pointStrokeColor : "#fff",
                data : [2,4,6,12,15,7,10,2,13,7,6]
            }
        ]
    };

    //chart
    var ctx = $("#chart").get(0).getContext("2d");
    var myNewChart = new Chart(ctx);
    new Chart(ctx).Line(data);
});