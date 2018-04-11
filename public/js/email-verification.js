function EmailVerification(AeState) {

    this.AeState = AeState;
}

EmailVerification.prototype = {

    constructor: EmailVerification,
    /**
     * Handles onpage email verification flow
     */
    doManualEmailVerify: function() {

        this.AeState.globalAEJS.settings['default_flow_handlers'] = true; // why does this work? we should need to pass AeJsSettings?
        this.AeState.globalSignupState = 4;

        this.purgeOldDomElements();

        this.AeState.globalAEJS.trigger.verify_email(window.location.href);

        this.attachRetryEmailVButton();
        this.attachSignInLink();

        jQuery('#ae-email-sent').show();

    },

    /**
     * @todo unit test
     * Removes #ae-signup, #ae-email-retry
     */
    purgeOldDomElements: function() {

        jQuery('#ae-signup').remove();
        jQuery("#ae-email-retry").remove();

    },

    /**
     * Attached an Email retry send verification button to the DOM
     */
    attachRetryEmailVButton() {

        var retryVEmail = jQuery(
            '<button id="ae-email-retry" onClick="globalAEJS.trigger.verify_email(window.location.href);">Retry Verification Email</button>'
        );

        jQuery('#ae-email-sent').append(retryVEmail);

    },

    /**
     * @todo unit test
     * Attached an Sign In Link to the DOM
     * @return {boolean}    link was attached
     */
    attachSignInLink: function() {

        var signin = jQuery(
            '<a href="/" id="ae-signin" >Sign In</a>'
        );

        if(!this.AeState.globalAEJS.settings.verify_email_for_login){ // user doesn't have to verify email before logging in
            jQuery('#ae-email-sent').append(signin);
            return true; // link was attached to the DOM
        }

        return false; // link was not attached to the DOM

    }

}
