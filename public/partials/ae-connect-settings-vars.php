<?php

/*
 *
 * A list of social logins that are to be populated with key value pairs of the structure:
 * 'social_service_name' => true/false
 * true indicates the service is enabled in the backend, false indicates it is disabled
 *
 */
$admin_settings_social_logins = array();

// A list of available social logins in the database
$social_logins = get_option('ae_connect_social_logins');

// If the ae_social_logins array is set Loop through logins and determine whether the service is activated or not.
// Activates *global* JS boolean JS variables for the AEJS social login functionality
if (!empty($social_logins)) {
    foreach ($social_logins as $login) {
        // First check if the option exists, then check if the social service is enabled
        if (get_option('ae_connect_' . $login) != null && get_option('ae_connect_' . $login) == 'on') {
            $admin_settings_social_logins[$login] = true;
        } else {
            $admin_settings_social_logins[$login] = false;
        }
    }
} else {
    $social_logins = array();
}

$ae_opt_ins = get_option('ae-connect-general-opt-ins', array());
// if the user has removed the very last optin, $ae_opt_ins will be of size 1,
// and the label field will be empty (because otherwise they could not submit a
// blank label). Thus, know that the user has specified zero optins
if(sizeof($ae_opt_ins) == 1 && empty($ae_opt_ins[0]['label'])) {
    $ae_opt_ins = array();
}

?>
