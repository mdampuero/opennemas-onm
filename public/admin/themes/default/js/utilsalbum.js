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
        

        if(($(id).getAttribute('de:type_img')=='swf') || ($(id).getAttribute('de:type_img')=='SWF')){
            min.setAttribute('style','width:16px; height:16px;');
            min.setAttribute('de:url', $(id).getAttribute('de:url'));
            min.setAttribute('de:type_img', $(id).getAttribute('de:type_img'));
            min.setAttribute('ondblclick', 'return false;');
            span = document.createElement('span');
            span.setAttribute('style','float: right; clear: none;');
            span.appendChild(min);
            div = document.createElement('div');
            div.innerHTML='<object id="image_view" de:type_img="'+ $(id).getAttribute('de:type_img')+">"
                                                    +'<param name="movie" value="'+ $(id).getAttribute('de:url') +'/'+ $(id).getAttribute('de:mas')
                                                    + '"><embed src="'+ $(id).getAttribute('de:url')
                                                    +'/'+$(id).getAttribute('de:mas')+ '" width="68" height="50" ></embed></object>';

            div.appendChild(span);
            a.appendChild(div);
        }else{
            min.setAttribute('width', $(id).getAttribute('de:width'));
            a.appendChild(min);
        }
        li.appendChild(a);

        ul.appendChild(li);
        //Definimos la papelera
        funcion='del_img("'+del+'")';
         $('remove_img').setAttribute('onclick', funcion);
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

    Droppables.add('droppable_div1', {
        accept: 'draggable',
        hoverclass: 'hover',
        onDrop: function(element, droppable) {				
            var OID = album_put_mini(element.id);
            if(OID) {
                if((element.getAttribute('de:type_img')=='swf') || (element.getAttribute('de:type_img')=='SWF')){

                     var ancho=element.getAttribute('de:ancho');
                     if(element.getAttribute('de:ancho')>300) { ancho=300; }
                     $('droppable_div1').innerHTML='<object id="image_view" de:type_img=">'+ element.getAttribute('de:type_img')
                                                    +'<param name="movie" value="'+ element.getAttribute('de:url') +'/'+ element.getAttribute('de:mas')
                                                    + '"><embed src="'+ element.getAttribute('de:url')
                                                    +'/'+element.getAttribute('de:mas')+ '" width="'+ancho+'" ></embed></object>';
                     $('informa').innerHTML=' es un Flash';
                     $('informa').innerHTML="<b>Archivo: </b>"+element.getAttribute('de:mas') + "<br><b>Dimensiones: </b>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><b>Peso: </b>" + element.getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created');
                     
                     $('img_footer').value= element.getAttribute('de:description');

                } else {
                    update_footer();

                    var src =element.src;
                    src=src.replace( '140-100-','');
                    
                    $('informa').innerHTML="<b>Archivo: </b>"+element.getAttribute('de:mas') + "<br><b>Dimensiones: </b>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><b>Peso: </b>" + element.getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created') + "<br><b>Descripcion: </b>" + element.getAttribute('de:description') +"<br><b>Tags: </b>" + element.getAttribute('de:tags');
                    $('img_footer').value= element.getAttribute('de:description');

                    //var funcion='save_footer('+element.id+')';
                    var funcion = 'save_footer(\''+OID+'\')';
                    $('img_footer').setAttribute('onchange', funcion);
                    $('img_footer').setAttribute('rel', OID); // id da imaxe

                    if( /Firefox\/2/.test(navigator.userAgent) ) {
                            $('image_view').parentNode.style.height = $('image_view').height + 'px';
                    }

                    var ancho=element.getAttribute('de:ancho');
                    $('droppable_div1').innerHTML= '<img src="'+ src.replace( '140-100-','') + '"  id="image_view" border="0" style="max-width: 300px;" width="'+ancho+'" >';


                }
            }
       }
    });

    Droppables.add('testWrap', {
        accept: 'draggable2',
        onDrop: function(element, droppable) {
            if(crop != null) {
                crop.remove();
            }
            $('testImage').src  = element.src;
            $('path_img').value = element.getAttribute('de:path');
            $('name_img').value = element.getAttribute('de:mas');

            var a = element.getAttribute('de:dimensions');
            var b = a.split(' x ');
            var c = b[1].split(' (px)');

            $('testImage').width  = b[0];
            $('testImage').height = c[0];

            cropcreate();
        }
    });

}



//CROP

var crop = null;


function cropcreate() {
    var cropWidth = $('cropWidth').value;
    var cropHeight = $('cropHeight').value;
    crop = new Cropper.ImgWithPreview('testImage', {
        minWidth: cropWidth,
        minHeight: cropHeight,
        ratioDim: { x: cropWidth, y: cropHeight },
        displayOnInit: true,
        onEndCrop: onEndCrop,
        previewWrap: 'previewArea'
    });
}

function onEndCrop( coords, dimensions ) {
    $( 'x1' ).value = coords.x1;
    $( 'y1' ).value = coords.y1;

    $( 'width' ).value  = dimensions.width;
    $( 'height' ).value = dimensions.height;
}


function define_crop(element) {

    if(crop != null) {
        crop.remove();
    }
    if($('crop_img')) {
        $('crop_img').src='';
    }
    $$('#previewArea img').each( function(e) { e.src=''; } ); //Para ocultar las de debajo
    $('testImage').src = $('media_path').value +element.getAttribute('de:path') + element.getAttribute('de:mas');
    $('path_img').value = element.getAttribute('de:path');
    $('name_img').value = element.getAttribute('de:mas');
    var a = element.getAttribute('de:dimensions');
    var b = a.split(' x ');
    var c = b[1].split(' (px)');
    var wh = parseInt(b[0]);
    var ht = parseInt(c[0]);

    $('testImage').width = wh;
    $('testImage').height = ht;

    if(ht>wh){
        if (ht>400) {
            var w = Math.floor( (wh * 400) / ht );
             $('testImage').setStyle({
                  height: '400px',
                  width :  w +"px"
             });
         } else {
             $('testImage').setStyle({
                      height: ht +'px',
                      width : wh +'px'
                    });
        }

    } else {
        if(wh>600){
               var h = Math.floor( (ht * 600) / wh );
                $('testImage').setStyle({
                  width: '600px',
                  height : h +"px"
                });
        }else{
         $('testImage').setStyle({
                 height: ht+'px',
                 width :  wh +'px'
                });
        }
    }
 

    if ( wh < parseInt($('cropWidth').value) ) {
        alert('La foto escogida para portada no supera los '+ $('cropWidth').value +'px de ancho ('+ wh +').' );
    }else{
        if(ht <  parseInt($('cropHeight').value) ) {
            alert('La foto escogida para portada no supera los '+ $('cropHeight').value +'px de alto ('+ ht +').' ); }
    }

    cropcreate();
 }


function check_crop() {
    if( $('crop_img') != null){ //update
        return true;
    }
    if(($( 'width' ).value=="")  || ($( 'width' ).value==null) || ($( 'width' ).value.length==0)){
         showMsgContainer({'warn':[' Debe crear un crop para la portada  del album ']},'inline','album_msg');
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
function save_footer(img) {
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
    if(($(img).getAttribute('de:type_img')=='swf') || ($(img).getAttribute('de:type_img')=='SWF')){

        var ancho=$(img).getAttribute('de:ancho');
        if($(img).getAttribute('de:ancho')>300) { ancho=300; }
        $('droppable_div1').innerHTML='<object id="image_view" de:type_img=">'+ $(img).getAttribute('de:type_img')
                                +'<param name="movie" value="'+ $(img).getAttribute('de:url') +'/'+ $(img).getAttribute('de:mas')
                                + '"><embed src="'+ $(img).getAttribute('de:url')
                                +'/'+$(img).getAttribute('de:mas')+ '" width="'+ancho+'" ></embed></object>';
    }else{

        var src =$(img).src;

        var ancho=$(img).getAttribute('de:ancho');
        $('droppable_div1').innerHTML= '<img src="'+ src.replace( '140-100-','') + '"  id="image_view" border="0" style="max-width: 300px;" width="'+ancho+'" >';
    }
	$('informa').innerHTML="<b>Archivo: </b>"+$(img).getAttribute('name') + "<br><b>Dimensiones: </b>"+$(img).getAttribute('de:dimensions') + " <br><b>Peso: </b>" + $(img).getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + $(img).getAttribute('de:created') + "<br><b>Descripcion: </b>" + $(img).getAttribute('de:description') +"<br><b>Tags: </b>" + $(img).getAttribute('de:tags');
	$('img_footer').value= $(img).getAttribute('de:footer'); 
	var funcion='save_footer('+img+')';
	$('img_footer').setAttribute('onChange', funcion);
	$('img_des').value= $(img).getAttribute('value'); 
    
	// Establecer o rel ao id da imaxen que se está a modificar
	$('img_footer').setAttribute('rel', del)
	
	var id= $(img).getAttribute('value');
	funcion='del_img("'+del+'")';
	$('remove_img').setAttribute('onclick', funcion);
}



//Eliminar miniatura

function del_img(id) {
		var li=$(id);
		if (li) {
			li.parentNode.removeChild(li);
		}
		$('image_view').src= "/admin/themes/default/images/default_img.jpg";
        $('image_view').setAttribute('width',300);
		$('informa').innerHTML="<b>Archivo: default_img.jpg</b> <br><b>Dimensiones:</b> 300 x 208 (px)<br><b>Peso:</b> 4.48 Kb<br><b>Fecha de creaci&oacute;n:</b> 11/06/2008<br><b>Descripcion:</b>  Imagen por defecto.  <br><b>Tags:</b> Imagen<br>";
		$('img_footer').value= ""; 
		$('img_des').value= ""; 
}

// Get the list album and put in input for submit.

function album_get_order(){
	
	
	//coge el orden de las phtos en el album
    var orden = $('ordenAlbum');
    orden.value =" ";
   
    Nodes = document.getElementById('album_list').getElementsByTagName("img");
    for (var i=0;i < Nodes.length;i++) {
        pkfoto= Nodes[i].getAttribute('de:pk_photo');
        footer= Nodes[i].getAttribute('de:footer');
        // mirar si vacio
        if(pkfoto!=' '){ // el ultimo es espacio
                //	orden.value = orden.value + pkfoto + ", ";
                        orden.value = orden.value + pkfoto + "::" + footer + "++";
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


//Eliminar y recuperar imagen en articulos.
 function recuperar_eliminar(field){
	  var nombre='img_des';
	  if (document.getElementById( nombre ).value ==''){
	  	 recuperarOpacity(field);
	  }else{
	  	 vaciarImg(field);
	  }
 }

//Vaciar foto y meter img_default.
function vaciarImg(field){
 	/*	var nombre='remove_'+field;   //Icono papelera-recuperar
		document.getElementById( nombre ).src='themes/default/images/trash_no.png';
	    document.getElementById( nombre ).setAttribute('alt','Recuperar');
	    document.getElementById( nombre ).setAttribute('title','Recuperar');
*/
		if(field=='img'){
				document.getElementById( 'input_img' ).value ='';				 
				document.getElementById('image_view').setAttribute('style','opacity:0.4;');
				document.getElementById( nombre ).setAttribute('style','opacity:1;');
				document.getElementById('informa').setAttribute('style','opacity:0.4;overflow:auto;width:260px;');
				document.getElementById('img_footer').setAttribute('disabled','true');
		}

  }

 function recuperarOpacity(field){
	    var nombre='remove_'+field;
//		document.getElementById( nombre ).src='themes/default/images/trash.png';
//	    document.getElementById( nombre ).setAttribute('alt','Eliminar');
//	    document.getElementById( nombre ).setAttribute('title','Eliminar');
		if(field=='img'){
				document.getElementById( 'input_img' ).value =document.getElementById( 'image_view' ).name;
 				document.getElementById('image_view').setAttribute('style','opacity:1;');
				document.getElementById('informa').setAttribute('style','opacity:1;overflow:auto;width:260px;');
				document.getElementById('img_footer').removeAttribute('disabled');
	 	}

 }
