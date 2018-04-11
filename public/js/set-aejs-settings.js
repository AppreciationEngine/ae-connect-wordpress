function AeSettings(AeState) {

    this.AeState = AeState;
    this.WpAeJsSettings = AeState.WpAeJsSettings;

}

AeSettings.prototype = {

    constructor: AeSettings,

    /**
     * The WP backend AE Connect settings exist in an object: aeJS_WP_settings. This object is passed in as a global
     * variable via the localize_scripts function when wordpress fires its wp_enqueu_scripts hook.
     *
     * This method needs to cache this result, and only make the call if the cache is empty
     * @type {String}
     */
    setAeJsSettings: function() {

        this.WpAeJsSettings = JSON.parse(this.WpAeJsSettings);

        if (this.WpAeJsSettings['auth_window'] != undefined) {
            this.AeState.globalAEJS.settings['auth_window'] = this.WpAeJsSettings['auth_window'];
        }

        if (this.WpAeJsSettings['extra_fields'] && this.WpAeJsSettings['extra_fields'].length !== 0) {
            this.AeState.globalAEJS.settings['extra_fields'] = this.WpAeJsSettings['extra_fields'];
        }

        this.AeState.globalAEJS.settings['extra_fields_screen'] = 'after';


        if (this.WpAeJsSettings['services']) {
            this.AeState.globalAEJS.settings['services'] = this.WpAeJsSettings['services'];
        }

        if (this.WpAeJsSettings['verify_email_for_login']) {
            this.AeState.globalAEJS.settings['verify_email_for_login'] = this.WpAeJsSettings['verify_email_for_login'];
        }

        if (this.WpAeJsSettings['verify_email']) {
            this.AeState.globalAEJS.settings['verify_email'] = this.WpAeJsSettings['verify_email'];
        }

        if (this.WpAeJsSettings['email_format'] && this.WpAeJsSettings['email_format'].length !== 0) {
            // Strip double and single quotes from email_format strings
            for (setting in this.WpAeJsSettings['email_format']) {
                this.WpAeJsSettings['email_format'].setting = setting.replace(/(['"])/g, "\\$1");
            }
            this.AeState.globalAEJS.settings['email_format'] = this.WpAeJsSettings['email_format'];
        }


        if (this.WpAeJsSettings['global_top_title']) {
            this.AeState.globalAEJS.settings.extra_info = {
                global: {
                    'top': {
                        'text': ''
                    }
                }
            };
            this.AeState.globalAEJS.settings.extra_info.global.top.text = this.WpAeJsSettings['global_top_title'].replace(/(['"])/g, "\\$1");
        }

        if (this.WpAeJsSettings['global_bottom_title']) {
            if (this.AeState.globalAEJS.settings.extra_info.hasOwnProperty('global')) {
                this.AeState.globalAEJS.settings.extra_info.global['bottom'] = {
                    'text': ''
                };
            } else {
                this.AeState.globalAEJS.settings.extra_info = {
                    global: {
                        'bottom': {
                            'text': ''
                        }
                    }
                };
            }
            this.AeState.globalAEJS.settings.extra_info.global.bottom.text = this.WpAeJsSettings['global_bottom_title'].replace(/(['"])/g, "\\$1");
        }

        if (this.WpAeJsSettings['flow_text'] && this.WpAeJsSettings['flow_text'].length !== 0) {
            this.AeState.globalAEJS.settings['flow_text'] = this.WpAeJsSettings['flow_text'];
        }

        if (this.WpAeJsSettings['hide_email_form']) {
            this.AeState.globalAEJS.settings['hide_email_form'] = this.WpAeJsSettings['hide_email_form'];
        }

        if (this.WpAeJsSettings['flow_css']) {
            this.AeState.globalAEJS.settings['flow_css'] = this.WpAeJsSettings['flow_css'];
        }

        if (this.WpAeJsSettings['optins']) {
            this.AeState.globalAEJS.settings['optins'] = this.handleInvalidEmptyOptinsArray(this.WpAeJsSettings['optins']);
        }

        if (this.WpAeJsSettings['language'])
            this.AeState.globalAEJS.settings['language'] = this.WpAeJsSettings['language'];

        // Set onpageFlow settings Overrides
        if (this.AeState.onpageFlow) {
            this.toggleOnPageFlow(true);
        }

    },

    /**
     * [toggleOnPageFlow description]
     * @param  {Boolean} isOnPageFlow [description]
     * @return {[type]}               [description]
     */
    toggleOnPageFlow: function(isOnPageFlow) {

        if (isOnPageFlow) {
            this.AeState.globalAEJS.settings['extra_fields_screen'] = 'disabled';
            this.AeState.globalAEJS.settings['auth_window'] = true;
            this.AeState.globalAEJS.settings['display_error_message'] = false;

            // sets verify_email to false or else
            // AE highjacks the flow before required fields is fired
            this.AeState.globalAEJS.settings['verify_email'] = false;

            // JS global scope variable to keep track of what the user selected in the backend
            this.AeState.wpVerifyEmail = this.WpAeJsSettings['verify_email'];
            this.AeState.onpageFlow = true;
            this.AeState.globalAEJS.settings['default_flow_handlers'] = false;

        } else {
            this.AeState.globalAEJS.settings['extra_fields_screen'] = 'after';
            this.AeState.globalAEJS.settings['auth_window'] = this.WpAeJsSettings['auth_window'];
            this.AeState.globalAEJS.settings['display_error_message'] = true;
            this.AeState.onpageFlow = false;

            // sets verify_email to false or else
            // AE highjacks the flow before required fields is fired
            this.AeState.globalAEJS.settings['verify_email'] = this.WpAeJsSettings['verify_email'];
            this.AeState.globalAEJS.settings['default_flow_handlers'] = true;
        }

    },

    /**
     * catches the case where the admin deletes all optins and then saves. This causes a blank optin to be saved as a structure of false values
     * @param  {array} optins ae optins
     * @return {array}        empty array if admin has saved a blank optin
     */
    handleInvalidEmptyOptinsArray: function(optins) {

        if(optins.length == 1) {
            if(optins[0].label == false || optins[0].label === '' || optins[0].label == undefined) {
                return []; // Admin has specified no optins in the backend
            }
        }

        return optins;

    }

}

if(typeof module != 'undefined') {
    module.exports = AeSettings;
}
