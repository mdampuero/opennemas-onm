if(!OpenNeMas) {
    var OpenNeMas = {};
}

OpenNeMas.ArticleButtons = Class.create({    
    
    initialize: function(elm, options) {
        this.element   = $(elm);        
        this.zoomAreas = options.zoomAreas || [];
        
        this.print_url    = options.print_url;
        this.sendform_url = options.sendform_url;
        this.title = options.title;
        
        // Increment to text
        this.incrTxt   = 4;
        
        this.resetSizes = new Array();
        this.zoomAreas.each(function(item, i) {
            this.resetSizes[i] = item.getStyle('font-size')
        }, this);
        
        
        this.render();
    },
    
    render: function() {
        var links = this.element.select('a');
        
        links.each( function(item) {
            var action = item.getAttribute('href').sub(/^.*?#([a-z]+)$/, function(match) {return match[1];});
                        
            if(this[action]) {
                item.observe('click', this[action].bindAsEventListener(this));
            }
        }, this);

        this.element.setStyle({'display': ''});

        /*
        // Botón imprimir
        var imprimirBtn = new Element('a', {'href': '#imprimir', 'style': 'color: #024687'}).update('Imprimir');        
        this.element.appendChild( imprimirBtn );        
        imprimirBtn.observe('click', this.printer.bindAsEventListener(this));
        
        // Botón enviar
        var sendBtn = new Element('a', {'href': '#reducir', 'style': 'color: #024687'}).update('Imprimir');        
        this.element.appendChild( sendBtn );        
        sendBtn.observe('click', this.send.bindAsEventListener(this));        
        
        // Botón ampliar
        var ampliarBtn = new Element('a', {'href': '#ampliar', 'style': 'color: #024687'}).update('[+] Ampliar');        
        this.element.appendChild( ampliarBtn );        
        ampliarBtn.observe('click', this.ampliar.bindAsEventListener(this));
        
        // Botón reestablecer
        var resetBtn = new Element('a', {'href': '#reestablecer', 'style': 'color: #024687'}).update('[0] Reestablecer');        
        this.element.appendChild( resetBtn );        
        resetBtn.observe('click', this.reestablecer.bindAsEventListener(this));
        
        // Botón reducir
        var reducirBtn = new Element('a', {'href': '#reducir', 'style': 'color: #024687'}).update('[-] Reducir');        
        this.element.appendChild( reducirBtn );        
        reducirBtn.observe('click', this.reducir.bindAsEventListener(this));
        */                       
        
        /*if(!Prototype.Browser.IE) {
            this.enhanceUI();
        }*/
    },
    
    /**
     * @deprecated
    */
    enhanceUI: function() {
        // Fixar
        //var positions = this.element.up().positionedOffset();
        var positions = this.element.up().cumulativeOffset();
        this.element.setStyle({
            backgroundColor: '#FFF',            
            padding: '4px',
            position: 'fixed',
            zIndex: 100,
            top: positions.top + 'px',
            left: (positions.left + this.element.up().getWidth()) + 'px'
        });
                
        //var positions = this.element.cumulativeOffset();
        this.element.setOpacity(0.9);
        //this.element.setStyle({left: (parseInt(this.element.getStyle('left')) - this.element.getWidth()) + 'px'});
        
        this.element.setOpacity(0.0);
        
        this.element.up().observe('mouseover', this.show.bindAsEventListener(this));
        this.element.up().observe('mouseout', this.hide.bindAsEventListener(this));
    },
    
    imprimir: function() {
        if(myLightWindow) {
            myLightWindow.activateWindow({
                href: this.print_url, 
                title: this.title,
                width: 640,
                height: 480            
            });
        }
    },
    
    enviar: function() {
        if(myLightWindow) {
            myLightWindow.activateWindow({
                href: this.sendform_url, 
                title: 'Enviar a un amigo: ' + this.title,
                width: 640,
                height: 400            
            });
        }
    },
    
    show: function() {
        this.element.setOpacity(0.9);
    },
    
    hide: function() {
        this.element.setOpacity(0.0);
    },
    
    ampliar: function(event) {
        Event.stop(event);
        
        this.zoomAreas.each( function(item){
            var current = parseInt( item.getStyle('font-size') );
            if(current<33) {
                $(item).setStyle({
                    fontSize: parseInt(current + this.incrTxt) + 'px'
                });
            }
            
        }, this);
    },
    
    reducir: function(event) {
        Event.stop(event);
        
        this.zoomAreas.each( function(item){
            var current = parseInt( item.getStyle('font-size') );
            if(current>5) {
                $(item).setStyle({
                    fontSize: parseInt(current - this.incrTxt) + 'px'
                });
            }
        }, this);
    },
    
    reestablecer: function(event) {
        Event.stop(event);
        
        this.zoomAreas.each( function(item, i){            
            $(item).setStyle({
                fontSize: this.resetSizes[i]
            });
            
        }, this);
    }
    
});