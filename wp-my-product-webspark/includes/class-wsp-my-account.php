<?php  
if (!defined('ABSPATH')) {  
    exit; // Exit if accessed directly  
}  
  
class WSP_My_Account {  
    public function __construct() {  
        add_filter('woocommerce_account_menu_items', [$this, 'add_my_account_links']);  
        add_action('init', [$this, 'add_my_account_endpoints']);  
        add_action('woocommerce_account_add-product_endpoint', [$this, 'add_product_content']);  
        add_action('woocommerce_account_my-products_endpoint', [$this, 'my_products_content']);  
    }  
  
    public function add_my_account_links($menu_links) {  
        $menu_links['add-product'] = __('Add Product', 'wspark');  
        $menu_links['my-products'] = __('My Products', 'wspark');  
        return $menu_links;  
    }  
  
    public function add_my_account_endpoints() {  
        add_rewrite_endpoint('add-product', EP_ROOT | EP_PAGES);  
        add_rewrite_endpoint('my-products', EP_ROOT | EP_PAGES);  
    }  
/** Paths */
    public function add_product_content() {  
        wc_get_template('my-account/add-product.php', [], '', WSP_PLUGIN_PATH . 'templates/');  
    }  
    public function my_products_content() {  
        wc_get_template('my-account/my-products.php', [], '', WSP_PLUGIN_PATH . 'templates/');  
    }  
}