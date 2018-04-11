(function ($) {

    $(window).load(function () {
        // Redirects the user to the general settings page if the framework
        // was successfully initialized
        status = $('#init_status').text();
        if (status === 'OK') {
            nextStepButton = $('<button>').addClass('button button-primary');
            nextStepLink = $('<a>').attr('href', 'admin.php?page=ae-connect-general-settings');
            nextStepLink.text('Next Step');
            nextStepLink.css("text-decoration", "none");
            nextStepLink.css("color", "white");
            nextStepButton.append(nextStepLink);
            $('.submit').append(nextStepButton);
        }

        // Logic that only shows/hides the required field check boxes if the
        // extra field they are attached to changed
        $('input:checkbox').change(
                function () {
                    required_extra_field_id = '#_required' + $(this).attr('name'); // ae_connect_optionname
                    required_extra_field_id = required_extra_field_id.replace("ae_connect", "");

                    if ($(this).is(':checked')) {
                        $(required_extra_field_id).show();
                    } else {
                        $(required_extra_field_id).hide();
                    }
                });

        // Logic that only shows the required field check boxes if the
        // extra field they are attached to checked (needed for page reloads)
        $('.enable').each(function (i) {
            if ($(this).is(":checked")) {
                required_extra_field_id = '#_required' + $(this).attr('name'); // ae_connect_optionname
                required_extra_field_id = required_extra_field_id.replace("ae_connect", "");
                $(required_extra_field_id).show();
            }
        });


        // Logic that hides _disable_user_log_in_session field if
        //_disable_local_wp_user is checked
        if ($('#ae_connect_disable_local_wp_user').is(":checked")) {
            $('#ae_connect_disable_user_log_in_session').hide();
        }

        if ($("input[name='ae_connect_state']").is(":checked")) {
            $("input[name='ae_connect_country']").prop('checked', true);
        }
    });

})(jQuery);

/**
 * Resources used to write this function:
 *  https://wpalchemists.com/2014/08/wordpress-settings-api-repeating-fields/
 *
 */
function incrementOptInIndex(optIn) {
    var tags = optIn.find('input'), idx = optIn.index() - 1;

    tags.each(function () {

        var attrVal = jQuery(this).attr('name');
        if (attrVal) {
            jQuery(this).attr('name', attrVal.replace(/\[\d+\]\[/, '\[' + (idx + 1) + '\]\['))
        }

    })

}

/**
 * Resources used to write this function:
 *  https://wpalchemists.com/2014/08/wordpress-settings-api-repeating-fields/
 *
 */
function addNewOptIn() {

    var optIn = jQuery(".opt-in").last();
    var clonedOptIn = optIn.clone(true);
    // optin
    // jQuery("#opt-ins-table").append(optin.html());
    clonedOptIn.insertAfter(optIn);
    clonedOptIn.find("input").val("");
    clonedOptIn.find("input:checkbox").attr("checked", false);
    incrementOptInIndex(clonedOptIn);

    return false;
}

function removeParent(element) {
    var lastOptIn = jQuery(".opt-in").last();
    // if there are more than 1 opt ins left on the page
    if (lastOptIn.index() >= 1) { // remove the opt in
        var jQElement = jQuery(element);
        jQElement.parents('tbody.opt-in').remove();
    } else { // clear the values to avoid losing all of the opt ins on the page
        lastOptIn.find("input").val("");
        lastOptIn.find("input").removeAttr('required');//allow to be saved as blank if want to remove initial value
        lastOptIn.find("input:checkbox").attr("checked", false);
        lastOptIn.find("input:checkbox").removeAttr('required');
    }

    return false;

}
