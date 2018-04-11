<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<!--Define JS variables for settings specified in the AE Connect Settings fields-->
<!-- this needs to get cleaned up -->
<?php require 'ae-connect-settings-vars.php'; ?>
<?php $WP_LOGOUT_URL = wp_logout_url(home_url()) ?>
<?php $WP_HOME_URL= home_url() ?>

<div style="display:none;" class="onpage-error">
    <div style="display:none;" id="ae-forgot-password">
        <?php echo do_shortcode('[ae-forgot-password]'); ?>
        <p style="color:green;">Create a new account?</p>
    </div>
    <div style="display:none;" id="ae-account-dne">
        <p>
            Please either sign in with a different account
            or Create a new account
        </p>
    </div>
</div>

<!--turn off extra fields screen-->
<div id="ae-signup">
    <div>Social Signup</div>
    <div id="social-logins">
        <?php
        /*
         * Loop through active social logins
         * And if they are activated in the wordpress backend
         * add an ae data tag link specifying that social login
         *
         * $social_logins is an array that is included by /ae-connect-settings-vars.php
         */
        foreach ($social_logins as $login) {
            if ($admin_settings_social_logins[$login]) {
                echo '<a href="#" data-ae-register-link="' . lcfirst($login)
                . '" data-ae-return="' . $WP_HOME_URL . '"
                class="button button-primary" id="' . 'ae-' . lcfirst($login) . '">
                <span class="service-label">' . $login . '</span></a> ';
            }
        }
        ?>
    </div>

    <!-- render email form only if no_email is not set -->
    <?php if (empty(get_option('ae_connect_no_email'))): ?>
        <form id="email-signup" data-ae-type="register" data-ae-register-form="email" method="post">
            <p>
                <label for="email">Email address</label>
                <input size="20" type="email" required="required" id="ae-email" name="email">
                <small id="emailHelp">We'll never share your email with anyone else.</small>
            </p>
            <p>
                <label for="password">Password</label>
                <input size="20" type="password" required="required" id="password" name="password">
            </p>

            <?php
            if(sizeof($ae_opt_ins) > 0) {
                echo '<p>Subscribe to these Mailing list(s)</p>';
                // iterable $ae_opt_ins comes from the ae-connect-settings-vars.php
                foreach ($ae_opt_ins as $opt_in) {
                    $checked = $opt_in['checked'] ? 'checked' : '';

                    echo '<input ' . $checked . ' type="checkbox" data-ae-optin="' . $opt_in['label'] . '"
                    data-ae-optin-segmentid="' . $opt_in['segmentid'] . '"><br/>';
                }
            }

            ?>

            <input type="submit" name="wp-submit" id="ae-onpage-submit" class="button button-primary" value="Sign In">
        </form>
    <?php endif; ?>

</div>


<!-- Extra Fields for the user to enter when they login-->
<div id="additional-data" style="display: none;">

    <div>Additional Information Required</div>
    <div class="panel-body">
        <form id="additional-data-form" data-ae-register-form="email" method="post">
            <?php include_once 'ae-connect-extra-fields-display.php'; ?>

            <input type="submit" name="wp-submit" id="ae-extra-fields-submit" class="button button-primary" value="Submit">
        </form>
    </div>

</div>
<div id="ae-email-verified" style="display: none;">
    Email Verified!
</div>

<div class="loggedin" style="display:none;">
    <a href="#" class="button button-primary" data-ae-logout-link="true">Logout</a>
</div>

<div id="loggedin" style="display: none;">
    <a href="#" data-ae-logout-link="true">LOGOUT</a>
</div>

<div id="ae-email-sent" style="display: none;">
    Thank you for signing up, a Verification email is on its way
</div>

<!-- Loader content -->
<?php $loader_img = plugin_dir_path(__FILE__) . 'img/small-circle-loader.gif' ?>
<div id="loader-content" style="display:none;">
    <img src="<?php echo $loader_img; ?>" alt="loader-img"/>
</div>
