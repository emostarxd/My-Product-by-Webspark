<?php  
/**  
 * Plugin Name:       My Product by Webspark  
 * Plugin URI:        https://t.me/emostarxd  
 * Description:       Add/Manage per-user products via WooCommerce My Account area. Requires WooCommerce.  
 * Version:           0.3.4  
 * Author:            Oliver Aoki  
 * Author URI:        https://t.me/emostarxd/  
 * Text Domain:       wspark  
 */  
  
if (!defined('WPINC')) {  
    die;  
}  

// Plugin constants  
define('WSP_PLUGIN_VERSION', '0.3.4');  
define('WSP_PLUGIN_PATH', plugin_dir_path(__FILE__));  
define('WSP_PLUGIN_URL', plugin_dir_url(__FILE__));  

/**
 * Check if WooCommerce is active before activation
 */
function wsp_check_woocommerce() {
    if (!class_exists('WooCommerce')) {
        wp_die(
            __('My Product by Webspark requires WooCommerce to be installed and active.', 'wspark'),
            __('Plugin activation error', 'wspark'),
            ['back_link' => true]
        );
    }
}
register_activation_hook(__FILE__, 'wsp_check_woocommerce');

/**
 * Show warning for admin if WooCommerce is not active
 */
add_action('admin_notices', function () {
    if (!class_exists('WooCommerce')) {
        echo '<div class="notice notice-error"><p>' . __('My Product by Webspark requires WooCommerce to be installed and active.', 'wspark') . '</p></div>';
    }
});

/**
 * Now start plugin functionality only if WooCommerce is active
 */
add_action('plugins_loaded', function () {
    if (!class_exists('WooCommerce')) {
        return;
    }

    require_once WSP_PLUGIN_PATH . 'includes/class-wsp-crud-operations.php';  
    require_once WSP_PLUGIN_PATH . 'includes/class-wsp-my-account.php';  

    new WSP_CRUD_Operations();  
    new WSP_My_Account();  

    // Register email class
    add_filter('woocommerce_email_classes', function ($emails) {  
        require_once WSP_PLUGIN_PATH . 'includes/class-wsp-email-new-product.php';  

        if (class_exists('WSP_Email_New_Product')) {  
            $emails['WSP_Email_New_Product'] = new WSP_Email_New_Product();  
            error_log('CLASS WSP_Email_New_Product registered successfully.');
        } else {  
            error_log('ERROR: WSP_Email_New_Product not found!');
        }  
        return $emails;  
    }, 20);  

    // Register action for emails
    add_filter('woocommerce_email_actions', function ($actions) {  
        $actions[] = 'wsp_new_product';  
        error_log('wsp_new_product action added.');  
        return $actions;  
    });  

    // Trigger email at product saving
    add_action('wsp_new_product', function ($product_id) {  
        require_once WSP_PLUGIN_PATH . 'includes/class-wsp-email-new-product.php';  

        if (class_exists('WSP_Email_New_Product')) {  
            $email = new WSP_Email_New_Product();  
            $email->trigger($product_id);  
            error_log('OK: wsp_new_product email triggered for product ID: ' . $product_id);  
        } else {  
            error_log('FAIL: WSP_Email_New_Product not found during email send process.');  
        }  
    });
});

/**
 * Load plugin CSS and JS
 */
add_action('wp_enqueue_scripts', function () {
    if (is_account_page()) {
        wp_enqueue_media();
            wp_enqueue_script('wsp-media-uploader', WSP_PLUGIN_URL . 'assets/js/media-uploader.js', ['jquery'], WSP_PLUGIN_VERSION, true);
            wp_enqueue_style('wsp-styles', WSP_PLUGIN_URL . 'assets/css/wsp-styles.css', [], WSP_PLUGIN_VERSION);
        }
});

/**
 * Filter media library for users
 */
add_filter('ajax_query_attachments_args', function ($query) {
    $current_user = wp_get_current_user();
    if (in_array('customer', $current_user->roles) || in_array('subscriber', $current_user->roles)) {
        $query['author'] = $current_user->ID;
    }
    return $query;
});

/**
 * Show admin notice at top if new products waiting to approove. TEMPORARY alternative till email sending will be fixed
 */
add_action('admin_notices', function () {
    if (!current_user_can('manage_woocommerce')) {
        return;
    }
    $pending_count = wp_count_posts('product')->pending;
    if ($pending_count > 0) {
        $products_page = admin_url('edit.php?post_type=product&post_status=pending');
        echo '<div class="notice notice-warning is-dismissible">
                <p>' . sprintf(
                    __('You have <strong>%d</strong> pending products from users. <a href="%s">Check and approve em all!</a>.', 'wspark'),
                    $pending_count,
                    esc_url($products_page)
                ) . '</p>
              </div>';
    }
});



/**
 * Bugfix:
 * Disable strange WooCommerce reviews and ratings form (for first added post id if added) at our plugin frontend pages  
 * VIOLENT WRONG RADICAL FIX, needs to reinvent in more correct way!
 */
add_action('init', function () {
    remove_post_type_support('product', 'comments');
    add_filter('woocommerce_product_get_rating_counts', '__return_empty_array');
    add_filter('woocommerce_product_get_average_rating', '__return_zero');
    add_filter('woocommerce_product_get_review_count', '__return_zero');
});