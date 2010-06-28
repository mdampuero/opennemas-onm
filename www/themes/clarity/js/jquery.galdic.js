(function($){
    $.fn.galdic = function(options) {
        var opts = $.extend({}, $.fn.galdic.defaults, options);        
        $this = $(this);
        opts.elem = $this;
        
        // Singleton
        if($.fn.galdic.__instance__ == null) {
            $.fn.galdic.__instance__ = new GalDicKlass(opts);
        }                
        
        return $.fn.galdic.__instance__;
    };
    
    $.fn.galdic.defaults = {
        url:  'http://galdic.vifito.eu/index/xsearch/q/',
        elem: null
    };
    
    $.fn.galdic.__instance__ = null;
    
    $.fn.galdic.getInstance = function() {
        return $.fn.galdic.__instance__;
    };
    
})(jQuery);

// Class GalDicKlass 
GalDicKlass = function(options) {
    this.options = options;
    this.elem    = options.elem;
    this.rsDiv   = null;
    
    this.init();
};

GalDicKlass.prototype = {
    
    init: function() {
        // Default properties
        this.elem.val('Procurar en GalDic...');
        this.elem.attr('title', 'Escriba o termo e prema enter ↵');
        this.elem.attr('maxlength', '40');
        
        // Event to search
        this.elem.keypress($.proxy(this, "onKeypress"));
        
        // Event focus
        this.elem.focus($.proxy(this, "onFocus"));                
        
        // Enhance CSS
        this.enhanceCSS();
        
        // Div results container
        this.elem.parent().append('<div class="results"></div>');
        this.rsDiv = this.elem.parent().find('div.results').css('display', '');
    },
    
    enhanceCSS: function() {
        this.elem.css({
            'background-image': 'url(http://galdic.vifito.eu/images/favicon.png)',
            'background-repeat': 'no-repeat',
            'background-position': '4px 50%',
            'background-color': '#ffffff',                        
            
            'padding': '4px',
            'padding-left': '24px',            
            'border': '1px solid #ccc',
            'color': '#666',
            'border-radius': '4px',
            '-moz-border-radius': '4px',
            '-webkit-border-radius': '4px'
        });
    },
    
    submit: function(value) {
        var url = this.options.url + encodeURIComponent(value);
        this.searchMsg(value);
        
        jQuery.ajax({
            'url': url,
            'success': jQuery.proxy(this, "onSuccess"),
            'dataType': "jsonp"
        });
    },
    
    searchMsg: function(value) {
        this.rsDiv.html("Procurando <strong>" + value + "</strong>...");
    },
    
    onSuccess: function(data) {
        this.renderResult(data);
    },
    
    onKeypress: function(event) {
        if (event.keyCode == '13') {
            event.preventDefault();
            
            var term = this.elem.val();
            if( term.length > 2 ) {
                this.submit(term);
            } else {
                this.rsDiv.html("");
            }
        }
    },
    
    onFocus: function() {
        this.elem.val('');
    },
    
    renderResult: function(data) {
        this.rsDiv.css({'display': 'none'});
        
        if(data['name'] != undefined) {
            var output = '';
            
            for(var i=0; i<data.definitions.length; i++) {
                if(data.definitions.length > 1) {
                    output += '<strong>' + (i+1) + '.</strong> ' + data.definitions[i].content + ' ';
                } else {
                    output += data.definitions[i].content + ' ';
                }                
            }
            
            this.rsDiv.html(output);
        } else {
            this.rsDiv.html('<strong>Non se atoparon entradas relacionadas.</strong>');
        }
        
        this.rsDiv.slideDown('slow', $.proxy(this, "onRenderComplete"));
    },    
    
    onRenderComplete: function() {
        // Nada
    }
    
};
