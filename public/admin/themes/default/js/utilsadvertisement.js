//Funciones js para advertisement

/////// Advertisement


function permanencia(my) { 
   if(my.id=='clic'){		
		$('porclic').show();
		$('porview').hide();
		$('porfecha').hide();
	}else{
	  if(my.id=='view'){		
				$('porview').show();
				$('porclic').hide();
				$('porfecha').hide();
			}else{
				  if(my.id=='fecha'){		
					$('porclic').hide();
					$('porview').hide();
					$('porfecha').show();
				  }else{
				   if(my.id=='non'){		
					$('porclic').hide();
					$('porview').hide();
					$('porfecha').hide();
					}
				  }
			}
	}
}


function with_without_script(my) { 
   if(my.checked==true){		
		Effect.BlindDown($('div_script'));
		Effect.BlindUp($('div_img_publi'));		
		Effect.BlindUp($('div_url1'));		
		Effect.BlindUp($('div_url2'));		
		Effect.BlindUp($('photos'));	
		Effect.BlindUp($('div_permanencia'));
	}else{	 	
		Effect.BlindDown($('div_img_publi'));
		Effect.BlindDown($('photos'));
		Effect.BlindDown($('div_url1'));
		Effect.BlindDown($('div_url2'));		
		Effect.BlindDown($('div_permanencia'));
		Effect.BlindUp($('div_script'));
	}
}

function isflash(name) {
//ojo ruta: 
	var posic=name.lastIndexOf('.');
	var extension= name.substring(posic);	
	
	if (extension =='.swf')
	   return true;
	else
	   return false;	
}
function noticiasshow(my) {
  if( $(my.id).checked==true){
	$('noticias-relacionadas').show();
  }else{	
	$('noticias-relacionadas').hide();
  }	
}


function  get_advertisements(page) {
   new Ajax.Updater('photos', "advertisement_images.php?page="+page, {
         evalScripts: false, 
         onComplete: function() {		   			
            var photos = $('photos').select('img');
            for(var i=0; i<photos.length; i++) {
               //console.log("'" + photos[i].id + "'");
               try {
                  new Draggable(photos[i].id, { revert:true, scroll: window, ghosting:true }  );
               } catch(e) {
               //	console.debug( e );
               }
            }
         }
   });
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
		document.getElementById( nombre ).src='/admin/themes/default/images/trash_no.png';
	    document.getElementById( nombre ).setAttribute('alt','Recuperar');
	    document.getElementById( nombre ).setAttribute('title','Recuperar');


		//Publicidad
		if(field=='img'){
			document.getElementById( 'input_img' ).value ='';
			document.getElementById('change1').setAttribute('style','opacity:0.4;');
			document.getElementById('informa').setAttribute('style','opacity:0.4;overflow:auto;width:260px;');
			document.getElementById('noinfor').setAttribute('style','opacity:0.4;');
		}


  }

 function recuperarOpacity(field){
	    var nombre='remove_'+field;
		document.getElementById( nombre ).src='/admin/themes/default/images/remove_image.png';
	    document.getElementById( nombre ).setAttribute('alt','Eliminar');
	    document.getElementById( nombre ).setAttribute('title','Eliminar');
		if(field=='img'){
				document.getElementById( 'input_img' ).value =document.getElementById( 'change1' ).name;
                document.getElementById('change1').setAttribute('style','opacity:1;');
				document.getElementById('div_img_publi').setAttribute('style','opacity:1;');
				document.getElementById('informa').setAttribute('style','opacity:1;overflow:auto;width:260px;');

	 	}



 }
