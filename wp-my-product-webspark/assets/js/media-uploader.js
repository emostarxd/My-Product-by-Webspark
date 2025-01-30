jQuery(document).ready(function ($) {  
    let mediaUploader;  
  
    $('#wsp_upload_image_button').on('click', function (e) {  
        e.preventDefault();  
  
        if (mediaUploader) {  
            mediaUploader.open();  
            return;  
        }  
  
        mediaUploader = wp.media({  
            title: 'Select Product Image',  
            button: { text: 'Use this Image' },  
            library: { type: 'image' },  
            multiple: false,  
        });  
  
        mediaUploader.on('select', function () {  
            const attachment = mediaUploader.state().get('selection').first().toJSON();  
            $('#wsp_product_image_id').val(attachment.id);  
            $('#wsp_image_preview').html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto;">');  
        });  
  
        mediaUploader.open();  
    });  
});  
