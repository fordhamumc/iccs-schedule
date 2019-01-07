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

<select id="iccsSettingsYear" name="<?php echo $name; ?>">
    <?php foreach($options as $option) {
        printf('<option value="%1$s" %2$s>%1$s</option>', $option, selected($value, $option, false));
    }
    ?>
</select>