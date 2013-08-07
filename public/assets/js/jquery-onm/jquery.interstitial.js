/**
 * Intersticial banner
 *
 * Usage:
 * var intersticialBanner = new IntersticialBanner({
 *      iframeSrc: '/public.html',  // use with useIframe
 *      timeout: 4000,    // -1 don't disappear
 *      useIframe: true   // embed a iframe
 * });
 *
 * var intersticialBanner = new IntersticialBanner({
 *      publiId: '200908...',
 *      content: '<strong>HTML content</strong>',  // HTML to innerHTML
 *      timeout: 4000,    // timeout (ms) to hide banner (4000ms., 4sec. by default)
 *      daysExpire: 1,     // days for waiting to expire the cookie (one day by default)
 *      noRender: true
 * });
 */

function IntersticialBanner(options) {
    this.element = null; // Container Div

    // FIX iframe compatibility
    this.rootDocument = window.top.document;
    this.rootElement  = options.rootElement || this.rootDocument.getElementsByTagName('body')[0];
    this.rootElement  = $(this.rootElement);

    this.publiId = options.publiId || null;
    this.timeout = options.timeout || 4000;
    this.content = options.content || '';

    // Days to expire cookie
    this.daysExpire = options.daysExpire || 1;

    this.useIframe = options.useIframe || false;
    this.iframeSrc = options.iframeSrc || false;
    this.noRender  = options.noRender || false;

    this.iframeHTML = '<ifr' + 'ame width="100%" height="100%" frameborder="0" src="#{src}" marginheight="0" marginwidth="0" scrolling="no"></iframe>';
    this.template = '<div id="intesticial-ad">'+
                        '<div class="wrapper"><div class="header"><div class="logo-and-phrase"><div class="logo"></div>Entrando en la página solicitada</div>'+
                            '<div class="closeButton"><a href="/" title="Saltar publicidad"><span>Saltar publicidad</span></a></div>'+
                        '</div>'+
                        '<div class="content">#{content}</div></div>'+
                    '</div>';



    this.cookieName = options.cookieName || 'intersticial';

    if($.cookie(this.cookieName) == null) {
        this.buildHTML();

        if(!this.noRender) {
            this.render();
        }
    }
};

IntersticialBanner.prototype = {

    buildHTML: function() {
        //this.element = new Element('div');
        this.element = this.rootDocument.createElement('DIV');
        this.element = $(this.element);
        this.element.addClass('intersticial');
        // <style type="text/css" media="screen, projection">
        // @import url(/themes/lucidity/css/parts/intersticial.css);
        // </style>

        var $content = this.content;
        if(this.useIframe) {
            $content = this.iframeHTML.replace(/#\{src\}/, this.iframeSrc);
        }

        $content = this.template.replace(/#\{content\}/, $content);
        this.element.html($content);

        $('div.closeButton a', this.element).click( $.proxy(this, 'onClose') );
        if (this.publiId != null) {
            $('div.content', this.element).click( $.proxy(this, 'gotoPubli') );
        }
    },

    render: function() {
        if($.browser.IE) {
            $(this.rootDocument).ready($.proxy(this, "_hideSelectElementBugIE", [true]));
        }

        try {
            var container = this.rootElement;

            container.prepend( this.element );

            if(this.timeout > 0) {
                window.setTimeout($.proxy(this, "close"), this.timeout);
            }
        } catch(e) {
            alert('#' + e.number + ' ' + e.message);
        }

        var expireDate = new Date();
        expireDate.setMinutes(expireDate.getMinutes() + this.daysExpire);

        // Write cookie before display
        $.cookie(this.cookieName, '1', {expires: expireDate, path: '/'});
    },

    onClose: function(event) {
        event.stopPropagation(); // stop event
        event.preventDefault();

        this.close();
    },

    close: function() {
        // Set cookie

        this._hideSelectElementBugIE(false);

        //$(this.element).css({display: 'none'});
        $(this.element).fadeOut();
    },

    gotoPubli: function(event) {
        event.stopPropagation(); // stop event
        event.preventDefault();


        window.open('/ads/' + this.publiId + '.html', '_blank');

        this.close();
    },

    show: function() {
        this._hideSelectElementBugIE(true);
        $(this.element).css({display: ''});
    },

    _hideSelectElementBugIE: function(hide) {
        if($.browser.msie) {
            var selects = this.rootDocument.getElementsByTagName('select');
            for(var i=0; i<selects.length; i++) {
                if(hide) {
                    selects[i].style.display = 'none';
                } else {
                    selects[i].style.display = '';
                }
            }
        }
    }
};
