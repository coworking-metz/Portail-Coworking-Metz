<?php

if (class_exists('WP_CLI')) {

    /**
     * Handles the 'polaroid' WP-CLI commands.
     */
    class Polaroid_Commands extends WP_CLI_Command
    {

        /**
         * Checks and echoes the URL of the 'votre_photo' ACF field for each user.
         *
         * @subcommand check
         */
        public function check()
        {
            $args = [
                'meta_query' => [
                    [
                        'key' => 'votre_photo',
                        'compare' => 'EXISTS'
                    ]
                ]
            ];
            $users = get_users($args);

            foreach ($users as $user) {
                $photo_id = get_field('votre_photo', 'user_' . $user->ID);

                if (!$photo_id) continue;
                $photo_url = wp_get_attachment_url($photo_id);
                if (!$photo_url) continue;

                WP_CLI::line("User ID {$user->ID}: $photo_url");

                if (get_field('polaroid_checked', $photo_id)) continue;
                WP_CLI::line("\tChecking image, please wait...");

                if (isImagePhoto($photo_url)) {
                    update_field('polaroid_checked', true, $photo_id);
                    delete_field('polaroid_bad', $photo_id);
                    continue;
                }
                update_field('polaroid_bad', true, $photo_id);
                WP_CLI::line("\t\tThis image does not seem to be a real photo");
            }
        }
    }

    WP_CLI::add_command('polaroid', 'Polaroid_Commands');
}
