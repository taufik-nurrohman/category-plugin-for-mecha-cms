(function(win, doc) {
    if (typeof Widget.tag !== "function") return;
    Widget.category = Widget.tag; // copy ...
    var $ = doc.getElementsByClassName('widget-category-dropdown');
    if (!$.length) return;
    for (var i = 0, len = $.length; i < len; ++i) {
        Widget.category('DROPDOWN', $[i]);
    }
})(window, document);