(function ($) {

    $(window).load(function () {
        const aeLinkAdder = jQuery("#ae-link button");
        const aeWindowAdder = jQuery("#ae-window button");
        const aeOnPageAdder = jQuery("#ae-on-page button");
        const aeLogoutAdder = jQuery("#ae-logout button");
        const generate = jQuery(".ae-generate");
        const remove = jQuery(".remove-generator");

        aeLinkAdder.click({type: "link"}, addGenerator);
        aeWindowAdder.click({type: "window"}, addGenerator);
        aeOnPageAdder.click({type: "on-page"}, addGenerator);
        aeLogoutAdder.click({type: "logout"}, addGenerator);

        generate.click(generateShortcode);
        remove.click(removeGenerator);

    });

})(jQuery);

function addGenerator(event) {
    // alert("Adding an AE Link!");
    const type = event.data.type;
    const shortcodeArea = jQuery(".shortcode-config-area");
    const OGShortcodeGen = jQuery("#"+type+"-1");
    const wrapperClassName = "ae-"+type+"-wrapper";
    const newShortcodeGen = OGShortcodeGen.clone(true);

    var generatorsOfType = document.getElementsByClassName(wrapperClassName);
    console.log(generatorsOfType);

    do {
        var id = "#"+type+"-"+(generatorsOfType.length +=1);
    } while(document.querySelector(id))

    console.log(id);
    newShortcodeGen.attr('id', id);
    shortcodeArea.append(newShortcodeGen);
    if(newShortcodeGen.css('display') == 'none') {
        newShortcodeGen.show();
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

function removeGenerator(event) {
    const shortCodeConfig = jQuery(event.target).parent(".shortcode-config");
    shortCodeConfig.remove();
}
