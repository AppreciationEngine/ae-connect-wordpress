function OnPageExtraFields(AeState, AeEmailVerify) {
    this.AeState = AeState;
    this.AeEmailVerify = AeEmailVerify;
}

OnPageExtraFields.prototype = {

    constructor: OnPageExtraFields,

    /**
     * Processes logic for the onpage extra fields form
     *
     * Adds required argument to each input and select on the customization
     * removes redundant fields that the user already has
     */
    addExtraFieldsBrowserLogic: function() {

        var self = this; // Define self as the OnPageExtraFields object as to avoid collisions with the jQuery this
        var isRequired;

        // If no extra fields are enabled, remove the form and return
        if( self.extraFieldsAreEmpty(self) ) {
            return;
        }

        self.doAdditionalDataView();
        self.enableJqueryDatepicker();

        // reformat user data keys to lowercase for parsing by field name
        userDataLc = self.objectKeysToLowerCase(self.AeState.globalAEJS.user.data);

        var numExtraField = Object.keys(self.AeState.globalAEJS.settings.extra_fields).length;
        numExtraField = self.removeRedundantFields('input', userDataLc, numExtraField);
        self.applyRequiredAtts(self, 'input');

        // If all of the extra fields were removed because the user has them all
        // skip extra fields and return from addExtraFieldsBrowserLogic
        if (numExtraField <= 0) {
            self.skipExtraFields(self);
            return;
        }

        numExtraField = self.removeRedundantFields('select', userDataLc);
        self.applyRequiredAtts(self, 'select');

    },

    /**
     * function that handles extra Fields flow
     */
    doExtraFields: function() {
        jQuery('.onpage-error').remove(); // Removes onpage error messages from the screen if any occured
        jQuery('#ae-signup').remove(); // Removes initial signup form from the page
        this.AeState.globalSignupState = 2; // User entered extra fields form

        this.addExtraFieldsBrowserLogic();
    },

    /**
     * @todo unit test
     * removes fields from additional form that already are present in the User data object
     * @param  {userDataLc}             userDataLc            lowercase keys extra fields data
     * @param  {string}                 field_type            field type
     * @param  {int}                    numExtraField         numExtraField
     *
     * @return {int}                    numExtraField         Number of remaining extra fields
     */
    removeRedundantFields: function(field_type, userDataLc, numExtraField) {

        // Remove any field_type from the form that the AE user already has filled out at a previous date
        // set required selects as required
        jQuery('#additional-data-form').children(field_type).each(function() {
            // If the user already has that extra field, remove it from the form:
            // remove that field and continue to the next select tag
            if (userDataLc[this.name]) {
                jQuery('.' + this.name).remove();
                jQuery(this).remove();
                numExtraField -= 1;
                return true; // continue
            }
        });
        return numExtraField;

    },

    /**
     * @todo unit test
     * applies the required attribute to any required fields on the onpage additional data form
     * @param  {OnPageExtraFields}      self                  enclosing class instance
     * @param  {string}                 field_type            field type
     */
    applyRequiredAtts: function(self, field_type) {

        // Remove any field_type from the form that the AE user already has filled out at a previous date
        // set required selects as required
        jQuery('#additional-data-form').children(field_type).each(function() {

            // Check to see if this is a valid field:
            if (!self.AeState.globalAEJS.settings.extra_fields.hasOwnProperty(this.name)) {
                return true; // invalid field, continue to next input field
            }

            isRequired = self.AeState.globalAEJS.settings.extra_fields[this.name].required;
            if (isRequired) {
                jQuery(this).attr("required", 'required');
                jQuery(this).addClass("form-control");
            }
        });

    },

    skipExtraFields: function(self) {

        self.AeState.globalSignupState = 3;
        jQuery("#additional-data").hide();
        if (self.AeState.wpVerifyEmail) {
            self.AeEmailVerify.doManualEmailVerify();
        }

    },

    /**
     * @todo unit test
     * shows Additional data form, hides signup form
     */
    doAdditionalDataView: function() {

        jQuery("#additional-data").show();
        jQuery("#ae-signup").remove();

    },

    /**
     * @todo (maybe) unit test
     * If the browser doesn't support the native HTML5 date picker,
     * replace it with a jQuery date picker
     */
    enableJqueryDatepicker: function() {

        jQuery('input[type=date]').datepicker({
            // Consistent format with the HTML5 picker
            dateFormat: 'yy-mm-dd'
        });

    },

    /**
     * @todo unit test
     * returns boolean extra fields are empty
     * @return {boolean} [description]
     */
    extraFieldsAreEmpty: function(self) {

        if (jQuery.isEmptyObject(self.AeState.globalAEJS.settings.extra_fields)) {
            jQuery("#additional-data").remove();
            return true;
        }

    },

    /**
     * @todo unit test
     * Custom function which takes in an object and converts its keys to lowercase
     * @return Object
     */
    objectKeysToLowerCase: function(ob) {

        var keys = Object.keys(ob);
        var n = keys.length;

        obLowerCaseKeys = {};
        while (n--) {
            key = keys[n];
            obLowerCaseKeys[key.toLowerCase()] = ob[key];
        }

        return obLowerCaseKeys;
    }

}

if(typeof module != 'undefined') {
    module.exports = OnPageExtraFields;
}
