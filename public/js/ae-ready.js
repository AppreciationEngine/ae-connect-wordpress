var globalAEJS;
var AeJsSettings;
var AeState;
var AeEmailVerify;
var OpExtraFields;
var AeUserHandling;

/**
 * @todo modulating flowHandler, various helper functions
 */

function initAeConnectJS() {
    AeState = new AeState(aeJS_WP_settings); // aeJS_WP_settings is passed through the wp_enqueue_script hook
    AeUtilities = new AeUtilities();
    AeUserHandling = new AeUserHandling(AeState);
    AeEmailVerify = new EmailVerification(AeState);
    AeJsSettings = new AeSettings(AeState, AeEmailVerify);
    OpExtraFields = new OnPageExtraFields(AeState);
}

function AEJSReady(aeJS) {
    globalAEJS = aeJS;
    initAeConnectJS();
    AeUtilities.addjQueryAlertDialog();

    AeState.globalAEJS = aeJS; // pass the aeJS settings object to the AeState object

    AeUtilities.runAeErrorCheck(AeUserHandling); // checks for ae errors that occured on a previous page load

    // Initialize aeJS settings with what the AE client has set in the WP admin settings
    AeJsSettings.setAeJsSettings();

    registerDOMEventHandlers();
    addAeJsHandlers(aeJS);
    // jQuery.alert("I am a jquery alert", "Big big title", AeUserHandling.testcloseCallback);
    // console.log(JSON.stringify(globalAEJS.settings.extra_fields));
    // console.log(globalAEJS.settings.extra_fields);

}

/**
 * Add AE JS event handlers
 */
function addAeJsHandlers(aeJS) {

    aeJS.events.onFlow.addHandler(flowHandler);
    aeJS.events.onLogin.addHandler(loginHandler);
    aeJS.events.onUser.addHandler(userHandler);
    aeJS.events.onLogout.addHandler(logoutHandler);
    aeJS.events.onWindow.addHandler(windowHandler);
    // aeJS.events.onEmailVerify.addHandler(emailVerifyHandler);

}

function flowHandler(event) {

    /**
     * @todo toggle onpage flow? How will this effect email verification
     */
    if (event.step == 'authenticate') { // user is signing on on the onpage through social signon data tags
        AeState.globalSignupState = 1;
        AeJsSettings.toggleOnPageFlow(false);
    }

    if (event.step == 'error') {
        jQuery('.onpage-error').hide();
        jQuery('#error-message').remove();
        sessionStorage.setItem('ae-connect-error', JSON.stringify(event));
        AeUserHandling.handleError(JSON.stringify(event));
        jQuery('.onpage-error').show();
    }

    if (event.step == 'required-fields') {
        OpExtraFields.doExtraFields();
    }

    // Verification email needs to be sent or has been sent and needs to be verified
    if (event.step == 'verify-email') {
        // User is signing up and fields are enabled but not required AND AE is
        // handling email verification
        if (AeState.globalSignupState == 1 && AeState.wpVerifyEmail &&
            !AeUtilities.areRequiredFields(AeState.globalAEJS.settings.extra_fields) &&
            !AeUtilities.isEmpty(AeState.globalAEJS.settings.extra_fields)) {

            if(!AeUtilities.isEmpty(AeState.globalAEJS.settings.extra_fields)) { // client has specified non-required fields
                OpExtraFields.doExtraFields();
            }

        }
        // user is signing up and there are no extra fields
        else if (AeState.globalSignupState == 1 && AeState.wpVerifyEmail && AeUtilities.isEmpty(AeState.globalAEJS.settings.extra_fields)) {
            AeEmailVerify.doManualEmailVerify();
        }
        // user has filled in extra fields form AND AE is handling
        // email verification
        else if (AeState.globalSignupState == 3 && AeState.wpVerifyEmail) {
            AeEmailVerify.doManualEmailVerify();
        }
        // Client is handling  email verification
        else if ((AeState.globalSignupState == 1 || AeState.globalSignupState == 3) && !AeState.wpVerifyEmail) {
            jQuery('#ae-signup').remove();
            jQuery("#ae-email-sent").show();
        }
    }

    if (event.step == 'verify-email-ok' && AeState.globalSignupState !== 3) {
        AeState.globalSignupState = 5; // Email verified
        jQuery('#ae-email-verified').show();
    }

}

function windowHandler(event) {
}

function loginHandler(user, type, sso) {
    AeState.globalLoginType = type;
    noReqFields = !AeUtilities.areRequiredFields(AeState.globalAEJS.settings.extra_fields);

    // manually handle extra fields if required-fields won't fire.
    if (AeState.onpageFlow && type == 'registration' && noReqFields
    && AeState.globalSignupState != 3
    && !jQuery.isEmptyObject(AeState.globalAEJS.settings.extra_fields)) {

        OpExtraFields.doExtraFields();

    }

    // trigger manual email verification on condition
    if (AeState.onpageFlow && (AeState.globalSignupState == 1 || AeState.globalSignupState == 3) && type == 'registration' &&
    !AeState.globalAEJS.settings['verify_email_for_login'] && AeState.wpVerifyEmail) {
        AeJsSettings.toggleOnPageFlow(false); // allow ae modals to handle emails
        AeEmailVerify.doManualEmailVerify();

    }

    // prepare a user data object to be passed to handleWpUserRecord which makes
    // an internal ajax call
    var userData = {
        'action': 'ae_login_flow',
        security: ajax_object.ajaxnonce
    };
    userData[type] = user;
    // Process login event on the server side
    AeUserHandling.handleWpUserRecord(userData, type);
}

function userHandler(user, state) {

    if (state == 'update') {
        jQuery("#additional-data").off('submit'); //remove AE handlers
    }

    if (state == 'update') {
        // prepare a user data object to be passed to handleWpUserRecord which makes an
        // internal ajax call
        var userData = {
            'action': 'ae_update_wp',
            security: ajax_object.ajaxnonce
        };
        userData[state] = user;
        // Process login event on the server side
        AeUserHandling.handleWpUserRecord(userData);
    }
}

function logoutHandler(user) {
    AeUserHandling.setLoggedOutState();

    // POST to wordpress that the ae user has logged out
    var logoutData = {
        'action': 'ae_login_flow',
        'logout': 'logout',
        security: ajax_object.ajaxnonce
    };
    jQuery.post(
        ajax_object.ajax_url, // Post URL
        logoutData, // Data
        function(data) { // Callback
            // redirect to homepage after login
            document.location.href = "/";
        }
    );
}

/**
 * register all jQuery event handlers for the AE DOM elements
 */
function registerDOMEventHandlers() {

    registerOnpageSubmitClickHandler();
    registerSkipEFieldsClickHandler();
    registerEFieldsSubmitHandler();
    registerModalFlowStepOneClickHandler();

}

function registerOnpageSubmitClickHandler() {

    jQuery("#email-signup").submit(function() { // user clicked the submit button for onpage Email/Password form
        var btn = jQuery('#ae-onpage-submit');
        btn.attr("disabled", "disabled"); // disable button
        btn.prop('disabled', true);
        btn.css("color", "white");
        btn.css("background-color", "grey"); // grey out button

        AeJsSettings.toggleOnPageFlow(true);
        AeState.globalSignupState = 1;
    });

}

function registerSkipEFieldsClickHandler() {

    jQuery("#ae-skip-extra-fields").click(function() { // user skipped extra fields form
        AeState.globalSignupState = 3;
        jQuery("#additional-data").remove();

        if (AeState.wpVerifyEmail) {
            AeEmailVerify.doManualEmailVerify();
        } else {
            document.location.href = '/';
        }
    });

}

function registerEFieldsSubmitHandler() {

    jQuery("#additional-data-form").submit(function() { // user submitted onpage extra fields form
        AeState.globalSignupState = 3;
        jQuery("#additional-data").hide();

        if (AeState.wpVerifyEmail && !AeUtilities.areRequiredFields(AeState.globalAEJS.settings.extra_fields)) {
            AeEmailVerify.doManualEmailVerify();
        } else {
            document.location.href = '/';
        }
    });

}

function registerModalFlowStepOneClickHandler() {

    // user is entering step 1 of the modal flow
    jQuery(".ae-flow-step1").click(function() {
        AeState.globalSignupState = 1;
        AeJsSettings.toggleOnPageFlow(false);
    });

}

function emailVerifyHandler(step, data) {
    var currentURL = window.location.href;
}
