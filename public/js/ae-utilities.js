function AeUtilities() {

}

AeUtilities.prototype = {

    constructor: AeUtilities,

    /**
     * Code snippit taken from https://stackoverflow.com/questions/679915/how-do-i-test-for-an-empty-javascript-object
     */
    isEmpty: function(obj) {
        for(var prop in obj) {
            if(obj.hasOwnProperty(prop))
                return false;
        }

        return JSON.stringify(obj) === JSON.stringify({});
    },

    /**
     * runs checks for ae errors that occured on a previous page load and calls appropriate error handlers
     * removes error from storage
     * @return {[type]} [description]
     */
    runAeErrorCheck: function(UserHandler) {

        // check if there are any errors set in the session storage
        aeConnectError = sessionStorage.getItem('ae-connect-error');
        if (aeConnectError) {
            // Handle the error
            UserHandler.handleError(aeConnectError);
            // remember the original return destination
            sessionStorage.setItem('ae-email-collision-return-url', aeConnectError.return_url)
            // Clear the error code in the session storage
            sessionStorage.removeItem('ae-connect-error');
        }

    },

    /**
     * Determines if there are required fields in the settings.extra_fields object
     * @param  {JSON} extra_fields    settings.extra_fields
     * @return {boolean}              existence of extra fields
     */
    areRequiredFields: function(extra_fields) {
        var result = false;
        for (var field in extra_fields) {
            currentField = extra_fields[field];
            if( currentField.hasOwnProperty('required') ) {
                if(currentField.required) {
                    return currentField.required;
                }
            }

        }
        return false;
    },

    addjQueryAlertDialog: function() {
        jQuery.extend({
            alert: function (message, title, closeCallback, context) {
                jQuery("<div></div>").dialog( {
                    autoOpen: true,
                    buttons: {
                        "Ok": function () {
                            closeCallback(context);
                            jQuery(this).dialog("close");
                        }
                    },
                    close: function () {
                        jQuery(this).remove();
                    },
                    resizable: false,
                    title: title,
                    modal: true,
                }).text(message);
            }
        });
    },

}

if(typeof module != 'undefined') {
    module.exports = AeUtilities;
}
