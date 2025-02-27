<?php
/**
 * Plugin Name: Medi Booking Tool
 * Description: Fügt einen Block zur Buchung von verschiedenen Buchungen für Räume hinzu.
 * Version: 1.0
 * Author: André Wester
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * create a new instance of the class MediBookingClass() and DatabaseControllerClass()
 */
function medi_autoload ( $class_name ) : void
{
    $prefix = 'Medi';
    $base_dir = plugin_dir_path(__FILE__) . 'src/';

    if (strpos($class_name, $prefix) !== 0) {
        return;
    }

    $relative_class = substr($class_name, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('medi_autoload');

/**
 *
 */
global $wpdb;

$medi = new \Medi\Medi\MediBookingInit($wpdb);

/**
 * create / delete table in the database when the plugin is activated / deactivated
 */
register_activation_hook(__FILE__,[$medi, 'medi_booking_install']);
register_deactivation_hook(__FILE__, [$medi, 'medi_booking_uninstall']);
