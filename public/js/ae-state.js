function AeState(aeJS_WP_settings) {

    this.WpAeJsSettings = aeJS_WP_settings;
    this.globalAEJS = {};
    this.globalLoginType = '';
    this.state = 'none';
    this.defaultFlowHandlers = true;
    // Step 0: No user, Step 1: User signed up, Step 2: User is on extra Fields page, Step 3: User submitted or skipped extraFields
    // Step 4: Verification Email Sent, Step 5: Email verified
    this.globalSignupState = 0;
    // In the event of an onpageflow, wpVerifyEmail is set to what the client has
    // specified 'verify_email' to be in the plugin admin settings. This is
    // because aeJS.settings.verify_email is set to false when onpageFlow is true.
    this.wpVerifyEmail = null;
    this.onpageFlow = false;
    this.noReqFields = null;
    this.is_email_collision_resolution = false; // If set to true, this indicates that the system is awaiting the email collision to be resolved
    this.wpUser = {}; // WP user that is created via AJAX calls
    this.currentEvent = null; // Current AE event
}

AeState.prototype = {

    constructor: AeState,

}

if(typeof module != 'undefined') {
    module.exports = AeState;
}
