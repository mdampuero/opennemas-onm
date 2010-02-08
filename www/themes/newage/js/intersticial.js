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
var IntersticialBanner = Class.create({
    initialize: function(options) {
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
        this.template = '<di' + 'v class="closeButton"><' +
            'a href="/" title="Saltar publicidad">Saltar publicidad</a></div><d' +
            'iv class="content">#{content}</div>';
                
        this.cookieManager = new Cookies();
        this.cookieName = options.cookieName || 'intersticial';
        
        if(!this.cookieManager.get(this.cookieName)) {
            this.buildHTML();
            
            if(!this.noRender) {
                this.render();
            }
        }
    },
    
    buildHTML: function() {
        //this.element = new Element('div');
        this.element = this.rootDocument.createElement('DIV');
        this.element = $(this.element);
        this.element.addClassName('intersticial');
        
        var $content = this.content;
        if(this.useIframe) {
            $content = this.iframeHTML.interpolate({'src': this.iframeSrc});
        }
        
        $content = this.template.interpolate({'content': $content});
        this.element.update($content);
        
        this.element.select('div.closeButton a')[0].observe('click', this.onClose.bindAsEventListener(this));
        if(this.publiId!=null) {
            this.element.select('div.content')[0].observe('click', this.gotoPubli.bindAsEventListener(this));
        }
    },
    
    render: function() {
        if(Prototype.Browser.IE) {
            this.rootDocument.observe('dom:loaded', this._hideSelectElementBugIE.bind(this, true));
        }
        
        try {
            var container = this.rootElement;
            
            // bastardIE
            if(container.childNodes.length>0) {
                container.insertBefore(this.element, container.childNodes[0]);
            } else {
                container.appendChild(this.element);
            }
            
            if(this.timeout > 0) {
                window.setTimeout(this.close.bind(this), this.timeout);
            }
        } catch(e) {
            alert('#' + e.number + ' ' + e.message);
        }
        
        // Write cookie before display
        this.cookieManager.set(this.cookieName, '1', this.daysExpire);
    },
    
    onClose: function(e) {
        Event.stop(e); // stop event
        this.close();
    },    
    
    close: function() {
        // Set cookie
        //this.cookieManager.set(this.cookieName, '1', this.daysExpire);
        
        this._hideSelectElementBugIE(false);
        
        //this.element.setStyle({display: 'none'});
        Effect.Fade(this.element);
    },
    
    gotoPubli: function(e) {
        Event.stop(e); // stop event
        //location.href = '/advertisement.php?action=show&publi_id=' + this.publiId;
        window.open('/advertisement.php?action=show&publi_id=' + this.publiId, '_blank');
        this.close();
    },
    
    show: function() {
        this._hideSelectElementBugIE(true);
        this.element.setStyle({display: ''});
    },
    
    _hideSelectElementBugIE: function(hide) {
        if(Prototype.Browser.IE) {
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
});


var Cookies = Class.create({
    initialize: function(path, domain) {
        this.path = path || '/';
        this.domain = domain || null;
    },
    
    // Sets a cookie
    set: function(key, value, days) {
        if (typeof key != 'string') {
            throw "Invalid key";
        }
        if (typeof value != 'string' && typeof value != 'number') {
            throw "Invalid value";
        }
        if (days && typeof days != 'number') {
            throw "Invalid expiration time";
        }
        var setValue = key + '=' + escape(new String(value));
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var setExpiration = "; expires="+date.toGMTString();
        } else var setExpiration = "";
        var setPath = '; path='+escape(this.path);
        var setDomain = (this.domain) ? '; domain='+escape(this.domain) : '';
        var cookieString = setValue+setExpiration+setPath+setDomain;
        document.cookie = cookieString;
    },
    
    // Returns a cookie value or false
    get: function(key) {
        var keyEquals = key+"=";
        var value = false;
        document.cookie.split(';').invoke('strip').each(function(s){
            if (s.startsWith(keyEquals)) {
                value = unescape(s.substring(keyEquals.length, s.length));
                throw $break;
            }
        });
        return value;
    },
    
    // Clears a cookie
    clear: function(key) {
        this.set(key, '', -1);
    },
    
    // Clears all cookies
    clearAll: function() {
        document.cookie.split(';').collect(function(s){
            return s.split('=').first().strip();
        }).each(function(key){
            this.clear(key);
        }.bind(this));
    }
});
