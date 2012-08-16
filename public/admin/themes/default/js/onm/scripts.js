// usage: log('inside coolFunc', this, arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function f(){ log.history = log.history || []; log.history.push(arguments); if(this.console) { var args = arguments, newarr; args.callee = args.callee.caller; newarr = [].slice.call(args); if (typeof console.log === 'object') log.apply.call(console.log, console, newarr); else console.log.apply(console, newarr);}};

(function(a){function b(){}for(var c="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),d;!!(d=c.pop());){a[d]=a[d]||b;}})
(function(){try{console.log();return window.console;}catch(a){return (window.console={});}}());

// Prototype free code, must be cleaned

function salir(msg,url) {
    if (confirm(msg)) {
        location.href = url;
    }
}

function countWords(text,counter){

    var y=text.value;
    var r = 0;
    a=y.replace(/\s/g,' ');
    a=a.split(' ');
    for (z=0; z<a.length; z++) {if (a[z].length > 0) r++;}
    counter.value=r;
}

function counttiny(counter, editor){

    //var codigo = document.getElementById('body_ifr').contentWindow.document.getElementById('tinymce').innerHTML;
    var codigo = editor.getContent();

    resul=codigo.replace(/<[^>]+>/g,''); //Quitamos html;
    var y=resul;
    var r = 0;
    a=y.replace(/\s/g,' ');
    a=a.split(' ');
    for (z=0; z<a.length; z++) {if (a[z].length > 0) r++;}
    counter.value=r;

}

function fill_tags (raw_info, target_element) {
    jQuery.ajax({
        url: "/admin/controllers/common/content.php?action=calculate-tags&data="+raw_info
    }).done(function(data) {
        log(data);
        jQuery(target_element).val(data);
    });
}