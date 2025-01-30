<?php
if (!defined('ABSPATH')) {
    exit;
}

global $current_user;
$product_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$product = $product_id ? wc_get_product($product_id) : null;

if ($product_id && get_post_field('post_author', $product_id) != $current_user->ID) {
    wp_die(__('You do not have permission to edit this product.', 'wspark'));
}
?>

<h2><?php echo $product_id ? __('Edit Product', 'wspark') : __('Add Product', 'wspark'); ?></h2>

<form method="post" enctype="multipart/form-data">
    <?php wp_nonce_field('wsp_save_product', '_wpnonce'); ?>
    <p>
        <label for="wsp_product_name"><?php _e('Product Name', 'wspark'); ?></label>
        <input type="text" class="wsp_add_name" id="wsp_product_name" name="wsp_product_name" value="<?php echo $product ? esc_attr($product->get_name()) : ''; ?>" required>
    </p>
    <p>
        <label for="wsp_product_price"><?php _e('Product Price', 'wspark'); ?></label>
        <input type="number" class="wsp_add_price" id="wsp_product_price" name="wsp_product_price" step="0.01" value="<?php echo $product ? esc_attr($product->get_price()) : ''; ?>" required>
    </p>
    <p>
        <label for="wsp_product_quantity"><?php _e('Product Quantity', 'wspark'); ?></label>
        <input type="number" class="wsp_count_field" id="wsp_product_quantity" name="wsp_product_quantity" value="<?php echo $product ? esc_attr($product->get_stock_quantity()) : ''; ?>" required>
    </p>
    <p>
        <label class="wsp_add_description" for="wsp_product_description"><?php _e('Product Description', 'wspark'); ?></label>
        <?php
        wp_editor(
            $product ? $product->get_description() : '',
            'wsp_product_description',
            array(
                'textarea_name' => 'wsp_product_description',
                'media_buttons' => false,
                'teeny' => true,
            )
        );
        ?>
    </p>
    <p>
        <label for="wsp_product_image"><?php _e('Product Image', 'wspark'); ?></label>
        <input type="hidden" id="wsp_product_image_id" name="wsp_product_image_id" value="<?php echo $product ? esc_attr($product->get_image_id()) : ''; ?>">
        <button type="button" id="wsp_upload_image_button" class="woocommerce-Button wsp_uploader button"><?php _e('Upload Media', 'wspark'); ?></button>
        <div id="wsp_image_preview">
            <?php if ($product && $product->get_image_id()) : ?>
                <?php echo wp_get_attachment_image($product->get_image_id(), 'thumbnail'); ?>
            <?php endif; ?>
        </div>
    </p>

    <?php if ($product_id) : ?>
        <input type="hidden" name="wsp_product_id" value="<?php echo esc_attr($product_id); ?>">
    <?php endif; ?>

    <p>
        <button type="submit" class="wsp_button woocommerce-Button button" name="wsp_save_product"><?php _e('Save Product', 'wspark'); ?></button>
    </p>
</form>