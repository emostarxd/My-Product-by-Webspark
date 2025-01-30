# My Product by Webspark

**My Product by Webspark** is a custom WordPress plugin that extends WooCommerce functionality. It allows customers to manage their own products via the "My Account" page. WooCommerce is required for this plugin to work.

---

## Features

- **Add Product**: Users can add new products with the following fields:
  - Product Name
  - Product Price
  - Product Quantity
  - Product Description (WYSIWYG editor)
  - Product Image (using WP Media)
- **My Products**: Users can view responsive grid of their own products, with pagination. Every card includes:
  - Product image preview
  - Product Name
  - Quantity
  - Price
  - Current Status
  - Edit and Delete buttons
- **Pending Review**: All new products created or edited via "My Account" are set to "Pending Review" status and waiting admin to approove or declain posting.

- **Admin Notifications**: Admins receive notice in dashboard when a product is created or edited. Notice contains number of pending products and link to check em all.
    Admins receive an email notification when a product is created or edited. The email includes:
  - Product Name
  - Link to the product edit page in the admin panel
  - Link to the author's profile in the admin panel
- **Custom Email Template**: The email notification uses a custom template based on WooCommerce's `WC_Email` class.
- **WooCommerce Integration**: The plugin integrates seamlessly with WooCommerce and adds two new endpoints to the "My Account" page:
  - Add Product
  - My Products

---

## Installation

1. Download the plugin ZIP file.
2. Go to your WordPress admin panel  Plugins  Add New  Upload Plugin.
3. Upload the ZIP file and click "Install Now."
4. Activate the plugin.
5. Ensure WooCommerce is installed and activated.

---

## Usage

1. **Add Product**:
   - Go to "My Account"  "Add Product."
   - Fill out the form and click "Save Product."
   - The product will be saved with a "Pending Review" status.

2. **My Products**:
   - Go to "My Account"  "My Products."
   - View, edit, or delete your products.
   - Use pagination to navigate through the list.

3. **Admin Notifications**:
   - Admins will receive an email when a product is created or edited.
   - The email includes links to the product and author in the admin panel.

---
## Bugs

Please note that the plugin does not have all the functionality, as its development was severely time-constrained. The following are the author's comments.
Currently not working:

- Email sending (templates are visible, the sending itself does not work and the logs are clean, this requires a lot of digging and I'm not good at mail)
- The WP Media uploader will not fully open for the WooCommerce Customer user role, it opens for higher roles, there is no time to fix it, you need to add capabilities
- The appearance of the fields for adding requires adequate layout
- The page with the user's products needs styling of the output as HTML as well as styles, mobile optimization
- Until the mail is sent, a notice has been added to the admin panel for the convenience of the admin with the number of products waiting for verification and a link to the woocommerce admin panel to make it more convenient
- There is a bug: on one of our pages, a product rating form pops up, and specifically for the first one by ID from those that the user added, and it is not possible to remove it normally, I had to disable all the commenting functionality
- Maybe something else, there is simply no time for testing and debugging everything

---

## Requirements

- WordPress 5.0 or higher
- WooCommerce 5.0 or higher

---

## Customization

- **Email Notifications**: You can enable/disable email notifications via WooCommerce  Settings  Emails.
- **Localization**: The plugin is translation-ready. Use the `wspark` text domain for localization. Default files included for en_US locale. Use LocoTranslate to create your own.

---

## Changelog

### 0.3.4
- Preview alpha release w/o some functions. Do not use on production.

---

## Support

For support, contact [Oliver Aoki](https://t.me/emostarxd).

---

## License

This plugin is licensed under the GPL-2.0+ license. See the [LICENSE](http://www.gnu.org/licenses/gpl-2.0.txt) file for details.