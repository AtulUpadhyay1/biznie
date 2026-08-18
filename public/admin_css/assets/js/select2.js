// npm package: select2
// github link: https://github.com/select2/select2

$(function () {
    'use strict'

    // NOTE: the selectors are scoped to `select` on purpose. select2 builds its
    // own wrapper as <span class="select2 select2-container ...">, so a bare
    // ".select2" also matches that wrapper and select2 ends up initialising
    // itself on its own output — which produced stacked, 4px-wide duplicate
    // controls next to every field.
    if ($("select.js-example-basic-single").length) {
        $("select.js-example-basic-single").select2();
    }
    if ($("select.js-example-basic-multiple").length) {
        $("select.js-example-basic-multiple").select2();
    }
    if ($("select.select2").length) {
        $("select.select2").select2();
    }
});
