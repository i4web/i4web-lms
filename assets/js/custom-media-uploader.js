/* Custom Media Uploader Script
 /* Props: Built off of the WordPress Options Framework Media Uploader Script and customized to work for widgets
 *
 * Known Bug: Upload Image button stops working after the Widget is saved
 */

(function ($) {
    $(document).ready(function () {

        //Add a File
        function i4framework_add_file(event, selector) {

            //Set the variable for the frame
            var i4_frame;

            var $el = $(this);

            //Prevent the default action from occuring when you click the button
            event.preventDefault();

            // If the media frame already exists, reopen it.
            if (i4_frame) {
                i4_frame.open();
                return;
            }

            //Setup the media frame
            i4_frame = wp.media({
                // Set the title of the modal.
                title: $el.data('choose'),

                // Customize the submit button.
                button: {
                    // Set the text of the button.
                    text: $el.data('update'),
                    // Tell the button not to close the modal, since we're
                    // going to refresh the page when the image is selected.
                    close: false
                }
            });

            // When an image is selected, run a callback.
            i4_frame.on('select', function () {
                // Grab the selected attachment.
                var attachment = i4_frame.state().get('selection').first();
                i4_frame.close();

                //Insert the URL into the input box via jQuery
                selector.find('.login-logo-url').val(attachment.attributes.url);


                //	i4framework_file_bindings();
            });

            // Finally, open the modal.
            i4_frame.open();

        }

        function i4framework_file_bindings() {

            $('.upload-button').click(function (event) {

                //Find the ID of the input field closest to the upload button that was clicked
                var inputID = $(this).closest('.upload-button').attr('id');

                //Pass in the event, the selector object, and the input ID
                i4framework_add_file(event, $(this).parents('.section-' + inputID));
            });
        }

        i4framework_file_bindings();

    });

})(jQuery);
