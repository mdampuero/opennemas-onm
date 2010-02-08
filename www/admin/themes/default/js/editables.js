/*  Listado de pendientes
 * Editar titulos, autor y secciones y guardar cambios
 */


//Cogido de newsletter
Editable = Class.create({
    initialize: function(elem, options) {
        this.elem = $(elem);
        this.options = options;
        this.title = $(elem).getAttribute('title');
        this.itemId=$(elem).getAttribute('name');
 
        // for stopObserving
     //   this.handlerOnInlineEdit = this.onInlineEdit.bindAsEventListener(this);
        //this.onInlineEdit();

        // NOVO
        this.renderFormElement();
       // this.miHandler = this.onDblClickText.bindAsEventListener(this);
       // this.elem.observe('dblclick', this.miHandler);

    },
    renderFormElement: function(evt) {

        if(this.options=='input') {             
            this.miHandler = this.onDblClickText.bindAsEventListener(this);

        }else if(this.options=='select'){
            this.miHandler = this.onDblClickSelect.bindAsEventListener(this);

        }
         this.elem.observe('dblclick', this.miHandler);
    },

    onDblClickText: function(evt) {
        try {
            Event.stop(evt);
            this.elem.stopObserving('dblclick', this.miHandler);

            var texto = this.elem.innerHTML;

            var textBox = new Element('input', {type: 'text', style:'width:100%'});
            textBox.value = texto;
            textBox.focus();
            this.elem.update(textBox);
            

            textBox.observe('blur', this.onBlurTextBox.bindAsEventListener(this));
            textBox.observe('keypress', this.onKeyPress.bindAsEventListener(this));
        }catch(e) {console.log(e);}
    },

    onBlurTextBox: function(evt) {
        Event.stop(evt);
        try{
            var texto = this.elem.select('input')[0].value;
            this.elem.update(texto);
            this.updateItem(texto);
            this.elem.observe('dblclick', this.miHandler);
        }catch(e){console.log(e);}
    },
/*
    onEditInput: function() {

        if(this.title =='agency'){
            var textBox = new Element('input', {type: 'text', size: '16', value: this.elem.innerHTML});
        }else{
            var textBox = new Element('input', {type: 'text', size: '48', value: this.elem.innerHTML});
        }
        return textBox;
    },
    */

    onDblClickSelect: function(evt) {
       Event.stop(evt);
       this.elem.stopObserving('dblclick', this.miHandler);
       if(this.title=='author'){
           this.get_list_authors();

        }else{
           this.get_list_categorys();
        }

    },

    onKeyPress: function(evt) {
        if(evt.keyCode == Event.KEY_RETURN) {
            $('formulario').observe('submit', function(evt) {Event.stop(evt);});
            Event.stop(evt);
            var elt = Event.findElement(evt, 'input');            
            elt.blur();
            
        }
    },

   
    get_list_authors: function(){
        new Ajax.Request( 'opinion.php?action=get_authors_list',
        {
            onSuccess:  this.get_response.bind(this)
        });

    },

    get_list_categorys: function(){
 
          new Ajax.Request( 'article.php?action=get_categorys_list',
        {
             onSuccess:  this.get_response.bind(this)
      /*      onSuccess: function(transport) {
                  var titems = transport.responseJSON;
                  console.log(titems);

                  return titems;

            }*/
             /*
              * [Object pk_content_category=238 title=POLÍTICA, Object pk_content_category=10 title=GALICIA name=galicia, Object pk_content_category=11 title=ESPAÑA name=espana, Object pk_content_category=12 title=MUNDO name=mundo, Object pk_content_category=14 title=ECONOMÍA name=economia, Object pk_content_category=13 title=SOCIEDAD name=sociedad, Object pk_content_category=21 title=CULTURA name=cultura, Object pk_content_category=15 title=DEPORTES name=deportes, Object pk_content_category=17 title=GENTE name=gente, Object pk_content_category=251 title=de.hueso name=dehueso, Object pk_content_category=170 title=SUPLEMENTOS, Object pk_content_category=176 title=EXTRAS name=extras]
            */
        });

 
    },

    get_response: function(transport){

                 this.response = transport.responseJSON;
                 
                 if(!this.response) {
                 	eval('var data = ' + transport.responseText + ';');
                 	this.response = data;
                 }

                 this.renderSelect();
                 
    },
    
    renderSelect: function(){
	// Para atopar erros poñer bloques try/catch dentro das clausuras
	// noutro caso non aparecen os erros na consola de firebug
	try {
            var textBox = new Element('select');

            if(this.title=='author'){ //lista autor de opinion
                var old_author=this.elem.getAttribute('old_author');
                this.response.each(function(element, index){
                  //  console.log(element.name +  element.pk_author);
                    if(old_author==element.pk_author) {
                         textBox.options[index] = new Option(element.name, element.pk_author, "defauldSelected", true);
                    }else{
                        textBox.options[index] = new Option(element.name, element.pk_author, false, false);
                    }
                }, this);
            }else{ //lista categoria de article
                 var old_cat=this.elem.getAttribute('old_cat');
                 this.response.each(function(element, index){
                     if(old_cat==element.pk_content_category) {
                         textBox.options[index] = new Option(element.title, element.pk_content_category, "defauldSelected", true);
                    }else{
                         textBox.options[index] = new Option(element.title, element.pk_content_category, false, false);
                    }
                }, this);
            }
            textBox.observe('change', this.onChangeSelect.bindAsEventListener(this));
            textBox.observe('blur', this.onBlurSelect.bindAsEventListener(this));

	    // Insertar select en span
	    this.elem.update(textBox);

            textBox.focus();

            
        } catch(e) {
            console.log(e);
        }
    },


    
    onChangeSelect: function(evt) {
    	// Cando cambien valor
 	var elt = Event.findElement(evt, 'select');
        var data  = [];
        data[0] =elt.options[elt.selectedIndex].value;
        data[1]= elt.options[elt.selectedIndex].text ;
        
       
 
    	
    },
    
    onBlurSelect: function(evt) {
    	// Cando perda o foco
    	var elt = Event.findElement(evt, 'select');
    	var str = elt.options[elt.selectedIndex].text ;
    	this.elem.update( str.replace('⇒',''));
        var data  = [];
        data[0] =elt.options[elt.selectedIndex].value;
        data[1]= elt.options[elt.selectedIndex].text ;
        this.updateItem(data);
        this.elem.observe('dblclick', this.miHandler);
    },

     updateItem: function( data ) {
         var field = this.title;
         var itemId = this.itemId;
         if($('user_name')){
             var name='editor_'+itemId;
             $(name).innerHTML=$('user_name').value;
         }
         //guardar en bd by ajax.
         if(field =='title'){
            new Ajax.Request( 'article.php?action=update_title&id='+itemId+'&title='+data,
                {
                  onSuccess: function() {

                      console.log('title ok ');
                  }
                 });
         }else if(field =='agency'){
            new Ajax.Request( 'article.php?action=update_agency&id='+itemId+'&agency='+data,
                {
                  onSuccess: function() {

                      console.log('title ok ');
                  }
                 });
         }else if(field =='opinion'){
            new Ajax.Request( 'opinion.php?action=update_title&id='+itemId+'&title='+data,
                {
                  onSuccess: function() {

                      console.log('title opinion ok ');
                  }
                 });

         }else if(field =='category'){

                 new Ajax.Request( 'article.php?action=update_category&id='+itemId+'&pk_fk_content_category='+data[0]+'&catName='+data[1],
                {
                  onSuccess: function() {

                      console.log('category ok');
                  }
                 });
         }else{
               new Ajax.Request( 'opinion.php?action=update_author&id='+itemId+'&fk_author='+data[0]+'&authorName='+data[1],
                {
                  onSuccess: function() {

                      console.log('author ok');
                  }
                 });
         }

    }




});

 