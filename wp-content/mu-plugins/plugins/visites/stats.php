<?php

add_action('admin_menu', function () {
    add_menu_page(
        'Stats de visites', // Titre de la page
        'Stats de visites', // Titre du menu
        'manage_options', // Capability
        'stats-visites', // Slug de la page
        'render_stats_visites_page' // Fonction de rendu
    );
});

function render_stats_visites_page()
{
    $annee = $_GET['annee'] ?? false;
?>
    <div class="stats-visites">
        <div>
            <h2><?php echo get_admin_page_title(); ?></h2>
            <form action="admin.php">
                <input type="hidden" name="page" value="stats-visites">
                <select name="annee" oninput="this.closest('form').submit()">
                    <?php for ($y = date('Y'); $y > 2013; $y--) { ?>
                        <option <?= $y == $annee ? 'selected' : ''; ?>><?= $y; ?></option>
                    <?php } ?>
                </select>
            </form>
            <canvas id="stats-visites-chart"></canvas>
        </div>
    </div>
    <style>
        .stats-visites {
            >div {
                max-width: 800px;
                margin: 0 auto;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('stats-visites-chart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                        label: 'Nombre de visites',
                        type: 'bar',
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        yAxisID: 'y',
                        data: statsData.visits
                    }, {
                        label: 'Visites avec commande',
                        type: 'bar',
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        yAxisID: 'y',
                        data: statsData.orders
                    }, {
                        label: 'Taux de transformation (%)',
                        type: 'line',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        backgroundColor: 'rgba(255, 206, 86, 0.5)',
                        fill: false,
                        yAxisID: 'y1',
                        data: statsData.transformation
                    }]
                },
                options: {
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        annotation: {
                            annotations: {
                                line1: {
                                    type: 'line',
                                    yMin: statsData.averageTransformation,
                                    yMax: statsData.averageTransformation,
                                    borderColor: '#333',
                                    borderWidth: 2,
                                    yScaleID: 'y1', // Assurez-vous que la ligne est ancrée sur l'axe y1
                                    label: {
                                        content: 'Taux moyen annuel',
                                        enabled: true,
                                        position: 'end',
                                        backgroundColor: 'rgba(99, 99, 99, 0.5)'
                                    }
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>


<?php
}


add_action('admin_enqueue_scripts', function ($hook) {
    if ('toplevel_page_stats-visites' !== $hook) {
        return;
    }

    // Enqueue Chart.js
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js');
    wp_enqueue_script('chartjs-plugin-annotation', 'https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.0.2/dist/chartjs-plugin-annotation.min.js');
    $stat_year = $_GET['annee'] ?? date('Y');
    $visits_data = []; // Nombre de visites par mois
    $orders_data = []; // Visites avec commande par mois

    // Initialisation des tableaux de données
    for ($i = 1; $i <= 12; $i++) {
        $visits_data[$i] = 0;
        $orders_data[$i] = 0;
    }

    $args = [
        'meta_query' => [
            [
                'key'     => 'visite',
                'value'   => $stat_year,
                'compare' => 'LIKE',
            ],
        ],
    ];
    $users = get_users($args);

    foreach ($users as $user) {
        $visite_date = get_user_meta($user->ID, 'visite', true);
        $visite_month = date('n', strtotime($visite_date));
        $visits_data[$visite_month]++;

        // Vérification de la première commande
        $orders = wc_get_orders(['customer_id' => $user->ID]);
        if (!empty($orders)) {
            $first_order_date = $orders[0]->get_date_created()->date('Y-m-d');
            $first_order_year = date('Y', strtotime($first_order_date));
            if ($first_order_year === $stat_year) {
                $orders_data[$visite_month]++;
            }
        }
    }

    $transformation_rate = []; // Taux de transformation par mois

    for ($i = 1; $i <= 12; $i++) {
        if ($visits_data[$i] > 0) {
            $transformation_rate[$i] = ($orders_data[$i] / $visits_data[$i]) * 100;
        } else {
            $transformation_rate[$i] = 0;
        }
    }
    $average_transformation_rate = array_sum($transformation_rate) / count(array_filter($transformation_rate, function ($value) {
        return $value > 0;
    }));
    wp_localize_script('chartjs', 'statsData', [
        'visits' => array_values($visits_data),
        'orders' => array_values($orders_data),
        'transformation' => array_values($transformation_rate),
        'averageTransformation' => $average_transformation_rate
    ]);
});
