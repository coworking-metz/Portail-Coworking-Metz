<?php
add_action('wp_head', function () {
    ?>
    <script>
        const WP_API_URL = <?=json_encode(WP_API_URL);?>;
    </script>
    <?php
});
