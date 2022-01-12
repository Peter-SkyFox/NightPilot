<?php
/**
 * Plugin Name: Cryptochain
 * Description: Cryptochain allow users to pay for woocommerce orders in cryptocurrency with help of metamask wallet.
 * Version: 1.0.1
 * Author: NightPilot
 * Author URI: https://www.skyfox.world/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


register_activation_hook(__FILE__, 'cryptoplugin_activate');

function cryptoplugin_activate()
{   

    if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
  }
  if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'WooCommerce' ) ) {
    // Deactivate the plugin.
    deactivate_plugins( plugin_basename( __FILE__ ) );
    // Throw an error in the WordPress admin console.
    $error_message = '<p style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen-Sans,Ubuntu,Cantarell,\'Helvetica Neue\',sans-serif;font-size: 13px;line-height: 1.5;color:#444;">' . esc_html__( 'This plugin requires ', 'crypt' ) . '<a href="' . esc_url( 'https://wordpress.org/plugins/woocommerce/' ) . '">WooCommerce</a>' . esc_html__( ' plugin to be active.', 'crypt' ) . '</p>';
    die( $error_message ); // WPCS: XSS ok.
  } else {

    global $table_prefix, $wpdb;
    require_once(ABSPATH . '/wp-admin/includes/upgrade.php');

    $tblname = 'cryptochain';
    $wp_track_table = $table_prefix . $tblname;


    if ($wpdb->get_var("show tables like '$wp_track_table'") != $wp_track_table) {

        $sql = "CREATE TABLE $wp_track_table (
        id int(11) NOT NULL AUTO_INCREMENT,
        wallet_address varchar(255) NOT NULL,
        display_status varchar(255) NOT NULL,
        mode varchar(255) NOT NULL,
        chain_id varchar(255) NOT NULL,
        network varchar(255) NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sql);
    }


    $tblname2 = 'transactions';
    $wp_track_table2 = $table_prefix . $tblname2;


    if ($wpdb->get_var("show tables like '$wp_track_table2'") != $wp_track_table2) {

        $sql2 = "CREATE TABLE $wp_track_table2 (
        id int(11) NOT NULL AUTO_INCREMENT,
        cur_date varchar(255) NOT NULL,
        date_time varchar(255) NOT NULL,
        user_name varchar(255) NOT NULL,
        order_id int(11) NOT NULL,
        trans_id varchar(255) NOT NULL,
        product_description text NOT NULL,
        sender_address varchar(255) NOT NULL,
        receiver_address varchar(255) NOT NULL,
        eth_value FLOAT(21) NOT NULL,
        eth_fees FLOAT(21) NOT NULL,
        doller_value int(11) NOT NULL,
        status varchar(255) NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql2);
    }
  }
}

add_action('admin_menu', 'crypt_menu');

function crypt_menu()
{


    add_menu_page("Crypto Wallet", "Crypto Wallet", 'manage_options', "crypto_chain", "crypto_chain", '', '10');

    add_submenu_page("crypto_chain", "Transactions", "Transactions", 'manage_options', "crypto_chain", "crypto_chain");

    add_submenu_page("crypto_chain", "Settings", "Settings", 'manage_options', "crypt_setting", "crypt_setting");
}

define('CRYPT', plugin_dir_path(__FILE__));
require_once(CRYPT . 'crypt_setting.php');
require_once(CRYPT . 'crypto_chain.php');
require_once(CRYPT . 'functions.php');


function crypt_front_scripts()
{
    $plugin_directory = plugin_dir_url(__FILE__);
    wp_enqueue_style('theme', $plugin_directory . 'theme.css');
    wp_enqueue_style( 'Fontasd',  plugin_dir_url(__FILE__) . 'css/css2.css' );
    wp_enqueue_script('ether', plugin_dir_url(__FILE__) . 'js/ether.js');
}
add_action('wp_enqueue_scripts', 'crypt_front_scripts');

add_filter('woocommerce_payment_gateways', 'crypto_add_gateway_class');
function crypto_add_gateway_class($gateways)
{
    $gateways[] = 'WC_crypto_Gateway'; // your class name is here
    return $gateways;
}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */

add_action('plugins_loaded', 'crypto_init_gateway_class');
add_action('wp_enqueue_scripts', 'payment_scripts');


function crypto_init_gateway_class()
{

    class WC_crypto_Gateway extends WC_Payment_Gateway
    {

        /**
         * Class constructor, more about it in Step 3
         */
        public function __construct()
        {
            $this->id = 'crypto'; // payment gateway plugin ID
            $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'Pay With Crypto';
            $this->method_description = 'Description of Crypto payment gateway'; // will be displayed on the options page

            // gateways can support subscriptions, refunds, saved payment methods,
            // but in this tutorial we begin with simple payments
            $this->supports = array(
                'products'
            );

            // Method with all the options fields
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled = $this->get_option('enabled');
            $this->testmode = 'yes' === $this->get_option('testmode');
            $this->private_key = $this->testmode ? $this->get_option('test_private_key') : $this->get_option('private_key');
            $this->publishable_key = $this->testmode ? $this->get_option('test_publishable_key') : $this->get_option('publishable_key');

            // This action hook saves the settings
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        /**
         * Plugin options, we deal with it in Step 3 too
         */
        public function init_form_fields()
        {

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable crypto Gateway',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'yes'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'Pay With Crypto',
                    'class'       => 'turr',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => '',
                ),

            );
        }

        /**
         * You will need it if you want your custom credit card form, Step 4 is about it
         */
        public function payment_fields()
        {

            // ok, let's display some description before the payment form
            if ($this->description) {
                // you can instructions for test mode, I mean test card numbers etc.
                if ($this->testmode) {
                    $this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#">documentation</a>.';
                    $this->description  = trim($this->description);
                }
                // display the description with <p> tags etc.
                echo wpautop(wp_kses_post($this->description));
            }

            // I will echo() the form, but you can close PHP tags and print it directly in HTML
            echo '<fieldset id="wc-' . esc_attr($this->id) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

            // Add this action hook if you want your custom payment gateway to support it
            do_action('woocommerce_credit_card_form_start', $this->id);

            // I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
            include(CRYPT . 'front.php');

            do_action('woocommerce_credit_card_form_end', $this->id);

            echo '<div class="clear"></div></fieldset>';
        }

        /*
         * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
         */
        public function payment_scripts()
        {

            // we need JavaScript to process a token only on cart/checkout pages, right?
            if (!is_cart() && !is_checkout() && !isset($_GET['pay_for_order'])) {
                return;
            }

            // if our payment gateway is disabled, we do not have to enqueue JS too
            if ('no' === $this->enabled) {
                return;
            }

            // no reason to enqueue JavaScript if API keys are not set
            if (empty($this->private_key) || empty($this->publishable_key)) {
                return;
            }

            // do not work with card detailes without SSL unless your website is in a test mode
            if (!$this->testmode && !is_ssl()) {
                return;
            }

            // let's suppose it is our payment processor JavaScript that allows to obtain a token

            // and this is our custom JS in your plugin directory that works with token.js
            /*wp_register_script( 'woocommerce_crypto', plugins_url( 'crypto.js', __FILE__ ), array( 'jquery', 'crypto_js' ) );
    echo "plugins_url";
*/
            // in most payment processors you have to use PUBLIC KEY to obtain a token
            /*wp_localize_script( 'woocommerce_crypto', 'crypto_params', array(
        'publishableKey' => $this->publishable_key
    ) );

    wp_enqueue_script( 'woocommerce_crypto' );*/
        }

        /*
         * Fields validation, more in Step 5
         */
        public function validate_fields()
        {
        }

        /*
         * We're processing the payments here, everything about it is in Step 5
         */
        public function process_payment($order_id)
        {


            $order = wc_get_order($order_id);

            // Mark as on-hold (we're awaiting the payment)
            $order->update_status('on-hold', __('Awaiting offline payment', 'WC_crypto_Gateway'));

            // Reduce stock levels
            $order->reduce_order_stock();

            // Remove cart
            WC()->cart->empty_cart();

            // Return thankyou redirect
            return array(
                'result'    => 'success',
                'redirect'  => $this->get_return_url($order)
            );
        }

        /*
         * In case you need a webhook, like PayPal IPN etc
         */
        public function webhook()
        {
        }
    }
}
?>