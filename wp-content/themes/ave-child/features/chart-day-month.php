<?php
/**
 * chart_presences_day();
 * Create a chart with number of presences by day 
 */
function chart_presences_day() {
    $date_from = date('Y-m-d', strtotime("-28 days"));
    $date_to = date('Y-m-d', strtotime("-1 day"));
    
    $data_day = file_get_contents('https://stats.coworking-metz.fr/stats/day?from=' . $date_from . '&to=' . $date_to);
    $json_day = json_decode($data_day, true);

    foreach ($json_day as $key => $value) {
      $date = $value['date'];
      $date_transform = DateTime::createFromFormat('Y-m-d', $date)->format('D d/m');
      $date_fr = strftime($date_transform);

      $search = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
      $replace = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
      $date_in_french = str_replace($search, $replace, $date_fr);

      $day = date('D', $date_timestamp);

      $coworkers = $value['data']['coworkersCount'];
    
    $array_day [] = $date_in_french;
    $array_count[] = $coworkers;
    
    }
    
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.js"></script>';
    echo '<script src="https://www.coworking-metz.fr/charts/chartjs-plugin-datalabels.js"></script>';
    
    echo '<canvas id="myChartDay"></canvas>';
    
    
    ?>
    
<script>
    var ctx = document.getElementById('myChartDay');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($array_day); ?>,
            datasets: [{
                label: '',
                data: <?php echo json_encode($array_count); ?>,
                backgroundColor: 'rgba(224, 171, 78, 0.5)',
                borderColor: 'rgb(224, 171, 78)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    gridLines: {
                        color: 'rgba(255,255,255,0.25)'
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            aspectRatio: 5,
            legend: false,
            animation: {
                onComplete: function() {
                    var chartInstance = this.chart,
                    ctx = chartInstance.ctx;

                    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';
                    ctx.fillStyle = '#eab234';

                    this.data.datasets.forEach(function(dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function(bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                    });
                }
            }
        }
    });
    Chart.defaults.global.defaultFontColor = "#fff";
</script>

<?php
}

/**
 * chart_presences_month();
 * Create a chart with number of presences by month 
 */

function chart_presences_month() {
    $date_from = date('Y-m-d', strtotime("-735 days"));
    $date_to = date('Y-m-d', strtotime("-1 day"));
    
    $data_month = file_get_contents('https://stats.coworking-metz.fr/stats/month?from=' . $date_from . '&includesCurrent=1');
    $json_month = json_decode($data_month , true);


    foreach ($json_month as $key => $value) {
      $date = $value['date'];
      $date_transform = DateTime::createFromFormat('Y-m-d', $date)->format('M Y');
      $date_fr = strftime($date_transform);

      $search = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
      $replace = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
      $date_in_french = str_replace($search, $replace, $date_fr);

      $day = date('D', $date_timestamp);
      $coworkers = $value['data']['coworkedDaysCount'];
    
      $array_day [] = $date_in_french;
      $array_count[] = $coworkers;
    }

    echo '<canvas id="myChartMonth"></canvas>';

    ?>
    
    <script>
        var ctx = document.getElementById('myChartMonth');
        var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($array_day); ?>,
        datasets: [{
            label: '',
            data: <?php echo json_encode($array_count); ?>,
            backgroundColor: 'rgba(224, 171, 78, 0.5)',
            borderColor: 'rgb(224, 171, 78)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                gridLines: {
                    color: 'rgba(255,255,255,0.25)'
                },
                ticks: {
                    beginAtZero: true
                }
            }]
        },
        aspectRatio: 5,
        legend: false,
        animation: {
            onComplete: function() {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                ctx.fillStyle = '#eab234';

                this.data.datasets.forEach(function(dataset, i) {
                var meta = chartInstance.controller.getDatasetMeta(i);
                meta.data.forEach(function(bar, index) {
                    var data = dataset.data[index];
                    ctx.fillText(data, bar._model.x, bar._model.y - 5);
                });
                });
            }
        }
    }
});
Chart.defaults.global.defaultFontColor = "#fff";
</script>

<?php
}