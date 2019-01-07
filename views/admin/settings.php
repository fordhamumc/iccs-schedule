<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://michaeldfoley.com
 * @since      1.0.0
 *
 * @package    Iccs_Schedule
 * @subpackage Iccs_Schedule/admin/partials
 */
?>

<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <form method="post" action="options.php">
    <?php
        settings_fields( $this->post_type . '-settings-section' );
        do_settings_sections( $this->post_type . '-settings' );
        submit_button();
    ?>
    </form>
</div>
