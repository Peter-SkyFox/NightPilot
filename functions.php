<?php
function enqueue_crypt_scripts()
{


    global $wpdb, $table_prefix;

    $tblnme = $table_prefix . 'cryptochain';

    $found = $wpdb->get_results("SELECT display_status from $tblnme");

    $k =   $found[0]->display_status;

    if ($k == 'on') {
        wp_enqueue_script('custom', plugin_dir_url(__FILE__) . 'js/custom.js', array('jquery'), false, true);
    }
}

add_action('wp_enqueue_scripts', 'enqueue_crypt_scripts');

function crypt_back_script(){

    wp_enqueue_style( 'Fontasd',  plugin_dir_url(__FILE__) . 'css/css2.css' );
    wp_enqueue_style( 'Bootasd',  plugin_dir_url(__FILE__) . 'css/bootstrap.min.css' );
    wp_enqueue_style( 'Databoot',  plugin_dir_url(__FILE__) . 'css/dataTables.bootstrap4.min.css' );
    wp_enqueue_script('ether', plugin_dir_url(__FILE__) . 'js/ether.js');
    wp_enqueue_script('datamin', plugin_dir_url(__FILE__) . 'js/jquery.dataTables.min.js');
    wp_enqueue_script('databootmin', plugin_dir_url(__FILE__) . 'js/dataTables.bootstrap4.min.js');
    

}
add_action('admin_enqueue_scripts', 'crypt_back_script');

add_action('wp_head', 'crypt_ajaxurl');

function crypt_ajaxurl()
{

    echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}


add_filter('woocommerce_available_payment_gateways', 'pay_with_crypto_disable');

function pay_with_crypto_disable($available_gateways)
{

    global $wpdb, $table_prefix;

    $tblnme = $table_prefix . 'cryptochain';

    $found = $wpdb->get_results("SELECT display_status from $tblnme");

    $k =   $found[0]->display_status;

    //echo $k;
    if (isset($available_gateways['crypto'])  && $k == 'off') {
        unset($available_gateways['crypto']);
    }
    return $available_gateways;
}




function register_crypt_payment_order_status()
{
    register_post_status('wc-crypto-payment', array(
        'label'                     => 'ETH Payment Processing',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop('ETH Payment Processing <span class="count">(%s)</span>', 'ETH Payment Processing<span class="count">(%s)</span>')
    ));
}
add_action('init', 'register_crypt_payment_order_status');
function crypt_payment_order_status($order_statuses)
{
    $new_order_statuses = array();
    foreach ($order_statuses as $key => $status) {
        $new_order_statuses[$key] = $status;
        if ('wc-processing' === $key) {
            $new_order_statuses['wc-crypto-payment'] = 'ETH Payment Processing';
        }
    }
    return $new_order_statuses;
}
add_filter('wc_order_statuses', 'crypt_payment_order_status');





function   crypt_change_order_status($order_id)
{
    if (!$order_id) {
        return;
    }
    $order = wc_get_order($order_id);

    $payment_method = $order->get_payment_method();

    if ($payment_method == "crypto") {
        $order->update_status('wc-crypto-payment');
    }
}
add_action('woocommerce_order_status_changed', 'crypt_change_order_status');


add_filter('woocommerce_locate_template', 'crypt_plugin_template', 1, 3);



function crypt_plugin_template($template, $template_name, $template_path)
{
    global $woocommerce;

    $_template = $template;
    if (!$template_path)
        $template_path = $woocommerce->template_url;

    $plugin_path  = untrailingslashit(plugin_dir_path(__FILE__))  . '/template/woocommerce/';

    // Look within passed path within the theme - this is priority
    $template = locate_template(
        array(
            $template_path . $template_name,
            $template_name
        )
    );

    if (!$template && file_exists($plugin_path . $template_name))
        $template = $plugin_path . $template_name;

    if (!$template)
        $template = $_template;

    return $template;
}





add_action('wp_ajax_cryptstoredata', 'cryptstoredata');
add_action('wp_ajax_nopriv_cryptstoredata', 'cryptstoredata');



function cryptstoredata()
{
    $cur_date = sanitize_text_field($_POST['currDate']);
    $datet = sanitize_text_field($_POST['Date']);
    $seth = sanitize_text_field($_POST['sendeth']);
    $transfee = sanitize_text_field($_POST['tranfee']);
    $doller = sanitize_text_field($_POST['dollerval']);
    $sadd = sanitize_text_field($_POST['sendadd']);
    $radd = sanitize_text_field($_POST['recieveadd']);
    $txhsh = sanitize_text_field($_POST['txhash']);
    $name = sanitize_text_field($_POST['uname']);
    $desc = sanitize_text_field($_POST['desc']);
    $status = sanitize_text_field($_POST['status']);
    global $wpdb;
    $wpdb->insert(
        'wp_transactions',
        array(

            'cur_date' => $cur_date,
            'date_time' => $datet,
            'user_name' => $name,
            'trans_id' => $txhsh,
            'product_description' => $desc,
            'sender_address' => $sadd,
            'receiver_address' => $radd,
            'eth_value' => $seth,
            'eth_fees' => $transfee,
            'doller_value' => $doller,
            'status' => $status
        ),
        array(
            '%s'
        )
    );
    die();
    return true;
}


add_action('wp_ajax_cryptupdatedata', 'cryptupdatedata');
add_action('wp_ajax_nopriv_cryptupdatedata', 'cryptupdatedata');



function cryptupdatedata()
{
    $txhash = sanitize_text_field($_POST['transactionhash']);
    $txstatus = sanitize_text_field($_POST['txstatus']);

    global $wpdb;
    $wpdb->update(
        'wp_transactions',
        array(
            'status' => $txstatus
        ),
        array('trans_id' => $txhash)
    );
    die();
    return true;
}



add_action('wp_ajax_cryptupdateorderid', 'cryptupdateorderid');
add_action('wp_ajax_nopriv_cryptupdateorderid', 'cryptupdateorderid');



function cryptupdateorderid()
{
    $orderid = sanitize_text_field($_POST['orderid']);
    $txhash1 = sanitize_text_field($_POST['txhsh']);

    global $wpdb;
    $wpdb->update(
        'wp_transactions',
        array(
            'order_id' => $orderid
        ),
        array('trans_id' => $txhash1)
    );
    die();
    return true;
}