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

<div class="iccs-box">
    <style scoped>
        .iccs-box__field {
            display: grid;
            grid-template-columns: 5rem 1fr;
            grid-gap: 1rem;
        }
        .iccs-box__input {
            grid-area: 1 / 2
        }
    </style>
    <p class="meta-options iccs-box__field">
        <label for="iccsSpeakers"><?php _e('Name', 'iccs-schedule') ?></label>
        <textarea class="iccs-box__input" id="iccsSpeakers" name="iccs_speakers" rows="5"><?php echo $speakers; ?></textarea>
    </p>
</div>

