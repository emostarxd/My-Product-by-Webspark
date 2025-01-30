<?php  
/**  
 * Woo new product email template (Plain Text).  
 */  
  
if (!defined('ABSPATH')) {  
    exit; // Exit if accessed directly  
}  
  
echo esc_html__('New Product Waiting for Review', 'wspark') . "\n\n";  
echo esc_html__('A new product has been added for review:', 'wspark') . "\n\n";  
echo esc_html__('Product Name:', 'wspark') . ' ' . esc_html($product->get_name()) . "\n";  
echo esc_html__('Posted by:', 'wspark') . ' ' . esc_url($placeholders['{author_link}']) . "\n";  
echo esc_html__('Edit Product:', 'wspark') . ' ' . esc_url($placeholders['{product_link}']) . "\n";  
