(function($) {
    
    $.fn.frontmanager = function(options) {
        var opts = $.extend({}, $.fn.frontmanager.defaults, options);
        $this = $(this);
        opts.elem = $this;
        
        // Singleton
        if($.fn.frontmanager.__instance__ == null) {
            $.fn.frontmanager.__instance__ = new FrontManagerKlass(opts);
        }
        
        return $.fn.frontmanager.__instance__;
    };
    
    $.fn.frontmanager.defaults = {
        'savePosURI':  null,
        'repaintURI':  null,
        'getMasksURI': null,
        'pk_page':     $('#pk_page').val(),
        'version':     $('#version').val(),
        'confirmText': 'Are you sure?',
        'waitingText': 'Rendering content...',
        'connectWith': 'div[role=wairole:gridcell], #searcher-results'
    };
    
    $.fn.frontmanager.__instance__ = null;
    
    $.fn.frontmanager.getInstance = function() {
        return $.fn.frontmanager.__instance__;
    };
})(jQuery);



/**
 * FrontManagerKlass
 * 
 */ 
var FrontManagerKlass = function(options) {
    // Property options
    this.options = options;
    
    // div#board - Root element of grid
    this.elem = options.elem; // jQuery element
    this.id   = this.elem.attr('id');
    
    this.selectorOpened = null;
    
    // Grid cells
    this.gridCells = this.elem.find('div[role=wairole:gridcell]');
    
    // Template for ContentBox
    this.templateContentBox = '<div class="ui-widget content-box" data-pk_content="${pk_content}" data-mask="${mask}"> \
    <div class="ui-widget-header ui-corner-top ui-helper-clearfix"> \
        <span>${title}</span> \
        <ul class="ui-helper-reset"> \
            <li><span data-action="toggle" class="ui-icon ui-icon-minus"></span></li> \
            <li><span data-action="repaint" class="ui-icon ui-icon-newwin"></span></li> \
            <li><span data-action="drop" class="ui-icon ui-icon-circle-close"></span></li> \
        </ul> \
        <div class="content-box-masks ui-corner-all"></div> \
    </div> \
    <div class="ui-widget-content ui-corner-bottom"> \
        <div class="clearfix"><img src="/admin/images/loading.gif" border="0" /> ' + this.options.waitingText + '</div> \
    </div>\
</div>';
    
    // Initialization
    this.init();
};

// Extend FrontManagerKlass
FrontManagerKlass.prototype = {
    
    init: function() {
        // Parse ContentBox in grid
        this.parseGrid();
        
        // Parse all buttons
        this.parseButtons('#' + this.id + ' div.ui-widget-header>ul>li>span');
    },
    
    parseGrid: function() {
        this.gridCells.each( $.proxy(this, 'parseGridCell') ); // END: this.gridCells.each
    },
    
    parseGridCell: function(ignoreThis, container) {
        $(container).css({
            minHeight: '100px', border: '1px dotted #999', padding: '0.2em', margin: '0.2em'
        });
        
        $(container).sortable({
            items: 'div.content-box',
            placeholder: 'ui-state-highlight',
            forcePlaceholderSize: true,
            dropOnEmpty: true,
            connectWith: this.options.connectWith,
            receive: $.proxy(this, 'onReceive')
        });
        
        $(container).find('div.content-box-masks ul li').each( $.proxy(this, 'connectMaskSelector') );
    },
    
    connectMaskSelector: function(ignoreThis, maskLiElem) {
        $(maskLiElem).click( $.proxy(this, 'onClickMask') );
    },
    
    onClickMask: function(event) {
        event.preventDefault();
        event.stopPropagation();
        
        console.log( $(event.target) );
    },
    
    // Parse button set
    parseButtons: function(selector) {
        $(selector).click( $.proxy(this, "handlerButtons") );
    },
    
    /* Handle click buttons of ContentBox */
    handlerButtons: function(event) {
        event.preventDefault();
        event.stopPropagation();
        
        var action = $(event.target).attr('data-action');
        switch(action) {
            case 'toggle':
                if($(event.target).hasClass('ui-icon-minus')) {
                    $(event.target).removeClass('ui-icon-minus')
                        .addClass('ui-icon-plus')
                        .parents("div.ui-widget")
                        .find('div.ui-widget-content')
                        .hide();
                } else {                
                    $(event.target).removeClass('ui-icon-plus')
                        .addClass('ui-icon-minus')
                        .parents("div.ui-widget")
                        .find('div.ui-widget-content')
                        .show();
                }
            break;
            
            case 'drop':
                if(confirm( this.options.confirmText )) {
                    var deletable = $(event.target).parents("div.ui-widget").remove();
                }
            break;
            
            case 'repaint':
                if($(event.target).parents("div.ui-widget-header").find('div.content-box-masks').css('display') == 'none') {
                    this.closeSelector();
                    
                    pos = $(event.target).position();
                    $(event.target).parents("div.ui-widget-header").find('div.content-box-masks').css({
                        top: (parseInt(pos.top) + 16) + 'px',
                        left: (parseInt(pos.left) - 134) + 'px',
                        display: 'block'
                    });
                    
                    this.selectorOpened = $(event.target).parents("div.ui-widget-header").find('div.content-box-masks');
                } else {
                    /* $(event.target).parents("div.ui-widget-header").find('div.content-box-masks').css({
                        display: 'none'
                    }); */
                    
                    this.closeSelector();
                }
            break;
        }
    },
    
    closeSelector: function() {
        if(this.selectorOpened != null) {
            this.selectorOpened.css({
                display: 'none'
            });
            this.selectorOpened = null;
        }
    },
    
    /* Save ContentBox positions and masks */
    saveGrid: function() {
        var data = {
            pk_page: this.options.pk_page,
            version: this.options.version,
            contents: {}
        };
        
        this.gridCells.each(function(i, container) {        
            placeholder = $(container).attr('id');
            
            $(container).find('div.content-box').each(function(weight, content) {
                if(!data.contents[placeholder]) {
                    data.contents[placeholder] = [];
                }
                
                obj = {
                    'pk_content': $(content).attr('data-pk_content'),
                    'mask': $(content).attr('data-mask')
                };
                
                data.contents[placeholder].push(obj);
            });
        });
        
        if(this.options.savePosURI != null) {
            $.ajax({
                'url': this.options.savePosURI,
                'type': 'post',
                'data': data,
                'success': $.proxy(this, 'onSuccessSaveGrid')
            });
        }
    },
    
    onSuccessSaveGrid: function(response) {
        this.showMessage(response);
    },
    
    showMessage: function(text, type) {
        
    },
    
    repaintMask: function(context, pk_content, mask) {
        context.html('<img src="/admin/images/loading.gif" border="0" /> ' + this.options.waitingText);
        
        $.ajax({
            'url': this.options.repaintURI + '?pk_page=' + this.options.pk_page + '&pk_content=' + pk_content + '&mask=' + mask,
            'success': function(data) {
                $(this).html(data);
            },
            'context': context
        });
    },
    
    onReceive: function(event, ui) {
        if(ui.item.hasClass('searcher-item')) {
            var pk_content = ui.item.attr('data-pk_content');
            var newContentBox = $.template(this.templateContentBox).apply({
                title: ui.item.text(),
                pk_content: pk_content,
                mask: '' // Máscara por defecto
            });
            
            var context = $(newContentBox).replaceAll(ui.item);
            //context.find('div.ui-widget-header>ul>li>span').click( $.proxy(this, 'handlerButtons') );
            this.parseButtons( context.find('div.ui-widget-header>ul>li>span') );
            
            $.ajax({
                url: this.options.getMasksURI + '?pk_content=' + pk_content + '&pk_page=' + this.options.pk_page,
                success: $.proxy(this, 'onGetMasks'),
                context: context.find('div.ui-widget-header>div.content-box-masks')
            });
            
            this.repaintMask(context.find('div.ui-widget-content>div'), pk_content, '');
            /* $.ajax({
                'url': this.options.repaintURI + '?pk_page=' + this.options.pk_page + '&pk_content=' + pk_content,
                'success': function(data) {
                    $(this).find('div.ui-widget-content>div').html(data);
                },
                'context': context
            }); */
        }
    },
    
    onGetMasks: function(data) {
        console.log( $(this) );
        $(this).html(data);
    }
    
    
}; // FrontManagerKlass.prototype
