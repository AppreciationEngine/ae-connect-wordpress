<div class="shortcode-config">
    <a class="remove-generator" href="#">X</a>
    <h3>AE LOGIN WINDOW</h3>
    <div class="shortcode-config-inner">
        <p style="display: none;">
            <input name="shortcode" type="hidden" value="ae-window">
        </p>
        <p style="display: none;">
            <input name="endshortcode" type="hidden" value="[/ae-window]">
        </p>
        <p>
            <label for="text">Link Text</label>
            <input class="widefat" name="text" type="text" value="Sign In">
        </p>
        <p>
            <label for="type">Type</label>
            <select class="widefat" name="type" value="register">
                <option value="register">Register</option>
                <option value="login">Login</option>
            </select>
        </p>
        <p>
            <label for="type">Social Media Service</label>
            <select class="widefat" name="service" value="">
                <?php foreach (get_active_social_services() as $active_service) { ?>
                    <option
                        value="<?php echo $active_service; ?>"><?php echo $active_service; ?>
                    </option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="text">Return URL</label>
            <input class="widefat" name="return" type="text">
        </p>
        <p>
            <label for="type">Visibility</label>
            <select class="widefat" name="show_after_login" value="0">
                <option value="0">HIDE sign on link when users are logged in</option>
                <option value="1">SHOW sign on link when users are logged in</option>
            </select>
        </p>
        <button class="button button-primary ae-generate">Submit</button>
        <p class="shortcode-rendered">[ae-window ]Sign In[/ae-window]</p>
    </div>
</div>
