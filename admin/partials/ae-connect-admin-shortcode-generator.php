<?php ?>

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
                        <option value="facebook">Facebook</option>
                        <option value="spotify">Spotify</option>
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

        <div class="shortcode-config">
            <h3>AE LOGIN WINDOW</h3>
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
                        <option value="facebook">Facebook</option>
                        <option value="spotify">Spotify</option>
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
                <p class="shortcode-rendered">[ae-window ]Sign In[/ae-window]</p>
            </div>
        </div>

        <div class="shortcode-config">
            <h3>AE ON PAGE FORM</h3>
            <div class="shortcode-config-inner">
                <label for="type">Your Shortcode!</label>
                <p class="shortcode-rendered">[ae-form]</p>
            </div>
        </div>

        <div class="shortcode-config">
            <h3>AE LOGOUT LINK</h3>
            <div class="shortcode-config-inner">
                <p>
                    <label for="text">Link Text</label>
                    <input class="widefat" name="text" type="text" value="Sign In">
                </p>
                <p>
                    <label for="text">Return URL</label>
                    <input class="widefat" name="return" type="text">
                </p>
                <label for="type">Your Shortcode!</label>
                <p class="shortcode-rendered">[ae-form]</p>
            </div>
        </div>
    </div>
</div>
