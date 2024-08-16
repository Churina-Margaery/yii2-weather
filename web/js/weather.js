data = {
    labels: dateChart3,
    series: [tempChart3]
    };
isMonth = false;
    
var line_options = {
    showPoint: false,
    fullWidth: true,
    lineSmooth: false,
    chartPadding: {
        right: 15,
        left: 15,
        top: 15,
        bottom: 15
    },
    width: 400,
    height: 350,
    axisX: {
        showGrid: false,
        labelInterpolationFnc: function(value, index) {
            const date = new Date(value);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const formattedDate = `${day}.${month}`;
            if (!isMonth) {
                return index % 24 === 0 ? formattedDate : null;
            }
            return index % 120 === 0 ? formattedDate : null;
        }
    },
    axisY: {
        labelInterpolationFnc: function(value) {
            return value + '°C';
        }
    }
};

document.getElementById("days3").addEventListener("click", function() {
    event.preventDefault();
    data = {
    labels: dateChart3,
    series: [tempChart3]
    };
    isMonth = false;
    Chartist.Line('.ct-chart', data, line_options);
});

document.getElementById("days10").addEventListener("click", function() {
    event.preventDefault();
    data = {
    labels: dateChart10,
    series: [tempChart10]
    };
    isMonth = false;
    Chartist.Line('.ct-chart', data, line_options);
});

document.getElementById("daysMonth").addEventListener("click", function() {
    event.preventDefault();
    var data = {
    labels: dateChart,
    series: [tempChart]
    };
    isMonth = true;
    Chartist.Line('.ct-chart', data, line_options);
});

new Chartist.Line('.ct-chart', data, line_options);
