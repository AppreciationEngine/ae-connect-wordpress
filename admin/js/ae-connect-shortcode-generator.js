var SCgenerators = {};

(function ($) {

    $(window).load(function () {
        const aeLinkAdder = jQuery("#ae-link button");
        const aeWindowAdder = jQuery("#ae-window button");
        const aeOnPageAdder = jQuery("#ae-on-page button");
        const aeLogoutAdder = jQuery("#ae-logout button");
        const generate = jQuery(".ae-generate");
        const remove = jQuery(".remove-generator");

        aeLinkAdder.click({type: "link"}, addGeneratorListener);
        aeWindowAdder.click({type: "window"}, addGeneratorListener);
        aeOnPageAdder.click({type: "on-page"}, addGeneratorListener);
        aeLogoutAdder.click({type: "logout"}, addGeneratorListener);

        generate.click(generateShortcode);
        remove.click(removeGenerator);

        setSCgeneratorsFromStorage();

    });

})(jQuery);

function setSCgeneratorsFromStorage() {
    data = localStorage.getItem('scgens');
    data = JSON.parse(data);

    for(gen in data) {
        let type = data[gen].type;
        addGenerator(type, gen);
    }
}

function updateSCgenerators(id, data) {
    if(!(id in SCgenerators)) SCgenerators[id] = {};
    for(key in data) {
        SCgenerators[id][key] = data[key];
    }
    var serialized = JSON.stringify(SCgenerators);
    localStorage.setItem('scgens', serialized);
}

function deleteSCgenoratorKey(key) {
    console.log("deleting " + key);
    delete SCgenerators[key];
    console.log(SCgenerators);
    var serialized = JSON.stringify(SCgenerators);
    localStorage.setItem('scgens', serialized);
}

function addGeneratorListener(event) {
    const type = event.data.type;
    addGenerator(type);
}

function addGenerator(type, id = false) {
    const shortcodeArea = jQuery(".shortcode-config-area");
    const OGShortcodeGen = jQuery("#"+type+"-1");
    const wrapperClassName = "ae-"+type+"-wrapper";
    const newShortcodeGen = OGShortcodeGen.clone(true);

    if(!id) {
        var generatorsOfType = document.getElementsByClassName(wrapperClassName);
        console.log(generatorsOfType);

        do {
            var id = "#"+type+"-"+(generatorsOfType.length +=1);
        } while(document.querySelector(id))
    }

    console.log(id);
    newShortcodeGen.attr('id', id);
    shortcodeArea.append(newShortcodeGen);
    if(newShortcodeGen.css('display') == 'none') {
        newShortcodeGen.show();
    }

    updateSCgenerators(id, {"type": type});
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
        }
        if(select) {
            data[select.name] = select.value;
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
    const id = shortCodeConfig.parent('div').attr('id');
    deleteSCgenoratorKey(id);
    shortCodeConfig.remove();
}
