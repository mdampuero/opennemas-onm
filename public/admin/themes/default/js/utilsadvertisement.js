//Funciones js para advertisement

/////// Advertisement


function permanencia(my) {
	console.log(my.options[my.selectedIndex].value);
	var selectedOption = my.options[my.selectedIndex].value;
	if (selectedOption=='CLIC') {
		$('porclic').show();
		$('porview').hide();
		$('porfecha').hide();
	} else if (selectedOption=='VIEW') {
		$('porview').show();
		$('porclic').hide();
		$('porfecha').hide();
	} else if (selectedOption=='DATE') {
		$('porclic').hide();
		$('porview').hide();
		$('porfecha').show();
	} else {
		$('porclic').hide();
		$('porview').hide();
		$('porfecha').hide();
	}
}


function with_without_script(my) {

   if (my.checked === true){
		$('div_script').setStyle({ display: 'block'});
		$('div_url1').setStyle({ display: 'none'});
		$('advertisement-images').setStyle({ display: 'none'});
	} else {
		$('advertisement-images').setStyle({ display: 'block'});
		$('photos').setStyle({ display: 'block'});
		$('div_url1').setStyle({ display: 'block'});
		$('div_script').setStyle({ display: 'none'});
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
  if( $(my.id).checked===true){
	$('noticias-relacionadas').show();
  }else{
	$('noticias-relacionadas').hide();
  }
}

//////////////////////////// CUADROS IMAGEN Y VIDEO //////////////////////////////////////////////////////////

// TODO: this code is identical utils.js or utilsarticle.js

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
		document.getElementById( nombre ).src='/admin/themes/default/images/trash.png';
	    document.getElementById( nombre ).setAttribute('alt','Eliminar');
	    document.getElementById( nombre ).setAttribute('title','Eliminar');
		if(field=='img'){
				document.getElementById( 'input_img' ).value =document.getElementById( 'change1' ).name;
                document.getElementById('change1').setAttribute('style','opacity:1;');
				document.getElementById('div_img_publi').setAttribute('style','opacity:1;');
				document.getElementById('informa').setAttribute('style','opacity:1;overflow:auto;width:260px;');

	 	}



 }
