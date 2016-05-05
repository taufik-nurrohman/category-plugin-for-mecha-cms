(function(win, doc) {
    if (typeof Widget !== "function") return;
    Widget.prototype.category = function(type, id) {
        if (type === 'DROPDOWN') {
            var elem = doc.getElementById(id);
            if (!elem) return;
            var select = elem.getElementsByTagName('select');
            if (!select.length) return;
            select[0].onchange = function() {
                win.location.href = this.value;
            };
        }
    };
    var category = doc.getElementsByClassName('widget-category-dropdown');
    if (!category.length) return;
    for (var i = 0, len = category.length; i < len; ++i) {
        var widget = new Widget();
        widget.category('DROPDOWN', category[i].id);
    }
})(window, document);