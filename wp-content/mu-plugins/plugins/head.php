<?php
add_action('wp_head', function () {
    ?>
    <script>
        const WP_API_URL = <?php echo json_encode(WP_API_URL);?>;
    </script>
    <?php
});
