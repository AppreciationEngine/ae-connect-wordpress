<?php

/**
 * the ae-form widget class
 *
 * @link       avery.dev.appreciationengine.com
 * @since      1.0.0
 *
 * @package    Ae_Connect
 * @subpackage Ae_Connect/public
 */
class AE_Link_Widget extends WP_Widget {

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'AE_Link_Widget',
            'description' => 'AE Link For Social Sign-on',
        );
        parent::__construct('AE_Link_Widget', 'AE Link', $widget_ops);
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance) {
        $text = $instance['text'];
        $type = $instance['type'];
        $service = $instance['service'];
        $return = $instance['return'];
        $show_after_login = $instance['show_after_login'];

        echo do_shortcode(
            '[ae-link type="' . $type . '" service="' . $service . '" return="' . $return . '" show_after_login="' . $show_after_login . '" ]' . $text . '[/ae-link]'
        );
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form($instance) {
        // outputs the options form on admin
        $text = !empty($instance['text']) ? $instance['text'] : esc_html__('Sign In', 'text_domain');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('text')); ?>">
                <?php esc_attr_e('Sign In Link Text:', 'text_domain'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('text')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('text')); ?>" type="text"
                   value="<?php echo esc_attr($text); ?>">
        </p>
        <?php
        // outputs the options form on admin
        $type = !empty($instance['type']) ? $instance['type'] : esc_html__("register", 'text_domain');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('type')); ?>">
                <?php esc_attr_e("Do you wan't your users to login or register from this link?", 'text_domain'); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('type')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('type')); ?>"
                    value="<?php echo esc_attr($type); ?>">
                <option <?php selected($type, 'register'); ?> value="register" class="widefat">
                    Register
                </option>
                <option <?php selected($type, 'login'); ?> value="login" class="widefat">
                    Login
                </option>
            </select>
        </p>
        <?php
        // outputs the service options on admin
        $active_services = $this->get_active_social_services();
        $default_socail = !empty($active_services) ? $active_services[0] : '';
        $service = !empty($instance['service']) ? $instance['service'] : esc_html__($default_socail, 'text_domain');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('service')); ?>">
                <?php esc_attr_e("Which social service do you want your users to sign in with? Make sure you have social services enabled in the AE Connect settings, or else these options will be blank.", 'text_domain'); ?>
            </label>
            <select id="<?php echo $this->get_field_id('service'); ?>" name="<?php echo $this->get_field_name('service'); ?>" class="widefat" style="width:100%;">
                <?php foreach ($active_services as $active_service) { ?>
                    <option <?php selected($service, $active_service); ?>
                        value="<?php echo $active_service; ?>"><?php echo $active_service; ?>
                    </option>
                <?php } ?>
                <!-- end of foreach -->
            </select>
        </p>
        <?php
        // outputs the options form on admin
        $return = !empty($instance['return']) ? $instance['return'] : esc_html__("", 'text_domain');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('return')); ?>">
                <?php esc_attr_e("Return URL (leave blank for default)", 'text_domain'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('return')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('return')); ?>" type="text"
                   value="<?php echo esc_attr($return); ?>">
        </p>
        <?php
        $show_after_login = !empty($instance['show_after_login']) ? $instance['show_after_login'] : esc_html__(0, 'text_domain');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('show_after_login')); ?>">
                <?php esc_attr_e("Show/hide AE Link When users are signed in", 'text_domain'); ?>
            </label>
            <select id="<?php echo $this->get_field_id('show_after_login'); ?>" name="<?php echo $this->get_field_name('show_after_login'); ?>" class="widefat" style="width:100%;">
                <option <?php selected($show_after_login, 0); ?> value=0>
                    HIDE sign on link when users are logged in
                </option>
                <option <?php selected($show_after_login, 1); ?> value=1>
                    SHOW sign on link when users are logged in
                </option>
            </select>
        </p>
        <?php
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     *
     * @return array
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['text'] = (!empty($new_instance['text']) ) ? strip_tags($new_instance['text']) : '';
        $instance['type'] = (!empty($new_instance['type']) ) ? strip_tags($new_instance['type']) : '';
        $instance['service'] = (!empty($new_instance['service']) ) ? strip_tags($new_instance['service']) : '';
        $instance['return'] = (!empty($new_instance['return']) ) ? strip_tags($new_instance['return']) : '';
        $instance['show_after_login'] = (!empty($new_instance['show_after_login']) ) ? strip_tags($new_instance['show_after_login']) : '';

        return $instance;
    }

    /**
     * returns an array of active social logins
     * @return [type] [description]
     */
    protected function get_active_social_services() {
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

}
