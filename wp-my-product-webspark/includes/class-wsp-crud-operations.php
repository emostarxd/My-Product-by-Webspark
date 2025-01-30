<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WSP_CRUD_Operations {
    public function __construct() {
        add_action('init', [$this, 'handle_product_submission']);
        add_action('init', [$this, 'handle_product_deletion']);
    }

/**
* Handle product submission (adding/editing)
*/
    public function handle_product_submission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (!isset($_POST['wsp_save_product'])) {
            return;
        }

        // Verify nonce to prevent wp security errors
        if (!check_admin_referer('wsp_save_product', '_wpnonce')) {
            error_log('Security check failed: Invalid or missing nonce.');
            wp_die(__('Security check failed', 'wspark'));
        }
        $product_id = isset($_POST['wsp_product_id']) ? intval($_POST['wsp_product_id']) : 0;

        // Check user' perms for editing this product
        if ($product_id && get_post_field('post_author', $product_id) != get_current_user_id()) {
            wp_die(__('You do not have permission to edit this product.', 'wspark'));
        }

        // Set product data
        $product_data = [
            'post_title'   => sanitize_text_field($_POST['wsp_product_name']),
            'post_content' => wp_kses_post($_POST['wsp_product_description']),
            'post_status'  => 'pending',
            'post_type'    => 'product',
            'post_author'  => get_current_user_id(),
        ];

        // Update existing edited or add new product
        if ($product_id) {
            $product_data['ID'] = $product_id;
            $product_id = wp_update_post($product_data);
        } else {
            $product_id = wp_insert_post($product_data);
        }

        // If product was posted successfully
        if ($product_id && !is_wp_error($product_id)) {
            // Set woo product type to 'simple'
            wp_set_object_terms($product_id, 'simple', 'product_type');

            // Update woo product meta fields
            update_post_meta($product_id, '_regular_price', sanitize_text_field($_POST['wsp_product_price']));
            update_post_meta($product_id, '_price', sanitize_text_field($_POST['wsp_product_price']));
            update_post_meta($product_id, '_stock', intval($_POST['wsp_product_quantity']));
            update_post_meta($product_id, '_manage_stock', 'yes');
            update_post_meta($product_id, '_stock_status', intval($_POST['wsp_product_quantity']) > 0 ? 'instock' : 'outofstock');

            // For product image
            if (!empty($_FILES['wsp_product_image']['name'])) {
                $attachment_id = $this->upload_product_image($_FILES['wsp_product_image']);
                if (!is_wp_error($attachment_id)) {
                    set_post_thumbnail($product_id, $attachment_id);
                }
            }

            // Send email after product saving
            if (!isset($_POST['wsp_product_id'])) {
                if (class_exists('WSP_Email_New_Product')) {
                    $email = new WSP_Email_New_Product();
                    $email->trigger($product_id);
                }
            }

            // action for other plugins
            do_action('wsp_after_product_save', $product_id, isset($_POST['wsp_product_id']));

            // GO to my products page
            wp_redirect(wc_get_account_endpoint_url('my-products'));
            exit;
        } else {
            // debug
            wp_die(__('Error saving product. Please try again.', 'wspark'));
        }
    }

    /**
     * Handle product removing
     */
    public function handle_product_deletion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return;
        }

        if (isset($_GET['delete']) && isset($_GET['_wpnonce'])) {
            $product_id = intval($_GET['delete']);

            // wp nonce
            if (!wp_verify_nonce($_GET['_wpnonce'], 'wsp_delete_product')) {
                error_log('Security check failed: Invalid nonce for deletion.');
                wp_die(__('Security check failed', 'wspark'));
            }

            // Check if user owns this product
            if (get_post_field('post_author', $product_id) != get_current_user_id()) {
                wp_die(__('You do not have permission to delete this product.', 'wspark'));
            }

            // Move to recycle bin instead of full deletion
            wp_trash_post($product_id);

            // GO back to acct products page
            wp_redirect(wc_get_account_endpoint_url('my-products'));
            exit;
        }
    }

    /**
     * For product image uploader
     */
    private function upload_product_image($file) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $attachment_id = media_handle_upload('wsp_product_image', 0);

        if (is_wp_error($attachment_id)) {
            error_log('Error uploading product image: ' . $attachment_id->get_error_message());
            return $attachment_id;
        }

        // Set the attachment author as current user bi id
        wp_update_post([
            'ID' => $attachment_id,
            'post_author' => get_current_user_id()
        ]);

        return $attachment_id;
    }
}