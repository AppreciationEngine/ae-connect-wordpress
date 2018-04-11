function AeUserHandling(AeState) {

    this.AeState = AeState;

}

AeUserHandling.prototype = {

    constructor: AeUserHandling,

    /**
     * Triggers an ajax call that will send AE User data to wordpress'
     * core files to be processed by the ae-connect-wordpress plugin
     *
     * Checks for email collision resolution indicated in the Sessison storage and triggers internal ajax call
     * in order to handle the email collison
     *
     * @param {json}    userData    ae User object
     * @param {string}    type      ae signon type ('registration' || 'login')
     */
    handleWpUserRecord: function(userData, type = '') {
        var self = this;

        // If the system is waiting for collision resolution from the user:
        // make another ajax call and send through the data of the user verifying their account.
        if (self.AeState.is_email_collision_resolution) {
            userData['resolution'] = self.AeState.globalAEJS.user;
            userData['action'] = 'verify_email_colision_resolution';
            jQuery.ajax({
                method: 'POST',
                async: false,
                dataType: 'json',
                url: ajax_object.ajax_url, // Post URL
                data: userData, // Data
                context: self,
                success: self.handleEmailCollision,
                error: self.error
            })
        } else {
            jQuery.ajax({
                method: 'POST',
                async: false,
                dataType: 'json',
                url: ajax_object.ajax_url, // Post URL
                data: userData, // Data
                context: self,
                success: self.handleUserResponse,
                error: self.error
            });
        }

    },

    /**
     * AJAX success callback for handling ae WP users
     */
    handleUserResponse: function(data, textStatus, jqXHR) {
        // IF email collision
        if (data.hasOwnProperty('code')) {
            if (data.code == 1111) {
                data['return_url'] = document.location.protocol + document.location.host; // by default redirect is to homepage
                if(this.AeState.globalAEJS.flow.return) {
                    data['return_url'] = this.AeState.globalAEJS.flow.return;
                }
                sessionStorage.setItem('ae-connect-error', JSON.stringify(data));
                top.location.replace('ae-connect-sorry-there-was-a-problem/');
                return;
            }
        }

        this.AeState.wpUser = data;
        jQuery("#ae-signup").remove(); // Remove email/pass/socials form

        var self = this;

        if (jQuery("#additional-data").length == 0 || !jQuery("#additional-data").is(":visible")) {
            // If the user is not in a verification state
            // or the user verified their email reload the page
            if (self.AeState.globalSignupState !== 4) {
                self.setLoggedInState();
                if(self.AeState.globalAEJS.flow.return) {
                    document.location.href = self.AeState.globalAEJS.flow.return;
                } else {
                    document.location.href = "/";
                }
            }
        }

    },

    /**
     * Hides and displays html that the user shouldnt and should have access to
     * when logged in
     */
    setLoggedInState: function() {
        jQuery('.onpage-error').remove();
        jQuery("#additional-data").remove();
        jQuery("#loggedin").show(); //allow logout
        jQuery(".loggedin").show(); //allow logout
    },

    /**
     * Hides and displays html that the user shouldnt and should have access to
     * when logged out
     */
    setLoggedOutState: function() {
        jQuery("#loggedin").hide(); //prevent logout
        jQuery(".loggedin").hide(); //prevent logout
    },

    error: function(qXHR, textStatus, errorThrown) {

    },

    /**
     * handleError Is called when an error has been thrown by the sign-on flow
     * Parses the error code and responds with the appropriate handling action
     *
     * @param  String  aeConnectError [A stringified JSON object which contains the error code and the error maessage]
     */
    handleError:function(aeConnectError) {
        aeConnectError = JSON.parse(aeConnectError);

        var errMsg = jQuery('<p id="error-message" style="color:red;">');
        errMsg.append(aeConnectError.error);

        // Error code 13: No existing account error
        if (aeConnectError.code == 13) {
            jQuery("#ae-account-dne").show();
        }
        // Error code 1111: No existing account error
        if (aeConnectError.code == 1111) {
            var collisionEmail = this.AeState.globalAEJS.user.data.Email;
            this.AeState.is_email_collision_resolution = true;

            var ae_verify_data_tag = jQuery('<p>Hey, it looks like you already have an account with this email, <em>' +
                collisionEmail +
                '</em>.</p><p>If this is you, please click the <em>Merge my accounts</em> link below to merge the accounts.</p><a id="ae-account-merge-link" href="#" data-ae-login-link="' +
                aeConnectError.userA_service_type + '" data-ae-return="' +
                aeConnectError.return_url + '">Merge my accounts' +
                '</a>'
            );
            this.AeState.globalAEJS.settings.auth_window = true;
            jQuery("#ae-email-collision").show();
            jQuery("#ae-email-collision").append(ae_verify_data_tag);
            this.AeState.globalAEJS.trigger.attach(ae_verify_data_tag);

            var logoutLink = jQuery('<a id="ae-account-merge-not-you" href="#" data-ae-logout-link="true" data-ae-return="/">This is not me</a>');
            jQuery("#ae-email-collision").append(logoutLink);
            self.AeState.globalAEJS.trigger.attach(logoutLink);

            var userBServiceType = this.AeState.globalAEJS.user.services[0].Service;
            var serviceLink = this.AeState.globalAEJS.user.services[0].UserURL;
            var logoutofServiceLink = this.logoutOfServiceLink(userBServiceType, serviceLink);
            jQuery("#ae-email-collision").append(logoutofServiceLink);
        }

        // Error code 18: Incorrect password
        if (aeConnectError.code == 18) {
            jQuery("#ae-forgot-password").show();
            jQuery(errMsg).insertBefore("#ae-forgot-password");
        }

        else { // general handler/error message output

            jQuery(".onpage-error").append(errMsg);

        }

    },

    /**
     * @param  {[type]} res_data [description]
     */
    handleEmailCollision:function(res_data) {
        var self = this;
        var resolution = res_data;

        // If the user successfully resolved the collision by claiming the service account:
        // Merge The account that they are trying to sign in with into the existing account they just claimed
        if (resolution.success) {
            // alert("Your accounts have been merged. You will be signed in.");
            jQuery.alert("Your accounts have been merged. You will be signed in.", "Thank you", self.emailCollisionSuccess, self);

        } else {
            jQuery.alert("Sorry, that info doesn't match our records. You will be logged out.", "There was a problem", self.emailCollisionError, self)
            // alert("Sorry, that info doesn't match our records. You will be logged out");
        }

        this.AeState.is_email_collision_resolution = false; // seems like this should go at the top of the function.
    },

    emailCollisionSuccess: function(self) {
        sessionStorage.setItem('email_collision_resolved', true);

        sessionStorage.removeItem('email_collision_resolved', true);
        self.setLoggedInState();
        self.AeState.globalAEJS.settings.auth_window = self.AeState.WpAeJsSettings.auth_window;
        if(self.AeState.globalAEJS.flow.return) {
            document.location.href = self.AeState.globalAEJS.flow.return;
        } else {
            document.location.href = "/";
        }
    },

    emailCollisionError: function(self) {
        // Log the user out:
        var logoutLink = jQuery('<a href="#" data-ae-logout-link="true" >Logout</a>');
        jQuery("body").append(logoutLink);
        self.AeState.globalAEJS.trigger.attach(logoutLink);
        logoutLink.click();
    },

    logoutOfServiceLink: function(service, serviceURL) {
        if(service != 'email') {
            var logoutOfServiceLink = jQuery('<p>Want to sign in with a different account? Make sure to logout of ' +
                service +
                ' first.' +
                '</p><a id="ae-account-merge-logout-of-service"' +
                'href="' + serviceURL + '" >Sign in with a different account</a>'
            );
            return logoutOfServiceLink;
        } else {
            return logoutOfServiceLink = jQuery('<div>');
        }
    },

    jQueryAlertDialog: function(msg) {
        jQuery('<div></div>').appendTo('body')
        .html('<div><h6>Yes or No?</h6></div>')
        .dialog({
          modal: true, title: msg, zIndex: 10000, autoOpen: true,
          width: 'auto', resizable: false,
          buttons: {
              Yes: function () {
                  jQuery(this).dialog("close");
              },
              No: function () {
                  jQuery(this).dialog("close");
              }
          },
          close: function (event, ui) {
              jQuery(this).remove();
          }
        });
    },

}

if(typeof module != 'undefined') {
    module.exports = AeUserHandling;
}
