Chart.defaults.global.defaultFontFamily = 'Nunito, -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
Chart.defaults.global.defaultFontColor = '#5a5c69';

function renderBarChart(canvasId, labels, values, seriesName, horizontal, formatValue) {
    var canvas = document.getElementById(canvasId);
    var format = formatValue || function (value) {
        return value;
    };

    if (!canvas) {
        return null;
    }

    var valueAxis = {
        ticks: { beginAtZero: true, maxTicksLimit: 6, padding: 8, callback: format },
        gridLines: { color: 'rgba(133, 135, 150, 0.15)', zeroLineColor: 'rgba(133, 135, 150, 0.4)', drawBorder: false, borderDash: [2] }
    };
    var categoryAxis = {
        ticks: { padding: 8 },
        gridLines: { display: false, drawBorder: false },
        maxBarThickness: 28
    };

    return new Chart(canvas, {
        type: horizontal ? 'horizontalBar' : 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: seriesName,
                data: values,
                backgroundColor: '#8a5a3b',
                hoverBackgroundColor: '#6b4329',
                borderColor: '#ffffff',
                borderWidth: 2,
                maxBarThickness: 28
            }]
        },
        options: {
            maintainAspectRatio: false,
            legend: { display: false },
            layout: { padding: { left: 4, right: 12, top: 8, bottom: 0 } },
            scales: {
                xAxes: [horizontal ? valueAxis : categoryAxis],
                yAxes: [horizontal ? categoryAxis : valueAxis]
            },
            tooltips: {
                backgroundColor: '#ffffff',
                bodyFontColor: '#5a5c69',
                titleFontColor: '#3a3b45',
                titleMarginBottom: 6,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 12,
                yPadding: 10,
                displayColors: false,
                callbacks: {
                    label: function (item) {
                        return seriesName + ': ' + format(horizontal ? item.xLabel : item.yLabel);
                    }
                }
            }
        }
    });
}
