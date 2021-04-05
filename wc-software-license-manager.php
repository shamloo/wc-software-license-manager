<?php
/**
 * Plugin Name:                 WC Software License Manager
 * Plugin URI:                  https://github.com/shamloo/wc-software-license-manager
 * Description:                 Seamless integration between Woocommerce and Software License Manager
 * Version:                     2.0.2
 * Author:                      Omid Shamlu
 * Author URI:                  http://wp-master.ir
 * Text Domain:                 wc-slm
 * Domain Path:                 /languages
 * WC requires at least         2.5.0
 * WC tested up to:             3.2.0
 *
 *
 * Copyright 2015-2017 Omid Shamloo - http://wp-master.ir
 * Copyright 2017-2018 Anthony Hortin - https://maddisondesigns.com
 */

// TODO:
// https://wordpress.org/support/topic/modifying-for-variable-products
// Add option to recreate manual linense in order edit page
// Add license columns in order table lists

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WC_SLM')) {

    class WC_SLM
    {

        // WC_SLM $instance The one true WC_SLM
        private static $instance;

        /**
         * Get active instance
         *
         * @access public
         * @return object self::$instance The one true WC_SLM
         * @since 1.0.0
         */
        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new WC_SLM();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
            }

            return self::$instance;
        }

        /**
         * Setup plugin constants
         *
         * @access private
         * @return void
         * @since 1.0.0
         */
        private function setup_constants()
        {

            // Plugin version
            define('WC_SLM_VER', '2.0.0');

            // Plugin path
            define('WC_SLM_DIR', plugin_dir_path(__FILE__));

            // Plugin URL
            define('WC_SLM_URL', plugin_dir_url(__FILE__));

            // SLM Credentials
            $api_url = trim(get_option('wc_slm_api_url', ''));
            $api_url = rtrim($api_url, '/');
            if ((strpos($api_url, 'http://') === false) && (strpos($api_url, 'https://') === false)) {
                $api_url = 'http://' . $api_url;
            }

            // Software License Manager API URL
            define('WC_SLM_API_URL', $api_url);

            // Secret Key for Creation
            define('WC_SLM_API_SECRET', get_option('wc_slm_api_secret'));

            // Secret Key for Verification
            if ('' != get_option('wc_slm_api_secret_verify', '')) {
                define('WC_SLM_API_SECRET_VERFIY', get_option('wc_slm_api_secret_verify'));
            }

            // Enable Debug Logging
            if ('yes' === get_option('wc_slm_debug_logging', false)) {
                define('WC_SLM_DEBUG_LOGGING', true);
            }
        }

        /**
         * Include necessary files
         *
         * @access private
         * @return void
         * @since 1.0.0
         */
        private function includes()
        {

            // Get out if WC is not active
            if (!function_exists('WC')) {
                return;
            }

            // Include files and scripts
            require_once WC_SLM_DIR . 'includes/helper.php';

            if (is_admin()) {
                require_once WC_SLM_DIR . 'includes/meta-boxes.php';
                require_once WC_SLM_DIR . 'includes/settings.php';
            }

            require_once WC_SLM_DIR . 'includes/emails.php';
            require_once WC_SLM_DIR . 'includes/purchase.php';
        }

        /**
         * Internationalization
         *
         * @access public
         * @return void
         * @since 1.0.0
         */
        public function load_textdomain()
        {
            // Load the default language files
            load_plugin_textdomain('wc-slm', false, 'wc-software-license-manager/languages');
        }

        /**
         * Activation function fires when the plugin is activated.
         *
         * @return void
         * @since 1.0.0
         * @access public
         */
        public static function activation()
        {
            // nothing
        }

        /**
         * Uninstall function fires when the plugin is being uninstalled.
         *
         * @return void
         * @since 1.0.0
         * @access public
         */
        public static function uninstall()
        {
            // nothing
        }
    }

    /**
     * The main function responsible for returning the one true WC_SLM
     * instance to functions everywhere
     *
     * @return \WC_SLM The one true WC_SLM
     * @since 1.0.0
     */
    function WC_SLM_load()
    {
        return WC_SLM::instance();
    }

    /**
     * The activation & uninstall hooks are called outside of the singleton because WordPress doesn't
     * register the call from within the class hence, needs to be called outside and the
     * function also needs to be static.
     */
    register_activation_hook(__FILE__, array('WC_SLM', 'activation'));
    register_uninstall_hook(__FILE__, array('WC_SLM', 'uninstall'));

    add_action('plugins_loaded', 'WC_SLM_load');

}
