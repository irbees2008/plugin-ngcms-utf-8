
function cuselScrollToCurent(a) {
    var b = null;
    if (a.find(".cuselOptHover").eq(0).is("span")) b = a.find(".cuselOptHover").eq(0);
    else if (a.find(".cuselActive").eq(0).is("span")) b = a.find(".cuselActive").eq(0);
    if (a.find(".jScrollPaneTrack").eq(0).is("div") && b) {
        var c = b.position(),
            d = a.find(".cusel-scroll-pane").eq(0).attr("id");
        jQuery("#" + d)[0].scrollTo(c.top)
    }
}

function cuselShowList(a) {
    var b = a.parent(".cusel");
    if (a.css("display") == "none") {
        $(".cusel-scroll-wrap").css("display", "none");
        $(".cuselOpen").removeClass("cuselOpen");
        b.addClass("cuselOpen");
        a.css("display", "block");
        var c = false;
        if (b.prop("class").indexOf("cuselScrollArrows") != -1) c = true;
        if (!a.find(".jScrollPaneContainer").eq(0).is("div")) {
            a.find("div").eq(0).jScrollPaneCusel({
                showArrows: c
            })
        }
        cuselScrollToCurent(a)
    } else {
        a.css("display", "none");
        b.removeClass("cuselOpen")
    }
}

function cuSelRefresh(a) {
    var b = a.refreshEl.split(","),
        c = b.length,
        d;
    for (d = 0; d < c; d++) {
        var e = jQuery(b[d]).parents(".cusel").find(".cusel-scroll-wrap").eq(0);
        e.find(".cusel-scroll-pane").jScrollPaneRemoveCusel();
        e.css({
            visibility: "hidden",
            display: "block"
        });
        var f = e.find("span"),
            g = f.eq(0).outerHeight();
        if (f.length > a.visRows) {
            e.css({
                height: g * a.visRows + "px",
                display: "none",
                visibility: "visible"
            }).children(".cusel-scroll-pane").css("height", g * a.visRows + "px")
        } else {
            e.css({
                display: "none",
                visibility: "visible"
            })
        }
    }
}

function cuSel(a) {
    function b() {
        jQuery("html").unbind("click");
        jQuery("html").click(function(a) {
            var b = jQuery(a.target),
                c = b.attr("id"),
                d = b.prop("class");
            if ((d.indexOf("cuselText") != -1 || d.indexOf("cuselFrameRight") != -1) && b.parent().prop("class").indexOf("classDisCusel") == -1) {
                var e = b.parent().find(".cusel-scroll-wrap").eq(0);
                cuselShowList(e)
            } else if (d.indexOf("cusel") != -1 && d.indexOf("classDisCusel") == -1 && b.is("div")) {
                var e = b.find(".cusel-scroll-wrap").eq(0);
                cuselShowList(e)
            } else if (b.is(".cusel-scroll-wrap span") && d.indexOf("cuselActive") == -1) {
                var f;
                b.attr("val") == undefined ? f = b.text() : f = b.attr("val");
                b.parents(".cusel-scroll-wrap").find(".cuselActive").eq(0).removeClass("cuselActive").end().parents(".cusel-scroll-wrap").next().val(f).end().prev().text(b.text()).end().css("display", "none").parent(".cusel").removeClass("cuselOpen");
                b.addClass("cuselActive");
                b.parents(".cusel-scroll-wrap").find(".cuselOptHover").removeClass("cuselOptHover");
                if (d.indexOf("cuselActive") == -1) b.parents(".cusel").find(".cusel-scroll-wrap").eq(0).next("input").change()
            } else if (b.parents(".cusel-scroll-wrap").is("div")) {
                return
            } else {
                jQuery(".cusel-scroll-wrap").css("display", "none").parent(".cusel").removeClass("cuselOpen")
            }
        });
        jQuery(".cusel").unbind("keydown");
        jQuery(".cusel").keydown(function(a) {
            var b, c;
            if (window.event) b = window.event.keyCode;
            else if (a) b = a.which;
            if (b == null || b == 0 || b == 9) return true;
            if (jQuery(this).prop("class").indexOf("classDisCusel") != -1) return false;
            if (b == 40) {
                var d = jQuery(this).find(".cuselOptHover").eq(0);
                if (!d.is("span")) var e = jQuery(this).find(".cuselActive").eq(0);
                else var e = d;
                var f = e.next();
                if (f.is("span")) {
                    jQuery(this).find(".cuselText").eq(0).text(f.text());
                    e.removeClass("cuselOptHover");
                    f.addClass("cuselOptHover");
                    $(this).find("input").eq(0).val(f.attr("val"));
                    cuselScrollToCurent($(this).find(".cusel-scroll-wrap").eq(0));
                    return false
                } else return false
            }
            if (b == 38) {
                var d = $(this).find(".cuselOptHover").eq(0);
                if (!d.is("span")) var e = $(this).find(".cuselActive").eq(0);
                else var e = d;
                cuselActivePrev = e.prev();
                if (cuselActivePrev.is("span")) {
                    $(this).find(".cuselText").eq(0).text(cuselActivePrev.text());
                    e.removeClass("cuselOptHover");
                    cuselActivePrev.addClass("cuselOptHover");
                    $(this).find("input").eq(0).val(cuselActivePrev.attr("val"));
                    cuselScrollToCurent($(this).find(".cusel-scroll-wrap").eq(0));
                    return false
                } else return false
            }
            if (b == 27) {
                var g = $(this).find(".cuselActive").eq(0).text();
                $(this).removeClass("cuselOpen").find(".cusel-scroll-wrap").eq(0).css("display", "none").end().find(".cuselOptHover").eq(0).removeClass("cuselOptHover");
                $(this).find(".cuselText").eq(0).text(g)
            }
            if (b == 13) {
                var h = $(this).find(".cuselOptHover").eq(0);
                if (h.is("span")) {
                    $(this).find(".cuselActive").removeClass("cuselActive");
                    h.addClass("cuselActive")
                } else var i = $(this).find(".cuselActive").attr("val");
                $(this).removeClass("cuselOpen").find(".cusel-scroll-wrap").eq(0).css("display", "none").end().find(".cuselOptHover").eq(0).removeClass("cuselOptHover");
                $(this).find("input").eq(0).change()
            }
            if (b == 32 && $.browser.opera) {
                var j = $(this).find(".cusel-scroll-wrap").eq(0);
                cuselShowList(j)
            }
            if ($.browser.opera) return false
        });
        var a = [];
        jQuery(".cusel").keypress(function(b) {
            function g() {
                var b = [];
                for (var c in a) {
                    if (window.event) b[c] = a[c].keyCode;
                    else if (a[c]) b[c] = a[c].which;
                    b[c] = String.fromCharCode(b[c]).toUpperCase()
                }
                var d = jQuery(e).find("span"),
                    f = d.length,
                    g, h;
                for (g = 0; g < f; g++) {
                    var i = true;
                    for (var j in a) {
                        h = d.eq(g).text().charAt(j).toUpperCase();
                        if (h != b[j]) {
                            i = false
                        }
                    }
                    if (i) {
                        jQuery(e).find(".cuselOptHover").removeClass("cuselOptHover").end().find("span").eq(g).addClass("cuselOptHover").end().end().find(".cuselText").eq(0).text(d.eq(g).text());
                        cuselScrollToCurent($(e).find(".cusel-scroll-wrap").eq(0));
                        a = a.splice;
                        a = [];
                        break;
                        return true
                    }
                }
                a = a.splice;
                a = []
            }
            var c, d;
            if (window.event) c = window.event.keyCode;
            else if (b) c = b.which;
            if (c == null || c == 0 || c == 9) return true;
            if (jQuery(this).prop("class").indexOf("classDisCusel") != -1) return false;
            var e = this;
            a.push(b);
            clearTimeout(jQuery.data(this, "timer"));
            var f = setTimeout(function() {
                g()
            }, 500);
            jQuery(this).data("timer", f);
            if (jQuery.browser.opera && window.event.keyCode != 9) return false
        })
    }
    jQuery(a.changedEl).each(function(c) {
        var d = jQuery(this),
            e = d.outerWidth(),
            f = d.prop("class"),
            g = d.prop("id") ? d.prop("id") : "cuSel-" + c,
            h = d.prop("name"),
            j = d.val(),
            k = d.find("option[value='" + j + "']").eq(0),
            l = k.text(),
            m = d.prop("disabled"),
            n = a.scrollArrows,
            o = d.prop("onchange"),
            p = d.prop("tabindex"),
            q = d.prop("multiple");
        if (!g || q) return false;
        var r = d.data("events"),
            s = [];
        if (r && r["change"]) {
            $.each(r["change"], function(a, b) {
                s[s.length] = b
            })
        }
        if (!m) {
            classDisCuselText = "", classDisCusel = ""
        } else {
            classDisCuselText = "classDisCuselLabel";
            classDisCusel = "classDisCusel"
        }
        if (n) {
            classDisCusel += " cuselScrollArrows"
        }
        k.addClass("cuselActive");
        var t = d.html(),
            u = t.replace(/option/ig, "span").replace(/value=/ig, "val=");
        if ($.browser.msie && parseInt($.browser.version) < 9) {
            var v = /(val=)(.*?)(>)/g;
            u = u.replace(v, "$CI")
        }
        var w = '<div class="cusel ' + f + " " + classDisCusel + '"' + " id=cuselFrame-" + g + ' style="width:' + e + 'px"' + ' tabindex="' + p + '"' + ">" + '<div class="cuselFrameRight"></div>' + '<div class="cuselText">' + l + "</div>" + '<div class="cusel-scroll-wrap"><div class="cusel-scroll-pane" id="cusel-scroll-' + g + '">' + u + "</div></div>" + '<input type="hidden" id="' + g + '" name="' + h + '" value="' + j + '" />' + "</div>";
        d.replaceWith(w);
        if (o) jQuery("#" + g).bind("change", o);
        if (s.length) {
            $.each(s, function(a, b) {
                $("#" + g).bind("change", b)
            })
        }
        var x = jQuery("#cuselFrame-" + g),
            y = x.find("span"),
            z;
        if (!y.eq(0).text()) {
            z = y.eq(1).innerHeight();
            y.eq(0).css("height", y.eq(1).height())
        } else {
            z = y.eq(0).innerHeight()
        }
        if (y.length > a.visRows) {
            x.find(".cusel-scroll-wrap").eq(0).css({
                height: z * a.visRows + "px",
                display: "none",
                visibility: "visible"
            }).children(".cusel-scroll-pane").css("height", z * a.visRows + "px")
        } else {
            x.find(".cusel-scroll-wrap").eq(0).css({
                display: "none",
                visibility: "visible"
            })
        }
        var A = jQuery("#cusel-scroll-" + g).find("span[addTags]"),
            B = A.length;
        for (i = 0; i < B; i++) A.eq(i).append(A.eq(i).attr("addTags")).removeAttr("addTags");
        b()
    });
    jQuery(".cusel").focus(function() {
        jQuery(this).addClass("cuselFocus")
    });
    jQuery(".cusel").blur(function() {
        jQuery(this).removeClass("cuselFocus")
    });
    jQuery(".cusel").hover(function() {
        jQuery(this).addClass("cuselFocus")
    }, function() {
        jQuery(this).removeClass("cuselFocus")
    })
}(function(a) {
    a.jScrollPaneCusel = {
        active: []
    };
    a.fn.jScrollPaneCusel = function(b) {
        b = a.extend({}, a.fn.jScrollPaneCusel.defaults, b);
        var c = function() {
            return false
        };
        return this.each(function() {
            var d = a(this);
            var e = this.parentNode.offsetWidth;
            d.css("overflow", "hidden");
            var f = this;
            if (a(this).parent().is(".jScrollPaneContainer")) {
                var g = b.maintainPosition ? d.position().top : 0;
                var h = a(this).parent();
                var i = e;
                var j = h.outerHeight();
                var k = j;
                a(">.jScrollPaneTrack, >.jScrollArrowUp, >.jScrollArrowDown", h).remove();
                d.css({
                    top: 0
                })
            } else {
                var g = 0;
                this.originalPadding = d.css("paddingTop") + " " + d.css("paddingRight") + " " + d.css("paddingBottom") + " " + d.css("paddingLeft");
                this.originalSidePaddingTotal = (parseInt(d.css("paddingLeft")) || 0) + (parseInt(d.css("paddingRight")) || 0);
                var i = e;
                var j = d.innerHeight();
                var k = j;
                d.wrap("<div class='jScrollPaneContainer'></div>").parent().css({
                    height: j + "px",
                    width: i + "px"
                });
                if (!window.navigator.userProfile) {
                    var l = parseInt(a(this).parent().css("border-left-width")) + parseInt(a(this).parent().css("border-right-width"));
                    i -= l;
                    a(this).css("width", i + "px").parent().css("width", i + "px")
                }
                a(document).bind("emchange", function(a, c, e) {
                    d.jScrollPaneCusel(b)
                })
            }
            if (b.reinitialiseOnImageLoad) {
                var m = a.data(f, "jScrollPaneImagesToLoad") || a("img", d);
                var n = [];
                if (m.length) {
                    m.each(function(c, e) {
                        a(this).bind("load", function() {
                            if (a.inArray(c, n) == -1) {
                                n.push(e);
                                m = a.grep(m, function(a, b) {
                                    return a != e
                                });
                                a.data(f, "jScrollPaneImagesToLoad", m);
                                b.reinitialiseOnImageLoad = false;
                                d.jScrollPaneCusel(b)
                            }
                        }).each(function(a, b) {
                            if (this.complete || this.complete === undefined) {
                                this.src = this.src
                            }
                        })
                    })
                }
            }
            var o = this.originalSidePaddingTotal;
            var p = {
                height: "auto",
                width: i - b.scrollbarWidth - b.scrollbarMargin - o + "px"
            };
            if (b.scrollbarOnLeft) {
                p.paddingLeft = b.scrollbarMargin + b.scrollbarWidth + "px"
            } else {
                p.paddingRight = b.scrollbarMargin + "px"
            }
            d.css(p);
            var q = d.outerHeight();
            var r = j / q;
            if (r < .99) {
                var s = d.parent();
                s.append(a('<div class="jScrollPaneTrack"></div>').css({
                    width: b.scrollbarWidth + "px"
                }).append(a('<div class="jScrollPaneDrag"></div>').css({
                    width: b.scrollbarWidth + "px"
                }).append(a('<div class="jScrollPaneDragTop"></div>').css({
                    width: b.scrollbarWidth + "px"
                }), a('<div class="jScrollPaneDragBottom"></div>').css({
                    width: b.scrollbarWidth + "px"
                }))));
                var t = a(">.jScrollPaneTrack", s);
                var u = a(">.jScrollPaneTrack .jScrollPaneDrag", s);
                if (b.showArrows) {
                    var v;
                    var w;
                    var x;
                    var y;
                    var z = function() {
                        if (y > 4 || y % 4 == 0) {
                            Q(J + w * I)
                        }
                        y++
                    };
                    var A = function(b) {
                        a("html").unbind("mouseup", A);
                        v.removeClass("jScrollActiveArrowButton");
                        clearInterval(x)
                    };
                    var B = function() {
                        a("html").bind("mouseup", A);
                        v.addClass("jScrollActiveArrowButton");
                        y = 0;
                        z();
                        x = setInterval(z, 100)
                    };
                    s.append(a("<div></div>").attr({
                        "class": "jScrollArrowUp"
                    }).css({
                        width: b.scrollbarWidth + "px"
                    }).bind("mousedown", function() {
                        v = a(this);
                        w = -1;
                        B();
                        this.blur();
                        return false
                    }).bind("click", c), a("<div></div>").attr({
                        "class": "jScrollArrowDown"
                    }).css({
                        width: b.scrollbarWidth + "px"
                    }).bind("mousedown", function() {
                        v = a(this);
                        w = 1;
                        B();
                        this.blur();
                        return false
                    }).bind("click", c));
                    var C = a(">.jScrollArrowUp", s);
                    var D = a(">.jScrollArrowDown", s);
                    if (b.arrowSize) {
                        k = j - b.arrowSize - b.arrowSize;
                        t.css({
                            height: k + "px",
                            top: b.arrowSize + "px"
                        })
                    } else {
                        var E = C.height();
                        b.arrowSize = E;
                        k = j - E - D.height();
                        t.css({
                            height: k + "px",
                            top: E + "px"
                        })
                    }
                }
                var F = a(this).css({
                    position: "absolute",
                    overflow: "visible"
                });
                var G;
                var H;
                var I;
                var J = 0;
                var K = r * j / 2;
                var L = function(a, b) {
                    var c = b == "X" ? "Left" : "Top";
                    return a["page" + b] || a["client" + b] + (document.documentElement["scroll" + c] || document.body["scroll" + c]) || 0
                };
                var M = function() {
                    return false
                };
                var N = function() {
                    bc();
                    G = u.offset(false);
                    G.top -= J;
                    H = k - u[0].offsetHeight;
                    I = 2 * b.wheelSpeed * H / q
                };
                var O = function(b) {
                    N();
                    K = L(b, "Y") - J - G.top;
                    a("html").bind("mouseup", P).bind("mousemove", R);
                    if (a.browser.msie) {
                        a("html").bind("dragstart", M).bind("selectstart", M)
                    }
                    return false
                };
                var P = function() {
                    a("html").unbind("mouseup", P).unbind("mousemove", R);
                    K = r * j / 2;
                    if (a.browser.msie) {
                        a("html").unbind("dragstart", M).unbind("selectstart", M)
                    }
                };
                var Q = function(a) {
                    a = a < 0 ? 0 : a > H ? H : a;
                    J = a;
                    u.css({
                        top: a + "px"
                    });
                    var c = a / H;
                    F.css({
                        top: (j - q) * c + "px"
                    });
                    d.trigger("scroll");
                    if (b.showArrows) {
                        C[a == 0 ? "addClass" : "removeClass"]("disabled");
                        D[a == H ? "addClass" : "removeClass"]("disabled")
                    }
                };
                var R = function(a) {
                    Q(L(a, "Y") - G.top - K)
                };
                var S = Math.max(Math.min(r * (j - b.arrowSize * 2), b.dragMaxHeight), b.dragMinHeight);
                u.css({
                    height: S + "px"
                }).bind("mousedown", O);
                var T;
                var U;
                var V;
                var W = function() {
                    if (U > 8 || U % 4 == 0) {
                        Q(J - (J - V) / 2)
                    }
                    U++
                };
                var X = function() {
                    clearInterval(T);
                    a("html").unbind("mouseup", X).unbind("mousemove", Y)
                };
                var Y = function(a) {
                    V = L(a, "Y") - G.top - K
                };
                var Z = function(b) {
                    N();
                    Y(b);
                    U = 0;
                    a("html").bind("mouseup", X).bind("mousemove", Y);
                    T = setInterval(W, 100);
                    W()
                };
                t.bind("mousedown", Z);
                s.bind("mousewheel", function(a, b) {
                    N();
                    bc();
                    var c = J;
                    Q(J - b * I);
                    var d = c != J;
                    return false
                });
                var _;
                var ba;

                function bb() {
                    var a = (_ - J) / b.animateStep;
                    if (a > 1 || a < -1) {
                        Q(J + a)
                    } else {
                        Q(_);
                        bc()
                    }
                }
                var bc = function() {
                    if (ba) {
                        clearInterval(ba);
                        delete _
                    }
                };
                var bd = function(c, e) {
                    if (typeof c == "string") {
                        $e = a(c, d);
                        if (!$e.length) return;
                        c = $e.offset().top - d.offset().top
                    }
                    s.scrollTop(0);
                    bc();
                    var f = -c / (j - q) * H;
                    if (e || !b.animateTo) {
                        Q(f)
                    } else {
                        _ = f;
                        ba = setInterval(bb, b.animateInterval)
                    }
                };
                d[0].scrollTo = bd;
                d[0].scrollBy = function(a) {
                    var b = -parseInt(F.css("top")) || 0;
                    bd(b + a)
                };
                N();
                bd(-g, true);
                a("*", this).bind("focus", function(c) {
                    var e = a(this);
                    var f = 0;
                    while (e[0] != d[0]) {
                        f += e.position().top;
                        e = e.offsetParent()
                    }
                    var g = -parseInt(F.css("top")) || 0;
                    var h = g + j;
                    var i = f > g && f < h;
                    if (!i) {
                        var k = f - b.scrollbarMargin;
                        if (f > g) {
                            k += a(this).height() + 15 + b.scrollbarMargin - j
                        }
                        bd(k)
                    }
                });
                if (location.hash) {
                    bd(location.hash)
                }
                a(document).bind("click", function(b) {
                    $target = a(b.target);
                    if ($target.is("a")) {
                        var c = $target.attr("href");
                        if (c.substr(0, 1) == "#") {
                            bd(c)
                        }
                    }
                });
                a.jScrollPaneCusel.active.push(d[0])
            } else {
                d.css({
                    height: j + "px",
                    width: i - this.originalSidePaddingTotal + "px",
                    padding: this.originalPadding
                });
                d.parent().unbind("mousewheel")
            }
        })
    };
    a.fn.jScrollPaneRemoveCusel = function() {
        a(this).each(function() {
            $this = a(this);
            var b = $this.parent();
            if (b.is(".jScrollPaneContainer")) {
                $this.css({
                    top: "",
                    height: "",
                    width: "",
                    padding: "",
                    overflow: "",
                    position: ""
                });
                $this.attr("style", $this.data("originalStyleTag"));
                b.after($this).remove()
            }
        })
    };
    a.fn.jScrollPaneCusel.defaults = {
        scrollbarWidth: 10,
        scrollbarMargin: 5,
        wheelSpeed: 18,
        showArrows: false,
        arrowSize: 0,
        animateTo: false,
        dragMinHeight: 1,
        dragMaxHeight: 99999,
        animateInterval: 100,
        animateStep: 3,
        maintainPosition: true,
        scrollbarOnLeft: false,
        reinitialiseOnImageLoad: false
    };
    a(window).bind("unload", function() {
        var b = a.jScrollPaneCusel.active;
        for (var c = 0; c < b.length; c++) {
            b[c].scrollTo = b[c].scrollBy = null
        }
    })
})(jQuery);
(function(a) {
    a.event.special.mousewheel = {
        setup: function() {
            var b = a.event.special.mousewheel.handler;
            if (a.browser.mozilla) a(this).bind("mousemove.mousewheel", function(b) {
                a.data(this, "mwcursorposdata", {
                    pageX: b.pageX,
                    pageY: b.pageY,
                    clientX: b.clientX,
                    clientY: b.clientY
                })
            });
            if (this.addEventListener) this.addEventListener(a.browser.mozilla ? "DOMMouseScroll" : "mousewheel", b, false);
            else this.onmousewheel = b
        },
        teardown: function() {
            var b = a.event.special.mousewheel.handler;
            a(this).unbind("mousemove.mousewheel");
            if (this.removeEventListener) this.removeEventListener(a.browser.mozilla ? "DOMMouseScroll" : "mousewheel", b, false);
            else this.onmousewheel = function() {};
            a.removeData(this, "mwcursorposdata")
        },
        handler: function(b) {
            var c = Array.prototype.slice.call(arguments, 1);
            b = a.event.fix(b || window.event);
            a.extend(b, a.data(this, "mwcursorposdata") || {});
            var d = 0,
                e = true;
            if (b.wheelDelta) d = b.wheelDelta / 120;
            if (b.detail) d = -b.detail / 3;
            b.data = b.data || {};
            b.type = "mousewheel";
            c.unshift(d);
            c.unshift(b);
            return a.event.handle.apply(this, c)
        }
    };
    a.fn.extend({
        mousewheel: function(a) {
            return a ? this.bind("mousewheel", a) : this.trigger("mousewheel")
        },
        unmousewheel: function(a) {
            return this.unbind("mousewheel", a)
        }
    })
})(jQuery);

(function(e, t) {
    function i(t, i) {
        var a, n, r, o = t.nodeName.toLowerCase();
        return "area" === o ? (a = t.parentNode, n = a.name, t.href && n && "map" === a.nodeName.toLowerCase() ? (r = e("img[usemap=#" + n + "]")[0], !!r && s(r)) : !1) : (/input|select|textarea|button|object/.test(o) ? !t.disabled : "a" === o ? t.href || i : i) && s(t)
    }

    function s(t) {
        return e.expr.filters.visible(t) && !e(t).parents().addBack().filter(function() {
            return "hidden" === e.css(this, "visibility")
        }).length
    }
    var a = 0,
        n = /^ui-id-\d+$/;
    e.ui = e.ui || {}, e.extend(e.ui, {
        version: "1.10.3",
        keyCode: {
            BACKSPACE: 8,
            COMMA: 188,
            DELETE: 46,
            DOWN: 40,
            END: 35,
            ENTER: 13,
            ESCAPE: 27,
            HOME: 36,
            LEFT: 37,
            NUMPAD_ADD: 107,
            NUMPAD_DECIMAL: 110,
            NUMPAD_DIVIDE: 111,
            NUMPAD_ENTER: 108,
            NUMPAD_MULTIPLY: 106,
            NUMPAD_SUBTRACT: 109,
            PAGE_DOWN: 34,
            PAGE_UP: 33,
            PERIOD: 190,
            RIGHT: 39,
            SPACE: 32,
            TAB: 9,
            UP: 38
        }
    }), e.fn.extend({
        focus: function(t) {
            return function(i, s) {
                return "number" == typeof i ? this.each(function() {
                    var t = this;
                    setTimeout(function() {
                        e(t).focus(), s && s.call(t)
                    }, i)
                }) : t.apply(this, arguments)
            }
        }(e.fn.focus),
        scrollParent: function() {
            var t;
            return t = e.ui.ie && /(static|relative)/.test(this.css("position")) || /absolute/.test(this.css("position")) ? this.parents().filter(function() {
                return /(relative|absolute|fixed)/.test(e.css(this, "position")) && /(auto|scroll)/.test(e.css(this, "overflow") + e.css(this, "overflow-y") + e.css(this, "overflow-x"))
            }).eq(0) : this.parents().filter(function() {
                return /(auto|scroll)/.test(e.css(this, "overflow") + e.css(this, "overflow-y") + e.css(this, "overflow-x"))
            }).eq(0), /fixed/.test(this.css("position")) || !t.length ? e(document) : t
        },
        zIndex: function(i) {
            if (i !== t) return this.css("zIndex", i);
            if (this.length)
                for (var s, a, n = e(this[0]); n.length && n[0] !== document;) {
                    if (s = n.css("position"), ("absolute" === s || "relative" === s || "fixed" === s) && (a = parseInt(n.css("zIndex"), 10), !isNaN(a) && 0 !== a)) return a;
                    n = n.parent()
                }
            return 0
        },
        uniqueId: function() {
            return this.each(function() {
                this.id || (this.id = "ui-id-" + ++a)
            })
        },
        removeUniqueId: function() {
            return this.each(function() {
                n.test(this.id) && e(this).removeAttr("id")
            })
        }
    }), e.extend(e.expr[":"], {
        data: e.expr.createPseudo ? e.expr.createPseudo(function(t) {
            return function(i) {
                return !!e.data(i, t)
            }
        }) : function(t, i, s) {
            return !!e.data(t, s[3])
        },
        focusable: function(t) {
            return i(t, !isNaN(e.attr(t, "tabindex")))
        },
        tabbable: function(t) {
            var s = e.attr(t, "tabindex"),
                a = isNaN(s);
            return (a || s >= 0) && i(t, !a)
        }
    }), e("<a>").outerWidth(1).jquery || e.each(["Width", "Height"], function(i, s) {
        function a(t, i, s, a) {
            return e.each(n, function() {
                i -= parseFloat(e.css(t, "padding" + this)) || 0, s && (i -= parseFloat(e.css(t, "border" + this + "Width")) || 0), a && (i -= parseFloat(e.css(t, "margin" + this)) || 0)
            }), i
        }
        var n = "Width" === s ? ["Left", "Right"] : ["Top", "Bottom"],
            r = s.toLowerCase(),
            o = {
                innerWidth: e.fn.innerWidth,
                innerHeight: e.fn.innerHeight,
                outerWidth: e.fn.outerWidth,
                outerHeight: e.fn.outerHeight
            };
        e.fn["inner" + s] = function(i) {
            return i === t ? o["inner" + s].call(this) : this.each(function() {
                e(this).css(r, a(this, i) + "px")
            })
        }, e.fn["outer" + s] = function(t, i) {
            return "number" != typeof t ? o["outer" + s].call(this, t) : this.each(function() {
                e(this).css(r, a(this, t, !0, i) + "px")
            })
        }
    }), e.fn.addBack || (e.fn.addBack = function(e) {
        return this.add(null == e ? this.prevObject : this.prevObject.filter(e))
    }), e("<a>").data("a-b", "a").removeData("a-b").data("a-b") && (e.fn.removeData = function(t) {
        return function(i) {
            return arguments.length ? t.call(this, e.camelCase(i)) : t.call(this)
        }
    }(e.fn.removeData)), e.ui.ie = !!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()), e.support.selectstart = "onselectstart" in document.createElement("div"), e.fn.extend({
        disableSelection: function() {
            return this.bind((e.support.selectstart ? "selectstart" : "mousedown") + ".ui-disableSelection", function(e) {
                e.preventDefault()
            })
        },
        enableSelection: function() {
            return this.unbind(".ui-disableSelection")
        }
    }), e.extend(e.ui, {
        plugin: {
            add: function(t, i, s) {
                var a, n = e.ui[t].prototype;
                for (a in s) n.plugins[a] = n.plugins[a] || [], n.plugins[a].push([i, s[a]])
            },
            call: function(e, t, i) {
                var s, a = e.plugins[t];
                if (a && e.element[0].parentNode && 11 !== e.element[0].parentNode.nodeType)
                    for (s = 0; a.length > s; s++) e.options[a[s][0]] && a[s][1].apply(e.element, i)
            }
        },
        hasScroll: function(t, i) {
            if ("hidden" === e(t).css("overflow")) return !1;
            var s = i && "left" === i ? "scrollLeft" : "scrollTop",
                a = !1;
            return t[s] > 0 ? !0 : (t[s] = 1, a = t[s] > 0, t[s] = 0, a)
        }
    })
})(jQuery);
(function(e, t) {
    var i = 0,
        s = Array.prototype.slice,
        n = e.cleanData;
    e.cleanData = function(t) {
        for (var i, s = 0; null != (i = t[s]); s++) try {
            e(i).triggerHandler("remove")
        } catch (a) {}
        n(t)
    }, e.widget = function(i, s, n) {
        var a, r, o, h, l = {},
            u = i.split(".")[0];
        i = i.split(".")[1], a = u + "-" + i, n || (n = s, s = e.Widget), e.expr[":"][a.toLowerCase()] = function(t) {
            return !!e.data(t, a)
        }, e[u] = e[u] || {}, r = e[u][i], o = e[u][i] = function(e, i) {
            return this._createWidget ? (arguments.length && this._createWidget(e, i), t) : new o(e, i)
        }, e.extend(o, r, {
            version: n.version,
            _proto: e.extend({}, n),
            _childConstructors: []
        }), h = new s, h.options = e.widget.extend({}, h.options), e.each(n, function(i, n) {
            return e.isFunction(n) ? (l[i] = function() {
                var e = function() {
                        return s.prototype[i].apply(this, arguments)
                    },
                    t = function(e) {
                        return s.prototype[i].apply(this, e)
                    };
                return function() {
                    var i, s = this._super,
                        a = this._superApply;
                    return this._super = e, this._superApply = t, i = n.apply(this, arguments), this._super = s, this._superApply = a, i
                }
            }(), t) : (l[i] = n, t)
        }), o.prototype = e.widget.extend(h, {
            widgetEventPrefix: r ? h.widgetEventPrefix : i
        }, l, {
            constructor: o,
            namespace: u,
            widgetName: i,
            widgetFullName: a
        }), r ? (e.each(r._childConstructors, function(t, i) {
            var s = i.prototype;
            e.widget(s.namespace + "." + s.widgetName, o, i._proto)
        }), delete r._childConstructors) : s._childConstructors.push(o), e.widget.bridge(i, o)
    }, e.widget.extend = function(i) {
        for (var n, a, r = s.call(arguments, 1), o = 0, h = r.length; h > o; o++)
            for (n in r[o]) a = r[o][n], r[o].hasOwnProperty(n) && a !== t && (i[n] = e.isPlainObject(a) ? e.isPlainObject(i[n]) ? e.widget.extend({}, i[n], a) : e.widget.extend({}, a) : a);
        return i
    }, e.widget.bridge = function(i, n) {
        var a = n.prototype.widgetFullName || i;
        e.fn[i] = function(r) {
            var o = "string" == typeof r,
                h = s.call(arguments, 1),
                l = this;
            return r = !o && h.length ? e.widget.extend.apply(null, [r].concat(h)) : r, o ? this.each(function() {
                var s, n = e.data(this, a);
                return n ? e.isFunction(n[r]) && "_" !== r.charAt(0) ? (s = n[r].apply(n, h), s !== n && s !== t ? (l = s && s.jquery ? l.pushStack(s.get()) : s, !1) : t) : e.error("no such method '" + r + "' for " + i + " widget instance") : e.error("cannot call methods on " + i + " prior to initialization; " + "attempted to call method '" + r + "'")
            }) : this.each(function() {
                var t = e.data(this, a);
                t ? t.option(r || {})._init() : e.data(this, a, new n(r, this))
            }), l
        }
    }, e.Widget = function() {}, e.Widget._childConstructors = [], e.Widget.prototype = {
        widgetName: "widget",
        widgetEventPrefix: "",
        defaultElement: "<div>",
        options: {
            disabled: !1,
            create: null
        },
        _createWidget: function(t, s) {
            s = e(s || this.defaultElement || this)[0], this.element = e(s), this.uuid = i++, this.eventNamespace = "." + this.widgetName + this.uuid, this.options = e.widget.extend({}, this.options, this._getCreateOptions(), t), this.bindings = e(), this.hoverable = e(), this.focusable = e(), s !== this && (e.data(s, this.widgetFullName, this), this._on(!0, this.element, {
                remove: function(e) {
                    e.target === s && this.destroy()
                }
            }), this.document = e(s.style ? s.ownerDocument : s.document || s), this.window = e(this.document[0].defaultView || this.document[0].parentWindow)), this._create(), this._trigger("create", null, this._getCreateEventData()), this._init()
        },
        _getCreateOptions: e.noop,
        _getCreateEventData: e.noop,
        _create: e.noop,
        _init: e.noop,
        destroy: function() {
            this._destroy(), this.element.unbind(this.eventNamespace).removeData(this.widgetName).removeData(this.widgetFullName).removeData(e.camelCase(this.widgetFullName)), this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName + "-disabled " + "ui-state-disabled"), this.bindings.unbind(this.eventNamespace), this.hoverable.removeClass("ui-state-hover"), this.focusable.removeClass("ui-state-focus")
        },
        _destroy: e.noop,
        widget: function() {
            return this.element
        },
        option: function(i, s) {
            var n, a, r, o = i;
            if (0 === arguments.length) return e.widget.extend({}, this.options);
            if ("string" == typeof i)
                if (o = {}, n = i.split("."), i = n.shift(), n.length) {
                    for (a = o[i] = e.widget.extend({}, this.options[i]), r = 0; n.length - 1 > r; r++) a[n[r]] = a[n[r]] || {}, a = a[n[r]];
                    if (i = n.pop(), s === t) return a[i] === t ? null : a[i];
                    a[i] = s
                } else {
                    if (s === t) return this.options[i] === t ? null : this.options[i];
                    o[i] = s
                }
            return this._setOptions(o), this
        },
        _setOptions: function(e) {
            var t;
            for (t in e) this._setOption(t, e[t]);
            return this
        },
        _setOption: function(e, t) {
            return this.options[e] = t, "disabled" === e && (this.widget().toggleClass(this.widgetFullName + "-disabled ui-state-disabled", !!t).attr("aria-disabled", t), this.hoverable.removeClass("ui-state-hover"), this.focusable.removeClass("ui-state-focus")), this
        },
        enable: function() {
            return this._setOption("disabled", !1)
        },
        disable: function() {
            return this._setOption("disabled", !0)
        },
        _on: function(i, s, n) {
            var a, r = this;
            "boolean" != typeof i && (n = s, s = i, i = !1), n ? (s = a = e(s), this.bindings = this.bindings.add(s)) : (n = s, s = this.element, a = this.widget()), e.each(n, function(n, o) {
                function h() {
                    return i || r.options.disabled !== !0 && !e(this).hasClass("ui-state-disabled") ? ("string" == typeof o ? r[o] : o).apply(r, arguments) : t
                }
                "string" != typeof o && (h.guid = o.guid = o.guid || h.guid || e.guid++);
                var l = n.match(/^(\w+)\s*(.*)$/),
                    u = l[1] + r.eventNamespace,
                    c = l[2];
                c ? a.delegate(c, u, h) : s.bind(u, h)
            })
        },
        _off: function(e, t) {
            t = (t || "").split(" ").join(this.eventNamespace + " ") + this.eventNamespace, e.unbind(t).undelegate(t)
        },
        _delay: function(e, t) {
            function i() {
                return ("string" == typeof e ? s[e] : e).apply(s, arguments)
            }
            var s = this;
            return setTimeout(i, t || 0)
        },
        _hoverable: function(t) {
            this.hoverable = this.hoverable.add(t), this._on(t, {
                mouseenter: function(t) {
                    e(t.currentTarget).addClass("ui-state-hover")
                },
                mouseleave: function(t) {
                    e(t.currentTarget).removeClass("ui-state-hover")
                }
            })
        },
        _focusable: function(t) {
            this.focusable = this.focusable.add(t), this._on(t, {
                focusin: function(t) {
                    e(t.currentTarget).addClass("ui-state-focus")
                },
                focusout: function(t) {
                    e(t.currentTarget).removeClass("ui-state-focus")
                }
            })
        },
        _trigger: function(t, i, s) {
            var n, a, r = this.options[t];
            if (s = s || {}, i = e.Event(i), i.type = (t === this.widgetEventPrefix ? t : this.widgetEventPrefix + t).toLowerCase(), i.target = this.element[0], a = i.originalEvent)
                for (n in a) n in i || (i[n] = a[n]);
            return this.element.trigger(i, s), !(e.isFunction(r) && r.apply(this.element[0], [i].concat(s)) === !1 || i.isDefaultPrevented())
        }
    }, e.each({
        show: "fadeIn",
        hide: "fadeOut"
    }, function(t, i) {
        e.Widget.prototype["_" + t] = function(s, n, a) {
            "string" == typeof n && (n = {
                effect: n
            });
            var r, o = n ? n === !0 || "number" == typeof n ? i : n.effect || i : t;
            n = n || {}, "number" == typeof n && (n = {
                duration: n
            }), r = !e.isEmptyObject(n), n.complete = a, n.delay && s.delay(n.delay), r && e.effects && e.effects.effect[o] ? s[t](n) : o !== t && s[o] ? s[o](n.duration, n.easing, a) : s.queue(function(i) {
                e(this)[t](), a && a.call(s[0]), i()
            })
        }
    })
})(jQuery);
(function(e) {
    var t = !1;
    e(document).mouseup(function() {
        t = !1
    }), e.widget("ui.mouse", {
        version: "1.10.3",
        options: {
            cancel: "input,textarea,button,select,option",
            distance: 1,
            delay: 0
        },
        _mouseInit: function() {
            var t = this;
            this.element.bind("mousedown." + this.widgetName, function(e) {
                return t._mouseDown(e)
            }).bind("click." + this.widgetName, function(i) {
                return !0 === e.data(i.target, t.widgetName + ".preventClickEvent") ? (e.removeData(i.target, t.widgetName + ".preventClickEvent"), i.stopImmediatePropagation(), !1) : undefined
            }), this.started = !1
        },
        _mouseDestroy: function() {
            this.element.unbind("." + this.widgetName), this._mouseMoveDelegate && e(document).unbind("mousemove." + this.widgetName, this._mouseMoveDelegate).unbind("mouseup." + this.widgetName, this._mouseUpDelegate)
        },
        _mouseDown: function(i) {
            if (!t) {
                this._mouseStarted && this._mouseUp(i), this._mouseDownEvent = i;
                var s = this,
                    n = 1 === i.which,
                    a = "string" == typeof this.options.cancel && i.target.nodeName ? e(i.target).closest(this.options.cancel).length : !1;
                return n && !a && this._mouseCapture(i) ? (this.mouseDelayMet = !this.options.delay, this.mouseDelayMet || (this._mouseDelayTimer = setTimeout(function() {
                    s.mouseDelayMet = !0
                }, this.options.delay)), this._mouseDistanceMet(i) && this._mouseDelayMet(i) && (this._mouseStarted = this._mouseStart(i) !== !1, !this._mouseStarted) ? (i.preventDefault(), !0) : (!0 === e.data(i.target, this.widgetName + ".preventClickEvent") && e.removeData(i.target, this.widgetName + ".preventClickEvent"), this._mouseMoveDelegate = function(e) {
                    return s._mouseMove(e)
                }, this._mouseUpDelegate = function(e) {
                    return s._mouseUp(e)
                }, e(document).bind("mousemove." + this.widgetName, this._mouseMoveDelegate).bind("mouseup." + this.widgetName, this._mouseUpDelegate), i.preventDefault(), t = !0, !0)) : !0
            }
        },
        _mouseMove: function(t) {
            return e.ui.ie && (!document.documentMode || 9 > document.documentMode) && !t.button ? this._mouseUp(t) : this._mouseStarted ? (this._mouseDrag(t), t.preventDefault()) : (this._mouseDistanceMet(t) && this._mouseDelayMet(t) && (this._mouseStarted = this._mouseStart(this._mouseDownEvent, t) !== !1, this._mouseStarted ? this._mouseDrag(t) : this._mouseUp(t)), !this._mouseStarted)
        },
        _mouseUp: function(t) {
            return e(document).unbind("mousemove." + this.widgetName, this._mouseMoveDelegate).unbind("mouseup." + this.widgetName, this._mouseUpDelegate), this._mouseStarted && (this._mouseStarted = !1, t.target === this._mouseDownEvent.target && e.data(t.target, this.widgetName + ".preventClickEvent", !0), this._mouseStop(t)), !1
        },
        _mouseDistanceMet: function(e) {
            return Math.max(Math.abs(this._mouseDownEvent.pageX - e.pageX), Math.abs(this._mouseDownEvent.pageY - e.pageY)) >= this.options.distance
        },
        _mouseDelayMet: function() {
            return this.mouseDelayMet
        },
        _mouseStart: function() {},
        _mouseDrag: function() {},
        _mouseStop: function() {},
        _mouseCapture: function() {
            return !0
        }
    })
})(jQuery);
(function(t) {
    var e = 5;
    t.widget("ui.slider", t.ui.mouse, {
        version: "1.10.3",
        widgetEventPrefix: "slide",
        options: {
            animate: !1,
            distance: 0,
            max: 100,
            min: 0,
            orientation: "horizontal",
            range: !1,
            step: 1,
            value: 0,
            values: null,
            change: null,
            slide: null,
            start: null,
            stop: null
        },
        _create: function() {
            this._keySliding = !1, this._mouseSliding = !1, this._animateOff = !0, this._handleIndex = null, this._detectOrientation(), this._mouseInit(), this.element.addClass("ui-slider ui-slider-" + this.orientation + " ui-widget" + " ui-widget-content" + " ui-corner-all"), this._refresh(), this._setOption("disabled", this.options.disabled), this._animateOff = !1
        },
        _refresh: function() {
            this._createRange(), this._createHandles(), this._setupEvents(), this._refreshValue()
        },
        _createHandles: function() {
            var e, i, s = this.options,
                n = this.element.find(".ui-slider-handle").addClass("ui-state-default ui-corner-all"),
                a = "<a class='ui-slider-handle ui-state-default ui-corner-all' href='#'></a>",
                o = [];
            for (i = s.values && s.values.length || 1, n.length > i && (n.slice(i).remove(), n = n.slice(0, i)), e = n.length; i > e; e++) o.push(a);
            this.handles = n.add(t(o.join("")).appendTo(this.element)), this.handle = this.handles.eq(0), this.handles.each(function(e) {
                t(this).data("ui-slider-handle-index", e)
            })
        },
        _createRange: function() {
            var e = this.options,
                i = "";
            e.range ? (e.range === !0 && (e.values ? e.values.length && 2 !== e.values.length ? e.values = [e.values[0], e.values[0]] : t.isArray(e.values) && (e.values = e.values.slice(0)) : e.values = [this._valueMin(), this._valueMin()]), this.range && this.range.length ? this.range.removeClass("ui-slider-range-min ui-slider-range-max").css({
                left: "",
                bottom: ""
            }) : (this.range = t("<div></div>").appendTo(this.element), i = "ui-slider-range ui-widget-header ui-corner-all"), this.range.addClass(i + ("min" === e.range || "max" === e.range ? " ui-slider-range-" + e.range : ""))) : this.range = t([])
        },
        _setupEvents: function() {
            var t = this.handles.add(this.range).filter("a");
            this._off(t), this._on(t, this._handleEvents), this._hoverable(t), this._focusable(t)
        },
        _destroy: function() {
            this.handles.remove(), this.range.remove(), this.element.removeClass("ui-slider ui-slider-horizontal ui-slider-vertical ui-widget ui-widget-content ui-corner-all"), this._mouseDestroy()
        },
        _mouseCapture: function(e) {
            var i, s, n, a, o, r, h, l, u = this,
                c = this.options;
            return c.disabled ? !1 : (this.elementSize = {
                width: this.element.outerWidth(),
                height: this.element.outerHeight()
            }, this.elementOffset = this.element.offset(), i = {
                x: e.pageX,
                y: e.pageY
            }, s = this._normValueFromMouse(i), n = this._valueMax() - this._valueMin() + 1, this.handles.each(function(e) {
                var i = Math.abs(s - u.values(e));
                (n > i || n === i && (e === u._lastChangedValue || u.values(e) === c.min)) && (n = i, a = t(this), o = e)
            }), r = this._start(e, o), r === !1 ? !1 : (this._mouseSliding = !0, this._handleIndex = o, a.addClass("ui-state-active").focus(), h = a.offset(), l = !t(e.target).parents().addBack().is(".ui-slider-handle"), this._clickOffset = l ? {
                left: 0,
                top: 0
            } : {
                left: e.pageX - h.left - a.width() / 2,
                top: e.pageY - h.top - a.height() / 2 - (parseInt(a.css("borderTopWidth"), 10) || 0) - (parseInt(a.css("borderBottomWidth"), 10) || 0) + (parseInt(a.css("marginTop"), 10) || 0)
            }, this.handles.hasClass("ui-state-hover") || this._slide(e, o, s), this._animateOff = !0, !0))
        },
        _mouseStart: function() {
            return !0
        },
        _mouseDrag: function(t) {
            var e = {
                    x: t.pageX,
                    y: t.pageY
                },
                i = this._normValueFromMouse(e);
            return this._slide(t, this._handleIndex, i), !1
        },
        _mouseStop: function(t) {
            return this.handles.removeClass("ui-state-active"), this._mouseSliding = !1, this._stop(t, this._handleIndex), this._change(t, this._handleIndex), this._handleIndex = null, this._clickOffset = null, this._animateOff = !1, !1
        },
        _detectOrientation: function() {
            this.orientation = "vertical" === this.options.orientation ? "vertical" : "horizontal"
        },
        _normValueFromMouse: function(t) {
            var e, i, s, n, a;
            return "horizontal" === this.orientation ? (e = this.elementSize.width, i = t.x - this.elementOffset.left - (this._clickOffset ? this._clickOffset.left : 0)) : (e = this.elementSize.height, i = t.y - this.elementOffset.top - (this._clickOffset ? this._clickOffset.top : 0)), s = i / e, s > 1 && (s = 1), 0 > s && (s = 0), "vertical" === this.orientation && (s = 1 - s), n = this._valueMax() - this._valueMin(), a = this._valueMin() + s * n, this._trimAlignValue(a)
        },
        _start: function(t, e) {
            var i = {
                handle: this.handles[e],
                value: this.value()
            };
            return this.options.values && this.options.values.length && (i.value = this.values(e), i.values = this.values()), this._trigger("start", t, i)
        },
        _slide: function(t, e, i) {
            var s, n, a;
            this.options.values && this.options.values.length ? (s = this.values(e ? 0 : 1), 2 === this.options.values.length && this.options.range === !0 && (0 === e && i > s || 1 === e && s > i) && (i = s), i !== this.values(e) && (n = this.values(), n[e] = i, a = this._trigger("slide", t, {
                handle: this.handles[e],
                value: i,
                values: n
            }), s = this.values(e ? 0 : 1), a !== !1 && this.values(e, i, !0))) : i !== this.value() && (a = this._trigger("slide", t, {
                handle: this.handles[e],
                value: i
            }), a !== !1 && this.value(i))
        },
        _stop: function(t, e) {
            var i = {
                handle: this.handles[e],
                value: this.value()
            };
            this.options.values && this.options.values.length && (i.value = this.values(e), i.values = this.values()), this._trigger("stop", t, i)
        },
        _change: function(t, e) {
            if (!this._keySliding && !this._mouseSliding) {
                var i = {
                    handle: this.handles[e],
                    value: this.value()
                };
                this.options.values && this.options.values.length && (i.value = this.values(e), i.values = this.values()), this._lastChangedValue = e, this._trigger("change", t, i)
            }
        },
        value: function(t) {
            return arguments.length ? (this.options.value = this._trimAlignValue(t), this._refreshValue(), this._change(null, 0), undefined) : this._value()
        },
        values: function(e, i) {
            var s, n, a;
            if (arguments.length > 1) return this.options.values[e] = this._trimAlignValue(i), this._refreshValue(), this._change(null, e), undefined;
            if (!arguments.length) return this._values();
            if (!t.isArray(arguments[0])) return this.options.values && this.options.values.length ? this._values(e) : this.value();
            for (s = this.options.values, n = arguments[0], a = 0; s.length > a; a += 1) s[a] = this._trimAlignValue(n[a]), this._change(null, a);
            this._refreshValue()
        },
        _setOption: function(e, i) {
            var s, n = 0;
            switch ("range" === e && this.options.range === !0 && ("min" === i ? (this.options.value = this._values(0), this.options.values = null) : "max" === i && (this.options.value = this._values(this.options.values.length - 1), this.options.values = null)), t.isArray(this.options.values) && (n = this.options.values.length), t.Widget.prototype._setOption.apply(this, arguments), e) {
                case "orientation":
                    this._detectOrientation(), this.element.removeClass("ui-slider-horizontal ui-slider-vertical").addClass("ui-slider-" + this.orientation), this._refreshValue();
                    break;
                case "value":
                    this._animateOff = !0, this._refreshValue(), this._change(null, 0), this._animateOff = !1;
                    break;
                case "values":
                    for (this._animateOff = !0, this._refreshValue(), s = 0; n > s; s += 1) this._change(null, s);
                    this._animateOff = !1;
                    break;
                case "min":
                case "max":
                    this._animateOff = !0, this._refreshValue(), this._animateOff = !1;
                    break;
                case "range":
                    this._animateOff = !0, this._refresh(), this._animateOff = !1
            }
        },
        _value: function() {
            var t = this.options.value;
            return t = this._trimAlignValue(t)
        },
        _values: function(t) {
            var e, i, s;
            if (arguments.length) return e = this.options.values[t], e = this._trimAlignValue(e);
            if (this.options.values && this.options.values.length) {
                for (i = this.options.values.slice(), s = 0; i.length > s; s += 1) i[s] = this._trimAlignValue(i[s]);
                return i
            }
            return []
        },
        _trimAlignValue: function(t) {
            if (this._valueMin() >= t) return this._valueMin();
            if (t >= this._valueMax()) return this._valueMax();
            var e = this.options.step > 0 ? this.options.step : 1,
                i = (t - this._valueMin()) % e,
                s = t - i;
            return 2 * Math.abs(i) >= e && (s += i > 0 ? e : -e), parseFloat(s.toFixed(5))
        },
        _valueMin: function() {
            return this.options.min
        },
        _valueMax: function() {
            return this.options.max
        },
        _refreshValue: function() {
            var e, i, s, n, a, o = this.options.range,
                r = this.options,
                h = this,
                l = this._animateOff ? !1 : r.animate,
                u = {};
            this.options.values && this.options.values.length ? this.handles.each(function(s) {
                i = 100 * ((h.values(s) - h._valueMin()) / (h._valueMax() - h._valueMin())), u["horizontal" === h.orientation ? "left" : "bottom"] = i + "%", t(this).stop(1, 1)[l ? "animate" : "css"](u, r.animate), h.options.range === !0 && ("horizontal" === h.orientation ? (0 === s && h.range.stop(1, 1)[l ? "animate" : "css"]({
                    left: i + "%"
                }, r.animate), 1 === s && h.range[l ? "animate" : "css"]({
                    width: i - e + "%"
                }, {
                    queue: !1,
                    duration: r.animate
                })) : (0 === s && h.range.stop(1, 1)[l ? "animate" : "css"]({
                    bottom: i + "%"
                }, r.animate), 1 === s && h.range[l ? "animate" : "css"]({
                    height: i - e + "%"
                }, {
                    queue: !1,
                    duration: r.animate
                }))), e = i
            }) : (s = this.value(), n = this._valueMin(), a = this._valueMax(), i = a !== n ? 100 * ((s - n) / (a - n)) : 0, u["horizontal" === this.orientation ? "left" : "bottom"] = i + "%", this.handle.stop(1, 1)[l ? "animate" : "css"](u, r.animate), "min" === o && "horizontal" === this.orientation && this.range.stop(1, 1)[l ? "animate" : "css"]({
                width: i + "%"
            }, r.animate), "max" === o && "horizontal" === this.orientation && this.range[l ? "animate" : "css"]({
                width: 100 - i + "%"
            }, {
                queue: !1,
                duration: r.animate
            }), "min" === o && "vertical" === this.orientation && this.range.stop(1, 1)[l ? "animate" : "css"]({
                height: i + "%"
            }, r.animate), "max" === o && "vertical" === this.orientation && this.range[l ? "animate" : "css"]({
                height: 100 - i + "%"
            }, {
                queue: !1,
                duration: r.animate
            }))
        },
        _handleEvents: {
            keydown: function(i) {
                var s, n, a, o, r = t(i.target).data("ui-slider-handle-index");
                switch (i.keyCode) {
                    case t.ui.keyCode.HOME:
                    case t.ui.keyCode.END:
                    case t.ui.keyCode.PAGE_UP:
                    case t.ui.keyCode.PAGE_DOWN:
                    case t.ui.keyCode.UP:
                    case t.ui.keyCode.RIGHT:
                    case t.ui.keyCode.DOWN:
                    case t.ui.keyCode.LEFT:
                        if (i.preventDefault(), !this._keySliding && (this._keySliding = !0, t(i.target).addClass("ui-state-active"), s = this._start(i, r), s === !1)) return
                }
                switch (o = this.options.step, n = a = this.options.values && this.options.values.length ? this.values(r) : this.value(), i.keyCode) {
                    case t.ui.keyCode.HOME:
                        a = this._valueMin();
                        break;
                    case t.ui.keyCode.END:
                        a = this._valueMax();
                        break;
                    case t.ui.keyCode.PAGE_UP:
                        a = this._trimAlignValue(n + (this._valueMax() - this._valueMin()) / e);
                        break;
                    case t.ui.keyCode.PAGE_DOWN:
                        a = this._trimAlignValue(n - (this._valueMax() - this._valueMin()) / e);
                        break;
                    case t.ui.keyCode.UP:
                    case t.ui.keyCode.RIGHT:
                        if (n === this._valueMax()) return;
                        a = this._trimAlignValue(n + o);
                        break;
                    case t.ui.keyCode.DOWN:
                    case t.ui.keyCode.LEFT:
                        if (n === this._valueMin()) return;
                        a = this._trimAlignValue(n - o)
                }
                this._slide(i, r, a)
            },
            click: function(t) {
                t.preventDefault()
            },
            keyup: function(e) {
                var i = t(e.target).data("ui-slider-handle-index");
                this._keySliding && (this._keySliding = !1, this._stop(e, i), this._change(e, i), t(e.target).removeClass("ui-state-active"))
            }
        }
    })
})(jQuery);

var cleaverFilterObj = {
    elClosed: '.icon_times_apply',
    elCount: '#apply-count',
    location: 'right',
    elPos: '.frame-group-checks .frame-label',
    apply: '.apply',
    dropDownEff: 'slideToggle',
    dropDownEffDur: '400',
    dropDownArr: [],
    currentPosScroll: []
};
