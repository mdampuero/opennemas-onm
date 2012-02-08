// JavaScript Document
//Funciones llamadas en article.tpl
//

var mutex = false;

 //Devuelve los ids de los divs que contienen noticias.
 getFrontpageHoles = function(){
    var huecos= $('columns').select('div');
    return huecos;
}

getOtherContentsHoles = function() {
	var huecos= $('contents-provider').select('div.seccion');
	//console.log(huecos);
    return huecos;
}

//Mensajes alerta para contenedores de noticias.
alertsDiv = function(){

    var Nodes=$('hole3').select('table');
    if(Nodes.length<1){
		$('warnings3').update('Debe contener noticias debajo de publicidad 2');
		$('warnings-validation').update('<div class="notice">Recuerde guardar posiciones</div>');
		new Effect.Highlight( $('warnings-validation'));
	} else {
		$('warnings3').update(' ');
	}

}


make_sortable_divs_portadas = function() {

	// Retrieve all the placeholder in this frontpage
	var huecos = getFrontpageHoles();
	// Fetch all the available contents available for drop in frontpage
    var otherHuecos = getOtherContentsHoles();

    for(var i=0; i< otherHuecos.length; i++) {
        huecos.push(otherHuecos[i].id);
    }
	//huecos.push($('div_no_home'));
    huecos.push($('art'));
    //console.log(huecos);

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
    var huecos = getFrontpageHoles();

  //  huecos.push($('div_no_home'));
    huecos.push($('art'));

    var places = {};
    var huecos_id = new Array();

    for (var i=0; i<huecos.length; i++) {
        huecos_id.push(huecos[i].id);
    }


    huecos.each(function(div_id, i){
        if( $(div_id) ) {
            $position = 1;
            $(div_id).select('table').each(function(item) {
                if(item.getAttribute('value')) {
                    if(item.getAttribute('data')) {
                            $content_type = item.getAttribute('data')
                    } else {
                            $content_type = 'Article';
                    }
                    places[ item.getAttribute('value') ] = {'placeholder':huecos_id[i], 'content_type': $content_type, 'position' : $position};
                    item.setAttribute('name',"selected_fld[]");
                    $position++;
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

    if (places.length < 1) {
        if (confirm('¿Esta seguro de eliminar todos elementos de la portada ?')) {
            return false;
        }
    }

    if (!mutex) {
            new Ajax.Request(
                'controllers/article/article_save_positions.php',
                {
                    method: 'post',
                    parameters: frm.serialize(),

                    onLoading: function() {
                       $('warnings-validation').update('<div class="ui-blocker"></div><div class="ui-blocker-message"><img src="/admin/themes/default/images/ajax-loader.gif" /><br />Guardando posiciones de portada.<br />Por favor, espere un momento...</div>');
                       mutex = true;
                    },
                    onComplete: function(transport) {
                        $('warnings-validation').update( transport.responseText );

                        // Establecer o valor de posicionesIniciales para controlar os cambios de posicións e amosar avisos
                        if(posicionesIniciales) {
                                        posicionesIniciales = $$('input[type=checkbox]');
                                        posicionesInicialesWarning = false;
                        }
                        mutex = false;
                    },

                    onFailure: function() {
                         $('warnings-validation').update( transport.responseText );
                        new Effect.Highlight( $('warnings-validation') );
                        mutex = false;
                    }
                }
            );
    }
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

   /* Nodes=$('div_no_home').select('a.no_home');
    for (i = 0; i < Nodes.length; i++) {
            Nodes[i].setAttribute('class',"go_home");
    }*/
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
    var huecos = getFrontpageHoles();

  //  huecos.push($('div_no_home'));
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
    // Send articles positions into 'id' text field
    frm.id.value =  Object.toJSON(places);
    frm.category.value = category;
    // frm.preview_time.value = 20;

	$('formulario').action.value = '';
	myLightWindow.activateWindow({
		href: '/controllers/preview.php?articles='+ Object.toJSON(places) +'&category='+category,
		title: 'Previsualización Portada',
		author: ' ',
		type: 'external'
	});

}

function previewArticle(id,formID,type){
    if(!validateForm(formID))
        return false;

    $(formID).id.value = id;

    $('formulario').action.value = '';
    myLightWindow.activateWindow({
        href: '/controllers/preview_content.php?id='+id+'&action=article',
        title: 'Previsualización',
        author: '',
        type: 'external'
    });

}
/**
 * Eliminar desde botonera las cachés
 */
function clearcache(category) {
	new Ajax.Request('controllers/tpl_manager/refresh_caches.php?category=' + encodeURIComponent(category), {
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

//Paginacion otros articulos.
function  get_suggested_articles(category,page) {
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
function  get_others_articles(category,page){
    new Ajax.Updater('other-articles', "article.php?action=get_others_articles&category="+category+"&page="+page,
    {
        evalScripts: true,
        onLoaded : $('other_articles_in_category').update('<h2> Cargando ...</h2>'),
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
function  change_style_link(link) {
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
                          if(id!=$$('#'+id)){
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
                          if(id!=$$('.interior#'+id)){
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

// Muestra listado de la busqueda avanzada
function search_adv(id, metadata,page) {
    var inputs = document.getElementsByTagName("input");
    var cbs = []; //will contain all checkboxes
    var checked = []; //will contain all checked checkboxes
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].type == "checkbox" && inputs[i].className == "search") {
            cbs.push(inputs[i]);
            console.log(inputs[i].checked);
            if (inputs[i].checked) {
                checked.push(inputs[i].id);
            }
        }
    }
    if(metadata){
        new Ajax.Updater('search-div2', 'article.php?action=search_adv&id='+id+'&metadata='+metadata+'&page='+page+'&type='+checked,
        {
           onComplete: function() {

                 //Posibilidad de marcar los que estan recien añadidos
                    var Nodes = $('thelist2').select('li');
                      for (var i=0;i < Nodes.length;i++) {
                              var id=Nodes[i].getAttribute('id');
                              if(id!=$$('#'+id)){
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
                              if(id!=$$('.interior#'+id)){
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
            //Shows the selected dive and hide others.
	var divs=$$('div.div_lists');
	for (var i=0;i < divs.length;i++) {
                if(divs[i].id!=div){
			Effect.DropOut(divs[i], { duration: 0.2 });
		}
	 }
            Effect.Appear(div,  { duration: 0.1 });
}
    });
}

//Muestra el div adecuado y oculta el resto.
function  divs_hide(mydiv)
{
	var divs=$$('div.div_lists');
	for (var i=0;i < divs.length;i++) {
		if(divs[i].id!=mydiv){
			Effect.DropOut(divs[i], { duration: 0.2 });
		}
	 }
	 Effect.Appear(mydiv,  { duration: 0.1 });
	 return false;
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
