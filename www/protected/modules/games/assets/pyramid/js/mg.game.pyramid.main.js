var device_ratio = 1,
    is_touch_device = is_touch_device();

if (is_touch_device) {
    device_ratio = window.devicePixelRatio;
}

$( document ).ready(function() {
    if (is_touch_device) {
        $("body").addClass('touch_device');
    }
    //            alert(device_ratio);

    BrowserDetect.init();
    $("body").addClass(BrowserDetect.browser);
})

var BrowserDetect = {
    init: function () {
        this.browser = this.searchString(this.dataBrowser) || "Other";
        this.version = this.searchVersion(navigator.userAgent) ||       this.searchVersion(navigator.appVersion) || "Unknown";
    },

    searchString: function (data) {
        for (var i=0 ; i < data.length ; i++) {
            var dataString = data[i].string;
            this.versionSearchString = data[i].subString;

            if (dataString.indexOf(data[i].subString) != -1) {
                return data[i].identity;
            }
        }
    },

    searchVersion: function (dataString) {
        var index = dataString.indexOf(this.versionSearchString);
        if (index == -1) return;
        return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
    },

    dataBrowser:
        [
            { string: navigator.userAgent, subString: "Chrome",  identity: "Chrome" },
            { string: navigator.userAgent, subString: "MSIE",    identity: "IE" },
            { string: navigator.userAgent, subString: "Firefox", identity: "FF" },
            { string: navigator.userAgent, subString: "Safari",  identity: "Safari" },
            { string: navigator.userAgent, subString: "Opera",   identity: "Opera" }
        ]

};

$.fn.centerVertival = function () {
    this.css("position", "absolute");
    if (this.outerWidth() < $(window).width()) {
        this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
        return this;
    }
};

function is_touch_device() {
    return !!('ontouchstart' in window) // works on most browsers
        || !!('onmsgesturechange' in window); // works on ie10
};

$.fn.centerHorizontal = function () {
    this.css("position", "absolute");
    if (this.outerWidth() < $(window).width()) {
        this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
        return this;
    }
};

$.fn.centerVertival = function () {
    this.css("position", "absolute");
    if (this.outerWidth() < $(window).width()) {
        this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
        return this;
    }
};
