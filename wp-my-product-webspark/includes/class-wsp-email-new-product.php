<?php  
if (!defined('ABSPATH')) {  
    exit;  
}  
if (!class_exists('WC_Email')) {  
    return;  
}  

class WSP_Email_New_Product extends WC_Email {  
    public function __construct() {  
        $this->id = 'wsp_new_product';  
        $this->title = __('New Product Notification', 'wspark');  
        $this->description = __('This email is sent to the admin when a new product is added by user.', 'wspark');  

        // Template paths for html and text versions  
        $this->template_html = 'emails/admin-new-product.php';  
        $this->template_plain = 'emails/plain/admin-new-product.php';  
        $this->template_base = WSP_PLUGIN_PATH . 'templates/';  

        // Default email subject and heading  
        $this->subject = __('[{site_title}] New Product Waiting Moderation: {product_name}', 'wspark');  
        $this->heading = __('New Product Submitted', 'wspark');  

        // Call parent constructor  
        parent::__construct();  

            $this->recipient = $this->get_option('recipient', get_option('admin_email'));  
            $this->init_form_fields();  
            $this->init_settings();  

        // Save settings  
        add_action('woocommerce_update_options_email_' . $this->id, [$this, 'process_admin_options']);  
    }  

    /**
     * Check if email is enabled or not
     */
    public function is_enabled() {
        return $this->get_option('enabled', 'yes') === 'yes';
    }

    /**
     * Trigger for email
     */
    public function trigger($product_id) {  
        if (!$product_id || !$this->is_enabled()) {  
            error_log('FAIL: WSP_Email_New_Product: Email not triggered - disabled or no product ID.');
            return;
        }

        $this->object = wc_get_product($product_id);
        if (!$this->object) {
            error_log('FAIL: WSP_Email_New_Product: Invalid product ID: ' . $product_id);
            return;
        }

        $this->placeholders['{product_name}'] = $this->object->get_name();
        $this->placeholders['{product_link}'] = get_edit_post_link($product_id);
        $this->placeholders['{author_link}'] = get_edit_user_link($this->object->get_author_id());

        if (!is_email($this->get_recipient())) {
            error_log('FAIL: WSP_Email_New_Product: Invalid recipient email: ' . $this->get_recipient());
            return;
        }

        $mailer = WC()->mailer();
        $mailer->emails[$this->id] = $this;
        $this->send(
            $this->get_recipient(),
            $this->get_subject(),
            $this->get_content(),
            $this->get_headers(),
            $this->get_attachments()
        );
    /** debug*/
        error_log('WSP_Email_New_Product: Email sent for product ID: ' . $product_id);
    }  

    /** HTML content */
    public function get_content_html() {  
        return wc_get_template_html(  
            $this->template_html,  
            ['product' => $this->object, 'email_heading' => $this->get_heading()],  
            '',  
            WSP_PLUGIN_PATH . 'templates/'  
        );  
    }  

    /** plain text    */
    public function get_content_plain() {  
        return wc_get_template_html(  
            $this->template_plain,  
            ['product' => $this->object, 'email_heading' => $this->get_heading()],  
            '',  
            WSP_PLUGIN_PATH . 'templates/'  
        );  
    }  

    /**
     * Initialize fields
     */
    public function init_form_fields() {  
        $this->form_fields = [  
            'enabled' => [  
                'title' => __('Enable/Disable', 'wspark'),  
                'type' => 'checkbox',  
                'label' => __('Enable this email notification', 'wspark'),  
                'default' => 'yes',  
            ],  
            'recipient' => [  
                'title' => __('Recipient(s)', 'wspark'),  
                'type' => 'text',  
                'description' => __('Enter the recipient email address(es). Separate multiple emails with commas.', 'wspark'),  
                'default' => get_option('admin_email'),  
                'placeholder' => '',  
            ],  
            'subject' => [  
                'title' => __('Subject', 'wspark'),  
                'type' => 'text',  
                'description' => sprintf(__('Available placeholders: %s', 'wspark'), '<code>{site_title}, {product_name}</code>'),  
                'default' => $this->subject,  
                'placeholder' => '',  
            ],  
            'heading' => [  
                'title' => __('Email Heading', 'wspark'),  
                'type' => 'text',  
                'description' => sprintf(__('Available placeholders: %s', 'wspark'), '<code>{site_title}, {product_name}</code>'),  
                'default' => $this->heading,  
                'placeholder' => '',  
            ],  
            'email_type' => [  
                'title' => __('Email type', 'wspark'),  
                'type' => 'select',  
                'description' => __('Choose which format of email to send.', 'wspark'),  
                'default' => 'html',  
                'class' => 'wc-enhanced-select',  
                'options' => $this->get_email_type_options(),  
            ],  
        ];  
    }  
}