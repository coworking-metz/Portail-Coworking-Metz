<?php

if (!defined('ABSPATH')) exit;

?>
<article class="daftplugAdminPage -error" data-page="error" data-title="<?php esc_html_e('Error', $this->textDomain); ?>">
    <div class="daftplugAdminPage_heading -flex12">
        <h2 class="daftplugAdminPage_title"><?php esc_html_e('Oops, something went wrong', $this->textDomain); ?></h2>
        <h5 class="daftplugAdminPage_subheading"><?php esc_html_e('The requested page was not found.', $this->textDomain); ?></h5>
        <a class="daftplugAdminButton" href="#/overview/" data-page="overview"><?php esc_html_e('Back to Home', $this->textDomain); ?></a>
    </div>
</article>