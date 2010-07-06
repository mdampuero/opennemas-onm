/*jslint debug: true, undef: true, eqeqeq: false, browser: true, on: true, indent: 4, onevar: true, plusplus: false, white: false */
/*global jQuery, $*/

//
// FrontManagerKlass
// 
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
    this.templateContentBox = '<div class="ui-widget content-box" data-pk_content="${pk_content}" data-mask="${mask}">' +
        '<div class="ui-widget-header ui-corner-top ui-helper-clearfix">' +
            '<span>${title}</span>' +
            '<ul class="ui-helper-reset">' +
                '<li><span data-action="toggle" class="ui-icon ui-icon-minus"></span></li>' +
                '<li><span data-action="repaint" class="ui-icon ui-icon-newwin"></span></li>' +
                '<li><span data-action="drop" class="ui-icon ui-icon-circle-close"></span></li>' +
            '</ul>' +
            '<div class="content-box-masks ui-corner-all"></div>' +
        '</div>' +
        '<div class="ui-widget-content ui-corner-bottom">' +
            '<div class="clearfix"><img src="/admin/images/loading.gif" border="0" /> ' + this.options.waitingText + '</div>' +
        '</div>' +
    '</div>';
    
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
        
        // Close selector opened
        $(document.body).click( $.proxy(this, 'closeSelector') );
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
            handle: 'div.ui-widget-header',
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
        var contentBox, mask, context, pk_content;
        
        event.preventDefault();
        event.stopPropagation();
        
        contentBox = $(event.target).parents('div.ui-widget');
        
        mask = $(event.target).parents('li').attr('data-mask');
        contentBox.attr('data-mask', mask);
        
        context = contentBox.find('div.ui-widget-content>div');
        pk_content = contentBox.attr('data-pk_content');
        
        this.closeSelector();
        this.repaintMask(context, pk_content, mask);
    },
    
    // Parse button set
    parseButtons: function(selector) {
        $(selector).click( $.proxy(this, "handlerButtons") );
    },
    
    // Handle click buttons of ContentBox 
    handlerButtons: function(event) {
        var action, pos;
        event.preventDefault();
        event.stopPropagation();
        
        action = $(event.target).attr('data-action');
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
                    $(event.target).parents("div.ui-widget").remove();
                }
            break;
            
            case 'repaint':
                if($(event.target).parents("div.ui-widget-header").find('div.content-box-masks').css('display') == 'none') {
                    this.closeSelector();
                    
                    pos = $(event.target).position();
                    $(event.target).parents("div.ui-widget-header").find('div.content-box-masks').css({
                        top: (parseInt(pos.top, 10) + 16) + 'px',
                        left: (parseInt(pos.left, 10) - 134) + 'px',
                        display: 'block'
                    });
                    
                    this.selectorOpened = $(event.target).parents("div.ui-widget-header").find('div.content-box-masks');
                } else {
                    this.closeSelector();
                }
            break;
        }
    },
    
    closeSelector: function() {
        if(this.selectorOpened !== null) {
            this.selectorOpened.css({
                display: 'none'
            });
            this.selectorOpened = null;
        }
    },
    
    // Save ContentBox positions and masks 
    saveGrid: function() {
        var data, obj, placeholder;
        
        data = {
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
        
        if(this.options.savePosURI !== null) {
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
        
        var url = this.options.repaintURI + '?pk_page=' + this.options.pk_page + '&pk_content=' + pk_content;
        if(mask !== undefined) {
            url += '&mask=' + mask;
        }
        
        $.ajax({
            'url': url,
            'success': function(data) {
                $(this).html(data);
            },
            'context': context
        });
    },
    
    onReceive: function(event, ui) {
        if(ui.item.hasClass('searcher-item')) {
            var pk_content, newContentBox, context;
            
            pk_content = ui.item.attr('data-pk_content');
            newContentBox = $.template(this.templateContentBox).apply({
                title: ui.item.text(),
                pk_content: pk_content,
                mask: '' // default mask
            });
            
            context = $(newContentBox).replaceAll(ui.item);
            this.parseButtons( context.find('div.ui-widget-header>ul>li>span') );
            
            this.getMasks(pk_content, context);
            
            this.repaintMask(context.find('div.ui-widget-content>div'), pk_content, '');
        }
    },
    
    getMasks: function(pk_content, context) {
        $.ajax({
            url: this.options.getMasksURI + '?pk_content=' + pk_content + '&pk_page=' + this.options.pk_page,
            success: function(data) {
                $(this).html(data);
                $(this).find('ul>li').each( $.proxy($.fn.frontmanager.getInstance(), 'connectMaskSelector') );
            },
            context: context.find('div.ui-widget-header>div.content-box-masks')
        });
    }
    
    
}; // FrontManagerKlass.prototype


//
// jQuery plugin
//
(function($) {
    $.fn.frontmanager = function(options) {
        var opts = $.extend({}, $.fn.frontmanager.defaults, options);
        opts.elem = $(this);
        
        // Singleton
        if($.fn.frontmanager.instance === null) {
            $.fn.frontmanager.instance = new FrontManagerKlass(opts);
        }
        
        return $.fn.frontmanager.instance;
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
    
    $.fn.frontmanager.instance = null;
    
    $.fn.frontmanager.getInstance = function() {
        return $.fn.frontmanager.instance;
    };
}(jQuery));
