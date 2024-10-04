<?php

add_action('wp_head', function () {
    if (!get_current_user_id()) return;
?>
    <script>
        const coworkingDevices = <?= json_encode(getDevices()); ?>;
        const coworkingNbOrders = <?= json_encode(get_current_user_order_count()); ?>;
    </script>
<?php
});
