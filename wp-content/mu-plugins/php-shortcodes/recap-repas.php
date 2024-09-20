<?php

if (is_user_logged_in()) {

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $member = tickets('/members/' . $user_id);
    $mealVouchers = tickets('/members/' . $user_id . '/meal-vouchers');
    $mealsActivity = tickets('/members/' . $user_id . '/meals-activity');
    $orders = get_orders_by_custom_order_numbers(array_column($mealVouchers, 'orderReference'), true);
?>
    <h2>Mes coupons repas</h2>
    <div class="tickets-status">
        <p>Vous disposez actuellement de <em><?= $member['meals']; ?></em> coupons repas utilisables dans la cantina, auprès de l'Amour Food</em></p>
    </div>
    <h3>Coupons repas utilisés</h3>
    <?= generateCalendar($_GET['cy'] ?? false, $_GET['cm'] ?? false, array_column($mealsActivity, 'mealDate')); ?>

    <h3>Historique d'achats de coupons</h3>
    <table class="table table-left">
        <caption></caption>
        <tbody>
            <tr>
                <th>Date d'achat</th>
                <th>Nombre de repas</th>
                <!-- <th>Prix unitaire</th> -->
                <th>Commande</th>
            </tr>
            <?php foreach ($mealVouchers as $voucher) {
                $order = $orders[$voucher['orderReference']] ?? false;
                $purchaseDate = date_i18n('d M Y', strtotime($voucher['purchaseDate']));
            ?>
                <tr>
                    <td><span><?= $purchaseDate; ?></span></td>
                    <td>
                        <center><?= $voucher['meals']; ?></center>
                    </td>
                    <!-- <td>
                        <center><?= $voucher['meal_price']; ?>€</center>
                    </td> -->
                    <?php if ($order) { ?>
                        <td class="order-reference"><a href="<?= $order->get_view_order_url(); ?>"><?= $voucher['orderReference']; ?></a></td>
                    <?php } else { ?>
                        <td class="order-reference"><?= $voucher['orderReference']; ?></td>';
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>

<?php }

/**
 * Generates a calendar table for a given month and year, defaulting to the current month if not specified.
 * Weeks start on Monday.
 *
 * @param int|null $year The year for which the calendar is generated, defaults to current year.
 * @param int|null $month The month for which the calendar is generated, defaults to current month.
 */
function generateCalendar($year = null, $month = null, $highlights=[])
{

    // Use current year and month if not provided
    $year = $year ? $year : date('Y');
    $month = $month ? $month : date('m');

    // First and last day of the month
    $firstDayOfMonth = strtotime("$year-$month-01");
    $lastDayOfMonth = strtotime("$year-$month-" . date('t', $firstDayOfMonth));

    // Day of the week for the first day of the month
    $startDayOfWeek = date('N', $firstDayOfMonth);

    // Adjust the start day to Monday
    $startPoint = $startDayOfWeek - 1;
    if ($startPoint < 0) {
        $startPoint = 6; // Adjust for Sunday
    }
    $monthName = ucfirst(date_i18n('F', strtotime("{$year}-{$month}-01")));
    $precMonth = strtotime("{$year}-" . ($month - 1) . "-01");
    $precMonthName = ucfirst(date_i18n('F', $precMonth));
    $nextMonth = strtotime("{$year}-" . ($month + 1) . "-01");
    $nextMonthName = ucfirst(date_i18n('F', $nextMonth));
    $uri = $_SERVER['REQUEST_URI'];

    // Prepare to create the table
    echo "<table class='calendrier' border='1'>";
    echo '<td colspan="7">';
    echo '<div class="navigation">
        <a href="' . add_query_arg(['cy' => date('Y', $precMonth), 'cm' => date('m', $precMonth)], $uri) . '">&laquo; ' . $precMonthName . '</a>
        <span>' . $monthName . ' ' . $year . '</span>
        <a href="' . add_query_arg(['cy' => date('Y', $nextMonth), 'cm' => date('m', $nextMonth)], $uri) . '">' . $nextMonthName . ' &raquo;</a>
        </div>';
    echo "</td>";
    echo "<tr><th>Lundi</th><th>Mardi</th><th>Mercredi</th><th>Jeudi</th><th>Vendredi</th><th>Samedi</th><th>Dimanche</th></tr>";

    // Fill the first week of the month with leading empty cells if necessary
    echo "<tr>";
    for ($i = 0; $i < $startPoint; $i++) {
        echo "<td></td>";
    }
    $today = date('Y-m-d'); // Today's date for comparison
    // Fill the table with day numbers
    $currentDay = $firstDayOfMonth;
    while ($currentDay <= $lastDayOfMonth) {
        $dayString = date('Y-m-d', $currentDay);
        $isToday = ($dayString == $today);
        $isHighlight = in_array($dayString, $highlights);
        if (date('N', $currentDay) == 1 && $currentDay != $firstDayOfMonth) {
            echo "</tr><tr>"; // Start a new row every Monday except the first entry
        }
        echo "<td class='" . ($isToday ? ' today' : '') . "" . ($isHighlight ? ' highlight' : '') . "'>" . date('j', $currentDay) . "</td>"; // Output the day number
        $currentDay = strtotime("+1 day", $currentDay);
    }

    // Fill the last week of the month with trailing empty cells if necessary
    if (date('N', $currentDay) != 1) {
        for ($i = date('N', $currentDay); $i <= 7; $i++) {
            echo "<td></td>";
        }
    }

    echo "</tr>";
    echo "</table>";
}
