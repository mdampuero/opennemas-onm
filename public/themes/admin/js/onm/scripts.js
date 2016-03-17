// usage: log('inside coolFunc', this, arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function f() { log.history = log.history || []; log.history.push(arguments); if (this.console) { var args = arguments, newarr; args.callee = args.callee.caller; newarr = [].slice.call(args); if (typeof console.log === 'object') log.apply.call(console.log, console, newarr); else console.log.apply(console, newarr);}};

(function(a) {function b() {}for (var c = 'assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn'.split(','), d; !!(d = c.pop());) {a[d] = a[d] || b;}});
(function() {try {console.log();return window.console;}catch (a) {return (window.console = {});}}());

jQuery.fn.flash = function(color, duration )
{
    var current = this.css('color');
    this.animate({ color: 'rgb(' + color + ')' }, duration / 2);
    this.animate({ color: current }, duration / 2);
};

function fill_tags(raw_info, target_element, url) {
    jQuery.ajax({
        url: url + '?data=' + raw_info
    }).done(function(data) {
        jQuery(target_element).val(data);
    });
}

function load_ajax_in_container(url, container) {
    jQuery.ajax({
        url: url,
        async: true,
        beforeSend: function() {
            container.html('<div class="spinner"></div>Loading request...');
        },
        success: function(data) {
            container.html(data);
        }
    });
}
