(function ($) {

    $(window).load(function () {
        const aeLinkAdder = jQuery("#ae-link button");
        const aeWindowAdder = jQuery("#ae-window button");
        const aeOnPageAdder = jQuery("#ae-on-page button");
        const aeLogoutAdder = jQuery("#ae-logout button");
        const generate = jQuery(".ae-generate");

        aeLinkAdder.click({type: "link"}, addGenerator);
        aeWindowAdder.click({type: "window"}, addGenerator);
        aeOnPageAdder.click({type: "on-page"}, addGenerator);
        aeLogoutAdder.click({type: "logout"}, addGenerator);

        generate.click(generateShortcode);

    });

})(jQuery);

function addGenerator(event) {
    // alert("Adding an AE Link!");
    const type = event.data.type;
    const originalLink = jQuery("#"+type+"-1");
    const aeLinkClassSel = ".ae-login-link-wrapper"

    if(originalLink.css('display') == 'none') {
        originalLink.show();
    }
}

function generateShortcode(event) {
    // var btn = jQuery(event.target);
    const shortCodeConfig = jQuery(event.target).parent(".shortcode-config-inner");
    // console.log(shortCodeConfig);
    var data = {};
    shortCodeConfig.children('p').each(function () {
        let input = this.querySelector('input');
        let select = this.querySelector('select');
        if(input) {
            data[input.name] = input.value;
            // console.log(input.name);
            // console.log(input.value);
        }
        if(select) {
            data[select.name] = select.value;
            // console.log(select.name);
            // console.log(select.value);
        }
    });
    console.log(data);

    setShortcodeText(shortCodeConfig, data);

}

function setShortcodeText(shortCodeConfig, data) {
    var endshortcode, text, shortcode;
    endshortcode = text = shortcode = "";

    if('text' in data) {
        text = data.text;
        delete data.text;
    }

    if('endshortcode' in data) {
        endshortcode = data.endshortcode;
        delete data.endshortcode;
    }

    shortcode = data.shortcode;
    delete data.shortcode;

    var args = "";
    for (var key in data) {
        args += key + '="' + data[key] + '" ';
    }
    var shortcode = "["+shortcode+" "+args+"]"+text+endshortcode;
    shortCodeConfig.find("p.shortcode-rendered").html(shortcode);
    console.log(shortcode);
}
