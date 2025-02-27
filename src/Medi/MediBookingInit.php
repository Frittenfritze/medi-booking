<?php

namespace Medi\Medi;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Medi\Handler\MediMigration;
use Medi\Handler\MediController;
use Medi\View\Frontend\MediFrontendBlock;

class MediBookingInit
{
    public MediMigration $migration;

    public MediController $controller_init;

    public function __construct($wpdb)
    {
        $this->migration = new MediMigration($wpdb);
        $this->controller_init = new MediController();

        add_action('init', [$this, 'medi_booking_register_block']);
        add_action('admin_enqueue_scripts', [$this, 'medi_booking_register_admin']);
        add_action('wp_enqueue_scripts', [$this, 'medi_booking_register_frontend']);
    }

    /**
     * uninstall method
     *
     * @return void
     */
    public function medi_booking_install() : void
    {
        $this->migration->run();
    }

    /**
     * uninstall method
     *
     * @return void
     */
    public function medi_booking_uninstall() : void
    {
        $this->migration->delete();
    }

    /**
     * Register the custom block for bookings in the WordPress editor.
     *
     * @return void
     */
    public function medi_booking_register_block() : void
    {
        $frontend_block = new MediFrontendBlock();

        wp_enqueue_style('medi-booking', plugins_url('styles/scss_frontend/frontend_view.css', dirname(__FILE__, 2)), array(), filemtime(plugin_dir_path(dirname(__FILE__, 2)) . 'styles/scss_frontend/frontend_view.css'));

        // Editor-Skript register
        wp_register_script( 'medi-booking', plugins_url('scripts/block.js', dirname(__FILE__, 2)), array('wp-blocks', 'wp-element', 'wp-editor'), filemtime(plugin_dir_path(dirname(__FILE__, 2)) . 'scripts/block.js'));

        wp_enqueue_script('medi-booking-zabuto-js', plugins_url('scripts/script_framework/jquery.zabuto_calendar.js', dirname(__FILE__, 2)), array('jquery'), filemtime(plugin_dir_path(dirname(__FILE__, 2)) . 'scripts/script_framework/jquery.zabuto_calendar.js'));
        wp_enqueue_script('medi-booking-frontend-function', plugin_dir_url(dirname(__FILE__, 2)) . 'scripts/script_main/_functions.js', array('jquery'), filemtime(plugin_dir_path(dirname(__FILE__, 2)) . 'scripts/script_main/_functions.js'),true);
        wp_enqueue_script('medi-booking-frontend', plugin_dir_url(dirname(__FILE__, 2)) . 'scripts/frontend.js', array('jquery'), filemtime(plugin_dir_path(dirname(__FILE__, 2)) . 'scripts/frontend.js'),true);

        // Editor-Style register
        wp_register_style('medi-booking-style', plugins_url('styles/style.css', dirname(__FILE__, 2)), array(), filemtime(plugin_dir_path(dirname(__FILE__, 2)) . 'styles/style.css'));

        // Block register
        register_block_type('custom/booking-form', array(
            'editor_script' => 'medi-booking',
            'editor_style' => 'medi-booking-style',
            'style' => 'medi-booking-style',
            'render_callback' => [$frontend_block, 'medi_booking_block_render'],
            'attributes'      => [
                // Falls du Attribute benötigst, hier definieren
            ],
        ));

        wp_localize_script('medi-booking-frontend', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('medi_booking_nonce') // Optional: Nonce für Sicherheit
        ]);
    }

    /**
     * Register the scss and js for admin view in the WordPress editor.
     *
     * @return void
     */
    public function medi_booking_register_admin() : void
    {
        wp_enqueue_style( 'medi-booking-admin-css', plugins_url('styles/scss_admin/admin_styles.css', dirname(__FILE__, 2)));

        wp_enqueue_script('medi-booking-admin-function', plugins_url('scripts/script_main/_functions.js', dirname(__FILE__, 2)), array('jquery'), filemtime(plugin_dir_path(dirname(__FILE__, 2)) . 'scripts/script_main/_functions.js'), true);
        wp_enqueue_script('medi-booking-admin', plugins_url('scripts/script_admin/admin_scripts.js', dirname(__FILE__, 2)), array('jquery'), filemtime(plugin_dir_path(dirname(__FILE__, 2)) . 'scripts/script_admin/admin_scripts.js'), true);

        // Übergib die AJAX-URL und Nonce an JS
        wp_localize_script('medi-booking-admin', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('medi_booking_nonce'),
        ]);
    }

    /**
     * Register the scss and js for frontend view in the WordPress site.
     *
     * @return void
     */
    function medi_booking_register_frontend() : void
    {
        wp_enqueue_style('medi-booking', plugins_url('styles/scss_frontend/frontend_view.css', dirname(__FILE__, 2)), array(), filemtime(plugin_dir_path(dirname(__FILE__, 2)) . 'styles/scss_frontend/frontend_view.css'));

        wp_enqueue_script('medi-booking-frontend-function', plugin_dir_url(dirname(__FILE__, 2)) . 'scripts/script_main/_functions.js', array('jquery'), filemtime(plugin_dir_path(dirname(__FILE__, 2)) . 'scripts/script_main/_functions.js'),true);
        wp_enqueue_script('medi-booking-frontend', plugin_dir_url(dirname(__FILE__, 2)) . 'scripts/frontend.js', array('jquery'), filemtime(plugin_dir_path(dirname(__FILE__, 2)) . 'scripts/frontend.js'),true);

        wp_localize_script('medi-booking-frontend', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('medi_booking_nonce') // Optional: Nonce für Sicherheit
        ]);
    }
}