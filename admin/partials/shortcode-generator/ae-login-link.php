<div class="shortcode-config">
    <h3>AE LOGIN LINK</h3>
    <div class="shortcode-config-inner">
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
            <select class="widefat" name="social" value="register">
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
            <select class="widefat" name="social" value="hide">
                <option value="hide">HIDE sign on link when users are logged in</option>
                <option value="show">SHOW sign on link when users are logged in</option>
            </select>
        </p>
        <label for="type">Your Shortcode!</label>
        <p class="shortcode-rendered">[ae-link ]Sign In[/ae-link]</p>
    </div>
</div>
