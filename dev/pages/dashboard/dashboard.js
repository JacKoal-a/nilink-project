google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);
$(window).resize(function(){
    drawChart();
  });
function drawChart() {
var data = google.visualization.arrayToDataTable([
    ['Month', 'Users'],
    ['Gen',  1000],
    ['Feb',  1170],
    ['Mar',  660],
    ['Apr',  1030],
    ['May',  1030],
    ['Jun',  2000],
    ['Jul',  800],
    ['Aug',  1030],
    ['Sep',  3000],
    ['Oct',  1000],
    ['Nov',  10000],
    ['Dic',  5000],
    ['',null]


]);

var options = {
    curveType: 'function',
    legend: {position: 'none'},
    vAxis: {
        viewWindowMode: "explicit", 
        viewWindow:{ min: 0 },
        
    },

};

var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
document.getElementById('curve_chart').classList.remove("button");
chart.draw(data, options);
}