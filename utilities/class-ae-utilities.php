<?php
/**
 * A set of custom utility functions for the plugin
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/utilities
 */
class Ae_Utilities {

    /**
     * @todo add query parameter handling
     * adds scheme to url if none is found.
     * If no scheme is found, returns the given url
     * If relative path (first character of the string is a "/") returns the given url
     *
     * *NOTE* this function does not convert http to https or visa versa.
     *
     * @param string $url       url
     * @param boolean $secure   determines whether the url should be returned
     *                          http or https
     * @return string           a URL with a valid scheme (http || https)
     */
    public function append_scheme_to_url($url, $secure) {
        $components = parse_url($url);
        if(array_key_exists('scheme', $components) || strlen($url) < 1 || $url[0] == '/') { // if the url already has a scheme
            return $url;
        } else {
            $host = array_key_exists('host', $components) ? $components['host'] : '';
            $path = array_key_exists('path', $components) ? $components['path'] : '';
            $query = array_key_exists('query', $components) ? $components['query'] : '';
            if($secure) { // caller specified a secure scheme
                $new_url = 'https://' . $host . $path;
            } else { // caller specified a insecure scheme
                $new_url = 'http://' . $host . $path;
            }
            $new_url = !empty($query) ? $new_url . '?' . $query : $new_url;
            return $new_url;
        }

    }

    /**
     * if no scheme is present in $desired_scheme_url, then the $url_to_be_changed scheme is stripped
     *
     * @param  string   $desired_scheme_url     a url with the desired scheme
     * @param  string   $url_to_be_changed      a url that will be changed to match $desired_scheme_url's scheme
     * @return string   the url with a changed scheme
     */
    public function match_and_change_scheme($desired_scheme_url, $url_to_be_changed) {

        if(empty($url_to_be_changed)) {
            return $url_to_be_changed;
        }

        $desired_scheme = parse_url($desired_scheme_url, PHP_URL_SCHEME) ? parse_url($desired_scheme_url, PHP_URL_SCHEME) . '://' : null;

        $url_to_be_changed_componenents = parse_url($url_to_be_changed);

        $host = array_key_exists('host', $url_to_be_changed_componenents) ? $url_to_be_changed_componenents['host'] : '';
        $path = array_key_exists('path', $url_to_be_changed_componenents) ? $url_to_be_changed_componenents['path'] : '';
        $query = array_key_exists('query', $url_to_be_changed_componenents) ? '?' . $url_to_be_changed_componenents['query'] : '';

        $changed_url = $desired_scheme . $host . $path . $query;
        return $changed_url;

    }

    public function flow_css_is_http($flow_css_url) {
        $flow_css_scheme = parse_url($flow_css_url, PHP_URL_SCHEME);
        if($flow_css_scheme == 'http') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * If $flow_css_url has an http scheme, $ae_js_url's scheme will be too changed to match http
     * otherwise, $ae_js_url is left untouched
     *
     * @param  string   $flow_css_url   ae flow_css URL
     * @param  string   $ae_js_url      ae JS framework URL
     * @return string                   ae JS framework URL
     */
    public function change_aejs_scheme_based_on_flow_css($flow_css_url, $ae_js_url) {
        $flow_css_is_http = $this->flow_css_is_http($flow_css_url);
        if( $flow_css_is_http ) {
            $aejs_url_changed = $this->match_and_change_scheme($flow_css_url, $ae_js_url);
            return $aejs_url_changed;
        } else {
            return $ae_js_url;
        }
    }

    /**
     * Excepts an option, loads the corresponding theme based upon the option
     * @param  int $option      integer representing which stylesheet is to be loaded
     * @return void
     */
    public function load_ae_form_style($option, $version) {

        switch($option) {
            case 0:
                return;
            case 1:
                wp_enqueue_style('ae-signup-theme-colourful.css', plugin_dir_url(__DIR__) . 'public/css/ae-signup-theme-colourful.css', array(), $version, 'all');
                return;
            case 2:
                wp_enqueue_style('ae-signup-theme-light.css', plugin_dir_url(__DIR__) . 'public/css/ae-signup-theme-light.css', array(), $version, 'all');
                return;
            case 3:
                wp_enqueue_style('ae-signup-theme-dark.css', plugin_dir_url(__DIR__) . 'public/css/ae-signup-theme-dark.css', array(), $version, 'all');
                return;
            default:
                return;
        }

    }

    /**
     * If the client has defined a custom flow_css URL, it will be loaded into flow_css
     * If the client has not defined a custom flow_css URL and also selected a css theme, the theme will be
     * loaded into flow_css
     *
     * @return string  flow_css URL
     */
    public function get_selected_flow_css_url() {
        $flow_css_url = get_option('ae_connect_flow_css');
        $theme_css_option = get_option('ae_connect_flow_css_themes');
        if( empty($flow_css_url) && ( $theme_css_option != 0 || empty($theme_css_option) ) ) {

            return $this->get_theme_stylesheet($theme_css_option);

        }
        return $flow_css_url;
    }

    /**
     * Excepts an option, returns the corresponding theme based upon the option
     * @param  int $option      integer representing which stylesheet is to be returned
     * @return string stylesheet url (empty string if case:0 or invalid option)
     */
    public function get_theme_stylesheet($option) {
        $plugin_dir = plugin_dir_url(__DIR__);
        $theme1 = $plugin_dir . 'public/css/ae-signup-theme-colourful.css';
        $theme2 = $plugin_dir . 'public/css/ae-signup-theme-light.css';
        $theme3 = $plugin_dir . 'public/css/ae-signup-theme-dark.css';
        switch($option) {
            case 0:
                return '';
            case 1:
                return $theme1;
            case 2:
                return $theme2;
            case 3:
                return $theme3;
            default:
                return '';
        }

    }

}
