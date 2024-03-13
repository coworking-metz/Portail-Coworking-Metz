<?php

/**
 * api_coworker_presences();
 * Get the user list of presences from the ticket service
 */

function api_coworker_presences(){
    if (is_user_logged_in()) {

        $current_user = wp_get_current_user();
        $coworker_email = $current_user->user_login;
        
        $url = TICKET_BASE_URL.'/user-presences?key=' . API_KEY_TICKET . '&email=' . $coworker_email;
        $data = file_get_contents($url);
        $json = json_decode($data, true);

        $html = '<h5 style="text-align: center">Décompte</h5>';
        $html .= '<div class="my-account-presences-list"><table class="table table-left">';
        $html .= '<caption></caption>';
        $html .= '<tr><th>Date</th><th>Durée</th><th>Couvert par</th></tr>';
        
        foreach ($json as $key => $value) {
            $presence_date = strtotime($value['date']);
			$presence_day = date_i18n('l', $presence_date);
			$presence_month = date_i18n('F', $presence_date);
			$presence_year = date_i18n('Y', $presence_date);
			$array_day [] = $presence_day;
			$array_month [] = $presence_month;
			$array_year [] = $presence_year;
            $result_type = ($value['type'] == 'T') ? '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/ticket-le-poulailler.png"> ticket' : '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/abonnement-type.png"> abonnement';
            $html .= '<tr>';
            $html .= '<td class="presence-date"><span>' . date_i18n('l d F Y', $presence_date) . '</span></td>';
            $html .= '<td class="presence-amount"><span>' . $value['amount'] . ' journée</span></td>';
            $html .= '<td class="result-type">' . $result_type . '</td>';
            
            $html .= '</tr>';
        }
        echo '<script src="https://cdn.jsdelivr.net/npm/chart.js@3.6.0/dist/chart.min.js"></script>';
        // values by days
        $json_value = '<script> const datasPresencesMonday = ';
        $json_value .= json_encode(array_count_values($array_day)['lundi']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesTuesday = ';
        $json_value .= json_encode(array_count_values($array_day)['mardi']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesWednesday = ';
        $json_value .= json_encode(array_count_values($array_day)['mercredi']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesThursday = ';
        $json_value .= json_encode(array_count_values($array_day)['jeudi']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesFriday = ';
        $json_value .= json_encode(array_count_values($array_day)['vendredi']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesSaturday = ';
        $json_value .= json_encode(array_count_values($array_day)['samedi']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesSunday = ';
        $json_value .= json_encode(array_count_values($array_day)['dimanche']);
        $json_value .= '</script>';
        //values by months
        $json_value .= '<script> const datasPresencesJanuary = ';
        $json_value .= json_encode(array_count_values($array_month)['janvier']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesFebruary = ';
        $json_value .= json_encode(array_count_values($array_month)['février']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesMarch = ';
        $json_value .= json_encode(array_count_values($array_month)['mars']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesApril = ';
        $json_value .= json_encode(array_count_values($array_month)['avril']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesMay = ';
        $json_value .= json_encode(array_count_values($array_month)['mai']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesJune = ';
        $json_value .= json_encode(array_count_values($array_month)['juin']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesJuly = ';
        $json_value .= json_encode(array_count_values($array_month)['juillet']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesAugust = ';
        $json_value .= json_encode(array_count_values($array_month)['août']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesSeptember = ';
        $json_value .= json_encode(array_count_values($array_month)['septembre']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesOctober = ';
        $json_value .= json_encode(array_count_values($array_month)['octobre']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesNovember = ';
        $json_value .= json_encode(array_count_values($array_month)['novembre']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesDecember = ';
        $json_value .= json_encode(array_count_values($array_month)['décembre']);
        $json_value .= '</script>';
        //values by years
        $json_value .= '<script> const datasPresences2014 = ';
        $json_value .= json_encode(array_count_values($array_year)['2014']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2015 = ';
        $json_value .= json_encode(array_count_values($array_year)['2015']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2016 = ';
        $json_value .= json_encode(array_count_values($array_year)['2016']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2017 = ';
        $json_value .= json_encode(array_count_values($array_year)['2017']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2018 = ';
        $json_value .= json_encode(array_count_values($array_year)['2018']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2019 = ';
        $json_value .= json_encode(array_count_values($array_year)['2019']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2020 = ';
        $json_value .= json_encode(array_count_values($array_year)['2020']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2021 = ';
        $json_value .= json_encode(array_count_values($array_year)['2021']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2022 = ';
        $json_value .= json_encode(array_count_values($array_year)['2022']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2023 = ';
        $json_value .= json_encode(array_count_values($array_year)['2023']);
        $json_value .= '</script>';
  
    echo $json_value;
    ?>

    <canvas id="dayChart" width="400" height="400"></canvas>

<script>
    const ctxDay = document.getElementById('dayChart').getContext('2d');
    const dayChart = new Chart(ctxDay, {
        type: 'bar',
        data: {
            labels: ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'],
            datasets: [{
                label: 'Nb de présences ',
                data: [datasPresencesMonday, datasPresencesTuesday, datasPresencesWednesday, datasPresencesThursday, datasPresencesFriday, datasPresencesSaturday, datasPresencesSunday],
                backgroundColor: 'rgba(224, 171, 78, 0.5)',
                borderColor: 'rgba(224, 171, 78, 1)',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Nombre de présences cumulées (jours)'
                },
                legend: {
                    display: false,
                    labels: {
                        color: 'rgb(255, 99, 132)'
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            aspectRatio: 2
        }
    });
</script>

    <canvas id="monthChart" width="400" height="400"></canvas>

<script>
    const ctxMonth = document.getElementById('monthChart').getContext('2d');
    const monthChart = new Chart(ctxMonth, {
        type: 'bar',
        data: {
            labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            datasets: [{
                label: 'Nb de présences ',
                data: [datasPresencesJanuary, datasPresencesFebruary, datasPresencesMarch, datasPresencesApril, datasPresencesMay, datasPresencesJune, datasPresencesJuly, datasPresencesAugust, datasPresencesSeptember,
                 datasPresencesOctober, datasPresencesNovember, datasPresencesDecember],
                backgroundColor: 'rgba(224, 171, 78, 0.5)',
                borderColor: 'rgba(224, 171, 78, 1)',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Nombre de présences cumulées (mois)',
                    padding: {
                        top: 30,
                        bottom: 10
                    },
                },
                legend: {
                    display: false,
                    labels: {
                        color: 'rgb(255, 99, 132)'
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            aspectRatio: 2
        }
    });
</script>

    <canvas id="yearChart" width="400" height="400"></canvas>

<script>
    const ctxYears = document.getElementById('yearChart').getContext('2d');
    const yearChart = new Chart(ctxYears, {
        type: 'bar',
        data: {
            labels: ['2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023'],
            datasets: [{
                label: 'Nb de présences ',
                data: [datasPresences2014, datasPresences2015, datasPresences2016, datasPresences2017, datasPresences2018, datasPresences2019, datasPresences2020, datasPresences2021, datasPresences2022, datasPresences2023],
                backgroundColor: 'rgba(224, 171, 78, 0.5)',
                borderColor: 'rgba(224, 171, 78, 1)',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Nombre de présences cumulées (année)',
                    padding: {
                        top: 30,
                        bottom: 10
                    },
                },
                legend: {
                    display: false,
                    labels: {
                        color: 'rgb(255, 99, 132)'
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            aspectRatio: 2
        }
    });
</script>

<?php        
        $html .= '</table></div>';
        
        echo $html;
    } else {
        echo 'Vous n\'êtes pas connecté';
    }
}