<?php

// Chart presence by gender
function chart_gender() {

    $hour_now = date('H');
    $hour_now += 2;
    $start_hour = 12;
    $end_hour = 14;

    if ($hour_now >= $start_hour && $hour_now < $end_hour) {
        $url = 'https://tickets.coworking-metz.fr/api/current-users?key=' . API_KEY_TICKET . '&delay=120';
    } else {
        $url = 'https://tickets.coworking-metz.fr/api/current-users?key=' . API_KEY_TICKET . '&delay=15';
    }

    $data = file_get_contents($url);
    $json = json_decode( $data, true );

    foreach ($json as $key => $value){
    $sexe_coworker = get_user_meta($value['wpUserId'], $key = 'sexe2', $single = true );
        
        if($sexe_coworker == 'Homme') {
            $men++;
        } elseif ($sexe_coworker == 'Femme') {
            $women++;
        } 
    }

    $total_users = $men + $women;
    $percent_men = round((($men * 100) / $total_users), 1);
    $percent_women = round((($women * 100) / $total_users), 1);

    echo '<script src="https://cdn.jsdelivr.net/npm/echarts@5.3.2/dist/echarts.min.js"></script>';
?>
    <!-- Chart stat presence by gender -->
<script>
    var chartDomGender = document.getElementById('chartGender');
    var myChartGender = echarts.init(chartDomGender);
    var optionGender;
    var colorPalette = ['#4B67B8', '#eab234'];

    optionGender = {
    tooltip: {
        trigger: 'item'
    },
    legend: {
        top: '5%',
        left: 'center',
        textStyle:{
        color:'#ffffff'
    },
    },
    series: [
        {
        name: 'Pourcentage',
        type: 'pie',
        radius: ['40%', '70%'],
        avoidLabelOverlap: false,
        itemStyle: {
            borderRadius: 10,
            borderColor: '#fff',
            borderWidth: 2
        },
        label: {
            show: true,
            position: 'inner',
            formatter : function (params){
                                return  params.value + '%'
                            },
        },
        emphasis: {
            label: {
            show: true,
            fontSize: '40',
            fontWeight: 'bold'
            }
        },
        labelLine: {
            show: false
        },
        data: [
            { value: <?php echo $percent_men; ?>, name: 'Coworkers' },
            { value: <?php echo $percent_women; ?>, name: 'Coworkeuses', color: '#eab234' }
        ],
        color: colorPalette
        }
    ]
    };

    optionGender && myChartGender.setOption(optionGender);
    
    window.onresize = function () {
        myChartGender.resize();
    };

</script>

<script>
    var chartDomPoulet = document.getElementById('poulet');
var myChartPoulet = echarts.init(chartDomPoulet);
var optionPoulet;
    const symbols = [
    'path://M364.548,56.284c10.458,7.481 18.897,-5.32 8.437,-24.914c-6.377,-11.946 -17.569,-27.521 -25.806,-31.37c44.249,7.81 86.991,45.749 88.758,96.372l-0,0.303c68.708,12.455 84.216,46.676 88.733,130.762c-38.745,-52.25 -193.37,-65.277 -223.124,-23.461c-27.27,38.319 71.108,57.563 172.066,75.324c-57.846,39.935 25.711,115.097 70.192,253.08c15.372,31.689 29.758,30.838 31.088,19.561c1.236,-10.51 -2.437,-28.69 -9.709,-39.73c33.847,18.12 23.29,79.603 30.333,82.016c16.162,5.548 22.655,-27.76 4.715,-64.188c32.299,24.642 35.382,92.216 23.568,143.66c-15.021,65.409 -101.532,196.584 -184.108,205.004c0,0 -1.403,0.063 -2.528,0c-3.684,27.112 -7.564,55.343 -64.001,99.751c14.608,-0.264 26.812,6.025 33.38,21.546c-23.959,-4.818 -47.839,-11.092 -73.31,-8.627c12.453,-7.133 25.084,-11.773 36.47,-12.739c13.468,-37.368 -8.096,-156.504 -36.028,-156.754c-28.648,2.416 -76.399,28.458 -101.635,155.94c15.431,-0.848 28.399,5.299 35.239,21.475c-23.959,-4.818 -47.839,-11.088 -73.31,-8.619c11.747,-6.73 23.654,-11.242 34.524,-12.544c-10.669,-32.639 -34.43,-94.825 -33.158,-127.858c-39.37,-21.318 -70.324,-45.737 -94.329,-71.686c-0.54,-0.295 -1.127,-0.739 -1.76,-1.342c-47.695,-45.378 -106.546,-159.937 -98.501,-234.371c14.229,31.871 19.87,42.581 27.078,50.073l0,-0.032c-12.296,-29.45 -18.734,-77.044 -4.866,-110.13c5.947,24.759 9.707,38.707 13.706,48.631c9.983,-34.344 26.852,-57.512 42.434,-60.762c-14.351,16.078 -15.264,80.258 17.33,80.513c32.591,0.256 14.132,-93.216 23.473,-223.774c9.218,-128.884 64.425,-211.779 163.638,-232.339l-0.222,-0.554c-5.688,-18.084 -35.882,-35.766 -49.849,-39.583c27.229,-9.484 69.113,0.718 81.175,19.6c5.887,-14.016 0.554,-31.314 -31.334,-61.236c44.935,1.34 62.959,18.295 75.112,36.352c0.538,0.799 1.078,1.539 1.62,2.222c1.489,1.593 2.975,3.086 4.509,4.428Zm19.057,277.254c-0.736,5.627 -2.471,25.784 -0.86,47.109c1.611,21.326 6.568,43.822 19.216,54.128c22.758,1.005 -0.69,-68.487 -18.356,-101.237Zm-6.031,-13.684c-12.544,9.92 -23.126,23.017 -28.525,32.604c-5.399,9.588 -5.615,15.666 2.572,11.545c16.369,-8.232 28.163,-29.667 25.953,-44.149Zm8.129,3.581c2.305,11.075 10.382,22.766 18.27,30.174c7.888,7.409 15.587,10.534 17.136,4.475c3.091,-12.117 -14.765,-28.542 -35.406,-34.649Zm-59.402,-199.423c-12.926,0.687 -14.061,11.218 -14.301,16c-0.301,5.988 5.497,13.978 14.301,13c9,-1 13.628,-6.82 14,-13c0.371,-6.18 -6.364,-16.406 -14,-16Z'
        //'path://M527 77L527 78C536.036 84.4184 544.063 96.328 547.222 107C548.383 110.921 548.622 120.839 541.985 119.94C534.17 118.881 527.871 105.346 521.996 100.669C509.506 90.7251 493.685 87.0018 478 87C488.466 101.872 503.528 111.981 501 132C483.676 119.942 461.635 109.898 440 118L462.996 132.145L478 148L478 149C464.462 150.106 451.015 155.746 439 161.753C354.015 204.246 349 312.146 349 395C349 418.845 351.565 444.368 348.424 468C347.333 476.215 347.286 486.23 341.468 492.811C332.743 502.679 320.999 492.978 316.796 484C309.339 468.068 313.774 451.579 319 436C301.64 442.454 291.908 465.106 288 482L287 482L278 446L277 446C267.214 468.745 269.492 506.16 281 528C271.293 519.355 265.909 503.791 261 492L260 492C260 553.717 288.681 625.909 334.039 668.83C349.02 683.006 363.886 697.01 381 708.656C388.169 713.534 403.494 718.966 407.972 726.329C411.38 731.931 410.281 742.53 411.661 749C415.277 765.953 420.517 782.568 426 799C427.711 804.127 434.931 816.47 432.647 821.686C430.897 825.681 414.37 829.305 410 831L410 832L463 839C459.477 827.137 448.372 823.094 437 823C441.594 788.883 455.452 746.892 480.015 722.004C487.574 714.344 506.061 699.2 517.999 705.179C525.855 709.114 530.456 720.186 533.305 728C540.912 748.866 544.179 770.934 545.039 793C545.352 801.035 546.898 813.57 542.683 820.853C539.562 826.245 522.026 829.494 516 832L516 833L570 839C564.416 828.565 556.731 824.032 545 824C559.983 809.66 576.545 797.28 585.68 778C590.692 767.42 590.527 755.54 595 745C600.915 748.81 611.035 743.966 617 741.6C637.108 733.622 653.95 719.227 668.996 704C709.889 662.615 741.023 606.107 741 547C740.992 525.839 737.61 495.387 719 482C722.924 493.6 728.281 505.472 726.076 518C725.316 522.318 719.523 535.267 713.938 527.731C710.484 523.07 711.36 512.631 710.975 507C710.013 492.916 705.481 474.998 692 468C695.382 476.134 699.01 483.979 698.992 493C698.984 496.979 698.27 502.637 692.985 502.377C687.519 502.109 683.266 496.065 680.52 492C672.337 479.887 668.997 464.492 663.6 451C648.91 414.275 627.62 380.661 613 344C605.126 324.256 602.061 304.47 621 290L621 289C587.179 287.548 546.371 277.51 515 265C505.164 261.077 485.333 253.554 486.869 240C488.96 221.542 517.335 215.602 532 213.95C566.031 210.116 604.137 214.939 635 230.37C644.461 235.101 651.503 242.02 660 248C659.997 220.997 656.093 183.551 632.999 165.899C625.15 159.9 616.463 156.128 607 153.427C603.143 152.327 595.46 152.122 593.028 148.567C588.627 142.135 589.163 128.774 585.511 121C575.133 98.9086 551.445 80.2945 527 77z',
   ];
   const bodyMax = 28;
   const labelSetting = {
     show: true,
     position: 'inside',
     color: '#fff',
     offset: [0, 20],
     formatter: function (param) {
       return ((<?php echo $total_users; ?> / bodyMax) * 100).toFixed(0) + '%';
     },
     fontSize: 36,
     fontFamily: 'Poppins'
   };
   const markLineSetting = {
     symbol: 'none',
     lineStyle: {
       opacity: 0
     },
     data: [
       {
         type: 'max',
         label: {
           formatter: ''
         }
       },
       {
         type: 'min',
         label: {
           formatter: ''
         }
       }
     ]
   };
   optionPoulet = {
     tooltip: {
      show: false
     },
     xAxis: {
       data: ['a'],
       axisTick: { show: false },
       axisLine: { show: false },
       axisLabel: { show: false }
     },
     yAxis: {
       max: bodyMax,
       offset: 20,
       show: false,
       splitLine: { show: false }
     },
     grid: {
       top: 'center',
       height: 300
     },
     markLine: {
       z: -100
     },
     series: [
       {
         name: '',
         type: 'pictorialBar',
         symbolClip: true,
         symbolBoundingData: bodyMax,
         itemStyle: {
        color: '#eab234'
      },
         label: labelSetting,
         data: [
           {
             value: <?php echo $total_users; ?>,
             symbol: symbols[0]
           }
         ],
         markLine: markLineSetting,
         z: 10
       },
       {
         name: '',
         type: 'pictorialBar',
         symbolBoundingData: bodyMax,
         animationDuration: 0,
         itemStyle: {
           color: '#696969'
         },
         data: [
           {
             value: 1,
             symbol: symbols[0]
           }
         ]
       }
     ]
   };
   optionPoulet && myChartPoulet.setOption(optionPoulet);
    window.onresize = function () {
        myChartPoulet.resize();
    };
</script>

<?php
}