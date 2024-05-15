<?php
// 30 - special fabrice jallet
 
$data_money = file_get_contents('https://tickets.coworking-metz.fr/stats/incomes/month?from=2020-06-01');
    $json_money = json_decode($data_money, true);

    foreach ($json_money as $key => $value) {
        $cost_month [] = '1500';
        $date_test = $value['date'];
        $date_test2 = DateTime::createFromFormat('Y-m-d', $date_test)->format('M Y');
        $date_test3 = strftime($date_test2);

        // Fix  translations that don't work
        $search_2 = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        $replace_2 = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        $date_in_french_test = str_replace($search_2, $replace_2, $date_test3);

        $date_money [] = $date_in_french_test;
        $used_tickets_money [] = $value['data']['usedTickets'];
        $days_abo_money [] = $value['data']['daysAbo'];
        $incomes [] = $value['data']['incomes'];
        $total_money_days = ($value['data']['daysAbo']) + ($value['data']['usedTickets']);
        $total_abo_tickets [] = $total_money_days;
    }

    ?>
    <style>
        h3 {text-align: center; font-weight: 300;}
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.3.2/dist/echarts.min.js"></script>
    
        <div id="presencesCoworkersMonth" style="width: 100%; height:500px"></div>

<?php

    // Get presences datasets
    $data_presences = file_get_contents('https://stats.coworking-metz.fr/stats/month?from=2014-05-01&includesCurrent=1');
    $json_users_presences = json_decode($data_presences, true);

    foreach ($json_users_presences as $key => $value) {
        $date = $value['date'];
        $date_transform = DateTime::createFromFormat('Y-m-d', $date)->format('M Y');
        $date_fr = strftime($date_transform);

        // Fix  translations that don't work
        $search = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        $replace = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        $date_in_french = str_replace($search, $replace, $date_fr);

        $day = date('D', $date_timestamp);
        $coworkers = $value['data']['coworkedDaysCount'];
        
        $array_day [] = $date_in_french;
        $array_count[] = $coworkers;
    }
?>

<script>
    var chartDomPresencesMonth = document.getElementById('presencesCoworkersMonth');
    var myChartPresencesMonth = echarts.init(chartDomPresencesMonth);
    var optionPresencesMonth;

    optionPresencesMonth = {
        dataZoom: [
        {
        type: 'inside'
        },
        {
        type: 'slider'
        },
    ],
    tooltip: {
    trigger: 'axis',
    axisPointer: { type: 'cross' }
  },
    xAxis: {
        type: 'category',
        data: <?php echo json_encode($array_day); ?>
    },
    yAxis: {
        type: 'value'
    },
    series: [
        {
        data: <?php echo json_encode($array_count); ?>,
        type: 'bar',
        color: '#eab234'
        }
    ]
    };

    optionPresencesMonth && myChartPresencesMonth.setOption(optionPresencesMonth);
    window.addEventListener('resize',function(){
            myChartPresencesMonth.resize();
        });
    </script>