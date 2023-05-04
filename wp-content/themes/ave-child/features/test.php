<?php

function tunasse() {




    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://www.coworking-metz.fr/api-json-wp/wc/v3/reports/top_sellers?consumer_key=ck_023a5f3f2557e4cfd5294f3abb648abfabe9785b&consumer_secret=cs_907eaba6d4342240167c25ce97739ac9e362466c&status=completed&date_min=2022-07-01&date_max=2022-07-31',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Cookie: PHPSESSID=31c9a71f1f2458c00cf0c6e92293170b; digits_countrycode=33'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$json_response = json_decode($response, true);

$quantity_abo = 0;
$quantity_carnet = 0;

foreach ($json_response as $key => $value) {
    if($value['product_id'] == '3023') {
        $quantity_abo = $value['quantity'];
    } else if ($value['product_id'] == '3022') {
        $quantity_carnet = $value['quantity'];
    }

}

$total_money = ($quantity_abo + $quantity_carnet) * 60;
echo $total_money . '<br><br>';

echo print_r($json_response);

}


function test_bruno(){

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


    // Get Netatmo datas
    $data_netatmo = file_get_contents('https://stats.coworking-metz.fr/netatmo/stations');
    $json_netatmo = json_decode($data_netatmo, true);

    $dashboard = $json_netatmo[0]['dashboard_data'];
    $module_ext = $json_netatmo[0]['modules'][0]['dashboard_data'];

    $co2 = $dashboard['CO2'];
    $temp_inside = $dashboard['Temperature'];
    $temp_outside = $module_ext['Temperature'];
    $max_temp_inside = $dashboard['max_temp'];
    $max_temp_outside = $module_ext['max_temp'];

    ?>
    <style>
        h3 {text-align: center; font-weight: 300;}
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.3.2/dist/echarts.min.js"></script>
    
        <div id="text-count-coworker"><h3>Nombre de journées coworkées par mois</h3></div>
        <div id="presencesCoworkersMonth" style="width: 100%; height:500px"></div>
        <div id="text-count-coworker"><h3>Money Money Money</h3></div>
        <div id="money" style="width: 100%; height:500px"></div>



<script>

        var chartDomMoney = document.getElementById('money');
var myChartMoney = echarts.init(chartDomMoney);
var optionMoney;

optionMoney = {
  title: {
    text: ''
  },
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'cross',
      label: {
        backgroundColor: '#283b56'
      }
    }
  },
  legend: {},
  toolbox: {
    show: true,
    feature: {
      dataView: { readOnly: false },
      restore: {},
      saveAsImage: {}
    }
  },
  dataZoom: {
    show: false,
    start: 0,
    end: 100
  },
  xAxis: [
    {
      type: 'category',
      boundaryGap: true,
      data: <?php echo json_encode($date_money); ?>
    },
    {
      type: 'category',
      boundaryGap: true,
      data: <?php echo json_encode($date_money); ?>
    }
  ],
  yAxis: [
    {
      type: 'value',
      scale: true,
      name: 'Jours',
      max: 1200,
      min: 0,
      boundaryGap: [0.2, 0.2]
    },
    {
      type: 'value',
      scale: true,
      name: 'Money',
      max: 4000,
      min: 0,
      boundaryGap: [0.2, 0.2]
    }
  ],
  series: [
    {
      name: 'Money',
      type: 'bar',
      xAxisIndex: 1,
      yAxisIndex: 1,
      data: <?php echo json_encode($incomes); ?>
    },
    {
      name: 'Ticket',
      type: 'line',
      data: <?php echo json_encode($used_tickets_money); ?>,
      smooth: true
    },
    {
      name: 'Abo',
      type: 'line',
      data: <?php echo json_encode($days_abo_money); ?>,
      smooth: true
    },
    {
      name: 'Total abo + tickets',
      type: 'line',
      data: <?php echo json_encode($total_abo_tickets); ?>,
      smooth: true
    },
    {
      name: 'Seuil mini',
      type: 'line',
      xAxisIndex: 1,
      yAxisIndex: 1,
      data: <?php echo json_encode($cost_month); ?>,
      smooth: true
    }
  ]
};

optionMoney && myChartMoney.setOption(optionMoney);
</script>


    <!-- Chart Temperature inside-->
    <script type="text/javascript">

    var chartDomTempInside = document.getElementById('tempInt');
    var myChartTempInside = echarts.init(chartDomTempInside);
    var optionTempInside;

    optionTempInside = {
    series: [
        {
        type: 'gauge',
        center: ['50%', '60%'],
        startAngle: 200,
        endAngle: -20,
        min: 0,
        max: 60,
        splitNumber: 12,
        itemStyle: {
            color: '#eab234'
        },
        progress: {
            show: true,
            width: 30
        },
        pointer: {
            show: false
        },
        axisLine: {
            lineStyle: {
            width: 30
            }
        },
        axisTick: {
            distance: -45,
            splitNumber: 5,
            lineStyle: {
            width: 2,
            color: '#999'
            }
        },
        splitLine: {
            distance: -52,
            length: 14,
            lineStyle: {
            width: 3,
            color: '#999'
            }
        },
        axisLabel: {
            distance: -20,
            color: '#999',
            fontSize: 20
        },
        anchor: {
            show: false
        },
        title: {
            show: false
        },
        detail: {
            valueAnimation: true,
            width: '60%',
            lineHeight: 40,
            borderRadius: 8,
            offsetCenter: [0, '-15%'],
            fontSize: 60,
            fontWeight: 'bolder',
            formatter: '{value}°C',
            color: 'auto'
        },
        data: [
            {
            value: <?php echo $temp_inside; ?>
            }
        ]
        },
        {
        type: 'gauge',
        center: ['50%', '60%'],
        startAngle: 200,
        endAngle: -20,
        min: 0,
        max: 60,
        itemStyle: {
            color: '#FD7347'
        },
        progress: {
            show: true,
            width: 8
        },
        pointer: {
            show: false
        },
        axisLine: {
            show: false
        },
        axisTick: {
            show: false
        },
        splitLine: {
            show: true
        },
        axisLabel: {
            show: false
        },
        detail: {
            show: false
        },
        data: [
            {
            value: <?php echo $max_temp_inside; ?>
            }
        ]
        }
    ]
    };

    optionTempInside && myChartTempInside.setOption(optionTempInside);
    window.addEventListener('resize',function(){
            myChartTempInside.resize();
        });
</script>

<!-- Chart Temperature outside-->
<script>

    var chartDomTempOutside = document.getElementById('tempOut');
    var myChartTempOutside = echarts.init(chartDomTempOutside);
    var optionTempOutside;

    optionTempOutside = {
    series: [
        {
        type: 'gauge',
        center: ['50%', '60%'],
        startAngle: 200,
        endAngle: -20,
        min: -20,
        max: 60,
        splitNumber: 10,
        itemStyle: {
            color: '#EAB234'
        },
        progress: {
            show: true,
            width: 30
        },
        pointer: {
            show: false
        },
        axisLine: {
            lineStyle: {
            width: 30
            }
        },
        axisTick: {
            distance: -45,
            splitNumber: 5,
            lineStyle: {
            width: 2,
            color: '#999'
            }
        },
        splitLine: {
            distance: -52,
            length: 14,
            lineStyle: {
            width: 3,
            color: '#999'
            }
        },
        axisLabel: {
            distance: -20,
            color: '#999',
            fontSize: 20
        },
        anchor: {
            show: false
        },
        title: {
            show: false
        },
        detail: {
            valueAnimation: true,
            width: '60%',
            lineHeight: 40,
            borderRadius: 8,
            offsetCenter: [0, '-15%'],
            fontSize: 60,
            fontWeight: 'bolder',
            formatter: '{value} °C',
            color: 'auto'
        },
        data: [
            {
            value: <?php echo $temp_outside; ?>
            }
        ]
        },
        {
        type: 'gauge',
        center: ['50%', '60%'],
        startAngle: 200,
        endAngle: -20,
        min: -20,
        max: 60,
        itemStyle: {
            color: '#FD7347'
        },
        progress: {
            show: true,
            width: 8
        },
        pointer: {
            show: false
        },
        axisLine: {
            show: false
        },
        axisTick: {
            show: false
        },
        splitLine: {
            show: false
        },
        axisLabel: {
            show: false
        },
        detail: {
            show: false
        },
        data: [
            {
            value: <?php echo $max_temp_outside; ?>
            }
        ]
        }
    ]
    };

    optionTempOutside && myChartTempOutside.setOption(optionTempOutside);
</script>

<!-- Chart CO2-->
<script>

    var chartDomCo2 = document.getElementById('ChartCo2');
    var myChartCo2 = echarts.init(chartDomCo2);
    var optionCo2;

    optionCo2 = {
    series: [
        {
        type: 'gauge',
        startAngle: 180,
        endAngle: 0,
        min: 350,
        max: 2000,
        splitNumber: 8,
        axisLine: {
            lineStyle: {
            width: 6,
            color: [
                [0.25, '#6AAA4D'],
                [0.8, '#EAB234'],
                [1, '#D33F21']
            ]
            }
        },
        pointer: {
            icon: 'path://M12.8,0.7l12,40.1H0.7L12.8,0.7z',
            length: '12%',
            width: 20,
            offsetCenter: [0, '-60%'],
            itemStyle: {
            color: 'auto'
            }
        },
        axisTick: {
            length: 12,
            lineStyle: {
            color: 'auto',
            width: 2
            }
        },
        splitLine: {
            length: 20,
            lineStyle: {
            color: 'auto',
            width: 5
            }
        },
        axisLabel: {
            color: '#464646',
            fontSize: 20,
            distance: -60,
            formatter: function (value) {
            if (value === 0.875) {
                return 'A';
            } else if (value === 0.625) {
                return 'B';
            } else if (value === 0.375) {
                return 'C';
            } else if (value === 0.125) {
                return 'D';
            }
            return '';
            }
        },
        title: {
            offsetCenter: [0, '-20%'],
            fontSize: 20
        },
        detail: {
            fontSize: 30,
            offsetCenter: [0, '0%'],
            valueAnimation: true,
            formatter: function (value) {
            return Math.round(value) + ' ppm';
            },
            color: 'auto'
        },
        data: [
            {
            value: <?php echo $co2; ?>,
            name: 'CO2'
            }
        ]
        }
    ]
    };

    optionCo2 && myChartCo2.setOption(optionCo2);

</script>

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

<?php
}
