/*
 * LivePreview jQuery Plugin v1.0
 *
 * Copyright (c) 2009 Phil Haack, http://haacked.com/
 * Licensed under the MIT license.
 */
(function (c) {
    c.fn.livePreview = function (f) {
        var d = c.extend({}, c.fn.livePreview.defaults, f),
            g = d.previewElement.length - 1,
            h = new RegExp("&lt;(/?(" + d.allowedTags.join("|") + ")(\\s+.*?)?)&gt;", "g");
        return this.each(function (i) {
            var b = c(this),
                e = c(d.previewElement[Math.min(i, g)]);
            b.handleKeyUp = function () {
                b.unbind("keyup", b.handleKeyUp);
                if (!e.updatingPreview) {
                    e.updatingPreview = true;
                    window.setTimeout(function () {
                        b.reloadPreview()
                    }, d.interval)
                }
                return false
            };
            b.htmlUnencode = function (a) {
                return a.replace(/&/g, "&amp;").replace(/</g,
                    "&lt;").replace(/>/g, "&gt;")
            };
            b.reloadPreview = function () {
                var a = this.val();
                if (a.length > 0) {
                    a = this.htmlUnencode(a);
                    a = a.replace(d.paraRegExp, "<p>$1</p><p>$2</p>");
                    a = a.replace(d.lineBreakRegExp, "$1<br />$2");
                    a = a.replace(h, "<$1>")
                }
                try {
                    e[0].innerHTML = a
                } catch (j) {
                    alert("Sorry, but inserting a block element within is not allowed here.")
                }
                e.updatingPreview = false;
                this.bind("keyup", this.handleKeyUp)
            };
            b.reloadPreview()
        })
    };
    c.fn.livePreview.defaults = {
        paraRegExp: new RegExp("(.*)\n\n([^#*\n\n].*)", "g"),
        lineBreakRegExp: new RegExp("(.*)\n([^#*\n].*)",
            "g"),
    allowedTags: ["a", "b", "strong", "blockquote", "p", "i", "em", "u", "strike", "super", "sub", "code"],
    interval: 80
    }
})(jQuery);