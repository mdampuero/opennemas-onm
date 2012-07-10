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

function onChangeGroup(evaluateControl, ids)
{
    if (document.getElementById)
    {
        var combo = document.getElementById('ids_category');
        //se define la variable "el" igual a nuestro div
        if (evaluateControl.options[evaluateControl.selectedIndex].text.toLowerCase() == "administrador")
        {
            for (iIndex=0; iIndex<ids.length; iIndex++)
            {
                var hideDiv = document.getElementById(ids[iIndex]);
                hideDiv.style.display = 'none'; //damos un atributo display:none que oculta el div
            }
            combo.options[0].selected = false;
            for(iIndex=1; iIndex<combo.options.length;  iIndex++)
                combo.options[iIndex].selected = true;
        }
        else
        {
            for (iIndex=0; iIndex<ids.length; iIndex++)
            {
                var showDiv = document.getElementById(ids[iIndex]);
                if (showDiv) {
                    showDiv.style.display = 'block'; //damos un atributo display:block que muestra el div
                }
            }
            if (combo) {
                for(iIndex=0; iIndex<combo.options.length;  iIndex++)
                    combo.options[iIndex].selected = false;
            }
        }

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