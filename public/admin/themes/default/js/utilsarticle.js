// JavaScript Document
//Funciones llamadas en article.tpl
//
 //Devuelve los ids de los divs que contienen noticias.
getHoles = function(){
    var huecos= $('columns').select('div');

   
    return huecos;
}

//Mensajes alerta para contenedores de noticias.
alertsDiv = function(){

    var Nodes=$('hole3').select('table');
    if(Nodes.length<1){$('warnings3').update('Debe contener noticias debajo de publicidad 2');  $('warnings-validation').update('Recuerde guardar posiciones');}
    else{$('warnings3').update(' ');}
    
}

 
make_sortable_divs_portadas = function() {    
    var huecos = getHoles();
     var _huecos = ['div_no_home','art'];
    for(var i=0; i<huecos.length; i++) {
        _huecos.push(huecos[i].id);
    }
    huecos.push($('div_no_home'));
    huecos.push($('art'));
    for(i=0; i<huecos.length; i++) {
        Sortable.create( huecos[i] ,{
            tag:'table',
            only:'tabla',
            dropOnEmpty: true,
            containment:huecos
        });
    }
 }


savePositions = function(category) {

   // changedTables(category);
    var huecos = getHoles();

    huecos.push($('div_no_home'));
    huecos.push($('art'));


    var places = {};
    var huecos_id = new Array();

    for(var i=0; i<huecos.length; i++) {
        huecos_id.push(huecos[i].id);
    }
 
    huecos.each(function(div_id, i){
        if( $(div_id) ) {
                $(div_id).select('table').each(function(item) {
                        if(item.getAttribute('value')) {
                                places[ item.getAttribute('value') ] = huecos_id[i];
                                item.setAttribute('name',"selected_fld[]");
                        }
                });
        }
    });
    


        // Form
	var frm = $('formulario');
      //  console.log(places);
	// Send articles positions into 'id' text field
	frm.id.value =  Object.toJSON(places);
        frm.category.value = category;

  
    new Ajax.Request('article_save_positions.php',{
        method: 'post',
        parameters: frm.serialize(),

        onLoading: function() {

           $('warnings-validation').update('Guardando posiciones...');
            // showMsg({'loading':['Guardando posiciones...']},'growl');
        },
        onComplete: function(transport) {
           $('warnings-validation').update( transport.responseText );
                new Effect.Highlight( $('warnings-validation') );

                // Establecer o valor de posicionesIniciales para controlar os cambios de posicións e amosar avisos
                if(posicionesIniciales) {
                        posicionesIniciales = $$('input[type=checkbox]');
                        posicionesInicialesWarning = false;
                }
                //showMsg({'info':['El artículo ha sido guardado tras la previsualización.']},'growl');      
        },

        onFailure: function() {
                $('warnings-validation').update( 'Hubo errores al guardar las posiciones. Inténtelo de nuevo.' );
                new Effect.Highlight( $('warnings-validation') );
        }
            
           
    })
        /*
   //Cambiar iconos
        items =  $(div_id).getElementsByClassName("minput");
        for (i = 0; i < items.length; i++) {
                items[i].setAttribute('name',"selected_fld[]");
        }
        items =  $(div_id).getElementsByClassName("noportada");
        for (i = 0; i < items.length; i++) {
                items[i].setAttribute('alt',"En portada");
                items[i].setAttribute('src',"/admin/themes/default/images/publish_g.png");
                items[i].setAttribute('class',"portada");
        }
        items =  $(div_id).getElementsByClassName("noinhome");
        for (i = 0; i < items.length; i++) {
                items[i].setAttribute('style','display:inline;');
                items[i].setAttribute('class',"inhome");
        }
        if(category == 'home') {
          items =  $(div_id).select("img.inhome");
            for (i = 0; i < items.length; i++) {
                    items[i].setAttribute('src',"/admin/themes/default/images/gohome.png");
            }
        }  
    } */
 
}


changedTables = function(category) {
// Al arrastrar noticas Visualiza/Oculta la celda class='no_view' (muestra home)
    var Nodes=$('columns').select('td');
    for (i = 0; i < Nodes.length; i++) {
        if(Nodes[i].getAttribute('class') == 'no_view'){
            Nodes[i].innerHTML="";
                Nodes[i].setAttribute('style','width:1px;');
        }
        if(Nodes[i].getAttribute('class') == 'no_width'){        
           Nodes[i].setAttribute('style','width:30px;');
        }
    }

    Nodes=$('art').select('td');
    for (i = 0; i < Nodes.length; i++) {
        if(Nodes[i].getAttribute('class') == 'un_fecha'){
                Nodes[i].setAttribute('style','width:400px;');
        }
        if(Nodes[i].getAttribute('class') == 'un_view'){
                Nodes[i].setAttribute('style','width:50px;');
        }
        if(Nodes[i].getAttribute('class') == 'un_width'){
           Nodes[i].setAttribute('style','width:50px;');
        }
        if(Nodes[i].getAttribute('class') == 'un_width_sec'){
           Nodes[i].setAttribute('style','width:60px;');
        }
        if(Nodes[i].getAttribute('class') == 'un_no_view'){
            Nodes[i].update('');
            Nodes[i].setAttribute('style','width:1px;');
        }
    }
     
    Nodes=$('div_no_home').select('a.no_home');
    for (i = 0; i < Nodes.length; i++) {
            Nodes[i].setAttribute('class',"go_home");
    }
}
  

/**
 * Preview
 *
 * show preview for frontpage, emulate a ajax request vía submit form with
 * target with _blank value. This function submit form to www/preview.php
 * showing positions before to save it
*/
function previewFrontpage(category) {


   // changedTables(category);
    var huecos = getHoles();

    huecos.push($('div_no_home'));
    huecos.push($('art'));


    var places = {};
    var huecos_id = new Array();

    for(var i=0; i<huecos.length; i++) {
        huecos_id.push(huecos[i].id);
    }

    huecos.each(function(div_id, i){
        if( $(div_id) ) {
                $(div_id).select('table').each(function(item) {
                        if(item.getAttribute('value')) {
                                places[ item.getAttribute('value') ] = huecos_id[i];

                        }
                });
        }
    });



        // Form
	var frm = $('formulario');
      //  console.log(places);
	// Send articles positions into 'id' text field
	frm.id.value =  Object.toJSON(places);
    frm.category.value = category;
   // frm.preview_time.value = 20;


    new Ajax.Request('article_save_positions.php',{
        method: 'post',
        parameters: frm.serialize(),

        onLoading: function() {
           showMsg({'loading':['Cargando previsualización...']},'growl');
        },
        onComplete: function(transport) {
            $('formulario').action.value = '';
            myLightWindow.activateWindow({
                        href: '/controllers/preview.php?articles='+ Object.toJSON(places) +'&category'+category,
                        title: 'Previsualización Portada',
                        author: ' ',
                        type: 'external'
                    });

            hideMsgContainer('msgBox');
        }
    });

}

/**
 * Eliminar desde botonera las cachés
 */
function clearcache(category) {	
	new Ajax.Request('refresh_caches.php?category=' + encodeURIComponent(category), {
		onSuccess: function(transport) {
			$('warnings-validation').update(transport.responseText);
			new Effect.Highlight( $('warnings-validation') );
		}
	});
}

///////////////////////////////////////////////////////////////////////////
//REVISAR: SE usa???
function isflash(name) {
//ojo ruta: 
	var posic=name.lastIndexOf('.');
	var extension= name.substring(posic);	
	
	if (extension =='.swf')
	   return true;
	else
	   return false;	
}

//Checkea el nombre de una imag expresion tipo: 20080512Cervantes.jpg

function isNameOk(name) {
 //ojo con la ruta: 
	var posic=name.lastIndexOf('/');
	posic=posic+1; //Para que coja la barra /
	var nombre= name.substring(posic);	
	var filter=/^[0-9A-Za-z_]+\.[A-Za-z][A-Za-z][A-Za-z]$/;
	
	if (filter.test(nombre))
	   return true;
	else
	   return false;	
}

///////////////////////////////////////////////////////////////////////////////////////
//REVISAR: SE usa???
function show_iframe(){

	$('overlay').setAttribute('style','visibility:visible;'); 
	$('black').setAttribute('style','visibility:visible;'); 
	$('iframecont').setAttribute('style','visibility:visible;');
}

//////////////////////////// CUADROS IMAGEN Y VIDEO //////////////////////////////////////////////////////////

//Eliminar y recuperar imagen en articulos.
 function recuperar_eliminar(field){  
	  var nombre='input_'+field; 
	  if (document.getElementById( nombre ).value ==''){
	  	 recuperarOpacity(field);
	  }else{
	  	 vaciarImg(field);
	  }
 }
  
  //Vaciar foto y meter img_default.
 function vaciarImg(field){    
 		var nombre='remove_'+field;   //Icono papelera-recuperar 		
		document.getElementById( nombre ).src='themes/default/images/trash_no.png';	
	    document.getElementById( nombre ).setAttribute('alt','Recuperar');
	    document.getElementById( nombre ).setAttribute('title','Recuperar');
	
		if(field=='img1'){				
				document.getElementById( 'input_img1' ).value ='';		
				document.getElementById( 'input_video' ).value ='';
				document.getElementById('img_portada').setAttribute('style','opacity:0.4;');
				document.getElementById( nombre ).setAttribute('style','opacity:1;');					
				document.getElementById('informa').setAttribute('style','opacity:0.4;overflow:auto;width:260px;');		
				document.getElementById('img1_footer').setAttribute('disabled','true');
		}
		
		if(field=='img2'){					
				document.getElementById( 'input_img2' ).value ='';
				document.getElementById('img_interior').setAttribute('style','opacity:0.4;');			
				document.getElementById(nombre).setAttribute('style','opacity:1;');
				document.getElementById('informa2').setAttribute('style','opacity:0.4;overflow:auto;width:260px;');					
				document.getElementById('img2_footer').setAttribute('disabled','true');
		}
		//Publicidad 
		if(field=='img'){		   
			document.getElementById( 'img' ).value ='';				
			document.getElementById('preview_img').setAttribute('style','opacity:0.4;');		
			document.getElementById('informa2').setAttribute('style','opacity:0.4;overflow:auto;width:260px;');
			document.getElementById('noinfor2').setAttribute('style','opacity:0.4;');				 
		}
		
		if(field=='video2'){			
			document.getElementById( 'input_video2' ).value ='';				
			document.getElementById('video_interior').setAttribute('style','opacity:0.4;');		
			document.getElementById(nombre).setAttribute('style','opacity:1;');
			document.getElementById('informa3').setAttribute('style','opacity:0.4;overflow:auto;width:260px;');
			document.getElementById('video2_footer').setAttribute('style','opacity:0.4;');				 
		}

  }
  
 function recuperarOpacity(field){  
	    var nombre='remove_'+field;   
		document.getElementById( nombre ).src='themes/default/images/remove_image.png';	
	    document.getElementById( nombre ).setAttribute('alt','Eliminar');
	    document.getElementById( nombre ).setAttribute('title','Eliminar');
		if(field=='img1'){				
				document.getElementById( 'input_img1' ).value =document.getElementById( 'change1' ).name;			
				document.getElementById( 'input_video' ).value =document.getElementById( 'change1' ).name;
				document.getElementById('img_portada').setAttribute('style','opacity:1;');			
				document.getElementById('informa').setAttribute('style','opacity:1;overflow:auto;width:260px;');
				document.getElementById('img1_footer').removeAttribute('disabled');
	 	}
		
		if(field=='img2'){			   
				document.getElementById( 'input_img2' ).value = document.getElementById( 'change2' ).name;
				document.getElementById('img_interior').setAttribute('style','opacity:1;');
				document.getElementById('informa2').setAttribute('style','opacity:1;overflow:auto;width:260px;');			
				document.getElementById('img2_footer').removeAttribute('disabled');
		}
	
		if(field=='video2'){		  
			
			document.getElementById('video_interior').setAttribute('style','opacity:1;');		
			document.getElementById(nombre).setAttribute('style','opacity:1;');
			document.getElementById('informa3').setAttribute('style','opacity:1;overflow:auto;width:260px;');
			document.getElementById('video2_footer').setAttribute('style','opacity:1;');		
			document.getElementById( 'input_video2' ).value =document.getElementById( 'change3' ).name;				
		}
 }
 
// Paginacion galeria videos.
 function  get_videos(page)
 {
 	   new Ajax.Updater('videos', "article_change_videos.php?page="+page,
		{
 		   	evalScripts: false, 
	   		onComplete: function() {		   				   			
	   			var photos = $('videos').select('img');
	   			for(var i=0; i<photos.length; i++) {
	   				//console.log("'" + photos[i].id + "'");
	   				try {
	   				//	new Draggable(photos[i].id, { revert:true, scroll: window, ghosting:true }  );
	   				} catch(e) {
	   				//	console.debug( e );
	   				}
	   			}
 			} 		   
	   	} );
 }


//Paginacion otros articulos.
function  get_suggested_articles(category,page)
{
   if(!page){page=1;}
   if(!category){category='home';}
   new Ajax.Updater('frontpages', "article.php?action=get_suggested_articles&category="+category+"&page="+page,
    {
        evalScripts: true,
        onComplete: function() {
           make_sortable_divs_portadas();
           Sortable.create('art',{
                   tag:'table',
                   only:'tabla',
                   dropOnEmpty: true,
                   containment:['des','even','odd','hole1','hole2','hole3','hole4','art']
           });
           Draggables.observers.each(function(item){
                    item.onEnd= avisoGuardarPosiciones;
                });
        }
    });
}

//Paginacion otros articulos.
function  get_others_articles(category,page)
{
    new Ajax.Updater('frontpages', "article.php?action=get_others_articles&category="+category+"&page="+page,
    {
        evalScripts: true,
        onLoaded : $('artic').update('<h2> Cargando ...</h2>'),
        onComplete: function() {
             make_sortable_divs_portadas();
             Sortable.create('art',{
                   tag:'table',
                   only:'tabla',
                   dropOnEmpty: true,
                   containment:['des','even','odd','hole1','hole2','hole3','hole4', 'art']
                });
            
        }
    } );
}
function  change_style_link(link)
{
 var Nodes=$('down_menu').select('a');
    for (i = 0; i < Nodes.length; i++) {
        Nodes[i].setAttribute('style','cursor:pointer;background-color:#F2F2F2;color:#999999;float:left;margin-left:0;margin-right:6px;padding:5px 8px;text-decoration:none;');
    }
 $(link).setAttribute('style',"cursor:pointer;color:#000000; font-weight:bold; background-color:#BFD9BF");

}

function  reload_div_menu(category){

    new Ajax.Updater('menu_front_category', "article.php?action=reload_menu&category="+category,
    {
          evalScripts: true
    });//
}

 //Paginacion otras portadas.
function  get_frontpage_articles(category)
{
    new Ajax.Updater('frontpages', "article.php?action=get_frontpage_articles&category="+category,
    {
        evalScripts: true,      
        onLoaded : $('frontpages').update('<h2> Cargando ...</h2>'),
        onComplete: function() {
            make_sortable_divs_portadas('home');
          /*  Sortable.create( 'left' ,{
                tag:'table',
                only:'tabla',
                dropOnEmpty: true,
                containment:['des','even','odd','art2','hole1','hole2','hole3','hole4']
                });
            Sortable.create( 'right' ,{
                tag:'table',
                only:'tabla',
                dropOnEmpty: true,
                containment:['des','even','odd','art2','hole1','hole2','hole3','hole4']
                }); */
            Sortable.create( 'top' ,{
                tag:'table',
                only:'tabla',
                dropOnEmpty: true,
                containment:['des','even','odd','art2','hole1','hole2','hole3','hole4']
                });
        }
    } );
}

//////////////////////////// CONTENIDOS RELACIONADOS //////////////////////////////////////////////////////////

// make sortable las listas para poder ordenarlas
function mover(){ 
    Sortable.create('thelist2',{constraint: 'false',scroll:'scroll-container2'});
    Sortable.create('thelist2int',{constraint: 'false',scroll:'scroll-container2int'});

}
 
// Recoge los li de las listas ver portada, ver interior y los mete en input de relacionados portada o interior
function recolectar() { 
		//ordenArti (listado portada)
		  var resul2 = $('ordenPortada'); 
		  Nodes= $$('#thelist2 li'); 
		 // Nodes = document.getElementById('thelist2').getElementsByTagName("li");      		
		  for (var i=0;i < Nodes.length;i++) {  		
	  			id= Nodes[i].getAttribute('id'); 
	  			// mirar si vaciodocument.getElementById('thelist2').getElementsByTagName("li");      		
	  			if(id){					  			  					  					
	  				resul2.value = resul2.value + id + ", ";	  					 				  					  			
	  			}
		 }
			 
		//ordenArtiInt   (listado interior)
		  var resul2 = $('ordenInterior');
		 // Nodes = document.getElementById('thelist2int').getElementsByTagName("li");
		  Nodes= $$('#thelist2int li'); 
		  for (var i=0;i < Nodes.length;i++) {  		
	  			id= Nodes[i].getAttribute('id'); 	  			
	  			if(id){				  			    
					   resul2.value = resul2.value + id + ", ";								
				 }
		 }
			 	   
}

//Contenidos relacionados check - verportada verinterior - generar o borrar en lista.

function probarArtic(eleto, div, lista){
	if(lista=="thelist2"){
		var clase='portada';
	}else{ var clase='interior';}
	
	 //alert(clase + lista + div);
	// alert(eleto.id + eleto.checked + eleto.value);
	  var ul = document.getElementById(lista);	 
	  Nodes = document.getElementById(lista).getElementsByTagName("li");	
	
	  if(eleto.checked==false){		 		
		  for (var i=0;i < Nodes.length;i++) {  	
			  if(Nodes[i].getAttribute('id')==eleto.id) {		  			  
	  			   ul.removeChild(Nodes[i]);  				  			 
	  			}
			 
		  }
		//  Checks = document.getElementById(div).getElementsByTagName("input");
		  Checks = $$(div+'#'+clase);
		  for (var i=0;i < Checks.length;i++) {  			 
			  if(Checks[i].getAttribute('id') == eleto.id){
				  Checks[i].checked=false; //Si es sugerida, desclicamos en su categoria o viceversa
				  
			  }
			 
		  }
	  }else{		
		    if(eleto.checked==true){	
		  		var li = document.createElement('LI');
				li.setAttribute('id', eleto.id);					
			    li.setAttribute('style', 'cursor: move; list-style-type: none;');			 
			    var datos ="<td width='120'> " + eleto.getAttribute('tipo') + "</td> <td width='120'> " + eleto.getAttribute('seccion') + "</td> ";
			    var trash= " <td width='120'> <a href='#' onClick=\"javascript:del_relation('" + eleto.id + "','" + lista + "');\" title='Quitar relacion'> <img src='/admin/themes/default/images/trash.png' border='0' /> </a></td>";
				li.innerHTML =   " <table width='99%'> <tr> <td>" + eleto.value +"</td>" + datos + trash +" </tr></table> ";				
				ul.appendChild(li);			
				 // Por si esta en sugerida
				  Checks = $$(div+'#'+clase);
				//Checks = document.getElementById(div).getElementsByTagName("input");
				  for (var i=0;i < Checks.length;i++) {  			 
					  if(Checks[i].getAttribute('id') == eleto.id){
						  Checks[i].checked=true; //Si es sugerida, clicamos en su categoria o viceversa
						  
					  }
					 
				  }
		  	}		
	}	 
}
	  
//Palelera elimina elemento en lista de organizar relacionados. (habra que quitar el checked de los listados.)
function del_relation(eleto, lista){
	if(lista=="thelist2"){
		var clase='portada';
	}else{ 
		var clase='interior';
	}	
	var ul = document.getElementById(lista);	 
	Nodes = document.getElementById(lista).getElementsByTagName("li");		 		
	for (var i=0;i < Nodes.length;i++) {  	
		if(Nodes[i].getAttribute('id')==eleto) {		  			  
  			ul.removeChild(Nodes[i]);  				  			 
  		}			 
	}	  
	var Checks = $$('input.'+clase);
	for (var i=0;i < Checks.length;i++) {  
		if(Checks[i].getAttribute('id') == eleto){	
			 Checks[i].checked=false; 
		}
	}
}

// Listado noticias sugeridas relacionados.
function search_related(id, metadata,page) {
    if(metadata){
        new Ajax.Updater('search-noticias', 'article.php?action=search_related&id='+id+'&metadata='+metadata+'&page='+page,
        {
           onComplete: function() {
                 //Posibilidad de marcar los que estan recien añadidos
                    var Nodes = $('thelist2').select('li');
                      for (var i=0;i < Nodes.length;i++) {
                              var id=Nodes[i].getAttribute('id');
                              if(id){
                                      //var el =$$('#'+id+' input[class="portada"]');
                                      var el =$$('#'+id);
                                     el.each(function(item, index){
                                             if(item.getAttribute('class')=='portada'){
                                                    item.checked=true;
                                             }
                                     });

                              }

                      }
                      var Nodes = $('thelist2int').select('li');
                      for (var i=0;i < Nodes.length;i++) {
                              var id=Nodes[i].getAttribute('id');
                              if(id){
                                      var el =$$('.interior#'+id);
                                      el.each(function(item, index){
                                                     if(item.getAttribute('class')=='interior'){
                                                    item.checked=true;
                                             }
                                     });

                              }

                      }
            }
        });
    }
}

// Muestra listados: albumes, opiniones, videos, archivos
function  get_div_contents(id,content,category,page)
{
    var div = content+'_div';
    var action = 'get_'+content;
	  
    new Ajax.Updater(div, "article.php?action="+action+"&category="+category+"&id="+id+"&page="+page,
    {
        onComplete: function() {
            //Posibilidad de marcar los que estan recien añadidos
            var Nodes = $('thelist2').select('li');
              for (var i=0;i < Nodes.length;i++) {
                      var id=Nodes[i].getAttribute('id');
                      if(id){
                              //var el =$$('#'+id+' input[class="portada"]');
                              var el =$$('#'+id);
                             el.each(function(item, index){
                                     if(item.getAttribute('class')=='portada'){
                                            item.checked=true;
                                     }
                             });
                      }
              }
              var Nodes = $('thelist2int').select('li');
              for (var i=0;i < Nodes.length;i++) {
                      var id=Nodes[i].getAttribute('id');
                      if(id){
                              var el =$$('.interior#'+id);
                              el.each(function(item, index){
                                             if(item.getAttribute('class')=='interior'){
                                            item.checked=true;
                                     }
                             });
                      }
              }
        }
    });
}

//Muestra el div adecuado y oculta el resto.
function  divs_hide(mydiv)
{
	var divs=$$('div.div_lists');
	for (var i=0;i < divs.length;i++) {  	
		if(divs[i].id!=mydiv){
			Effect.Fade(divs[i]); 
		}
	 }
	 Effect.Appear(mydiv); 
	 return false;
}


function previewArticle(id,formID,type){
    if(!validateForm(formID))
        return false;

    $(formID).action.value = 'preview';
    $(formID).id.value = id;

    new Ajax.Request('article.php',{
        method: 'post',
        parameters: $(formID).serialize(),
        onSuccess: function(transport) {
            $('formulario').action.value = '';
            myLightWindow.activateWindow({
                href: '/controllers/preview_content.php?id='+transport.responseText+'&action=article',
                title: 'Previsualización',
                author: 'retrincos.info',
                type: 'external'
            });
        },
        onLoading: function() {
            /*
            $('savePreview').setStyle({display: 'none'});
            $('reloadPreview').setStyle({display: ''});
            $('reloadPreviewText').update('Cargando previsualización...');
            */
           showMsg({'loading':['Cargando previsualización...']},'growl');
        },
        onComplete: function(transport) {
            $('reloadPreview').setStyle({display: 'none'});
            if (type=='create') {
                $('button_save').onclick = function() {
                   recolectar();
                   sendFormValidate(this, '_self', 'update', transport.responseText, 'formulario');
                };
                $('button_preview').onclick = function() {
                   recolectar();
                   previewArticle(transport.responseText,'formulario','update');
                   return false;
                };
                $(formID).available.value = 0;
            }
            /*
            $('savePreview').setStyle({display: ''});
            $('savePreviewText').update('El artículo ha sido guardado tras la previsualización.');
            */
           showMsg({'info':['El artículo ha sido guardado tras la previsualización.']},'growl');
           setTimeout("hideMsgContainer('msgBox')",6000);
        }
    })
}


function delete_article(id,category,page){

      new Ajax.Request( 'article.php?action=delete&category='+category+'&id='+id+'&page='+page,
        {
            onSuccess: function(transport) {
                   var msg = transport.responseText;
                   if(confirm(msg)) {
                      var ruta='article.php?action=yesdel&category='+category+'&id='+id+'&page='+page;
                      location.href= ruta;
                   }
                   return false;
                 //showMsg({'warn':[msg ]},'growl');
            }
        });


 }

