(function ($) {

    $(window).load(function () {
        const aeLinkAdder = jQuery("#ae-link button");
        const aeWindowAdder = jQuery("#ae-window button");
        const aeOnPageAdder = jQuery("#ae-on-page button");
        const aeLogoutAdder = jQuery("#ae-logout button");

        aeLinkAdder.click({type: "link"}, addGenerator);
        aeWindowAdder.click({type: "window"}, addGenerator);
        aeOnPageAdder.click({type: "on-page"}, addGenerator);
        aeLogoutAdder.click({type: "logout"}, addGenerator);

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
