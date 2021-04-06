jQuery(document).ready(function ($) {

    // Using this var to track which item on a page full of multiple upload buttons is currently being uploaded.
    var current_novelist_upload = 0;
    var image_size = 'medium';

    // Define uploader settings
    var frame = wp.media({
        title: NOVELIST_MEDIA.text_title,
        multiple: false,
        library: {type: 'image'},
        button: {text: NOVELIST_MEDIA.text_button}
    });

    // Call this from the upload button to initiate the upload frame.
    novelist_open_uploader = function (id, size) {
        current_novelist_upload = id;
        image_size = (typeof size !== 'undefined') ? size : 'medium';
        frame.open();
        return false;
    };

    // Handle results from media manager.
    frame.on('close', function () {
        var attachment = frame.state().get('selection').first().toJSON();
        novelist_render_image(attachment);
    });

    // Output selected image HTML.
    // This part could be totally rewritten for your own purposes to process the results!
    novelist_render_image = function (attachment) {
        var url = attachment.url;
        var width = attachment.width;
        var height = attachment.height;

        switch (image_size) {

            case 'thumbnail' :
                if (typeof attachment.sizes.thumbnail !== 'undefined') {
                    url = attachment.sizes.thumbnail.url;
                    width = attachment.sizes.thumbnail.width;
                    height = attachment.sizes.thumbnail.height;
                }
                break;

            case 'medium' :
                if (typeof attachment.sizes.medium !== 'undefined') {
                    url = attachment.sizes.medium.url;
                    width = attachment.sizes.medium.width;
                    height = attachment.sizes.medium.height;
                }
                break;

            case 'large' :
                if (typeof attachment.sizes.large !== 'undefined') {
                    url = attachment.sizes.large.url;
                    width = attachment.sizes.large.width;
                    height = attachment.sizes.large.height;
                }
                break;

        }

        var imageElem = $("#" + current_novelist_upload + "_image");

        // Remove all attributes.
        if (typeof imageElem.attributes != 'undefined') {
            while (imageElem.attributes.length > 0) {
                elem.removeAttribute(elem.attributes[0].name);
            }
        }

        imageElem.attr('src', url).attr('width', width).attr('height', height).show();
        $("#" + current_novelist_upload).val(attachment.id);
        $("#" + current_novelist_upload + "_remove").show();
    };

    novelist_clear_uploader = function (current_novelist_upload) {
        $("#" + current_novelist_upload + "_image").attr('src', '').hide();
        $("#" + current_novelist_upload).val('');
        $("#" + current_novelist_upload + "_remove").hide();
        return false;
    }

});