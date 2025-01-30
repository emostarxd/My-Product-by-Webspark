<?php  
if (!defined('ABSPATH')) {  
    exit; // Exit if accessed directly  
}  
?>  
  
<h1><?php echo esc_html($email_heading); ?></h1>  
<p><?php esc_html_e('A new pending product has been submitted for review.', 'wspark'); ?></p>  
<p><strong><?php esc_html_e('Product Name:', 'wspark'); ?></strong> <?php echo esc_html($product->get_name()); ?></p>  
<p><strong><?php esc_html_e('Author:', 'wspark'); ?></strong> <a href="<?php echo esc_url($placeholders['{author_link}']); ?>"><?php esc_html_e('View Author', 'wspark'); ?></a></p>  
<p><strong><?php esc_html_e('Edit Product:', 'wspark'); ?></strong> <a href="<?php echo esc_url($placeholders['{product_link}']); ?>"><?php esc_html_e('Edit Product', 'wspark'); ?></a></p>  
