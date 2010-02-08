/*jslint debug: true, eqeqeq: false, browser: true, on: true, indent: 4, plusplus: false, white: false */
if(!OpenNeMas) {
    var OpenNeMas = {};
}

OpenNeMas.Carousel = Class.create({    
    
    initialize: function(elm, options) {
        this.element = $(elm);
        this.offset  = options.offset || 0;
        this.numrows = options.numrows || 10;
        this.maxWidth  = 60; //90
        this.maxHeight = 60;
        
        this.url = '/carousel.php?offset=#{offset}&numrows=#{numrows}';
        this.preffix = options.preffixMedia || '/media/images';
        
        // O contedor é unha lista html
        this.data = this.element.select('div.carousel ul li');
        this.isLastest = options.isLastest || false;
        this.dataContainer = this.element.select('div.carousel ul')[0];
        this.messageBoard  = this.element.select('div.carousel-message')[0];
        
        // FIXME: how to do private members?
        this.divLoading = null;
        this.divPointer = null;
        this.buffer     = new Array();
        this.imageHits  = 0;
        
        // Asociar eventos
        this.buildUI();
    },
    
    
    buildUI: function() {
        // Botón de atrás
        var atras = this.element.select('div.carousel-left a')[0];
        if(atras) {
            atras.observe('click', this.prev.bindAsEventListener(this));
        }
        
        // Botón de adiante
        var adiante = this.element.select('div.carousel-right a')[0];
        if(adiante) {
            adiante.observe('click', this.next.bindAsEventListener(this));
        }
        
        this.attachMouseOver();
        
        // Hide left button
        //this.hideArrow('left');
        this.checkArrows();
        
        // Check if it's last row don't show right arrow
        if(this.isLastest) {
            this.hideArrow('right');
        }
        
        // Precarga da imaxe de precarga
        var tmpImgLoading = new Image();
        tmpImgLoading.src = '/themes/xornal/images/carousel/loading.gif';
        var tmpImagPointer = new Image();
        tmpImagPointer.src = '/themes/xornal/images/carousel/pointer.gif';
        
        var carousel = this.element.select('div.carousel')[0];
        
        this.divLoading = new Element('div');
        this.divLoading.setStyle({
            'position': 'absolute',
            'backgroundColor': 'transparent',
            'left':     0,
            'top':      0,
            'zIndex':   200,
            'width':    carousel.getWidth() + 'px',
            'height':   carousel.getHeight()+ 'px',
            'verticalAlign': 'middle',
            'display':  'none'
        });        
        carousel.appendChild( this.divLoading );
        
        this.divPointer = new Element('div');
        this.divPointer.setStyle({
            'position': 'absolute',
            'zIndex':   100,
            'width':    '18px',
            'height':   '12px',
            'border':  0,
            'padding': 0,
            'margin':  0,
            'display':  'none'             
        });
        this.divPointer.update('<img src="/themes/xornal/images/carousel/pointer.gif" border="0" />');
        
        carousel.appendChild( this.divPointer );
    },
    
    showHoverDiv: function() {
        // Show hover div
        this.divLoading.setStyle({'opacity': 0.8, 'display': ''});
        this.divLoading.update('<h2><img src="/themes/xornal/images/carousel/loading.gif" align="absmiddle" /></h2>');
        
        // Clean existent authors
        this.cleanAuthors();
        
        // Clean message div
        this.clearMessage();
        
        // Reboot private members
        this.buffer = new Array();
        this.imageHits = 0;
    },
    
    hideHoverDiv: function() {
        this.buffer.each(function(item) {            
            this.dataContainer.appendChild( item );                        
        }, this);
        
        //this.centerImages();
        
        this.divLoading.update('');        
        new Effect.Fade(this.divLoading, { duration: 2.0, from: 0.8, to: 0.0 });
        
        this.attachMouseOver();
    },
    
    attachMouseOver: function() {
        var lnks = this.dataContainer.select('li a');
        lnks.each(function(item, i) {
            item.observe('mouseover', this.onMouseOver.bindAsEventListener(this));
        }, this);        
    },
    
    onMouseOver: function(event) {
        var element = Event.element(event);
        
        if(element.tagName.toUpperCase() == 'IMG') {
            element = element.up();
        }
        
        if (element.tagName.toUpperCase() == 'A')  {
            this.showMessage(element, element.up().positionedOffset()[0], 60);
        }
        //new Effect.Fade(this.messageBoard, {from: 0.0, to:0.99});
    },
    
    showMessage: function(element, x, y) {
        this.divPointer.setStyle({
            'display': '',
            'top': y + 'px',
            'left': (x+30) + 'px'
        });
        this.element.select('div.carousel-center')[0].setStyle({
            'borderBottom': '2px solid #024687'
        });
        
        var texto = element.getAttribute('carousel:title').split('@@@');
        
        this.messageBoard.update( '<a href="'+element.href+'">' +
                    texto[0] + '</a>' + texto[1]);
        
        if(x<200) {
            this.messageBoard.setStyle({textAlign: 'left'});
        } else {
            if(x<450) {
                this.messageBoard.setStyle({textAlign: 'center'});
            } else {
                this.messageBoard.setStyle({textAlign: 'right'});
            }
        }
    },
    
    clearMessage: function() {
        this.divPointer.setStyle({'display': 'none'});
        /* this.element.select('div.carousel-center')[0].setStyle({
            'borderBottom': '2px solid #FFF'
        }); */
        
        this.messageBoard.update('');
    },
    
    /*centerImages: function() {
        this.dataContainer.select('img').each(function(item) {
            w = item.getWidth();
            h = item.getHeight();
            
            if((w>h) && (w>this.maxWidth)) {
                item.setStyle({'width': this.maxWidth+'px'});
                
                h = item.getHeight();
                item.setStyle({'marginTop': Math.ceil((this.maxWidth - h)) + 'px'});
            } else {
                item.setStyle({'height': this.maxWidth+'px'});
            }
        }, this);
    },*/
    
    /*centerImage: function(item) {
        w = item.getWidth();
        h = item.getHeight();
        
        if((w>h) && (w>this.maxWidth)) {
            item.setStyle({'width': this.maxWidth+'px'});
            
            h = item.getHeight();
            item.setStyle({'marginTop': Math.ceil((this.maxWidth-h)) + 'px'});
        } else {
            item.setStyle({'height': this.maxWidth+'px'});
        }
    },*/   
    
    cleanAuthors: function() {
        this.dataContainer.update();
    },
    
    next: function(event) {
        Event.stop(event);
        
        this.showHoverDiv();
        
        var url = this.url.interpolate({'offset': this.offset+1, 'numrows': this.numrows});
        new Ajax.Request(url, {
            onSuccess: this.onSuccessNext.bind(this)
        });
    },    
    onSuccessNext: function(transport) {
        this.data = transport.responseJSON.items;
        this.offset++;
        this.checkArrows();
        
        if(transport.responseJSON.isLastest) {
            this.hideArrow('right');
        }
        
        this.renderData( this.data );
    },
    

    prev: function(event) {
        Event.stop(event);
        
        if(this.offset > 0){
            this.showHoverDiv();
            
            var url = this.url.interpolate({'offset': this.offset-1, 'numrows': this.numrows});
            new Ajax.Request(url, {
                onSuccess: this.onSuccessPrev.bind(this)
            });
        }
    },    
    onSuccessPrev: function(transport) {
        this.data = transport.responseJSON.items;
        if(this.offset > 0) {
            this.offset--;
            
            this.checkArrows(); 
            
            this.renderData(this.data);
        }                
    },
    
  
    renderData: function(data) {        
        data.each(function(item, i) {
            //var rnd = Math.ceil(Math.random()*1000000); // evitar caché Opera
            //imageElement = new Element('img', {'src': this.preffix+item.photo+'?'+rnd});
            imageElement = new Element('img', {'src': this.preffix+item.photo, 'alt': item.author});
            //imageElement.observe('load', this.hitImage.bind(this));
            
            texto = item.title+'@@@, por '+item.author; 
            if(item.condition && (item.condition!='')) {
                texto += ' ('+ item.condition+')';
            }
            
            li = new Element('li').update(
                new Element('a',
                    {'href': item.permalink,
                    'title': '', /* item.author, */
                    'carousel:title': texto
                    }).update(
                        imageElement                    
                    )
            );
            
            this.buffer.push( li );
            /* img = new Element('img', {'src': this.preffix+item.photo}); 
            img = new Image();
            img.src = this.preffix+item.photo;
            
            img.observe('load', this.replaceImg.bind(this, img, li)); */
            
            this.dataContainer.appendChild( li );
        }, this);
        
        //this.checkLoadImages();
        
        this.hideHoverDiv();
    },
    
    /*hitImage: function() {
        this.imageHits++;
    },
    
    checkLoadImages: function() {
        if(this.imageHits == this.buffer.length) {
            this.hideHoverDiv();
        } else {
            window.setTimeout(this.checkLoadImages.bind(this), 100);
        }
    },*/
    
    replaceImg: function(img, li) {
        li.select('img')[0].setAttribute('src', img.src);
    },
    
    showArrow: function(arrow) {
        arrow = this.element.select('.carousel-' + arrow + ' a');
        if( arrow[0] ) {
            arrow[0].setStyle({'display': ''});          
        }
    },
    
    hideArrow: function(arrow) {
        arrow = this.element.select('.carousel-' + arrow + ' a');
        if( arrow[0] ) {
            arrow[0].setStyle({'display': 'none'});
        }
    },
    
    checkArrows: function() {
        if(this.offset>0) {
            this.showArrow('left');
        } else {
            this.hideArrow('left');
        }
        
        if(this.data.length < this.numrows) {
            this.hideArrow('right');
        } else {
            this.showArrow('right');
        }        
    },
    
    redirect: function(pk_author, author_name) {
        if(pk_author) {
            location.href = '/opinions/opinions_do_autor/' + pk_author + '/' + author_name + '.html';
        }
    }
});