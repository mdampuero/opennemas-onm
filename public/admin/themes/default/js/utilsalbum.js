// JavaScript Document
var num=0
//add the mini in the list of album
function album_put_mini(id){
	
	var ul = $('album_list');
	Nodes = $('album_list').select('img');
        for (var i=0;i < Nodes.length;i++) {      
            if(Nodes[i].getAttribute('de:mas')==$(id).getAttribute('de:mas')){
            //    showMsg({'warn':['La imagen ya esta en el album ']},'growl');
               //album_msg
            showMsgContainer({'warn':['La imagen ya esta en el album ']},'inline','album_msg');
                 return false;
            }
        }
        hideMsgContainer('album_msg');
        num++;
	var pkfoto=$(id).name;
	var imag=$(id).src;
	 // alert(pkfoto);
	var del='n'+num+'-'+pkfoto;
	li= document.createElement('li');  		
	li.setAttribute('id', del);	
	li.setAttribute('de:pk_photo', pkfoto);				
	li.setAttribute('value', imag);			
	li.setAttribute('class', 'family');
	li.setAttribute('style', 'cursor: move; list-style-type: none;');		

	a = document.createElement('A');		 
	a.title='Mostrar imagen';
	var funcion='show_image("img'+pkfoto+'","'+del+'")';
	a.setAttribute('onclick', funcion);
	a.className = 'album';
	a.setAttribute('class', 'album');
	min = document.createElement('img');
        min.id= 'img'+pkfoto;
        min.border=1;
        min.src= imag;
        min.setAttribute('name', $(id).name);
        min.setAttribute('de:pk_photo', pkfoto);
        min.setAttribute('value', 'n'+num+'-'+pkfoto);
        min.setAttribute('de:created', $(id).getAttribute('de:created'));
        min.setAttribute('de:peso', $(id).getAttribute('de:peso'));
        min.setAttribute('de:dimensions', $(id).getAttribute('de:ancho') + " x " +$(id).getAttribute('de:alto') );
        min.setAttribute('de:description', $(id).getAttribute('de:description'));
        min.setAttribute('de:footer', $(id).getAttribute('de:description'));
        min.setAttribute('de:tags', $(id).getAttribute('de:tags'));
        min.setAttribute('de:path', $(id).getAttribute('de:path'));
        min.setAttribute('de:mas', $(id).getAttribute('de:mas'));
        min.setAttribute('class', 'draggable2');
        min.setAttribute('ondblclick', 'define_crop(this)');

        // added by vifito
        min.setAttribute('width', $(id).getAttribute('de:width'));

        a.appendChild(min);

        li.appendChild(a);

        ul.appendChild(li);
        //Definimos la papelera
        funcion='del_img("'+del+'")';
        $('remove').setAttribute('onclick', funcion);
        //Lo hacemos movible.
        Sortable.create('album_list',{constraint: 'false', scroll:'scroll-album'});
       // new Draggable('img'+pkfoto, { revert:true, scroll: window, ghosting:true }  );
	return(del);
} 
  
 
 //Make moveable the list initial and define droppable the div show wath the image description
function album_make_mov(){
     //Para poder ordenarlas
      Sortable.create('album_list',{constraint: 'false', scroll:'scroll-album'});

    //Para poder arrastrarlas

  Droppables.add('nifty', { 
    accept: 'draggable',
    hoverclass: 'hover',
    onDrop: function(element, droppable) {
		
		
		var OID = album_put_mini(element.id);
                if(OID){
                    update_footer();
                //    $('image_view').src = element.src;
                    var src =element.src;
                    src=src.replace( '140x100-','');           
                    $('image_view').src= src;
                    
                    $('informa').innerHTML="<b>Archivo: </b>"+element.getAttribute('de:mas') + "<br><b>Dimensiones: </b>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><b>Peso: </b>" + element.getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created') + "<br><b>Descripcion: </b>" + element.getAttribute('de:description') +"<br><b>Tags: </b>" + element.getAttribute('de:tags');
                    $('img_footer').value= element.getAttribute('de:description');

                    //var funcion='save_footer('+element.id+')';
                    var funcion = 'save_footer(\''+OID+'\')';
                    $('img_footer').setAttribute('onchange', funcion);
                    $('img_footer').setAttribute('rel', OID); // id da imaxe

                    if( /Firefox\/2/.test(navigator.userAgent) ) {
                            $('image_view').parentNode.style.height = $('image_view').height + 'px';
                    }
                }
  	}
  });

}


function check_crop() {
   if($('crop_img')!=null){ //update
        return true;
      }
    if(($( 'width' ).value=="")  || ($( 'width' ).value==null) || ($( 'width' ).value.length==0)){
 //       alert('Debe crear un crop');
     //   var msgs2="Debe crear un crop para la portada  del album";
      // msg({'warn':['']});
     
     // showMsg({'warn':['Debe crear un crop para la portada  del album ']},'growl');
     alert('Debe crear un crop para la portada del album ');
        return false;
    }   
    return true;
}


function stripslashes( str ) {
    return (str).replace(/\0/g, '0').replace(/\\([\\'"])/g, '$1');
}

function update_footer() {
	if( $('img_footer').getAttribute('rel') && $($('img_footer').getAttribute('rel'))  ) {		
		$( $('img_footer').getAttribute('rel') ).select('img')[0].setAttribute('de:footer', stripslashes($('img_footer').value)); 
	}	
}

//Save footer_img
function save_footer(img){	
	var descrip = $('img_footer').value;	
	
	//alert( 's'+descrip);
	if( $(img).nodeName.toUpperCase() == 'IMG' ) {
		$(img).setAttribute('de:footer', descrip);
	} else {
		if( $(img).select('img').length > 0) {
			$(img).select('img')[0].setAttribute('de:footer', descrip);
		}
	}
}



//Show description and image in cuadro imagen.
function show_image(img,del) {
	update_footer();
	
	$('informa').innerHTML="";
//	$('image_view').src= $(img).src;
        var src =$(img).src;
        src=src.replace( '140x100-','');
        $('image_view').src= src;
	$('informa').innerHTML="<b>Archivo: </b>"+$(img).getAttribute('name') + "<br><b>Dimensiones: </b>"+$(img).getAttribute('de:dimensions') + " <br><b>Peso: </b>" + $(img).getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + $(img).getAttribute('de:created') + "<br><b>Descripcion: </b>" + $(img).getAttribute('de:description') +"<br><b>Tags: </b>" + $(img).getAttribute('de:tags');
	$('img_footer').value= $(img).getAttribute('de:footer'); 
	var funcion='save_footer('+img+')';
	$('img_footer').setAttribute('onChange', funcion);
	$('img_des').value= $(img).getAttribute('value'); 
    
	// Establecer o rel ao id da imaxen que se está a modificar
	$('img_footer').setAttribute('rel', del)
	
	var id= $(img).getAttribute('value');
	funcion='del_img("'+del+'")';
	$('remove').setAttribute('onclick', funcion); 
}



//Eliminar miniatura

function del_img(id) {
		var li=$(id);
		if (li) {
			li.parentNode.removeChild(li);
		}
		$('image_view').src= "../media/images/default_img.jpg";
		$('informa').innerHTML="<b>Archivo: default_img.jpg</b> <br><b>Dimensiones:</b> 300 x 208 (px)<br><b>Peso:</b> 4.48 Kb<br><b>Fecha de creaci&oacute;n:</b> 11/06/2008<br><b>Descripcion:</b>  Imagen por defecto.  <br><b>Tags:</b> Imagen<br>";
		$('img_footer').value= ""; 
		$('img_des').value= ""; 
}

// Get the list album and put in input for submit.

function album_get_order(){
	
	
	//coge el orden de las phtos en el album
    var orden = $('ordenAlbum');
    orden.value ="";
   
    Nodes = document.getElementById('album_list').getElementsByTagName("img");
    for (var i=0;i < Nodes.length;i++) {
        pkfoto= Nodes[i].getAttribute('de:pk_photo');
        footer= Nodes[i].getAttribute('de:footer');
        // mirar si vacio
        if(pkfoto!=' '){ // el ultimo es espacio
                //	orden.value = orden.value + pkfoto + ", ";
                        orden.value = orden.value + pkfoto + "::" + footer +"++";
        }

     }

}	


function get_images_album(param,page)
{
    new Ajax.Updater('photos', "album.php?page="+page+"&action=get_images_album",
        {
            evalScripts: true,
            onComplete: function() {
                var photos = $('photos').select('img');
                for(var i=0; i<photos.length; i++) {
                  //  console.log("'" + photos[i].id + "'");
                    try {
                        new Draggable(photos[i].id, { revert:true, scroll: window, ghosting:true }  );
                    } catch(e) {
                     //   console.debug( e );
                    }
                }
            }
        } );
}


function delete_album(id,page){

      new Ajax.Request( 'album.php?action=delete&id='+id+'&page='+page,
        {
            onSuccess: function(transport) {
                 var msg = transport.responseText;
                // showMsg({'warn':[msg ]},'growl');
                 if(confirm(msg)) {
                      var ruta='album.php?action=yesdel&id='+id+'&page='+page;
                      location.href= ruta;
                   }
            }
        });


 }
