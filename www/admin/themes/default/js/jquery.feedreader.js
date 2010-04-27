(function($) {
    
    $.fn.feedreader = function(url, options) {        
        // build main options before element iteration
        var opts = $.extend({}, $.fn.feedreader.defaults, options);
        
        // iterate and reformat each matched element
        return this.each(function() {
            $this = $(this);                        
            
            // build element specific options
            var o = $.meta ? $.extend({}, opts, $this.data()) : opts;
            
            new FeedReaderClass($this, url, o);
        });
    };

})(jQuery);


var FeedReaderClass = function(elem, url, options) {
    
    this.getRss = function() {
        this.elem.html('<img src="' + this.imgPath + 'loading.gif" border="0" />');
        
        this.request = jQuery.ajax({
            url: this.proxyUrl,
            data: {
                url: encodeURIComponent(this.url)
            },
            type:    'GET',
            success: jQuery.proxy(this, 'onSuccess'),            
            error:   jQuery.proxy(this, 'showNotice')
        });
        
        this.interval = window.setInterval(jQuery.proxy(this, 'onTimeout'), this.timeout);
    };

    this.onTimeout = function() {
        window.clearInterval(this.interval);
        
        this.request.abort();
        this.showNotice();
    };

    this.hideRssBox = function() {
        jQuery( this.elem.get(0).parentNode ).fadeOut("normal");
    };

    this.showNotice = function() {
        this.elem.html('<strong>Error al cargar noticias desde el servidor.</strong>');        
        window.setTimeout( jQuery.proxy(this, 'hideRssBox'), 8000);
    };
    
    this.onSuccess = function(data, codeStatus, xhr) {
        window.clearInterval(this.interval);        
        
        try {
            this[this.parser]( data );
            this.render();
        } catch(e) {
            console.log(e);
        }
    };
    
    this.render =  function() {
        this.output = '<ul>';
        
        var partial = '';
        for(var i=0; i<this.items.length; i++) {
            partial = this.themes[this.theme];
            
            for(var p in this.items[i]) {
                regEx = new RegExp('#\{' + p + '\}');
                partial = partial.replace(regEx, this.items[i][p]);
            }
            
            this.output += partial;
        }
        
        this.output += '<ul>';
        
        this.elem.html( this.output );
        console.log(this.elem.find('li a[description]'));
        this.elem.find('li a[description]').each(function() {
            // Tooltip if description text exists
            var text = $.trim(decodeURI($(this).attr('description')));
            if( text.length > 0 ) {
                $(this).qtip({
                    content: text,
                    position: {
                        corner: {
                            target: 'topMiddle',
                            tooltip: 'bottomMiddle'
                        }
                    },
                    style: {                         
                        tip: 'bottomMiddle',
                        name: 'light' 
                    }                    
                });
            }
        });

    };
    
    /**
     * Parse a date "Tue, 10 Nov 2009 00:12:49 +0100" and
     * convert to Javascript Date Object
     *
     * @param rssDate {string} Date with pubDate format
     * @returns {object} Return a Javascript Date Object
     */
    this.formatDate = function(rssDate) {
        var monthInt = {'Jan': 0, 'Feb': 1, 'Mar': 2,
                      'Apr': 3, 'May': 4, 'Jun': 5,
                      'Jul': 6, 'Aug': 7, 'Sep': 8,
                      'Oct': 9, 'Nov':10, 'Dec': 11 };
        var months = ['Enero', 'Febrero', 'Marzo',
                      'Abril', 'Mayo', 'Junio',
                      'Julio', 'Agosto', 'Septiembre',
                      'Octubre', 'Noviembre', 'Diciembre'];
        var days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        var formattedDate = '';
        var date = null;
        
        // Tue, 10 Nov 2009 00:12:49 +0100
        if(/^[a-z]{3}\,[ ]?([0-9]{1,2}) ([a-z]{3}) ([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2}) /i.test(rssDate)) {
            var matches = /^[a-z]{3}\,[ ]?([0-9]{1,2}) ([a-z]{3}) ([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2}) /i.exec(rssDate);
            date = new Date(matches[3], monthInt[matches[2]], matches[1], matches[4], matches[5], matches[6]);
        } else {            
            // 2009-11-10T14:35:41Z
            if(/^([0-9]{4})\-([0-9]{2})\-([0-9]{2})[T]([0-9]{2}):([0-9]{2}):([0-9]{2})[Z]$/i.test(rssDate)) {            
                var matches = /^([0-9]{4})\-([0-9]{2})\-([0-9]{2})[T]([0-9]{2}):([0-9]{2}):([0-9]{2})[Z]$/i.exec(rssDate);
                date = new Date(matches[1], matches[2], matches[3], matches[4], matches[5], matches[6]);
            }
        }
        
        formattedDate = days[date.getUTCDay()] + ', ' + date.getDate() + ' de ' + months[date.getUTCMonth()] + ' de ' + date.getFullYear();
        
        return formattedDate;
    };
    
    this.parseRss20 = function(xml) {
        var items = xml.getElementsByTagName('item');
        var elem  = null;
        
        for(var i=0; i<items.length; i++) {
            elem = {};
            
            for(var j=0; j<items[i].childNodes.length; j++) {
                var cur = items[i].childNodes[j];                
                switch(cur.nodeName) {
                    case 'title':
                        elem.title = $.trim(cur.firstChild.nodeValue);
                    break;
                    
                    case 'link':
                        elem.link = $.trim(cur.firstChild.nodeValue);
                    break;
                    
                    case 'description':
                        elem.description = encodeURI($.trim(cur.firstChild.nodeValue));
                    break;
                    
                    case 'author':
                        if(cur.childNodes.length > 0) {
                            elem.author = '(' + $.trim(cur.firstChild.nodeValue) + ')';
                        } else {
                            elem.author = '';
                        }
                    break;
                    
                    case 'pubDate':
                        elem.pubDate = this.formatDate($.trim(cur.firstChild.nodeValue));
                    break;
                    
                    case 'tema':
                    case 'category':
                        if(cur.childNodes.length > 0) {
                            elem.category = $.trim(cur.firstChild.nodeValue);                        
                            
                            if(/El Correo Gallego \- Diario de la capital de Galicia \-/.test(elem.category)) {
                                elem.category = elem.category.replace(/El Correo Gallego \- Diario de la capital de Galicia \-[ ]?/, '');
                            }
                        }
                    break;
                }
            }
            
            this.items.push(elem);
        }                
    };
    
    /* *********************************************************************** */
    /* Constructor */
    this.elem = jQuery(elem);    
    
    this.url  = url;

    this.request  = null;
    this.interval = null;
    this.timeout  = options.timeout || 8000;

    this.proxyUrl = options.proxyUrl || '/admin/panel/rss/';
    this.title = options.title || null;                
    
    this.parser = 'parseRss20';
    this.items  = [];
    this.output = null;
    
    this.theme = options.theme || 'xornal';
    
    this.themes = [];
    this.themes['correo'] = '<li><span class="category">[#{category}]</span> \
                             <a href="#{link}" target="_blank" class="title"\
                             description="#{description}">\
                             #{title}</a><br /> \
                             <span class="author">#{author}</span> <span class="pubDate">#{pubDate}</span> \
                             </li>';
    this.themes['xornal'] = '<li><a href="#{link}" target="_blank" class="title" \
                             description="#{description}">\
                             #{title}</a><br /> \
                             <span class="author">#{author}</span> <span class="pubDate">#{pubDate}</span> \
                             </li>';
    this.themes['voz']    = '<li><span class="category">[#{category}]</span> \
                             <a href="#{link}" target="_blank" class="title"\
                             description="#{description}">\
                             #{title}</a><br /> \
                             <span class="author">#{author}</span> <span class="pubDate">#{pubDate}</span> \
                             </li>';
    
    this.imgPath = options.imgPath || '/admin/themes/default/images/';
    
    
    // ParseRss
    this.getRss();    
};
