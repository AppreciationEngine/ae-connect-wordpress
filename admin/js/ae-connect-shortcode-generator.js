/**
 * This JS file is a host of functions and event listeners that regulate the UI control
 * for the Shortcode Generator plugin page.
 *
 *  NOTE: Through out this file I use the terms "clone", "scgen" and "Shortcode generator" more or less
 *  interchangeably
 *
 * In the $(window).load callback, there are a series of event listeners that are tightly coupled
 * with the markup in the Shortcode Generator page. There is also a call to setSCgeneratorsFromStorage() which parses
 * the data in local storage corresponding to pre-existing clones and uses this data to generate the clone's markup
 *
 * Any clicks on the buttons at the top of the page trigger a cloning of some hidden markup on the page.
 * This markup corresponds to each type of AE shortcode (included through a php include)
 *
 * The cloning process takes place in the addGenerator() function:
 * - A new id for the new shortcode gerator form
 * - Adds the clone to the DOM, shows the markup
 * - Calls to updateSCgenerators() which saves a record of the clone to the session storage to be used
 * on subsequent page loads
 *
 * When a user submits their options for their shortcodes, generateShortcode() is triggered:
 * - Parses each field, and adds it to an intermediate object
 * - Calls to updateSCgenerators() which updates the clone's record with the user's field values
 * - Calls to setShortcodeText which parses the intermediate data object constructing the shortcode string
 * and updates the clone's markup with this shortcode generated text
 *
 *
 *
 * @type {Object}
 */
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

/**
 * - Retrieves the scgens (shortcode generators) stored in localStorage
 * - parses scgens and
 *  - adds each scgen to the DOM
 *  - updates the user inputed values for each scgen
 */
function setSCgeneratorsFromStorage() {
    data = localStorage.getItem('scgens');
    data = JSON.parse(data);
    for(gen in data) {
        let scType = data[gen].scType;
        addGenerator(scType, gen);
        updateGeneratorValues(gen, data[gen]);
    }
    SCgenerators = data;
    var serialized = JSON.stringify(SCgenerators);
    localStorage.setItem('scgens', serialized);
}

/**
 * Loops through a clone's fields and updates it with the properties in the
 * scData object.
 *
 * If the scData doesn't have a certain key namely the hidden inputs (shortcode, enshortcode)
 * the scData is updated with those fields. NOTE: it could be argued that this should be done in the
 * generateShortcode() function, however, these fields are immutable characteristics of every clone, so updating
 * a variable object with an invariable input seems like a bad idea, albiet this isn't a great solution either, but
 * it works :P..
 *
 * @param  id     the id of the clone
 * @param  scData clone's data
 */
function updateGeneratorValues(id, scData) {
    // const shortCodeConfig = jQuery(id);
    const shortCodeConfig = jQuery('#'+id).find(".shortcode-config-inner");
    const data = jQuery.extend(true,{},scData);
    shortCodeConfig.children('p').each(function () {
        let input = this.querySelector('input');
        let select = this.querySelector('select');
        if(input) {
            if(data[input.name] !== undefined) {
                input.value = data[input.name];
            } else {
                data[input.name] = input.value;
            }
        }
        if(select) {
            if(data[select.name] !== undefined) {
                select.value = data[select.name];
            } else {
                data[select.name] = select.value;
            }
        }
    });
    updateSCgenerators(id, data);
    setShortcodeText(shortCodeConfig, data);
}

/**
 * takes in an id of a given clone and if it doesn't exists, creates a record for it in
 * SCgenerators. Then parses the data and adds it to the clone's record
 * @param  id   clone id
 * @param  data data to be added to the clone's record in SCgenerators, scgens
 */
function updateSCgenerators(id, data) {
    if(!(id in SCgenerators)) SCgenerators[id] = {};
    for(key in data) {
        SCgenerators[id][key] = data[key];
    }
    var serialized = JSON.stringify(SCgenerators);
    localStorage.setItem('scgens', serialized);
}

/**
 * deletes a clone's record in SCgenerators, updates scgens
 * @param   key key corresponding to a clone
 */
function deleteSCgenoratorKey(key) {
    delete SCgenerators[key];
    var serialized = JSON.stringify(SCgenerators);
    localStorage.setItem('scgens', serialized);
}

/**
 * callback for adding new clones
 */
function addGeneratorListener(event) {
    const scType = event.data.type;
    addGenerator(scType);
}

/**
 * Adds a Shortcode generator clone to the DOM
 * @param scType     type of shortcode clone that will be created
 *
 * @param id         optional id parameter of the clone that will be created.
 *                   In cases where the clone is being generated from the
 *                   localStorage, the id of that clone is used
 */
function addGenerator(scType, id = false) {
    const shortcodeArea = jQuery(".shortcode-config-area");
    const OGShortcodeGen = jQuery("#"+scType+"-1");
    const wrapperClassName = "ae-"+scType+"-wrapper";
    const newShortcodeGen = OGShortcodeGen.clone(true);

    if(!id) {
        var generatorsOfType = document.getElementsByClassName(wrapperClassName);

        do {
            var id = scType+"-"+(generatorsOfType.length +=1);
        } while(document.querySelector(id))
    }

    newShortcodeGen.attr('id', id);
    shortcodeArea.append(newShortcodeGen);
    if(newShortcodeGen.css('display') == 'none') {
        newShortcodeGen.show();
    }

    updateSCgenerators(id, {"scType": scType});
}

/**
 * Parses the field data for the clone, and passes this data to a shortcode text generator
 */
function generateShortcode(event) {
    const shortCodeConfig = jQuery(event.target).parent(".shortcode-config-inner");
    const id = shortCodeConfig.parent(".shortcode-config").parent("div").attr('id');
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

    updateSCgenerators(id, data);
    setShortcodeText(shortCodeConfig, data);

}

/**
 * parses data into a shortcode string that is added to the clone's markup
 * @param shortCodeConfig clone's div that encloses the shortcode fields
 * @param data            clone's field data
 */
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

}

/**
 * Responds to clone removal events and clears clone from the DOM and also from
 * SCgenerators + localStorage
 */
function removeGenerator(event) {
    const shortCodeConfig = jQuery(event.target).parent(".shortcode-config");
    const id = shortCodeConfig.parent('div').attr('id');
    deleteSCgenoratorKey(id);
    shortCodeConfig.remove();
}
