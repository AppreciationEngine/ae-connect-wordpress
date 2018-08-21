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
    console.log(data);
    for(gen in data) {
        let scType = data[gen].scType;
        addGenerator(scType, gen);
        updateGeneratorValues(gen, data[gen]);
    }
    SCgenerators = data;
    var serialized = JSON.stringify(SCgenerators);
    localStorage.setItem('scgens', serialized);
}

function updateGeneratorValues(id, scData) {
    // const shortCodeConfig = jQuery(id);
    const shortCodeConfig = jQuery('#'+id).find(".shortcode-config-inner");
    const data = jQuery.extend(true,{},scData);
    console.log(shortCodeConfig);
    shortCodeConfig.children('p').each(function () {
        let input = this.querySelector('input');
        let select = this.querySelector('select');
        if(input) {
            if(data[input.name] !== undefined) {
                input.value = data[input.name];
            } else {
                data[input.name] = input.value;
            }
            console.log("Updating "+input.name);
        }
        if(select) {
            if(data[select.name] !== undefined) {
                select.value = data[select.name];
            } else {
                data[select.name] = select.value;
            }
            console.log("Updating "+select.name);
        }
    });
    updateSCgenerators(id, data);
    setShortcodeText(shortCodeConfig, data);
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
    const scType = event.data.type;
    addGenerator(scType);
}

function addGenerator(scType, id = false) {
    const shortcodeArea = jQuery(".shortcode-config-area");
    const OGShortcodeGen = jQuery("#"+scType+"-1");
    const wrapperClassName = "ae-"+scType+"-wrapper";
    const newShortcodeGen = OGShortcodeGen.clone(true);

    if(!id) {
        var generatorsOfType = document.getElementsByClassName(wrapperClassName);
        console.log(generatorsOfType);

        do {
            var id = scType+"-"+(generatorsOfType.length +=1);
        } while(document.querySelector(id))
    }

    console.log(id);
    newShortcodeGen.attr('id', id);
    shortcodeArea.append(newShortcodeGen);
    if(newShortcodeGen.css('display') == 'none') {
        newShortcodeGen.show();
    }

    updateSCgenerators(id, {"scType": scType});
}

function generateShortcode(event) {
    const shortCodeConfig = jQuery(event.target).parent(".shortcode-config-inner");
    const id = shortCodeConfig.parent(".shortcode-config").parent("div").attr('id');
    console.log(id);
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

    updateSCgenerators(id, data);
    setShortcodeText(shortCodeConfig, data);

}

function setShortcodeText(shortCodeConfig, data) {
    var endshortcode, text, shortcode;
    endshortcode = text = shortcode = "";
    if('scType' in data) delete data.scType;

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
