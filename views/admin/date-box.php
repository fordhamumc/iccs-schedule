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
        <label for="iccsDate"><?php _e('Date', 'iccs-schedule') ?></label>
        <input class="iccs-box__input" id="iccsDate" type="date" name="iccs_date" value="<?php echo $values['iccs_date']; ?>">
    </p>
    <p class="meta-options iccs-box__field">
        <label for="iccsStartTime"><?php _e('Start Time', 'iccs-schedule') ?></label>
        <input class="iccs-box__input" id="iccsStartTime" type="time" name="iccs_start_time" value="<?php echo $values['iccs_start_time']; ?>">
    </p>
    <p class="meta-options iccs-box__field">
        <label for="iccsEndTime"><?php _e('End Time', 'iccs-schedule') ?></label>
        <input class="iccs-box__input" id="iccsEndTime" type="time" name="iccs_end_time" value="<?php echo $values['iccs_end_time']; ?>">
    </p>
</div>
