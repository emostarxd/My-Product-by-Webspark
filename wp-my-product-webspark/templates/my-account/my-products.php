<?php  
if (!defined('ABSPATH')) {  
    exit; // Exit if accessed directly  
}  
  
global $current_user;  
  
// Pagination  
$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;  
$per_page = 10;  
$offset = ($paged - 1) * $per_page;  
  
// Query user's products  
$args = array(  
    'author' => $current_user->ID,  
    'post_type' => 'product',  
    'posts_per_page' => $per_page,  
    'offset' => $offset,  
    'post_status' => array('publish', 'pending'),  
);  
  
$products = new WP_Query($args);  
$total_products = $products->found_posts;  
?>  
  
<h2 class="wsp-my-products-title"><?php _e('My Products', 'wspark'); ?></h2>  
  
<?php if ($products->have_posts()) : ?>  
    <!-- FLEX cards with users products -->  
    <div class="wsp-my-products-container">  
        <?php while ($products->have_posts()) : $products->the_post(); ?>  
            <?php $product = wc_get_product(get_the_ID()); ?>  
            <div class="wsp-product-card">  
                <div class="wsp-product-image">  
                    <?php if ($product->get_image_id()) : ?>  
                        <?php echo wp_get_attachment_image($product->get_image_id(), 'thumbnail'); ?>  
                    <?php else : ?>  
                        <img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" alt="<?php esc_attr_e('Placeholder', 'wspark'); ?>">  
                    <?php endif; ?>  
                </div>  
                <div class="wsp-product-details">  
                    <h3 class="wsp-product-name"><?php echo esc_html($product->get_name()); ?></h3>  
                    <p class="wsp-product-quantity"><?php _e('Quantity:', 'wspark'); ?> <?php echo esc_html($product->get_stock_quantity()); ?></p>  
                    <p class="wsp-product-price"><?php _e('Price:', 'wspark'); ?> <?php echo esc_html(strip_tags(wc_price($product->get_price()))); ?></p>  
                    <p class="wsp-product-status"><?php _e('Status:', 'wspark'); ?> <?php echo esc_html(ucfirst($product->get_status())); ?></p>  
                </div>  
                <div class="wsp-product-actions">  
                    <a href="<?php echo esc_url(add_query_arg('edit', $product->get_id(), wc_get_account_endpoint_url('add-product'))); ?>" class="wsp-edit-button"><?php _e('Edit', 'wspark'); ?></a>  
                    <a href="<?php echo esc_url(wp_nonce_url(add_query_arg('delete', $product->get_id()), 'wsp_delete_product')); ?>" class="wsp-delete-button"><?php _e('Delete', 'wspark'); ?></a>  
                </div>  
            </div>  
        <?php endwhile; ?>  
    </div>  
  
    <!-- Pagination -->  
    <div class="wsp-pagination">  
        <?php  
        echo paginate_links(array(  
            'base' => add_query_arg('paged', '%#%'),  
            'format' => '',  
            'prev_text' => __('&laquo; Previous', 'wspark'),  
            'next_text' => __('Next &raquo;', 'wspark'),  
            'total' => ceil($total_products / $per_page),  
            'current' => $paged,  
        ));  
        ?>  
    </div>  
<?php else : ?>  
    <p><?php _e('No products found.', 'wspark'); ?></p>  
<?php endif;  
  
// Ensure review is disabled  (bugfix)
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10);  
?>  
