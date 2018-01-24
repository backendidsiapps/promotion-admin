google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawMaterial);

function drawMaterial() 
{
    var data = google.visualization.arrayToDataTable(
        window.dataArr
    );

    var materialOptions = {
        bars: 'vertical',
        height: 550,
        colors: ['#e67e22', '#f1c40f', '#e74c3c', "#f1f2f9"]
    };
    var materialChart = new google.charts.Bar(document.getElementById('chart_div'));
    materialChart.draw(data, materialOptions);
}