/**
 * <input type="text" id="wiktionary-box" />
 * <script type="text/javascript" src="{$params.JS_DIR}jquery.wiktionary.js"></script>
 * <script type="text/javascript">
 * jQuery('#wiktionary-box').wiktionary();
 * </script>
 *
 */

(function($){
    $.fn.wiktionary = function(options) {
        var opts = $.extend({}, $.fn.wiktionary.defaults, options);        
        $this = $(this);
        opts.elem = $this;
        
        // Singleton
        if($.fn.wiktionary.__instance__ == null) {
            $.fn.wiktionary.__instance__ = new WiktionaryKlass(opts);
        }                
        
        return $.fn.wiktionary.__instance__;
    };
    
    $.fn.wiktionary.defaults = {
        url:  'http://gl.wiktionary.org/w/api.php?format=json&action=parse&page=',
        elem: null
    };
    
    $.fn.wiktionary.__instance__ = null;
    
    $.fn.wiktionary.getInstance = function() {
        return $.fn.wiktionary.__instance__;
    };
    
})(jQuery);

// Class WiktionaryKlass
WiktionaryKlass = function(options) {
    this.options = options;
    this.elem    = options.elem;
    this.rsDiv   = null;
    
    this.init();
};

WiktionaryKlass.prototype = {
    
    init: function() {
        // Default properties
        this.elem.val('Procurar no Galizionario...');
        this.elem.attr('title', 'Escriba un termo e prema enter ↵');
        this.elem.attr('maxlength', '40');
        
        // Event to search
        this.elem.keypress($.proxy(this, "onKeypress"));
        
        // Event focus
        this.elem.focus($.proxy(this, "onFocus"));
        
        // Div results container
        this.elem.parent().append('<div class="results"></div>');
        this.rsDiv = this.elem.parent().find('div.results').css('display', '');
        
        // Enhance CSS
        this.enhanceCSS();
    },
    
    enhanceCSS: function() {
        this.elem.css({
            'background-image': 'url(http://upload.wikimedia.org/wikipedia/commons/8/8a/New_wikipedia_favicon.png)',
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
        
        this.rsDiv.css({
            'max-height': '320px',
            'overflow': 'auto'
        });
    },
    
    submit: function(value) {
        var url = this.options.url + encodeURIComponent(value);
        this.searchMsg(value);
        
        jQuery.ajax({
            'url': url,
            'success': jQuery.proxy(this, 'onSuccess'),
            'dataType': 'jsonp'
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
        
        if(data['parse'] != undefined && data.parse.revid != 0) {
            var output = data.parse.text['*'];            
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
