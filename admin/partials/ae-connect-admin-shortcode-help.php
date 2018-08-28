<div class="shortcode-help">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <p>
        <!--StartFragment-->The frontend magic of AE Connect is powered by shortcodes. This document is a comprehensive detailing of AE Connect’s shortcodes.<br><br></p>
    <h4>[ae-link]</h4>
    <p>This shortcode corresponds to the data-ae-register-link and &nbsp;data-ae-login-link data tags data tag. More info about AE data tags can be found <a target="_blank" href="https://support.appreciationengine.com/article/OufSxd7tBj-ae-js-data-tags-reference">here</a>.</p>
    <h5>Attributes:</h5>
    <ul>
        <li><h5>type</h5>
            <ul>
                <li>Determines whether the link will be a login link or a register link </li>
                <li>Can either be set to register or login</li>
                <li>default is register</li>
                <li>Ex) [ae-link type="login"]Login With Spotify[/ae-link]</li>
            </ul>
        </li>
        <li><h5>service</h5>
            <ul>
                <li>The service that users may sign in with</li>
                <li>default is email</li>
                <li>Ex) [ae-link service="spotify"]Login With Spotify[/ae-link]</li>
            </ul>
        </li>
        <li><h5>return (might not be viable)</h5>
            <ul>
                <li>the url that users will be returned to after signing in.</li>
                <li>default is the client’s homepage</li>
                <li>Ex) [ae-link service="spotify" return="https://example.com/welcome"]Login With Spotify[/ae-link]</li>
            </ul>
        </li>
    </ul>
    <h4>[ae-window]</h4>
    <p>This shortcode corresponds to the data-ae-register-window and &nbsp;data-ae-login-window data tags. More info about Appreciation Engine data tags can be found <a target="_blank" href="https://support.appreciationengine.com/article/OufSxd7tBj-ae-js-data-tags-reference">here</a>.</p>
    <h5>Attributes:</h5>
    <ul>
        <li><h5>type</h5>
            <ul>
                <li>Determines whether the link will be a login link or a register link </li>
                <li>Can either be set to register or login</li>
                <li>default is register</li>
                <li>Ex) [ae-window type="register"]Login With Spotify[/ae-window]</li>
            </ul>
        </li>
        <li><h5>return (might not be viable)</h5>
            <ul>
                <li>the url that users will be returned to after signing in.</li>
                <li>default is the client’s homepage</li>
                <li>Ex) [ae-window return="https://example.com/welcome"]Login With Spotify[/ae-window]</li>
            </ul>
        </li>
    </ul>
    <h4>[ae-form]</h4>
    <p>This shortcode displays an email password form along with all social logins that the client has enabled at AE Connect settings -&gt; General -&gt; Social </p>
    <p>This shortcode accepts no arguments.</p>
    <h4>[ae-logout]</h4>
    <p>This shortcode displays a logout link for users to logout of their WP/AE session. This shortcode corresponds to the data-ae-logout-link data tag. More info about Appreciation Engine data tags can be found <a target="_blank" href="https://support.appreciationengine.com/article/OufSxd7tBj-ae-js-data-tags-reference">here</a>.</p>
    <h5>Attributes:</h5>
    <ul>
        <li><h5>return (might not be viable)</h5>
            <ul>
                <li>the url that users will be returned to after signing in.</li>
                <li>default is the client’s homepage</li>
                <li>Ex) [ae-window return="https://example.com/welcome"]Login With Spotify[/ae-window]</li>
            </ul>
        </li>
    </ul>
    <h4>[ae-forgot-password]</h4>
    <p>This shortcode corresponds to verify_reset_password trigger. More info about Appreciation Engine triggers can be found <a target="_blank" href="https://support.appreciationengine.com/article/gCzkzKV9LF-ae-js-methods-triggers-reference">here</a>.</p>
    <p>This shortcode accepts no arguments.</p>

    <h3>Attributes:</h3>
    <ul>
        <li><h5>type</h5><p>Shortcodes that have this attribute:</p>
            <ul>
                <li>[ae-link]</li>
                <li>[ae-window]</li>
            </ul>
            <p><b>arguments:</b> </p>
            <ul>
                <li>register</li>
                <li>login</li>
            </ul>
        </li>
    </ul>

    <ul>
        <li><h5>service</h5><p>Shortcodes that have this attribute:</p>
            <ul>
                <li>[ae-link]</li>
                <li>[ae-window]</li>
            </ul>
            <p><b>arguments:</b> (any service that the client has activated) </p>
            <ul>
                <li>facebook</li>
                <li>deezer</li>
                <li>spotify</li>
                <li>twitter</li>
                <li>google</li>
                <li>youtube</li>
                <li>instagram</li>
            </ul>
            <p>(+more)</p>
        </li>
    </ul>

    <ul>
        <li><h5>return</h5><p>Shortcodes that have this attribute:</p>
            <ul>
                <li>[ae-link]</li>
                <li>[ae-window]</li>
                <li>[ae-logout]</li>
            </ul>
            <p><b>arguments:</b> </p>
            <ul>
                <li>Any valid URL</li>
            </ul>
        </li>
    </ul>

</div>
