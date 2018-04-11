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
class AE_Logout_Widget extends WP_Widget {

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'AE_Logout_Widget',
            'description' => 'AE Logout Link',
        );
        parent::__construct('AE_Logout_Widget', 'AE Logout Link', $widget_ops);
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance) {
        echo do_shortcode('[ae-logout return="' . $instance['return'] . '"]' . $instance['text'] . '[/ae-logout]');
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form($instance) {
        // outputs the options form on admin
        $text = !empty($instance['text']) ? $instance['text'] : esc_html__('Logout', 'text_domain');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('text')); ?>">
                <?php esc_attr_e('Logout Link Text:', 'text_domain'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('text')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('text')); ?>" type="text"
                   value="<?php echo esc_attr($text); ?>">
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
        $instance['return'] = (!empty($new_instance['return']) ) ? strip_tags($new_instance['return']) : '';

        return $instance;
    }

}
