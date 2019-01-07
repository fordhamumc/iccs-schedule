<?php
/**
 * General utility functions for use throughout the project
 * @since      1.0.0
 *
 * @package    Iccs_Schedule
 * @subpackage Iccs_Schedule/utils
 * @author     Michael Foley <mifoley@fordham.edu>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Iccs__Schedule__Utils' ) ) {
    class Iccs__Schedule__Utils {
        /**
         * Sanitize a datetime string
         * @since   1.0.0
         *
         * @param   string  $input  The datetime string
         * @param   string  $format The format for the string
         * @return  string  Sanitized date
         */
        public static function sanitize_datetime($input, $format = 'Y-m-d H:i:s') {
            if ($input === '') return '';
            try {
                $date = new DateTime($input);
            } catch (Exception $e) {
                return '';
            }
            return $date->format($format);
        }

        /**
         * Sanitize a date string
         * @since   1.0.0
         *
         * @param   string  $input  The date string
         * @return  string  Sanitized date
         */
        public static function sanitize_date($input) {
            return self::sanitize_datetime($input, 'Y-m-d');
        }

        /**
         * Sanitize a time string
         * @since   1.0.0
         *
         * @param   string  $input  The date string
         * @return  string  Sanitized date
         */
        public static function sanitize_time($input) {
            return self::sanitize_datetime($input, 'H:i');
        }
    }
}