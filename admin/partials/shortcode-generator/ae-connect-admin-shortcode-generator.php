<?php
/**
 * returns an array of active social logins
 * @return [type] [description]
 */
function get_active_social_services() {
    $social_logins = get_option('ae_connect_social_logins');
    $active_social_logins = array();

    // filter active social logins
    foreach ($social_logins as $login) {
        $l = get_option('ae_connect_' . $login);
        if ($l == 'on' && !in_array($l, $active_social_logins)) {
            array_push($active_social_logins, lcfirst($login));
        }
    }
    return $active_social_logins;
}
?>

<div class="shortcode-generator-page-wrapper">
    <div id="add-new-shortcodes">
        <div class="shortcode-add" id="ae-link">
            <span>AE Login Link</span>
            <button class="button button-primary">+</button>
        </div>

        <div class="shortcode-add" id="ae-window">
            <span>AE Login Window</span>
            <button class="button button-primary">+</button>
        </div>

        <div class="shortcode-add" id="ae-on-page">
            <span>AE On Page Form</span>
            <button class="button button-primary">+</button>
        </div>

        <div class="shortcode-add" id="ae-logout">
            <span>AE Logout Link</span>
            <button class="button button-primary">+</button>
        </div>
    </div>

    <div class="shortcode-config-area">
        <?php $active_services = get_active_social_services(); ?>

        <div class="ae-link-wrapper" id="link-1" style='display:none'><?php include("ae-login-link.php"); ?></div>
        <div class="ae-window-wrapper" id="window-1" style='display:none'><?php include("ae-login-window.php"); ?></div>
        <div class="ae-on-page-wrapper" id="on-page-1" style='display:none'><?php include("ae-on-page.php"); ?></div>
        <div class="ae-logout-wrapper" id="logout-1" style='display:none'><?php include("ae-logout.php"); ?></div>

    </div>
</div>
